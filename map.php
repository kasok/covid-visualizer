<!-- <link rel="stylesheet" href="Leaflet.heat-gh-pages/leaflet.css" />
<script src="Leaflet.heat-gh-pages/leaflet.js"></script> -->
<link rel="stylesheet" href="leaflet-1.6.0/leaflet.css" />
<script src="leaflet-1.6.0/leaflet.js"></script>
<style type="text/css">
	#map { width: 800px; height: 600px;margin:auto; }
	body { font: 16px/1.4 "Helvetica Neue", Arial, sans-serif; }
	.ghbtns { position: relative; top: 4px; margin-left: 5px; }
	a { color: #0077ff; }
	
</style>
</head>
<body>


<div id="map"></div>

<!-- <script src="../node_modules/simpleheat/simpleheat.js"></script>
<script src="../src/HeatLayer.js"></script> -->

<script src="Leaflet.heat-gh-pages/dist/leaflet-heat.js"></script>

<!-- <script src="Leaflet.heat-gh-pages/realworld.10000.js"></script> -->
<script type="text/javascript"><?= renderDataScript(); ?></script>
<script>

var map = L.map('map').setView([52, 22], 5.5);

var tiles = L.tileLayer('https://{s}.tile.osm.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://osm.org/copyright">OpenStreetMap</a> contributors',
}).addTo(map);

addressPoints = addressPoints.map(function (p) { return [p[0], p[1], p[2]]; });

var heat = L.heatLayer(addressPoints, {max: 0.008, radius: 25, blur: 15, gradient: {0.4: 'blue', 0.65: 'navy'}}).addTo(map);
var markers = {};
<?php renderMarkerScript(); ?>

</script>