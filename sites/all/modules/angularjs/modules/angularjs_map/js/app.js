angular.module('earthquakeMap', ['angularMap', 'restwsSearch', 'restwsSearchFacet', 'angularMapFilters']).
  config(function($routeProvider) {
    $routeProvider.
      when('/', {controller: MapCtrl, templateUrl: 'partials/map.html', reloadOnSearch: false}).
      otherwise({redirectTo:'/'});
  });


function MapCtrl($scope, $location, $filter, $rootScope, restwsSearch) {
  $scope.mapCenter = [35.505, -0.09];
  $scope.facets = [];
  $scope.geojsonlayer = {};
  $scope.geoJsonUrl = '/earthquakes';
  
  var search = $location.search();
  
  if (undefined == search['limit']) {
    search.limit = 10;
  }

  $scope.facet_values = $filter('getFacetValues')(search);
  
  restwsSearch.async('/earthquake.json', search).then(function(d) {
    $scope.facets = d.facets;
  });
  
  $scope.$on('$routeUpdate', function() {
    search = $location.search();
    
    restwsSearch.async('/earthquake.json', $location.search()).then(function(d) {
      $scope.facets = d.facets;
    });
    
    $scope.facet_values = facet_values = $filter('getFacetValues')(search);
    $rootScope.$broadcast('reloadLayers', {});
  });
  
  $scope.layerFilter = function(feature, layer) {
    $scope.geojsonlayer = layer;
    return $filter('filterLayer')(feature, $scope.facet_values);
  }
}
