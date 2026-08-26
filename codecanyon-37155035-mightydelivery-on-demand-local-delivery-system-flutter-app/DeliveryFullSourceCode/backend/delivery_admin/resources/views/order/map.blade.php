<x-master-layout :assets="$assets ?? []">
    <div>
        <?php $id = $id ?? null;?>
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3">
                            <h5 class="font-weight-bold">{{ $pageTitle }}</h5>
                            <div class="float-right">
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                            {{ html()->label(__('message.filter_order'))->for('status_filter') }}
                                            {{ html()->form('GET', route('ordermap'))->id('filterForm')->open() }}
                                                <div class="form-group mb-0">
                                                    {{ html()->select('status_filter', [
                                                        'create' => __('message.accepted'),
                                                        'courier_assigned' => __('message.assigned'),
                                                        'courier_arrived' => __('message.arrived'),
                                                        'courier_picked_up' => __('message.picked_up'),
                                                        'courier_departed' => __('message.departed'),
                                                    ], $selectedStatus)->class('form-control select2js')->id('status_filter') }}
                                                </div>
                                            {{ html()->form()->close() }}
                                    </div>
                                    <div class="col-md-3 pr-2 mt-3">
                                        <div class="d-flex">
                                            <button type="submit" form="filterForm" class="btn btn-sm btn-primary">{{__('message.apply')}}</button>
                                            <a href="{{ route('ordermap') }}" class="btn btn-sm btn-success text-dark ml-2">{{__('message.reset')}}</a>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group ml-12">
                                            <img src="{{ asset("images/ic_pickup_pin.png") }}" alt="Pickup Pin Icon">
                                            <label for="pickup-pin">{{__('message.pickup_location')}}</label>
                                            <img src="{{ asset("images/ic_drop_pin.png") }}" alt="Drop Pin Icon">
                                            <label for="delivery-pin">{{__('message.delivery_location')}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                            <div style="height: 700px;width: 2000px;" id="location"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @section('bottom_script')
        <script>
            var map;
            var markers = [];
            var infoWindow;

            function initMap() {
                var myLatlng = {lat: 22.316258503105992, lng: 70.83204484790207};
                map = new google.maps.Map(document.getElementById('location'), {
                    zoom: 4,
                    center: myLatlng
                });

                infoWindow = new google.maps.InfoWindow();

                @foreach($pickupPoints as $pickupPoint)
                    var pickupLatLng = {lat: {{ $pickupPoint['latitude'] }}, lng: {{ $pickupPoint['longitude'] }}};
                    addMarker(pickupLatLng, '{{ $pickupPoint['address'] }}', '{{ asset("images/ic_pickup_pin.png") }}', '{{ $pickupPoint['status'] }}', '{{ $pickupPoint['order_id'] }}', '{{ $pickupPoint['created_at'] }}');
                @endforeach


                @foreach($deliveryPoints as $deliveryPoint)
                    var deliveryLatLng = {lat: {{ $deliveryPoint['latitude'] }}, lng: {{ $deliveryPoint['longitude'] }}};
                    addMarker(deliveryLatLng, '{{ $deliveryPoint['address'] }}', '{{ asset("images/ic_drop_pin.png") }}', '{{ $deliveryPoint['status'] }}', '{{ $deliveryPoint['order_id'] }}', '{{ $deliveryPoint['created_at'] }}');
                @endforeach
            }

            function addMarker(location, title, iconUrl, status, orderId, created_at) {
                var marker = new google.maps.Marker({
                    position: location,
                    map: map,
                    title: title,
                    icon: {
                        url: iconUrl,
                        scaledSize: new google.maps.Size(32, 32)
                    },
                    status: status,
                    orderId: orderId,
                    created_at: created_at
                });
                markers.push(marker);

                marker.addListener('click', function() {
                    var order_view = "{{ route('order.show', '' ) }}/"+this.orderId;
                    var contentString = '<div class="map_driver_detail"><ul class="list-unstyled mb-0">'+
                                '<li><i class="fa fa-address-card" aria-hidden="true"></i>: '+this.orderId+'</li>'+
                                '<li><i class="fa fa-clock"></i>: '+this.created_at+'</li>'+
                                '<li><i class="fa fa-toggle-on"></i>: '+this.status+'</li>'+
                                '<li><a href="'+order_view+'"><i class="fa fa-eye" aria-hidden="true"></i> {{ __("message.view_form_title",[ "form" => __("message.order") ]) }}</a></li>'+
                                '</ul></div>';
                    infoWindow.setContent(contentString);
                    infoWindow.open(map, marker);
                });
            }

            function filterMarkers() {
                var selectedStatus = document.getElementById('status_filter').value;

                markers.forEach(function(marker) {
                    if (marker.status === selectedStatus || selectedStatus === 'all') {
                        marker.setMap(map);
                    } else {
                        marker.setMap(null);
                    }
                });
            }

            window.onload = function() {
                initMap();
                document.getElementById('status_filter').addEventListener('change', filterMarkers);
            };
        </script>
    @endsection
</x-master-layout>
