<?
$this->pageTitle = 'GigaDB - Map Browse';
?>

<div class="container">

<?php
$this->widget('TitleBreadcrumb', [
  'pageTitle' => 'Map Browse',
  'breadcrumbItems' => [
    ['label' => 'Home', 'href' => '/'],
    ['isActive' => true, 'label' => 'Map Browse'],
  ]
]);
?>
</div>


<div class="mapcontainer" id="map" tabindex="0"></div>
<div class="btns-row mt-10 mb-10 ml-10">
  <button id="zoom-out" class="btn background-btn-o">Zoom out</button>
  <button id="zoom-in" class="btn background-btn-o">Zoom in</button>
</div>
<div id="popup" class="popup">
  <a href="/" id="popup-closer">Close</a>
  <div id="popup-content"></div>
</div>


<script src="https://cdn.jsdelivr.net/npm/ol@v8.1.0/dist/ol.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@v8.1.0/ol.css">

<script>
  var geojson_features = {"type":"FeatureCollection",
                  "features":[
<?php
$locationsLength = count($locations);
if ($locationsLength > 0) {
  $i = 1;
  foreach ($locations as $location) {
    $i++;
    if ($i > 50000) {
      break 1;
    }
    $locationValue = $location["value"];
    $locationValue = preg_replace('/\s+/', '', $locationValue);
    $formatCheck = preg_match('/-?[0-9]*[.][0-9]*[,]-?[0-9]*[.][0-9]*/', $locationValue);
    if (!$formatCheck == 1) {
      continue;
    }
    $val = explode(',', $locationValue);
    if (strpos($val[0], '.') == false || !is_numeric($val[0])) {
      continue;
    }
    if (strpos($val[1], '.') == false || !is_numeric($val[1])) {
      continue;
    }
    if ($val[1] > 180 || $val[1] < -180) {
      continue;
    }
    if ($val[0] > 85.05112878 || $val[0] < -85.05112878) {
      continue;
    }
    $location["sciname"] = str_replace(",", "", $location["sciname"]);


    ?>
                        {"type":"Feature",
                          "properties":{
                            "Sample ID": <?php echo $location["sampleid"]; ?>,
                            "Scientific name": <?php echo '"' . trim($location["sciname"]) . '"'; ?>,
                            "Dataset": <?php echo trim($location["identifier"]); ?>
                            },
                            "geometry":{
                              "type":"Point",
                              "coordinates":[<?php echo trim($val[1]); ?>,<?php echo trim($val[0]); ?>]
                            }
                          },
    <?php
  }
}
?>
                    ]
                  }
</script>
<script>
    console.log(geojson_features);
  var distance = document.getElementById('distance');
  var source = new ol.source.Vector({
      features: (new ol.format.GeoJSON()).readFeatures(geojson_features, { featureProjection: 'EPSG:3857' })
  });

  var clusterSource = new ol.source.Cluster({
    distance: 10,
    source: source
  });
  var styleCache = {};
  var clusters = new ol.layer.Vector({
    source: clusterSource,
    style: function(feature) {
      var size = feature.get('features').length;
      var style = styleCache[size];
      if (!style) {
        style = new ol.style.Style({
          image: new ol.style.Circle({
            radius: 10,
            stroke: new ol.style.Stroke({
              color: '#fff'
            }),
            fill: new ol.style.Fill({
              color: '#006633'
            })
          }),
          text: new ol.style.Text({
            text: size.toString(),
            fill: new ol.style.Fill({
              color: '#fff'
            })
          })
        });
        styleCache[size] = style;
      }
      return style;
    }
  });
  var raster = new ol.layer.Tile({
    source: new ol.source.OSM()
  });
  var map = new ol.Map({
    layers: [raster, clusters],
    target: 'map',
    view: new ol.View({
      center: [0, 0],
      zoom: 2
    })
  });

  document.getElementById('zoom-out').onclick = function () {
    const view = map.getView();
    const zoom = view.getZoom();
    view.setZoom(zoom - 1);
  };

  document.getElementById('zoom-in').onclick = function () {
    const view = map.getView();
    const zoom = view.getZoom();
    view.setZoom(zoom + 1);
  };

  function elem_id(id) {
    return document.getElementById(id);
  }
  var popup = elem_id('popup');
  var popup_closer = elem_id('popup-closer');
  var popup_content = elem_id('popup-content');
  var olpopup = new ol.Overlay({
      element: popup,
      autoPan: true,
      autoPanAnimation: {duration:250}
  });
  map.addOverlay(olpopup);
  popup_closer.onclick = function () {
      olpopup.setPosition(undefined);
      return false;
  };
  var OpenPopup = function (evt) {
      console.log("2222");
      var feature = map.forEachFeatureAtPixel(evt.pixel,
      function (feature, layer) {
          if (feature) {
              var coord = map.getCoordinateFromPixel(evt.pixel);
              if (typeof feature.get('features') === 'undefined') {
                  popup_content.innerHTML = '<h5><b>Dataset:<a href="http://dx.doi.org/10.5524/' + feature.get('Dataset') + '">'+feature.get('Dataset')+'</a></b></h5>';
              } else {
                  var cfeatures = feature.get('features');
                  if (cfeatures.length > 1) {
                      popup_content.innerHTML = '<h5><strong>"Samples"</strong></h5>';
                      for (var i = 0; i < cfeatures.length; i++) {
                          $(popup_content).append('<article><strong><a href="http://dx.doi.org/10.5524/'+cfeatures[i].get('Dataset')+'">'+cfeatures[i].get('Dataset') +':' + cfeatures[i].get('Scientific name') +'</a></article>');
                      }
                  }
                  if (cfeatures.length == 1) {
                      popup_content.innerHTML = '<h5><b>Dataset:<a href="http://dx.doi.org/10.5524/' + cfeatures[0].get('Dataset') + '">'+cfeatures[0].get('Dataset')+'</a></b></h5>'+cfeatures[0].get('Scientific name');
                  }
              }
              popup.scrollTop = 0;
              console.log(coord);
              olpopup.setPosition(coord);
          } else {
              console.log("11111");
              olpopup.setPosition(undefined);
          }
      });
  };
  map.on('click', OpenPopup);
</script>
</div>
