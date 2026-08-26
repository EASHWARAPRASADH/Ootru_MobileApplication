<x-master-layout :assets="$assets ?? []">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 mb-3">
                <div id="map-container" style="position: relative; width: 100%; height: 600px;">

                    <!-- Map Filter Menu -->
                    <div id="map-filter-menu">
                        @foreach (['high' => 'Red: Very high demand, few riders available.', 'moderate' => 'Orange: Moderate demand, needs more riders.', 'normal' => 'Yellow: Normal demand, well-balanced.', 'low' => 'Green: Low demand, no action needed.'] as $level => $description)
                            <div class="filter-button {{ $level }} active" data-level="{{ $level }}">
                                <div class="circle"></div> {{ $description }}
                            </div>
                        @endforeach
                    </div>

                    <!-- Country/City Filters -->
                    <div class="row mb-3">
                        <div class="form-group col-md-4">
                            {{ html()->select('country_id', isset($data) ? [$data->country->id => $data->country->name] : [], old('country_id'))->class('select2js form-group country_id')->attribute('data-placeholder', __('message.country'))->attribute('data-ajax--url', route('ajax-list', ['type' => 'country-list'])) }}
                        </div>

                        <div class="form-group col-md-4">
                            {{ html()->select('city_id', isset($data) ? [$data->city->id => $data->city->name] : [], old('city_id'))->class('select2js form-group city_id')->attribute('data-placeholder', __('message.city')) }}
                        </div>
                    </div>

                    <div id="map" style="width: 100%; height: 100%;"></div>
                </div>
            </div>
        </div>
    </div>

    @section('bottom_script')
        <script>
            const zones = @json($zones);
            const riders = @json($riders);

            let map;
            let zoneCircles = [];
            let riderMarkers = [];

            const getColorByLevel = level => ({
                high: '#FF0000',
                moderate: '#FFA500',
                normal: '#FFD700',
                low: '#49d009'
            } [level] || '#49d009');

            const haversineDistance = (lat1, lon1, lat2, lon2) => {
                const R = 6371;
                const toRad = deg => deg * Math.PI / 180;
                const dLat = toRad(lat2 - lat1);
                const dLon = toRad(lon2 - lon1);
                const a = Math.sin(dLat / 2) ** 2 + Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLon / 2) ** 2;
                return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            };

            const groupZones = (zones, threshold = 10) => {
                const clusters = [];

                zones.forEach(zone => {
                    let cluster = clusters.find(c =>
                        haversineDistance(c.center.lat, c.center.lng, zone.lat, zone.lng) <= threshold
                    );

                    if (cluster) {
                        const count = cluster.zones.length;
                        cluster.center.lat = (cluster.center.lat * count + zone.lat) / (count + 1);
                        cluster.center.lng = (cluster.center.lng * count + zone.lng) / (count + 1);
                        cluster.zones.push(zone);
                    } else {
                        clusters.push({
                            center: {
                                lat: zone.lat,
                                lng: zone.lng
                            },
                            zones: [zone]
                        });
                    }
                });

                return clusters;
            };

            let activeZoneCenter = null;

            const initMap = () => {
                map = new google.maps.Map(document.getElementById("map"), {
                    zoom: 3,
                    center: {
                        lat: 20.0,
                        lng: 0.0
                    }
                });

                const clusteredZones = groupZones(zones);

                clusteredZones.forEach(({
                    center,
                    zones
                }) => {
                    const level = ['high', 'moderate', 'normal', 'low'].find(l => zones.some(z => z.level === l)) ||
                        'low';

                    const circle = new google.maps.Circle({
                        strokeColor: getColorByLevel(level),
                        strokeOpacity: 0.8,
                        strokeWeight: 10,
                        fillColor: getColorByLevel(level),
                        fillOpacity: 0.35,
                        map,
                        center,
                        radius: 5000
                    });

                    circle.zoneLevel = level;
                    zoneCircles.push(circle);

                    circle.addListener('click', () => {
                        map.setCenter(center);
                        map.setZoom(12);
                        activeZoneCenter = center; // Track this zone
                        updateRiderMarkers();
                    });
                });

                // Rider markers - initially hidden
                riders.forEach(rider => {
                    const position = {
                        lat: parseFloat(rider.lat),
                        lng: parseFloat(rider.lng)
                    };
                    const icon =
                        `http://maps.google.com/mapfiles/ms/icons/${rider.is_available ? 'green' : 'red'}-dot.png`;

                    const marker = new google.maps.Marker({
                        position,
                        icon,
                        title: `Name: ${rider.name}`,
                        map: null
                    });

                    marker.addListener('click', () => {
                        new google.maps.InfoWindow({
                            content: `Name: ${rider.name}<br>Status: ${rider.is_available ? 'Available' : 'Busy'}`
                        }).open(map, marker);
                    });

                    riderMarkers.push(marker);
                });

                // Hide/show markers based on zoom level and active zone
                map.addListener('zoom_changed', () => {
                    updateRiderMarkers();
                });

                // Optional: also hide markers if map dragged too far
                map.addListener('dragend', () => {
                    updateRiderMarkers();
                });
            };

            const updateRiderMarkers = () => {
                if (!activeZoneCenter || map.getZoom() < 11) {
                    riderMarkers.forEach(marker => marker.setMap(null));
                    return;
                }

                riderMarkers.forEach(marker => {
                    const dist = haversineDistance(
                        activeZoneCenter.lat,
                        activeZoneCenter.lng,
                        marker.getPosition().lat(),
                        marker.getPosition().lng()
                    );
                    marker.setMap(dist <= 10 ? map : null);
                });
            };


            const loadCityList = (countryId) => {
                const route = "{{ route('ajax-list', ['type' => 'extra_charge_city', 'country_id' => '']) }}" + countryId;
                $.ajax({
                    url: route.replace('amp;', ''),
                    success: result => {
                        $('#city_id').select2({
                            width: '100%',
                            placeholder: "{{ __('message.select_name', ['select' => __('message.city')]) }}",
                            data: result.results
                        });
                    }
                });
            };

            $(document).ready(function() {
                const geocoder = new google.maps.Geocoder();

                $('.select2js').select2({
                    width: '100%'
                });

                $('#country_id').on('change', function() {
                    const countryName = $('#country_id option:selected').text();
                    $('#city_id').empty().trigger('change');
                    loadCityList($(this).val());

                    if (countryName) {
                        geocoder.geocode({
                            address: countryName
                        }, (results, status) => {
                            if (status === 'OK') {
                                map.setCenter(results[0].geometry.location);
                                map.setZoom(
                                    6); // Optional: you can remove this line if you want no zoom at all
                            }
                        });
                    }
                });

                $('#city_id').on('change', function() {
                    const cityName = $('#city_id option:selected').text();
                    if (!cityName) return;

                    geocoder.geocode({
                        address: cityName
                    }, (results, status) => {
                        if (status === 'OK') {
                            map.setCenter(results[0].geometry.location);
                            map.setZoom(12); // Optional: remove to keep current zoom
                        }
                    });
                });

                $('.filter-button').on('click', function() {
                    $(this).toggleClass('active');
                    const selectedLevels = $('.filter-button.active').map(function() {
                        return $(this).data('level');
                    }).get();

                    zoneCircles.forEach(circle => {
                        circle.setMap(selectedLevels.includes(circle.zoneLevel) ? map : null);
                    });
                });
            });

            $(window).on('load', initMap);
        </script>
    @endsection
</x-master-layout>
