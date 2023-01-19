$(document).ready(function () { initMap(); });
var map;
//marker clusterer
var mc;
var mcOptions = {
    gridSize: 20,
    maxZoom: 17,
    imagePath: "https://cdn.rawgit.com/googlemaps/v3-utility-library/master/markerclustererplus/images/m"
};
//global infowindow
var infowindow = new google.maps.InfoWindow();

function createMarker(latlng, text) {
    var marker = new google.maps.Marker({
        position: latlng,
        icon: 'icon.png',
        map: map
    });
    ///get array of markers currently in cluster
    var allMarkers = mc.getMarkers();
    //check to see if any of the existing markers match the latlng of the new marker
    if (allMarkers.length != 0) {
        for (i = 0; i < allMarkers.length; i++) {
            var existingMarker = allMarkers[i];
            var pos = existingMarker.getPosition();
            if (latlng.equals(pos)) {
                text = text + " , " + content[i]
            }
        }
    };
    google.maps.event.addListener(marker, 'click', function () {
        infowindow.close();
        infowindow.setContent(text);
        infowindow.open(map, marker);
    });
    mc.addMarker(marker);
    return marker;
};
function initMap() {
    var options = {
        zoom: 7,
        mapTypeId: 'satellite',
        center: { lat: 33.567384, lng: 132.800886 },
        disableDefaultUI: true,
        gestureHandling: 'greedy'
    };
    map = new google.maps.Map(document.getElementById('map'), options);
    //marker cluster
    var gmarkers = [];
    mc = new MarkerClusterer(map, [], mcOptions);
    for (i = 0; i < locations.length; i++) {
        var ptStr = locations[i];
        var coords = ptStr.split(",");
        var latlng = new google.maps.LatLng(parseFloat(coords[0]), parseFloat(coords[1]));
        gmarkers.push(createMarker(latlng, content[i]));
    }
};