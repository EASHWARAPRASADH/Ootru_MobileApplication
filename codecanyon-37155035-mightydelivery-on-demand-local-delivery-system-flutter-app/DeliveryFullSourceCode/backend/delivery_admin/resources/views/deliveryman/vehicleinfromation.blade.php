<div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ __('message.vehicle_information') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="card-body p-0 mt-0">
                            <div class="table-responsive mt-1">
                                <table id="basic-table" class="table mb-0 text-center" role="grid">
                                    <tbody>
                                            @foreach($ordervehiclehistory as $value)
                                                <tr>
                                                    <td>
                                                        @php
                                                            // $vehicleInfo = json_decode($value->vehicle_info, true);
                                                            $vehicleInfo = is_string($value->vehicle_info) ? json_decode($value->vehicle_info, true) : $value->vehicle_info;
                                                        @endphp
                                                        <div class="d-flex align-items-center mb-3">
                                                            <p class="mb-0">
                                                                <span class="font-weight-bold">{{ __('message.vehicle_name') }}</span> : {{ $vehicleInfo['make'] ?? '-' }}
                                                            </p>
                                                        </div>
                                                        <div class="d-flex align-items-center mb-3">
                                                            <p class="mb-0">
                                                                <span class="font-weight-bold">{{ __('message.vehicle_color') }}</span> : {{ $vehicleInfo['color'] ?? '-' }}
                                                            </p>
                                                        </div>
                                                        <div class="d-flex align-items-center mb-3">
                                                            <p class="mb-0">
                                                                <span class="font-weight-bold"> {{ __('message.vehicle_model') }}</span> : {{ $vehicleInfo['model'] ?? '-'  }}
                                                            </p>
                                                        </div>
                                                        <div class="d-flex align-items-center mb-3">
                                                            <p class="mb-0">
                                                                <span class="font-weight-bold">{{ __('message.vehicle_address') }}</span> : {{ $vehicleInfo['address'] ?? '-' }}
                                                            </p>                                                        </div>
                                                        <div class="d-flex align-items-center mb-3">
                                                            <p class="mb-0">
                                                                <span class="font-weight-bold">{{ __('message.vehicle_fuel_type') }}</span> : {{ $vehicleInfo['fuel_type'] ?? '-' }}
                                                            </p> 
                                                        </div>
                                                        <div class="d-flex align-items-center mb-3">
                                                            <p class="mb-0">
                                                                <span class="font-weight-bold">{{ __('message.vehicle_owner_number') }}</span> : {{ $vehicleInfo['owner_number'] ?? '-' }}
                                                            </p> 
                                                        </div>
                                                        <div class="d-flex align-items-center mb-3">
                                                            <p class="mb-0">
                                                                <span class="font-weight-bold">{{ __('message.vehicle_current_mileage') }}</span> : {{ $vehicleInfo['current_mileage'] ?? '-' }}
                                                            </p>
                                                        </div>
                                                        <div class="d-flex align-items-center mb-3">
                                                            <p class="mb-0">
                                                                <span class="font-weight-bold">{{ __('message.vehicle_registration_date') }}</span> : {{ dateAgoFormate($vehicleInfo['registration_date']) ?? '-' }}
                                                            </p>
                                                        </div>
                                                        <div class="d-flex align-items-center mb-3">
                                                            <p class="mb-0">
                                                                <span class="font-weight-bold">{{ __('message.vehicle_transmission_type') }}</span> : {{ $vehicleInfo['transmission_type'] ?? '-' }}
                                                            </p>
                                                        </div>
                                                        <div class="d-flex align-items-center mb-3">
                                                            <p class="mb-0">
                                                                <span class="font-weight-bold">{{ __('message.vehicle_year_of_manufacture') }}</span> : {{ $vehicleInfo['year_of_manufacture'] ?? '-' }}
                                                            </p>
                                                        </div>
                                                        <div class="d-flex align-items-center mb-3">
                                                            <p class="mb-0">
                                                                <span class="font-weight-bold">{{ __('message.vehicle_license_plate_number') }}</span> : {{ $vehicleInfo['license_plate_number'] ?? '-' }}
                                                            </p>
                                                        </div>
                                                        <div class="d-flex align-items-center mb-3">
                                                            <p class="mb-0">
                                                                <span class="font-weight-bold">{{ __('message.vehicle_identification_number') }}</span> : {{ $vehicleInfo['vehicle_identification_number'] ?? '-' }}
                                                            </p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                    </tbody>  
                                </table>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
