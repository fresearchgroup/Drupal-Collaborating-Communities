angular.module('nodelist', ['node', 'nodes']).
  config(function($routeProvider) {
    $routeProvider.
      when('/', {controller:ListCtrl, templateUrl: Drupal.settings.angularjsApp.basePath + '/angular/nodes/list'}).
      otherwise({redirectTo:'/'});
  });


function ListCtrl($scope, Nodes, Node) {
  // Init local cache.
  $scope.cache = {};

  var currentClass = this.constructor.name;
  // Set defaule values.
  if (!Drupal.settings.angularjs.hasOwnProperty(currentClass)) {
    return;
  }

  var values = Drupal.settings.angularjs[currentClass];
  angular.forEach(values, function(value, key) {
    $scope[key] = value;
  });

  $scope.promote = function(node, newValue) {
    var update = new Node();
    update.promote = newValue;
    update.nid = node.nid;
    update.update();
    node.promote = newValue;
  }

  $scope.filterNodeType = function() {
    var nodeType = $scope.nodeType.selected;
    $scope.cache[nodeType] = $scope.cache[nodeType] || {};
    if ($scope.cache[nodeType].list) {
      // Get values from cache.
      $scope.nodes = $scope.cache[nodeType];
    }
    else {
      // Call server.
      $scope.nodes = Nodes.get({limit: 5, type: $scope.nodeType.selected});
      $scope.cache[nodeType] = $scope.nodes;
    }

  }

  // Invoke the filter.
  $scope.filterNodeType();
}
