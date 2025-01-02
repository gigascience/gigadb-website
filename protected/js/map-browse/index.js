export function mapBrowse(geojsonFeatures) {
  function initializeMap() {
    var distance = document.getElementById("distance");
    var source = new ol.source.Vector({
      features: new ol.format.GeoJSON().readFeatures(geojsonFeatures, {
        featureProjection: "EPSG:3857",
      }),
    });

    var clusterSource = new ol.source.Cluster({
      distance: 10,
      source: source,
    });
    var styleCache = {};
    var clusters = new ol.layer.Vector({
      source: clusterSource,
      style: function (feature) {
        var size = feature.get("features").length;
        var style = styleCache[size];
        if (!style) {
          style = new ol.style.Style({
            image: new ol.style.Circle({
              radius: 10,
              stroke: new ol.style.Stroke({
                color: "#fff",
              }),
              fill: new ol.style.Fill({
                color: "#006633",
              }),
            }),
            text: new ol.style.Text({
              text: size.toString(),
              fill: new ol.style.Fill({
                color: "#fff",
              }),
            }),
          });
          styleCache[size] = style;
        }
        return style;
      },
    });
    var raster = new ol.layer.Tile({
      source: new ol.source.OSM(),
    });
    var map = new ol.Map({
      layers: [raster, clusters],
      target: "map-browse-container",
      view: new ol.View({
        center: [0, 0],
        zoom: 2,
      }),
    });

    return map;
  }

  const map = initializeMap();

  document.getElementById("zoom-out").onclick = function () {
    const view = map.getView();
    const zoom = view.getZoom();
    view.setZoom(zoom - 1);
  };

  document.getElementById("zoom-in").onclick = function () {
    const view = map.getView();
    const zoom = view.getZoom();
    view.setZoom(zoom + 1);
  };

  function elem_id(id) {
    return document.getElementById(id);
  }

  var popup = elem_id("popup");
  var popup_closer = elem_id("popup-closer");
  var popup_content = elem_id("popup-content");
  var olpopup = new ol.Overlay({
    element: popup,
    autoPan: true,
    autoPanAnimation: { duration: 250 },
  });
  map.addOverlay(olpopup);
  popup_closer.onclick = function () {
    olpopup.setPosition(undefined);
    return false;
  };
  var OpenPopup = function (evt) {
    var feature = map.forEachFeatureAtPixel(
      evt.pixel,
      function (feature, layer) {
        if (feature) {
          var coord = map.getCoordinateFromPixel(evt.pixel);
          if (typeof feature.get("features") === "undefined") {
            popup_content.innerHTML =
              '<h5><b>Dataset:<a href="http://dx.doi.org/10.5524/' +
              feature.get("Dataset") +
              '">' +
              feature.get("Dataset") +
              "</a></b></h5>";
          } else {
            var cfeatures = feature.get("features");
            if (cfeatures.length > 1) {
              popup_content.innerHTML = '<h5><strong>"Samples"</strong></h5>';
              for (var i = 0; i < cfeatures.length; i++) {
                $(popup_content).append(
                  '<article><strong><a href="http://dx.doi.org/10.5524/' +
                    cfeatures[i].get("Dataset") +
                    '">' +
                    cfeatures[i].get("Dataset") +
                    ":" +
                    cfeatures[i].get("Scientific name") +
                    "</a></article>"
                );
              }
            }
            if (cfeatures.length == 1) {
              popup_content.innerHTML =
                '<h5><b>Dataset:<a href="http://dx.doi.org/10.5524/' +
                cfeatures[0].get("Dataset") +
                '">' +
                cfeatures[0].get("Dataset") +
                "</a></b></h5>" +
                cfeatures[0].get("Scientific name");
            }
          }
          popup.scrollTop = 0;
          olpopup.setPosition(coord);
        } else {
          olpopup.setPosition(undefined);
        }
      }
    );
  };
  map.on("click", OpenPopup);
  $('.js-map-view-toggler').on('click', function(){
    console.log('map-view-toggler clicked');
    map.updateSize();
  })
}
