
<?
$this->pageTitle='GigaDB - Map Browse';
?>

<style>
	div.container#wrap{height:400px;width:100%;}
</style>
<script src="http://www.openlayers.org/api/OpenLayers.js"></script>
<?php
	$locationsLength = count($locations);
?>
<script type="text/javascript">
    map = new OpenLayers.Map("wrap");
    map.addLayer(new OpenLayers.Layer.OSM());
    var zoom = 16;

    var markers = new OpenLayers.Layer.Markers( "Markers" );
    map.addLayer(markers);
    
    <?php
     if ($locationsLength > 0) {        
			foreach ($locations as $location) {
        $val = explode(',', $location["value"]);
        if (!is_numeric ($val[0])) {
            continue;
          }          
    	?>
       var lonLat = new OpenLayers.LonLat( <?php echo trim($val[1]); ?> ,<?php echo trim($val[0]); ?> )
	          .transform(
	            new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
	            map.getProjectionObject() // to Spherical Mercator Projection
	          );
	    markers.addMarker(new OpenLayers.Marker(lonLat));
	    map.setCenter (lonLat, zoom);
    <?php
    	 }
  	}
    ?>
    map.zoomToMaxExtent();
</script>

