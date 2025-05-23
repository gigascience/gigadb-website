const MAP_CONFIG = {
  initialZoom: 2,
  initialCenter: [0, 0],
  clusterDistance: 10,
  circleRadius: 10,
  styles: {
    stroke: { color: "#fff" },
    fill: { color: "#0d6e36" },
    text: { color: "#fff" },
  },
};

export function mapBrowse(geojsonFeatures, config = {}) {
  const mergedConfig = { ...MAP_CONFIG, ...config };
  const styleCache = {};

  function createClusterStyle(size) {
    if (!styleCache[size]) {
      styleCache[size] = new ol.style.Style({
        image: new ol.style.Circle({
          radius: mergedConfig.circleRadius,
          stroke: new ol.style.Stroke(mergedConfig.styles.stroke),
          fill: new ol.style.Fill(mergedConfig.styles.fill),
        }),
        text: new ol.style.Text({
          text: size.toString(),
          fill: new ol.style.Fill(mergedConfig.styles.text),
        }),
      });
    }
    return styleCache[size];
  }

  function createVectorSource() {
    return new ol.source.Vector({
      features: new ol.format.GeoJSON().readFeatures(geojsonFeatures, {
        featureProjection: "EPSG:3857",
      }),
    });
  }

  function createClusterLayer(source) {
    const clusterSource = new ol.source.Cluster({
      distance: mergedConfig.clusterDistance,
      source,
    });

    return new ol.layer.Vector({
      source: clusterSource,
      style: (feature) => createClusterStyle(feature.get("features").length),
    });
  }

  function initializeMap() {
    const source = createVectorSource();
    const clusters = createClusterLayer(source);
    const raster = new ol.layer.Tile({ source: new ol.source.OSM() });

    return new ol.Map({
      layers: [raster, clusters],
      target: "map-browse-container",
      view: new ol.View({
        center: mergedConfig.initialCenter,
        zoom: mergedConfig.initialZoom,
      }),
    });
  }

  function setupPopup(map) {
    const $popup = $(".js-map-samples-popup");
    const $popupContent = $(".js-map-samples-popup__content");
    const $popupCloseBtn = $(".js-map-samples-popup__close-btn");

    const overlay = new ol.Overlay({
      element: $popup[0],
      autoPan: true,
      autoPanAnimation: { duration: 250 },
    });

    map.addOverlay(overlay);

    $popupCloseBtn.on("click", () => {
      overlay.setPosition(undefined);
      return false;
    });

    onClickOutside($popup, () => {
      overlay.setPosition(undefined);
    });

    return { overlay, $popupContent };
  }

  function onClickOutside(element, callback) {
    element.on("click", (evt) => {
      if (!element.contains(evt.target)) {
        callback();
      }
    });
  }

  function renderPopupContent(features) {
    function createHeading(text) {
      return $("<h2>")
        .addClass("map-samples-popup__heading h5")
        .append($("<strong>").text(text));
    }

    function createDatasetLink(dataset) {
      return $("<a>")
        .attr("href", `http://dx.doi.org/10.5524/${dataset}`)
        .text(dataset);
    }

    const $content = $(features.length === 1 ? "<div>" : "<section>").addClass('map-samples-popup__container');

    if (features.length === 1) {
      const [feature] = features;
      const $heading = createHeading("")
        .find("strong")
        .html("Dataset: ")
        .append(createDatasetLink(feature.get("Dataset")))
        .end();

      $content.append($heading).append(feature.get("Scientific name") || "");
    } else {
      $content.append(createHeading("Samples"));

      features.forEach((feature) => {
        const $article = $("<article>")
          .addClass("map-samples-popup__article")
          .append(
            $("<strong>").append(
              createDatasetLink(feature.get("Dataset")).text(
                `${feature.get("Dataset")}: ${feature.get("Scientific name")}`
              )
            )
          );
        $content.append($article);
      });
    }

    return $content.prop('outerHTML');
  }

  function handleMapClick(evt, map, { overlay, $popupContent }) {
    const feature = map.forEachFeatureAtPixel(evt.pixel, (feature) => feature);

    if (!feature) {
      overlay.setPosition(undefined);
      return;
    }

    const coord = map.getCoordinateFromPixel(evt.pixel);
    const features = feature.get("features") || [feature];

    $popupContent.html(renderPopupContent(features));
    $popupContent.scrollTop(0);
    overlay.setPosition(coord);
  }

  function initializeControls(map) {
    $("#zoom-out").on("click", () => {
      const view = map.getView();
      view.setZoom(view.getZoom() - 1);
    });

    $("#zoom-in").on("click", () => {
      const view = map.getView();
      view.setZoom(view.getZoom() + 1);
    });
  }

  const map = initializeMap();
  const popupElements = setupPopup(map);

  map.on("click", (evt) => handleMapClick(evt, map, popupElements));
  initializeControls(map);

  return map;
}
