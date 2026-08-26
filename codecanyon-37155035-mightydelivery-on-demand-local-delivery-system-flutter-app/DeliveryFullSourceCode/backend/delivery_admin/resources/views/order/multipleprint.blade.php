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
            flex-direction: column;
            align-items: center;
            margin: 0;
        }

        .container {
            width: auto;
            max-width: 570px;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #37637e;
            background-color: #ffffff;
            margin-bottom: 20px; /* Space between printed labels */
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            vertical-align: top;
        }

        .mb-2 {
            margin-bottom: 8px;
        }
    </style>
</head>

<body>
    @foreach($orders as $order)
        <div class="container">
            <table>
                <tr>
                    <td style="text-align: center; vertical-align: top;">
                        <div class="mb-2">
                            <p style="margin: 0; text-align: center;"><strong>{{ $companyName->value }}</strong></p>
                            @if($labelnumber == 1)
                            <p style="margin: 0; text-align: center;">{{ $companyNumber->value }}</p>
                            @endif
                        </div>
                        <div style="margin-left: 20px; margin-right: 20px;">
                            <img src="data:image/png;base64,{{ $barcodeBase64[$order->id] }}" alt="Barcode" style="height: 61px; width: 279px;">
                        </div>
                        <div style="margin-left: 15px;">
                            <span class="font-weight-bold">{{ $order->milisecond }}</span>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    @endforeach
</body>

</html>
