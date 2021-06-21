<html>
    <head>       
        <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
        <script type="text/javascript">
            var map;
            var geocoder;
            function loadMap() {
                // Using the lat and lng of Dehradun.
                var latitude = -6.1912223; 
                var longitude = 106.8503058;
                var latlng = new google.maps.LatLng(latitude,longitude);
                var feature = {
                    zoom: 10,
                    center: latlng,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };
                map = new google.maps.Map(document.getElementById("map_canvas"), feature);
                geocoder = new google.maps.Geocoder();
                var marker = new google.maps.Marker({
                    position: latlng,
                    map: map,
                    title: "Test for Location"
                });
            }
        </script>
    </head>
    <body onload="loadMap()">
        <div id="map" style="width:600px; height:400px">
            <div id="map_canvas" style="width:100%; height:200px"></div>
        </div>
    </body>
</html>