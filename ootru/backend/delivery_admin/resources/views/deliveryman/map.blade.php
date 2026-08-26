<x-master-layout :assets="$assets ?? []">
    <div>
        <?php $id = $id ?? null;?>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">{{ $pageTitle }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row" style="padding:10px">
                            <div style="height: 700px;width: 1600px;" id="location"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @section('bottom_script')
        <script>
            var map;
            var marker;
            var geocoder;
            var markers = [];
            var infoWindow;

            function initMap() {
                geocoder = new google.maps.Geocoder();
                infoWindow = new google.maps.InfoWindow();

                if ('{{ $id }}' !== '' && '{{ $id }}' !== undefined) {
                        var get_latitude_data = {{ $data->latitude ?? 0 }};
                        var get_longitude_data = {{ $data->longitude ?? 0 }};
                        var myLatlng = {lat : get_latitude_data, lng: get_longitude_data};
                        initializeMap(myLatlng);
                    } else {
                        // Use Geolocation to set user's current location
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(function(position) {
                                var myLatlng = {
                                    lat: position.coords.latitude,
                                    lng: position.coords.longitude
                                };
                                initializeMap(myLatlng);
                            }, function(error) {
                                // Use default coordinates if error or denied permission
                                var myLatlng = {lat: 22.316258503105992, lng: 70.83204484790207};
                                initializeMap(myLatlng);
                            });
                        } else {
                            // If browser doesn't support Geolocation, use default coordinates
                            var myLatlng = {lat: 22.316258503105992, lng: 70.83204484790207};
                            initializeMap(myLatlng);
                        }
                    }
                }

                function initializeMap(myLatlng) {
                    map = new google.maps.Map(document.getElementById('location'), {
                        zoom: 4,
                        center: myLatlng
                    });

                    marker = new google.maps.Marker({
                        position: myLatlng,
                        map: map,
                        draggable: false
                    });

                    google.maps.event.addListener(marker, 'dragend', function () {
                        updateMarkerPosition(marker.getPosition());
                    });

                    @foreach($deliveryMen as $deliveryMan)
                        var latlng = {lat: {{ $deliveryMan->latitude }}, lng: {{ $deliveryMan->longitude }}};
                        var name = "{{ $deliveryMan->name }}";
                        var id = "{{ $deliveryMan->id }}";
                        var created_at = "{{ $deliveryMan->created_at }}";
                        var latitude = "{{ $deliveryMan->latitude }}";
                        var longitude = "{{ $deliveryMan->longitude }}";
                        addMarker(latlng, name, id,created_at,latitude,longitude);
                    @endforeach
                }

                function placeMarker(location) {
                    if (marker) {
                        marker.setPosition(location);
                    } else {
                        marker = new google.maps.Marker({
                            position: location,
                            map: map,
                            draggable: false
                        });
                    }
                    updateMarkerPosition(location);
                }

                function updateMarkerPosition(location) {
                    document.getElementById('latitude').value = location.lat();
                    document.getElementById('longitude').value = location.lng();
                    geocoder.geocode({ 'location': location }, function (results, status) {
                        if (status === 'OK') {
                            if (results[0]) {
                                var fullAddress = results[0].formatted_address;
                                var city, state, country;

                                for (var i = 0; i < results[0].address_components.length; i++) {
                                    var component = results[0].address_components[i];
                                    if (component.types.includes("administrative_area_level_3")) {
                                        city = component.long_name;
                                    }
                                    if (component.types.includes("administrative_area_level_1")) {
                                        state = component.long_name;
                                    }
                                    if (component.types.includes("country")) {
                                        country = component.long_name;
                                    }
                                }
                                    document.getElementById('address').value = fullAddress || null;
                                    document.getElementById('city').value = city || null;
                                    document.getElementById('state').value = state || null;
                                    document.getElementById('country').value = country || null;

                            } else {
                                alert("{{ __('message.no_result_found') }}");
                            }
                        } else {
                            alert("{{ __('message.geocoder_failed') }} " + status);
                        }
                    });
                }

                function addMarker(location, name, id,created_at,latitude,longitude) {
                    var marker = new google.maps.Marker({
                        position: location,
                        map: map,
                        title: name
                    });

                    markers.push(marker);

                    marker.addListener('click', function() {
                        order_view = "{{ route('deliveryman.show', '' ) }}/"+id;
                        var contentString = '<div class="map_driver_detail"><ul class="list-unstyled mb-0">'+
                                    '<li><i class="fa fa-address-card" aria-hidden="true"></i>: '+name  +'</li>'+
                                    '<li><i class="fa fa-clock"></i>: '+created_at  +'</li>'+
                                    '<li><i class="fa fa-map-pin"></i>: '+ latitude + ', ' + longitude +'</li>' +
                                    '<li><a href="'+order_view+'"><i class="fa fa-eye" aria-hidden="true"></i> {{ __("message.view_form_title",[ "form" => __("message.delivery_man") ]) }}</a></li>'+
                                    '</ul></div>';
                        infoWindow.setContent(contentString);
                        infoWindow.open(map, marker);
                    });
                 }
                window.onload = initMap;

        </script>
@endsection
</x-master-layout>
