<x-master-layout :assets="$assets ?? []">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block">
                    <div class="card-body pb-2 pt-2">
                        <body>
                            <div class="button-center">
                                <a href="#" onclick="printLabel({{ $id }})" class="btn btn-primary">{{__('message.print_label')}}</a>
                            </div>
                            <div class="container print-container">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <table class="print-table">
                                            <tr>
                                                <td style="width: 60%; vertical-align: top;">
                                                    <div style="display: flex;">
                                                        <img src="{{ getSingleMedia($invoice, 'company_logo') }}" class="print-logo" style="height: 80px; width: 80px; margin-right: 10px;">
                                                        <div>
                                                            <p style="margin: 0;"><strong>{{ $companyName->value ?? '' }}</strong></p>
                                                            <p style="margin: 0;">{{ $companyAddress->value ?? '' }}</p>
                                                            @if($labelnumber == 1)
                                                            <p style="margin: 0;">{{ $companynumber->value ?? '' }}</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                @if($order->is_shipped == 1)
                                                    <td style="width: 40%; text-align: right; vertical-align: top;">
                                                        <p style="margin: 0;"><strong>{{ __('message.shipped_via') }}:</strong></p>
                                                        <p style="margin: 0;">
                                                            <strong>{{__('message.name')}}:</strong> {{ optional($order->couriercompany)->name }}
                                                        </p>
                                                        <p style="margin: 0;">
                                                            <strong>{{ __('message.shipping_date') }}:</strong> {{ optional($order)->shipped_verify_at ? \Carbon\Carbon::parse($order->shipped_verify_at)->format('Y-m-d') : 'N/A' }}
                                                        </p>
                                                    </td>
                                                @endif
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <hr class="print-border-hr">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p style="margin: 0;"><strong>{{ __('message.from') }}:</strong></p>
                                                    @if($order->is_shipped == 1)
                                                        <p style="margin: 0;">
                                                                <b>{{ __('message.shipping_date') }}:</b> {{ optional($order)->shipped_verify_at ? \Carbon\Carbon::parse($order->shipped_verify_at)->format('Y-m-d') : 'N/A' }}
                                                        </p>
                                                    @endif
                                                    <p style="margin: 0;"><strong>{{ $order->pickup_point['name'] ?? null }}</strong></p>
                                                    <p style="margin: 0;">{{ $order->pickup_point['address'] }}</p>
                                                    <p style="margin: 0;"><b>{{ __('message.phone') }}:</b> {{ $order->pickup_point['contact_number'] }}</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <hr class="print-border-hr">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p style="margin: 0;"><strong>{{ __('message.to') }}:</strong></p>
                                                    <p style="margin: 0;"><strong>{{  $order->delivery_point['name'] ?? null }}</strong></p>
                                                    <p style="margin: 0;">{{ $order->delivery_point['address'] ?? null }}</p>
                                                    <p style="margin: 0;"><b>{{ __('message.phone') }}:</b> {{ $order->delivery_point['contact_number'] }}</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <hr class="print-border-hr">
                                                </td>
                                            </tr>
                                            <tr>
                                                @if($order->is_shipped == 1)
                                                    <td style="text-align: center; vertical-align: top; text-align: center;">
                                                        <div style="margin-left: 100px;">
                                                            <strong>{{ __('message.tracking_number') }}</strong>
                                                        </div>
                                                        <div style="margin-left: 119px;">
                                                            <img src="data:image/png;base64,{{ $barcodeBase64 }}" alt="Barcode" style="height: 61px; width: 279px;">
                                                        </div>

                                                        <div style="margin-left: 119px;">
                                                            <span>{{ $order->milisecond }}</span>
                                                        </div>
                                                    </td>
                                                @else
                                                    <td>
                                                        <div style="margin-left: 195px;">
                                                            <strong>{{ __('message.tracking_number') }}</strong>
                                                        </div>
                                                        <div style="margin-left: 119px;">
                                                            <img src="data:image/png;base64,{{ $barcodeBase64 }}" alt="Barcode" style="height: 61px; width: 279px;">
                                                        </div>
                                                        <div style="margin-left: 170px;">
                                                            <span>{{ $order->milisecond }}</span>
                                                        </div>
                                                    </td>
                                                @endif
                                            </tr>
                                            @if(!empty($order->delivery_point['instruction']) || !empty($order->packaging_symbols))
                                                <td colspan="2">
                                                    <hr class="print-border-hr">
                                                </td>
                                            @endif

                                            <tr>
                                                @if(isset($order->delivery_point['instruction']) && $order->delivery_point['instruction'] != null)
                                                    <td>
                                                        <strong>{{ __('message.shipping_Ins') }}</strong>
                                                        <p>{{ $order->delivery_point['instruction'] }}</p>
                                                    </td>
                                                @endif

                                                <td class="print-icons">
                                                    @php
                                                        $packagingSymbols = json_decode($order->packaging_symbols, true);
                                                    @endphp
                                                    @if (is_array($packagingSymbols))
                                                        @foreach ($packagingSymbols as $symbol)
                                                            @php
                                                                $icon = '';

                                                                switch ($symbol['key']) {
                                                                    case 'fragile':
                                                                        $icon = asset('images/fragile.png');
                                                                        break;
                                                                    case 'keep_dry':
                                                                        $icon = asset('images/keep-dry.png');
                                                                        break;
                                                                    case 'this_way_up':
                                                                        $icon = asset('images/up-arrows-couple-sign-for-packaging.png');
                                                                        break;
                                                                    case 'do_not_stack':
                                                                        $icon = asset('images/do-not-stack.png');
                                                                        break;
                                                                    case 'temperature_sensitive':
                                                                        $icon = asset('images/temperature.png');
                                                                        break;
                                                                    case 'recycle':
                                                                        $icon = asset('images/symbols.png');
                                                                        break;
                                                                    case 'do_not_use_hooks':
                                                                        $icon = asset('images/do-not-hook.png');
                                                                        break;
                                                                    case 'explosive_material':
                                                                        $icon = asset('images/flammable.png');
                                                                        break;
                                                                    case 'hazardous_material':
                                                                        $icon = asset('images/hazard.png');
                                                                        break;
                                                                    case 'perishable':
                                                                        $icon = asset('images/ice-cube.png');
                                                                        break;
                                                                    case 'do_not_open_with_sharp_objects':
                                                                        $icon = asset('images/knives.png');
                                                                        break;
                                                                    case 'bike_delivery':
                                                                        $icon = asset('images/fast-delivery.png');
                                                                        break;
                                                                    default:
                                                                        $icon = '';
                                                                        break;
                                                                }
                                                            @endphp
                                                            @if ($icon)
                                                                <div class="print-icon-container">
                                                                    <img src="{{ $icon }}" alt="Icon" height="35px" width="35px">
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </body>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-master-layout>
