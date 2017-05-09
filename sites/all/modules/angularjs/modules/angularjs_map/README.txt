Introduction
------------

This is a slightly more complicated example of using AngularJS to show Drupal data. It contains
a Feature that has a Feed that pulls earthquake data from the USGS and displays it using 
Leaflet. It also exposes facets to filter the data using Search API and Facet API. 

Requirements
------------
 * Features [1]
 * Geofield [2]
 * Feeds [3]
 * RESTWS Search API Integration [4]
 * Twitter Bootstrap [5]
 * Leaflet [6]
 * Leaflet Markercluster Plugin [7]
 * Views GeoJSON [8]
 * Date [9]
 * Feeds JSONPath Parser [10]
 
Installation
------------
 1. Download the latest recommended release of various required modules
 2. Download and place Twitter Bootstrap, Leaflet, Leaflet Markercluster plugin in sites/all/libraries
 3. Log in as administrator and enable the module
 4. Configure Feed Importer as follows:

 
Feed Importer configuration
------------
URL: http://earthquake.usgs.gov/earthquakes/feed/v0.1/summary/2.5_month.geojson
Context: $.features.*
guid: properties.code
title: properties.place
field_magnitude: properties.mag
field_felt: properties.felt
field_cdi: properties.cdi
field_epicenter_distance: properties.dmin
field_location:lon: geometry.coordinates[0][0]
field_locatino:lat: geometry.coordinates[1]


Usage
------------
 1. Run the Feed Importer 
 2. Navigate to /map
 
References
------------
 [1] Features module: http://drupal.org/project/features
 [2] Geofiled module: http://drupal.org/project/geofield
 [3] Feeds module: http://drupal.org/project/feeds
 [4] RESTWS Search API Integration module: http://drupal.org/project/restws_search_apio
 [5] Twitter Bootstrap: http://twitter.github.io/bootstrap/
 [6] Leaflet: http://leafletjs.com/
 [7] Leaflet Markercluster plugin: https://github.com/Leaflet/Leaflet.markercluster
 [8] Views GeoJSON module: http://drupal.org/project/views_geojson
 [9] Date module: http://drupal.org/project/date
 [10] Feeds JSONPath Parser module: http://drupal.org/project/feeds_jsonpath_parser