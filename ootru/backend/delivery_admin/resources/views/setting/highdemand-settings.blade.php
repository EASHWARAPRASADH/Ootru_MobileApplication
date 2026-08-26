{!! html()->modelForm($setting_value, 'POST', route('settingUpdate'))->attribute('enctype', 'multipart/form-data')->open() !!}

{!! html()->hidden('id', null) !!}
{!! html()->hidden('page', $page) !!}

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="row">
    @foreach ($setting as $groupKey => $groupItems)
        <div class="col-md-12 card shadow mb-4">
            <div class="card-header">
                <h4>{{ str_replace('_', ' ', $groupKey) }}</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach ($groupItems as $fieldKey => $fieldValue)
                        @php
                            $fullKey = "{$groupKey}_{$fieldKey}";
                            $existingValue = collect($setting_value)->firstWhere('key', $fullKey);
                            $inputValue = $existingValue->value ?? '';
                            $columnClass = $groupKey === 'FIREBASE' ? 'col-md-12' : 'col-md-6';
                            $inputType = $groupKey === 'COLOR' ? 'color' : 'number';
                            $tooltips = [
                                'MAP_RED' =>
                                    '🟥 Show zone as RED if available riders are less than this % of total orders.',
                                'MAP_ORANGE' =>
                                    '🟧 Show zone as ORANGE if available riders are less than this % but more than red zone.',
                                'MAP_YELLOW' =>
                                    '🟨 Show zone as YELLOW if available riders are less than this % but more than orange zone.',
                            ];
                            $tooltip = $tooltips[$fullKey] ?? null;
                        @endphp

                        <div class="{{ $columnClass }} col-sm-12">
                            <div class="form-group">
                                <label for="{{ $fullKey }}">
                                    {{ str_replace('_', ' ', $fieldKey) }}
                                    @if ($tooltip)
                                        <i class="fas fa-info-circle" data-toggle="tooltip" data-placement="right"
                                            title="{{ $tooltip }}" style="cursor: pointer; color: #007bff;"></i>
                                    @endif
                                </label>

                                {!! html()->hidden('type[]', $groupKey) !!}
                                <input type="hidden" name="key[]" value="{{ $fullKey }}">
                                <input type="{{ $inputType }}" name="value[]" value="{{ $inputValue }}"
                                    id="{{ $fullKey }}" {{ $inputType === 'number' ? "min=0 step='any'" : '' }}
                                    class="form-control form-control-lg"
                                    placeholder="{{ str_replace('_', ' ', $fieldKey) }} IN %"
                                    oninput="enforceMaxValue(this)">
                                <div class="text-warning small mt-1" id="warn_{{ $fullKey }}"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach
</div>

{!! html()->submit(__('message.save'))->class('btn btn-primary float-right') !!}
{!! html()->form()->close() !!}


<script>
    function enforceMaxValue(input) {
        const warning = document.getElementById('warn_' + input.id);
        const value = parseInt(input.value);

        if (value > 100) {
            input.value = 100;
            warning.textContent = "Value cannot exceed 100. It has been reset.";
        } else {
            warning.textContent = "";
        }

        validateMapThresholds();
    }

    function validateMapThresholds() {
        const red = parseInt(document.getElementById('MAP_RED')?.value) || 0;
        const orange = parseInt(document.getElementById('MAP_ORANGE')?.value) || 0;
        const yellow = parseInt(document.getElementById('MAP_YELLOW')?.value) || 0;

        const warnRed = document.getElementById('warn_MAP_RED');
        const warnOrange = document.getElementById('warn_MAP_ORANGE');
        const warnYellow = document.getElementById('warn_MAP_YELLOW');

        // Clear previous warnings
        warnRed.textContent = '';
        warnOrange.textContent = '';
        warnYellow.textContent = '';

        if (red <= orange || red <= yellow) {
            warnRed.textContent = "RED must be greater than ORANGE and YELLOW.";
        }

        if (orange <= yellow) {
            warnOrange.textContent = "ORANGE must be greater than YELLOW.";
        }
    }

    $(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
