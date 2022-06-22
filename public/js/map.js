let map = null;
let placeService = null;
let placeTypes = [];
let currPlace = null;
let markers = [];
let sim;
let canvas;
const locationInfo = [
  'country',
  'administrative_area_level_1',
  'administrative_area_level_2',
  'administrative_area_level_3',
  'administrative_area_level_4',
  'administrative_area_level_5',
  'locality'
];

initMap = () => {
  map = new google.maps.Map(document.getElementById('map'), {
    center: new google.maps.LatLng(initLat, initLng),
    zoom: initZoom,
    gestureHandling: 'greedy',
    streetViewControl: false,
    mapTypeControl: true,
    mapTypeControlOptions: {
      style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
      position: google.maps.ControlPosition.TOP_CENTER
    },
    fullscreenControl: false,
    mapId: "ed1309c122a3dfcb",
    useStaticMap: false,
  });

  placeService = new google.maps.places.PlacesService(map);

  map.addListener('click', (mapsMouseEvent) => {
    if (mapsMouseEvent.placeId) {
      placeService.getDetails({
        placeId: mapsMouseEvent.placeId,
      }, function (place, status) {
        if (status == google.maps.places.PlacesServiceStatus.OK) {
          $('.post-group').scrollTop(0);
          map.fitBounds(place.geometry.viewport);

          let uri = [];
          for (let i = 0; i < place.address_components.length; i++) {
            locationInfo.forEach(area => {
              if (place.address_components[i].types[0] === area) {
                uri.push(place.address_components[i].short_name);
              }
            });
            if (uri.length === 2) break;
          }
          uri = uri.reverse().join('/');
          uri = uri.toLowerCase();
          uri = uri.replace(/ /g, "-");
          uri = uri.replace(/'/g, "");
          window.history.replaceState("", "Globetrotters", `${APP_URL}en/${uri}/category/post`);

          // saving data.
          let data = {};
          locationInfo.forEach(function (addressType) {
            place.address_components.forEach(function (item) {
              if (item['types'][0] == addressType) {
                data[addressType] = item['long_name'];
              }
            });
          });
          data['url'] = uri;
          data['lat'] = place.geometry.location.lat();
          data['lng'] = place.geometry.location.lng();
          data['zoom'] = map.getZoom();
          data['formatted_address'] = place.formatted_address;
          data['_token'] = csrfToken;

          $.ajax({
            url: APP_URL + 'areas/save',
            method: 'POST',
            data: data,
            success: (response) => {
              getPostInfo(response.response);
            }
          });
          currPlace = place;
          searchPlaces();
        }
      });
    }
  });
  map.addListener('drag', (mapsMouseEvent) => {
    drawRopes();
  });
  map.addListener('zoom_changed', (mapsMouseEvent) => {
    drawRopes();
  });
  map.addListener('dragend', (mapsMouseEvent) => {
    drawRopes();
  });
  map.addListener('idle', (mapsMouseEvent) => {
    drawRopes();
  });
}

const loop = () => {
  sim.frame(16);
  sim.draw();
  requestAnimFrame(loop);
};

const searchPlaces = () => {
  for (var m in markers) {
    markers[m].setMap(null);
  }
  markers = [];
  $('.place-photos').html('');

  if (currPlace) {
    for (var placeType of placeTypes) {
      placeService.nearbySearch({
        location: currPlace.geometry.location,
        rankBy: google.maps.places.RankBy.DISTANCE,
        type: placeType
      }, function (results, status) {
        if (status == google.maps.places.PlacesServiceStatus.OK) {
          for (var address of results) {
            if (address.photos) {
              let html = '<div class="place-photo" title="' + address.name + '">';
              html += '<img src="' + address.photos[0].getUrl() + '" class="dot-img">';
              html += '<span class="place-photo-label">' + address.name + '</span>';
              html += '</div>';
              $('.place-photos').append(html).scrollTop(0);

              markers[markers.length] = new google.maps.Marker({
                position: address.geometry.location,
                map: map
              });
            }
          }
          drawRopes();
        }
      });
    }
  }
};

const getPoints = (sPoint, ePoint) => {
  let points = [];
  let distance = 20;
  let hor = ePoint[0] - sPoint[0];
  let ver = ePoint[1] - sPoint[1];

  let segments = Math.sqrt(Math.pow(hor, 2) + Math.pow(ver, 2)) / distance;

  let h = sPoint[0], v = sPoint[1];
  for (var s = 0; s < segments - 1; s++) {
    h += hor / segments;
    v += ver / segments;
    points[s] = new Vec2(h, v);
  }
  points[s] = new Vec2(ePoint[0], ePoint[1]);
  return points;
};

const drawRope = (sPoint, ePoint) => {
  if (sPoint[0] > 1100 || sPoint[0] < 0) return false;
  if (sPoint[1] > 400 || sPoint[1] < 0) return false;
  if (ePoint[0] > 1100 || ePoint[0] < 0) return false;
  if (ePoint[1] > 400 || ePoint[1] < 0) return false;
  let points = getPoints(sPoint, ePoint);
  var segment = sim.lineSegments(points, 7);
  segment.pin(0);
  segment.pin(points.length - 1);
  return segment;
};

const drawRopes = () => {
  initCanvas();
  var scale = Math.pow(2, map.getZoom());
  var proj = map.getProjection();
  var bounds = map.getBounds();
  var nw = proj.fromLatLngToPoint(
    new google.maps.LatLng(
      bounds.getNorthEast().lat(),
      bounds.getSouthWest().lng()
    )
  );

  var point;
  $('.place-photo').each(function (i) {
    point = proj.fromLatLngToPoint(markers[i].position);
    drawRope([145, $(this).offset().top + 50], [(point.x - nw.x) * scale, (point.y - nw.y) * scale]);
  });

};

const initCanvas = () => {
  canvas = document.getElementById("canvas");
  var width = parseInt($(canvas).width());
  var height = parseInt($(canvas).height());
  canvas.width = width;
  canvas.height = height;
  sim = new VerletJS(width, height, canvas);
};

$(document).ready(() => {
  placeTypes = $('.place-type-link.active').attr('place_types').split(',');

  initCanvas();
  loop();

  $('.place-photos').scroll(function () {
    drawRopes()
  });

  $('.place-type-link').click(function () {
    $('.place-type-link').removeClass('active');
    $(this).addClass('active');

    if ($(this).hasClass('all')) {
      var allPlaceTypes = [];
      $('.place-type-link').each(function () {
        if ($(this).attr('place_types')) {
          allPlaceTypes = allPlaceTypes.concat($(this).attr('place_types').split(','));
        }
      });
      placeTypes = Array.from(new Set(allPlaceTypes));
    } else {
      placeTypes = $(this).attr('place_types').split(',');
    }
    searchPlaces();
  });
});
