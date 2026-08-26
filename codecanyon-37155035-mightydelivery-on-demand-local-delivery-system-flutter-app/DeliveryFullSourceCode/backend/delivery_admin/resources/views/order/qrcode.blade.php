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

    </style>
</head>

<body>
    <div class="container">
    <table>
        <tr>
            <td style="width: 60%; vertical-align: top;">
                <div class="mb-2">
                    <p style="margin: 0; text-align: center;"><strong>{{ $companyName->value }}</strong></p>
                    <p style="margin: 0; text-align: center;">{{ $companynumber->value }}</p>
                </div>
            </td>
        </tr>
        <tr>
            <td style="text-align: center; vertical-align: top; text-align: center;">
                <div style="margin-left: 20px; margin-right: 20px;">
                    <img src="data:image/png;base64,{{ $barcodeBase64 }}" alt="Barcode" style="height: 61px; width: 279px;">
                </div>
                <div style="margin-left: 15px;">
                    <span class="font-weight-bold">{{ $order->milisecond }}</span>
                </div>
            </td>
        </tr>
    </table>
    </div>
</body>

</html>
