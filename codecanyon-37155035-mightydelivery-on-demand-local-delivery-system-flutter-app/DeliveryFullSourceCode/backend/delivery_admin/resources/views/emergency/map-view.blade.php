<!DOCTYPE html>
<html>
<head>
    <title>Delivery Man Location</title>
    <meta name="viewport" content="initial-scale=1.0">
    <meta charset="utf-8">

    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map {
            height: 100vh;
            width: 100%;
        }
    </style>
</head>
<body>
    <div id="map"></div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        const lat = {{ $lat }};
        const lng = {{ $lng }};

        const map = L.map('map').setView([lat, lng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19
        }).addTo(map);

        L.marker([lat, lng])
            .addTo(map)
            .bindPopup("Delivery Man Location")
            .openPopup();
    </script>
</body>
</html>
