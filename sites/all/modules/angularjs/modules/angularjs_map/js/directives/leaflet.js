'use strict';

/* Directives */

angular.module('angularMap', [])
  .directive('leafletMap', function() {
    return {
      restrict: 'A',
      template: '<div id="map" ng-transclude></div>',
      transclude: true,
      replace: true,
      scope: {
        mapCenter: '=',
        mapZoom: '@',
        iconPath: '@'
      },
      link: function(scope, element, attrs) {
        scope.$watch('mapZoom + mapCenter + iconPath + mapHeight', function(newValue, oldValue) {
          if (angular.isDefined(scope.mapZoom) &&  angular.isDefined(scope.mapCenter) && angular.isDefined(scope.iconPath)) {
            scope.map = L.map('map').setView(scope.mapCenter, scope.mapZoom);
            L.Icon.Default.imagePath = scope.iconPath;
            
            angular.forEach(scope.layers, function(value, key) {
              value.addTo(scope.map);
            });
          }
        });
      },
      controller: function($scope, $element) {
        var layers = $scope.layers = [];
        
        this.addLayer = function(layer) {
          if (undefined == $scope.map) {
            layers.push(layer);
          }
          else {
            layer.addTo($scope.map);
          }
        }
        
        this.removeLayer = function(layer) {
          $scope.map.removeLayer(layer);
        }
      }
    }
  })
  .directive('leafletTileLayer', function() {
    return {
      require: '^leafletMap',
      restrict: 'A',
      scope: {
        layerPath: '@',
        maxZoom: '@',
        layerAttribution: '@'
      },
      link: function(scope, element, attrs, leafletMapCtrl) {
        scope.$watch('layerPath + maxZoom + layerAttribution', function(newValue, oldValue) {
          if (undefined != scope.layerPath && undefined != scope.maxZoom && undefined != scope.layerAttribution) {
            var layer = L.tileLayer(scope.layerPath, {
              attribution: scope.layerAttribution,
              maxZoom: scope.maxZoom
            });
            leafletMapCtrl.addLayer(layer);
          }
        });
      }
    }
  })
  .directive('leafletGmapLayer', function() {
    return {
      require: '^leafletMap',
      restrict: 'A',
      scope: {
        mapType: '@'
      },
      link: function(scope, element, attrs, leafletMapCtrl) {
        attrs.$observe('mapType', function(val) {
          if (!angular.isDefined(val)) {
            scope.mapType = 'ROADMAP';
          }
        });
        
        scope.$watch('mapType', function(newValue, oldValue) {
          if (angular.isDefined(scope.mapType)) {
            var layer = new L.Google(scope.mapType);
            leafletMapCtrl.addLayer(layer);
          }
        });
      }
    }
  })
  .directive('leafletGeojsonLayer', function($http, $rootScope) {
    return {
      require: '^leafletMap',
      restrict: 'A',
      scope: {
        layerUrl: '@',
        layerFilter: '='
      },
      controller: function($scope, $element, $rootScope) {
        $rootScope.$on('reloadLayers', function(event, args) {
          $scope.refreshLayer();
        });
        
        $scope.filter = function(feature, layer) {
          return (undefined != $scope.layerFilter) ? $scope.layerFilter(feature, layer) : true;
        }
        
        $scope.refreshLayer = function() {
          var markers = new L.MarkerClusterGroup();
          
          var newLayer = L.geoJson($scope.data, {
            onEachFeature: function (feature, layer) {
              layer.bindPopup(feature.properties.name);
            },
            filter: $scope.filter
          });

          markers.addLayer(newLayer);
          
          $scope.parentCtrl.removeLayer($scope.markers);
          $scope.markers = markers;
          $scope.layer = newLayer;
          
          $scope.parentCtrl.addLayer($scope.markers);
        }
        
        $scope.createLayer = function() {
          $http({ method: 'GET', url: $scope.layerUrl, cache: true})
            .success(function(data, status, headers, config) {
              $scope.data = data;
              
              $scope.markers = new L.MarkerClusterGroup();

              $scope.layer = L.geoJson($scope.data, {
                onEachFeature: function (feature, layer) {
                  layer.bindPopup(feature.properties.name);
                },
                filter: $scope.filter
              });
  
              $scope.markers.addLayer($scope.layer);
              
              $scope.parentCtrl.addLayer($scope.markers);
          });
        }
      },
      link: function(scope, element, attrs, leafletMapCtrl) {
        scope.parentCtrl = leafletMapCtrl;
        
        scope.$watch('layerUrl', function(newValue, oldValue) {
          if (true == angular.isDefined(scope.layerUrl)) {
            if (false == angular.isDefined(scope.markers)) {
              scope.createLayer();
            }
            else {
              scope.refreshLayer();
            }
          }
        });
      }
    }
  });
