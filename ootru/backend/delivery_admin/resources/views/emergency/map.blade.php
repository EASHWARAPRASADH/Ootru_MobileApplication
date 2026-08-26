<x-master-layout :assets="$assets ?? []">
    <div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title"> {{ $pageTitle . ' ' . Str::ucfirst(optional($emergency->deliveryMan)->name) . ' '. __('message.last_active').' '.  dateAgoFormate($emergency->deliveryMan->last_actived_at)}}</h4>
                        </div>
                        <a href="{{ route('emergency.index') }}" class="float-right ml-1 btn btn-sm btn-primary"><i class="fa fa-angle-double-left"></i> {{ __('message.back') }}</a>
                        
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row" style="padding:10px">
                            <div style="height: 700px;width: 2000px;" id="map"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @section('bottom_script')
        <script>
            $(document).ready(function () {
                let id = {{ optional($emergency->deliveryMan)->id ?? 0 }};
                let name = "{{ optional($emergency->deliveryMan)->name ?? '' }}";
                let created_at = "{{ optional($emergency->deliveryMan)?->last_actived_at ? \Carbon\Carbon::parse($emergency->deliveryMan->last_actived_at)->format('Y-m-d H:i:s') : '' }}";
                let latitude = {{ $latitude ?? 0 }};
                let longitude = {{ $longitude ?? 0 }};
                let status = {{ $status ?? 0 }};

                let map, marker, infoWindow;

                function initMap() {
                    const location = { lat: latitude, lng: longitude };
                    map = new google.maps.Map(document.getElementById("map"), {
                        zoom: 15,
                        center: location,
                    });

                    marker = new google.maps.Marker({
                        position: location,
                        map: map,
                    });

                    infoWindow = new google.maps.InfoWindow();

                    marker.addListener('click', function () {
                        let order_view = "{{ route('deliveryman.show', '') }}/" + id;
                        var contentString = '<div class="map_driver_detail"><ul class="list-unstyled mb-0">' +
                            '<li><i class="fa fa-address-card" aria-hidden="true"></i>: ' + name + '</li>' +
                            '<li><i class="fa fa-clock"></i>: ' + created_at + '</li>' +
                            '<li><i class="fa fa-map-pin"></i>: ' + latitude + ', ' + longitude + '</li>' +
                            '<li><a href="' + order_view + '"><i class="fa fa-eye" aria-hidden="true"></i> {{ __("message.view_form_title",[ "form" => __("message.delivery_man") ]) }}</a></li>' +
                            '</ul></div>';
                        infoWindow.setContent(contentString);
                        infoWindow.open(map, marker);
                    });
                }

                initMap();

                if (status === 0 || status === 1) {
                    setInterval(() => {
                        fetch(`{{ route('emergency.location', $id ?? 1) }}`)
                            .then(res => res.json())
                            .then(data => {
                                if (data.latitude && data.longitude) {
                                    const newLocation = {
                                        lat: parseFloat(data.latitude),
                                        lng: parseFloat(data.longitude)
                                    };
                                    marker.setPosition(newLocation);
                                    map.setCenter(newLocation);
                                }
                            })
                            .catch(err => console.log("Error:", err));
                    }, 10000);
                }
            });
        </script>
    @endsection
</x-master-layout>
