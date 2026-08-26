<!DOCTYPE html>
<html>
<head>
    <title>{{ __('message.invoice') }}</title>
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
            background-color: #ffffff;
            font-size: 13px;
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
            text-align: right;
        }
        .addresspickupdetails {
            text-align: left;
        }
        .addressdetails{
            text-align: right;
        }
        .mydetails {
            width: 100%;
            margin-bottom: 20px;
        }
        .details, .items, .totals {
            width: 100%;
            margin-bottom: 0;
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
                    <img src="{{ getSingleMedia($invoice,'company_logo') }}" width="80px">
                    <h2>{{ optional($companyName)->value }}</h2>
                    <strong>{{ __('message.contact_number') }}:</strong> {{ optional($companyNumber)->value }}<br>
                </td>
                <td class="invoice-details">
                    <strong>{{ __('message.invoice_no') }}</strong> 8021<br>
                    <strong>{{ __('message.invoice_date') }}</strong> {{ $today }}<br>
                    <strong>{{ __('message.order_date') }}</strong> 14/08/2024 <br>
                    <strong>{{ __('message.order_tracking') }}:</strong> 1650230335000<br>


                </td>
            </tr>
        </table>
        <table class="mydetails">
            <tr>
                <td class="addresspickupdetails">
                    <strong>{{ __('message.pickup_from') }}</strong><br>
                     Rajkot, Gujarat, India
                </td>
                <td class="addressdetails">
                    <strong>{{ __('message.deliverd_to') }}</strong><br>
                    Rajkot, Gujarat, India
                </td>
            </tr>
        </table>
        <table class="mydetails">
            <tr>
                <td class="addresspickupdetails">
                    <strong>{{ __('message.payment_via') }}:</strong> Cash
                </td>
                <td class="addressdetails">
                    <strong>{{ __('message.payment_date') }}:</strong> 14/08/2024
                </td>
            </tr>
        </table>

        <table class="items">
            <thead>
                <tr>
                    <th class="addresspickupdetails">{{ __('message.description') }} ({{ __('message.document') }})</th>
                    <th class="addressdetails">{{ __('message.price') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ __('message.delivery_charges') }}</td>
                    <td class="addressdetails">${{ number_format(30.00, 2) }}</td>
                </tr>
                <tr>
                    <td>{{ __('message.distance_charge') }}</td>
                    <td class="addressdetails">${{ number_format(50.00, 2) }}</td>
                </tr>
                <tr>
                    <td>{{ __('message.weight_charge') }}</td>
                    <td class="addressdetails">${{ number_format(40.00, 2) }}</td>
                </tr>

                    <tr>
                        <td>{{ __('message.vehicle_charge') }}</td>
                        <td class="addressdetails">${{ number_format(300.00, 2) }}</td>
                    </tr>
            </tbody>
        </table>
        <table class="totals">
            <tr class="totalsfinal">
                <td>{{__('message.total')}}</td>
                <td>${{ number_format( (float) 420.00) ?? number_format( (float) 920.00) }}</td>
            </tr>
        </table>
        <p class="address"><strong>{{ __('message.address') }}:</strong></p>
        <p class="address">{{ optional($companyAddress)->value }}</p>
        <p class="note">
            <strong>{{ __('message.notes') }}:</strong> {{ __('message.this_report_was_generated_by_a_computer_and_does_not_require_a_signature_or_company_stamp_to_be_considered_valid') }}
        </p>
    </div>
</body>
</html>
