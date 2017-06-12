angular.module('angularMapFilters', [])
  .filter('filterLayer', function() {
    return function(feature, facet_values) {
      var visible = true;
      angular.forEach(facet_values, function(value, key) {
        if (true === angular.isDefined(feature.properties[key]) && value != feature.properties[key]) {
          visible = false;
        } 
      });
      return visible;
    }
  })
  .filter('getFacetValues', function() {
    return function(search) {
      var facet_values = {};
      
      angular.forEach(search, function(value, key) {
        if ('f[' == key.substring(0, 2)) {
          var value_values = value.split(':');
          facet_values[value_values[0]] = value_values[1];
        }
      });
      
      return facet_values;
    }
  }); 
