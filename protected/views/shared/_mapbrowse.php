<?php
if (isset($locations)) {
  $USE_TEST_LOCATIONS = false;
  $NUMBER_OF_TEST_LOCATIONS = 1_000;

  if (YII_DEBUG && $USE_TEST_LOCATIONS) {
    // Generate random locations for testing since locally there are very few locations
    function generateRandomLocation() {
      $lat = rand(-90, 90);
      $lng = rand(-180, 180);
      $identifier = rand(100000, 999999);

      $species = array(
        array("name" => "Testius maximus", "common" => "Giant Test Creature"),
        array("name" => "Debuggus minimus", "common" => "Tiny Debug Bug"),
        array("name" => "Mockus dataensis", "common" => "Common Mock Beast"),
        array("name" => "Dummius placeholderus", "common" => "Placeholder Dragon"),
        array("name" => "Fakeius sampleius", "common" => "Sample Snark")
      );

      $randomSpecies = $species[array_rand($species)];
      $sciname = $randomSpecies["name"];
      $title = "Genomic data from " . $randomSpecies["common"] . " (" . $sciname . ").";
      $sampleid = rand(1, 1000);

      return array(
        "identifier" => $identifier,
        "title" => $title,
        "value" => sprintf("%.6f,%.6f", $lat, $lng),
        "sciname" => $sciname,
        "sampleid" => $sampleid
      );
    }

    for ($i = 0; $i < $NUMBER_OF_TEST_LOCATIONS; $i++) {
      $locations[] = generateRandomLocation();
    }
  }
  ?>

  <div class="container">
    <p>Numbers indicate number of samples in each cluster</p>
  </div>
  <div class="map-samples-container" id="map-browse-container" tabindex="0"></div>
  <div class="btns-row mt-10 mb-10 ml-10">
    <button id="zoom-out" class="btn background-btn-o">Zoom out</button>
    <button id="zoom-in" class="btn background-btn-o">Zoom in</button>
  </div>
  <div id="map-samples-popup" class="map-samples-popup js-map-samples-popup">
    <a href="/" id="map-samples-popup__close-btn" class="js-map-samples-popup__close-btn map-samples-popup__close-btn"><i class="fa fa-times" aria-label="Close popup"></i></a>
    <div id="map-samples-popup__content" class="js-map-samples-popup__content map-samples-popup__content"></div>
  </div>


  <script src="https://cdn.jsdelivr.net/npm/ol@v8.1.0/dist/ol.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@v8.1.0/ol.css">

  <?php
  Yii::app()->assetManager->forceCopy = YII_DEBUG; // prevent caching on development
  $jsDir = Yii::getPathOfAlias('application.js.map-browse');
  $jsUrl = Yii::app()->assetManager->publish($jsDir);

  Yii::app()->clientScript->registerScriptFile($jsUrl . '/index.js', CClientScript::POS_END, ['type' => 'module']);
  ?>


  <script type="module">
    import { mapBrowse } from "<?php echo $jsUrl; ?>/index.js";

    const geojsonFeatures = {
      "type": "FeatureCollection",
      "features": [
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
            {
              "type": "Feature",
                "properties": {
                "Sample ID": <?php echo $location["sampleid"]; ?>,
                  "Scientific name": <?php echo '"' . trim($location["sciname"]) . '"'; ?>,
                    "Dataset": <?php echo trim($location["identifier"]); ?>
              },
              "geometry": {
                "type": "Point",
                  "coordinates": [<?php echo trim($val[1]); ?>, <?php echo trim($val[0]); ?>]
              }
            },
        <?php
          }
        }
        ?>
      ]
    }

    $(document).ready(function () {
      mapBrowse(geojsonFeatures);
    })
  </script>
  <?php
}
?>