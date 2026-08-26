<x-master-layout :assets="$assets ?? []">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title ">{{ $pageTitle  ?? ''}}</h4>
                        </div>
                        <div class="card-header-toolbar">
                            @if(isset($button))
                            {!! $button !!}
                            @endif
                            @if(isset($reset_file_button))
                            {!! $reset_file_button !!}
                            @endif
                            @if(isset($filter_file_button))
                            {!! $filter_file_button !!}
                            @endif
                        </div>
                    </div>

                    <div class="card-body">

                        <div class="card-header-toolbar">
                            @if(isset($multi_checkbox_delete))
                               {!! $multi_checkbox_delete !!}
                            @endif
                            @if(isset($multi_checkbox_print))
                            {!! $multi_checkbox_print !!}
                            @endif
                            @if(isset($multi_checkbox_print_label))
                            {!! $multi_checkbox_print_label !!}
                            @endif
                        </div>
                        {{ $dataTable->table(['class' => 'table  w-100'],false) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @section('bottom_script')
       {{ $dataTable->scripts() }}
       <script>
        
            var state = null;
            var get_country_id = $('#country_id').val();
            var country_city_route = '';
            $(document).on('change', '#country_id', function () {
                get_country_id = $(this).val();
                runFunctionAfterChange();
            });
            runFunctionAfterChange();
            function runFunctionAfterChange() {
                var country = get_country_id;
                $('#city_id').empty();
                propertList(country);
            }
            function propertList(country=null) {
                
                // var country_city_route = "{{ route('ajax-list', ['type' => 'city-list-filter', 'country_id' => '']) }}" + country;
                if (get_country_id) {
                    country_city_route = "{{ route('ajax-list', ['type' => 'city-list-filter', 'country_id' => '']) }}" + country;
                } else {
                    country_city_route = "{{ route('ajax-list', ['type' => 'city-list']) }}";
                }
                country_city_route = country_city_route.replace('amp;', '');

                $.ajax({
                    url: country_city_route,
                    success: function (result) {
                        
                        $('#city_id').select2({
                            width: '100%',
                            placeholder: "{{ __('message.select_name', ['select' => __('message.city')]) }}",
                            data: result.results
                        });

                        if (state !== null) {
                            $('#city_id').val(state).trigger('change');
                        }
                    }
                });
            }
        
    </script>
    @endsection
</x-master-layout>
