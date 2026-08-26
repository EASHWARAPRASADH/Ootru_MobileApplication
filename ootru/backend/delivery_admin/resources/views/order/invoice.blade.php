<!-- resources/views/invoice.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>{{__('message.invoice')}}</title>
    <style>
        body {
            color: #555;
            margin: 0;
            padding: 0;
            font-family: "DejaVu Sans", sans-serif;
        }
        .invoice-container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            background-color: #ffffff`;
            font-size:13px;
        }
        .myheader {
           display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .mydetails h2 {
            margin: 0;

            font-size: 28px;
            color: #333;
        }
        .mydetails .invoice-details {
            vertical-align: text-top;
            text-align: right;

        }

        .addresspickupdetails  {
            text-align: left;
        }
        .addressdetails  {
            text-align: right;
        }
        .mydetails {
            width: 100%;
            margin-bottom: 20px;
        }
        .details, .items, .totals {
            width: 100%;
            margin-bottom: 0px;
        }
        .details td, .items td, .items th, .totals td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        .details td {
            width: 50%;
        }
        .items th {
            background-color: #f0f0f0;
        }
        .totals {
            text-align: right;
        }
        .totalsfinal {
            text-align: right;
            font-weight: bold;
            font-size: 20px;
        }
        .totals td:last-child {
            font-weight: bold;
        }
        .note {
            margin-top: 20px;
            font-size: 12px;
            color: #777;
        }
        .address {
            text-align: center;
            font-size: 12px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <table class="mydetails">
            <tr>
                <td>
                    <img src="{{ getSingleMediaSettingImage($invoice,'company_logo') }}" width="80px">
                    <h2> {{optional($companyName)->value }}  </h2>
                    <strong>{{__('message.contact_number')}}:</strong> {{ optional($companynumber)->value }}<br>
                </td>
                <td class="invoice-details">
                    <strong>{{__('message.invoice_no')}}</strong> {{ optional($order)->id }}<br>
                    <strong>{{__('message.invoice_date')}}</strong> {{ $today }}<br>
                <strong>{{__('message.order_date')}}</strong> {{date('d/m/Y', strtotime($order->created_at)) }}
                </td>
            </tr>
        </table>
         <table class="mydetails">
            <tr>
                <td class="addresspickupdetails">
                    <strong>{{__('message.pickup_from')}}</strong><br>
                    {{ $order->pickup_point['address'] }}
                </td>
                <td class="addressdetails">
                    <strong>{{__('message.deliverd_to')}}</strong><br>
                    {{ $order->delivery_point['address'] }}
                </td>
            </tr>
        </table>

        <table class="mydetails">
            <tr>
                <td class="addresspickupdetails"><strong>{{__('message.payment_via').''.':'}}</strong> {{ ucfirst(optional($order->payment)->payment_type) }}</td>
                <td class="addressdetails"><strong>{{__('message.payment_date').''.':'}}</strong> {{ date('d/m/Y', strtotime($order->payment->created_at ?? null)) }}</td>
            </tr>
        </table>

        <table class="items">
            <thead>
                <tr>
                    <th class="addresspickupdetails">{{__('message.description')}} ({{ optional($order)->parcel_type }})</th>
                    <th class="addressdetails">{{__('message.price')}}</th>
                </tr>
            </thead>
            <tbody>
                @if($order->bid_type == 0)
                    <tr>
                        <td>{{__('message.delivery_charges')}}</td>
                        <td class="addressdetails">{{ getPriceFormat($order->fixed_charges) }}</td>
                    </tr>
                    <tr>
                        <td>{{__('message.distance_charge')}}</td>
                        <td class="addressdetails">{{ getPriceFormat($order->distance_charge) }}</td>
                    </tr>
                    <tr>
                        <td>{{__('message.weight_charge')}}</td>
                        <td class="addressdetails"> {{ getPriceFormat($order->weight_charge) }}</td>
                    </tr>
                    @if(!is_null($order->vehicle_charge) && $order->vehicle_charge > 0)
                        <tr>
                            <td>{{ __('message.vehicle_charge') }}</td>
                            <td class="addressdetails">{{ getPriceFormat($order->vehicle_charge) }}</td>
                        </tr>
                    @endif
                    @if(!is_null($order->insurance_charge) && $order->insurance_charge > 0)
                    <tr>
                        <td>{{__('message.insurance_charge')}}</td>
                        <td class="addressdetails"> {{ getPriceFormat($order->insurance_charge) }}</td>
                    </tr> 
                    @endif
                @endif
            @php
                $extra_charges_values = [];
                $grand_total = 0;
                $service_class = 'd-none';
                $percentage = 0;
                $extra_charges_texts = [];
                if($order->bid_type == 0){
                    $sub_total = $order->fixed_charges + $order->distance_charge + $order->weight_charge + $order->vehicle_charge + $order->insurance_charge ;
                }else{
                    $sub_total = $order->total_amount;
                }
                if(is_array($order->extra_charges)){
                    foreach($order->extra_charges as $item){
                        if (isset($item['value_type'])) {
                            $formatted_value = ($item['value_type'] == 'percentage') ? $item['value'] . '%' : getPriceFormat($item['value']);
                            if ($item['value_type'] == 'percentage') {
                                $data_value = $sub_total * $item['value'] / 100;
                                $key = str_replace('_', ' ', ucfirst($item['key']));
                                $extra_charges_texts[] = $key . ' (' . $formatted_value . ')';
                                $extra_charges_values[] = getPriceFormat($data_value);
                            } else {
                                $key = str_replace('_', ' ', ucfirst($item['key']));
                                $extra_charges_texts[] = $key . ' (' . $formatted_value . ')';
                                $extra_charges_values[] = $formatted_value;
                            }
                        }
                    }
                    if(isset($item['value_type'])){
                        $values = [];
                        $countFixed = 0;
                        foreach ($order->extra_charges as $item) {
                            if (in_array($item['value_type'], ['percentage', 'fixed'])) {
                                if ($item['value_type'] == 'percentage') {
                                    $values[] = $sub_total * $item['value'] / 100;
                                } elseif ($item['value_type'] == 'fixed') {
                                    $values[] = $item['value'];
                                }
                            }
                        }
                        $percentage = array_sum($values);
                    }
                }
            @endphp
            </tbody>
        </table>

        <table class="totals">
            @if($order->extra_charges != null)
                <tr>
                    <td>{{__('message.sub_total')}}</td>
                    <td class="addressdetails">{{ getPriceFormat($sub_total) }}</td>
                </tr>
                @foreach ($extra_charges_texts as $index => $text)
                    <tr>
                        <td>{{ $text }}</td>
                        <td class="addressdetails">{{ $extra_charges_values[$index] }}</td>
                    </tr>
                @endforeach
            @else

            @endif
            <tr class="totalsfinal">
                <td>{{__('message.total')}}</td>
                <td>{{ getPriceFormat($percentage + $sub_total) ?? getPriceFormat($sub_total) }}</td>
            </tr>
        </table>
        <p class="address"><strong>{{ __('message.address') . ':' }}</strong></p>
        <p class="address">{{ optional($companyAddress)->value }}</p>
        <p class="note"><strong> {{ __('message.notes') . ':' }}</strong> {{ __('message.this_report_was_generated_by_a_computer_and_does_not_require_a_signature_or_company_stamp_to_be_considered_valid') }}</p>
    </div>
</body>
</html>
