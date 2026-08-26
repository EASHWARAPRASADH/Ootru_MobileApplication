<x-master-layout :assets="$assets ?? []">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block">
                    <div class="card-body pb-2 pt-2">
                        <body>
                            <div class="button-center">
                                <a href="#" onclick="printbarcode({{ $id }})" class="btn btn-primary">{{__('message.print_barcode')}}</a>
                            </div>
                                <div class="container col-lg-12 print-container">
                                    <table class="print-table">
                                        <tr>
                                            <td style="width: 60%; vertical-align: center;">
                                                <div class="mb-2">
                                                    <p style="margin: 0; text-align: center;"><strong>{{ $companyName->value }}</strong></p>
                                                    @if($labelnumber == 1)
                                                    <p style="margin: 0; text-align: center;">{{ $companynumber->value }}</p>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: center; vertical-align: top; text-align: center;">
                                                <div style="margin-left: 15px;">
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-master-layout>
