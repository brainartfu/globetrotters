"use strict";

var geocoder = null;
var map = null;
var locationInfo = ['country', 'administrative_area_level_1', 'administrative_area_level_2', 'administrative_area_level_3', 'administrative_area_level_4', 'administrative_area_level_5', 'locality'];

initMap = function initMap() {
  geocoder = new google.maps.Geocoder();
  map = new google.maps.Map(document.getElementById('map'), {
    center: new google.maps.LatLng(initLat, initLng),
    zoom: initZoom,
    gestureHandling: 'greedy',
    draggableCursor: 'pointer',
    zoomControl: false,
    streetViewControl: false,
    mapTypeControl: true
  });
  map.addListener('click', function (mapsMouseEvent) {
    zoomLevel = map.getZoom();
    var location = {
      lat: mapsMouseEvent.latLng.lat(),
      lng: mapsMouseEvent.latLng.lng()
    };
    geocoder.geocode({
      location: location
    }, function (results, status) {
      $('.address-list').html('');

      if (status === 'OK') {
        results.forEach(function (address, ind) {
          if ($.inArray(address['types'][0], ['postal_code', 'postal_town', 'route', 'establishment']) < 0) {
            var uri = address['formatted_address'].split(', ').reverse().join('/');
            $('.address-list').append('<li id="' + address['place_id'] + '" uri="' + uri + '" lat="' + address['geometry']['location'].lat() + '" lng="' + address['geometry']['location'].lng() + '">' + address['formatted_address'] + '</li>');
          }
        });
        $('.address-popup').css({
          top: mapsMouseEvent.ub.clientY,
          left: mapsMouseEvent.ub.clientX,
          display: 'block'
        });
      }
    });
  });
  $(document).on('click', '.address-list li', function () {
    var _this = this;

    geocoder.geocode({
      placeId: $(this).attr('id')
    }, function (results, status) {
      if (status === "OK") {
        var address = results[0]; // zooming map

        map.fitBounds(address.geometry.viewport); // change URL when clicking a point on the map.

        var uri = $(_this).attr('uri');
        window.history.pushState("", "Globetrotters", "".concat(BASE_APP_URL, "en/").concat(uri, "/category/post")); // preparing data to send to the server.

        var data = {};
        locationInfo.forEach(function (addressType) {
          address.address_components.forEach(function (item) {
            if (item['types'][0] == addressType) {
              data[addressType] = item['long_name'];
            }
          });
        });
        data['url'] = uri;
        data['lat'] = $(_this).attr('lat');
        data['lng'] = $(_this).attr('lng');
        data['zoom'] = map.getZoom();
        data['formatted_address'] = $(_this).html();
        data['_token'] = csrfToken; // saving data.

        $.ajax({
          url: BASE_APP_URL + 'areas/save',
          method: 'POST',
          data: data,
          success: function success(response) {
            areaId = response.status;
            console.log(areaId);
          }
        });
      }
    });
    $('.address-popup').css({
      display: 'none'
    });
  });
  $(document).click(function (event) {
    $('.address-popup').css({
      display: 'none'
    });
  });
};