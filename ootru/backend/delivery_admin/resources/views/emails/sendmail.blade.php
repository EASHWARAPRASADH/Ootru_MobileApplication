<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f9f9f9;
            padding: 30px;
            margin: 0;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .order-content {
            background-color: #f1f1f1;
            border-left: 5px solid {{ $themeColor }};
            padding: 15px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 14px;
            background-color: #f1f1f1;
            color: #555;
        }
        .footer a {
            color: {{ $themeColor }};
            text-decoration: none;
        }
    </style>
</head>
    <body>
        <div class="container">
            <div class="order-content">
                {!! $content !!}
            </div>

            <div class="footer">
                {{ config('app.name') }}<br>
                <a href="{{ route('email-order', $order_id) }}">{{ __('message.our_website') }}</a>
            </div>
        </div>
    </body>
</html>
