<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            width: auto;
            max-width: 570px;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #37637e;
            background-color: #ffffff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            vertical-align: top;
        }

        .logo {
            width: 70px;
            height: auto;
            margin-right: 15px;
        }

        .icons {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .icon-container {
            flex: 1 0 11.66%;
            box-sizing: border-box;
            text-align: center;
        }

        .border-hr {
            border: 1px solid #808080;
            margin-left: -11px;
            width: 103.6%;
        }

        .shipping-info {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        @media (prefers-color-scheme: light) {
            :root {
            --font-color: black; /* Use dark text for light mode */
            }
        }

  /* Dark mode */
        @media (prefers-color-scheme: dark) {
            :root {
            --font-color: rgb(255, 255, 255); /* Use light text for dark mode */
            }
        }

    </style>
</head>

<body>
    <div class="container">
    <table>
        <tr>
            <td style="width: 60%; vertical-align: top;">
                <div style="display: flex;">
                    <img src="{{ getSingleMedia($invoice, 'company_logo') }}" class="logo" style="height: 80px; width: 80px; margin-right: 10px;">
                    <div>
                        <p style="margin: 0; color: var(--font-color);" ><strong>{{ $companyName->value ?? '' }}</strong></p>
                        <p style="margin: 0; color: var(--font-color);">{{ $companyAddress->value ?? '' }}</p>
                        @if($labelnumber == 1)
                            <p style="margin: 0; color: var(--font-color);">{{ $companynumber->value ?? '' }}</p>
                        @endif
                    </div>
                </div>
            </td>
            @if($order->is_shipped == 1)
                <td style="width: 40%; text-align: right; vertical-align: top;">
                    <p style="margin: 0;"><strong>{{ __('message.shipped_via') }}:</strong></p>
                    <p style="margin: 0;" >
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
                <hr class="border-hr">
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
                <hr class="border-hr">
            </td>
        </tr>
        <tr>
            <td>
                <p style="margin: 0;"><strong>{{ __('message.to') }}:</strong></p>
                <p style="margin: 0;"><strong>{{  $order->delivery_point['name'] ?? null }}</strong></p>
                <p style="margin: 0;">{{ $order->delivery_point['address'] ?? null }}</p>
                <p style="margin: 0;">{{ __('message.phone') }}: {{ $order->delivery_point['contact_number'] }}</p>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <hr class="border-hr">
            </td>
        </tr>
        <tr>
            <td>
                <div style="margin-left: 150px;">
                    <strong>{{ __('message.tracking_number') }}</strong>
                </div>
                <div style="margin-left: 50px;">
                    <img src="data:image/png;base64,{{ $barcodeBase64  }}" alt="Barcode" style="height: 61px; width: 279px;">
                </div>
                <div style="margin-left: 110px;">
                    <span>{{ $order->milisecond }}</span>
                </div>
            </td>
        </tr>
        @if(!empty($order->delivery_point['instruction']) || !empty($order->packaging_symbols))
            <td colspan="2">
                <hr class="border-hr">
            </td>
        @endif

        <tr>
            @if(isset($order->delivery_point['instruction']) && $order->delivery_point['instruction'] != null)
                <td>
                    <strong>{{ __('message.shipping_Ins') }}</strong>
                    <p>{{ $order->delivery_point['instruction'] }}</p>
                </td>
            @endif

            <td class="icons">
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
                            <div class="icon-container">
                                <img src="{{ $icon }}" alt="Icon" height="35px" width="35px">
                            </div>
                        @endif
                    @endforeach
                @endif
            </td>
        </tr>
    </table>
    </div>
</body>

</html>
