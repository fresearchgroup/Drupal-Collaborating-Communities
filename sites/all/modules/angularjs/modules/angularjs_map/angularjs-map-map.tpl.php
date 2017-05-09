<div class="navbar">
  <div class="navbar-inner">
    <a class="brand" href="#">Earthquake Map</a>
    <ul class="nav">
      <li class="dropdown">
        <a id="drop1" href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Magnitude <b class="caret"></b></a>
        <div restws-search-facet search-facet="facets.field_magnitude" soft-limit="100" hide-expand-collapse="true" role="menu" class="dropdown-menu"></div>
      </li>
      <li class="dropdown">
        <a id="drop1" href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Felt <b class="caret"></b></a>
        <div restws-search-facet search-facet="facets.field_felt" soft-limit="100" hide-expand-collapse="true" role="menu" class="dropdown-menu"></div>
      </li>
    </ul>
  </div>
</div>

<div leaflet-map
  style="width: <?php print $width; ?>; height: <?php print $height; ?>"
  map-center="mapCenter"
  map-zoom="<?php print $zoom; ?>"
  icon-path="/<?php print libraries_get_path('leaflet') . '/dist/images'; ?>">

  <div leaflet-tile-layer layer-path="http://a.tiles.mapbox.com/v3/mapbox.world-bright/{z}/{x}/{y}.png" max-zoom="18" layer-attribution="test"></div>
  <div leaflet-geojson-layer layer-url="{{geoJsonUrl}}" layer-filter="layerFilter"></div>
</div>