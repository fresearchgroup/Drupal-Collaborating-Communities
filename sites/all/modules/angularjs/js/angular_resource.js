angular.module('node', ['ngResource']).factory('Node', function($resource) {
	var Node = $resource('/node/:nid', {}, {
	  update: { method: 'PUT' }
	});

	Node.prototype.update = function(node) {
	  return Node.update({nid: this.nid}, angular.extend({}, this, {nid: undefined}), node);
	}

	return Node;
}).config(["$httpProvider", function(provider) {
  provider.defaults.headers.common['X-CSRF-Token'] = Drupal.settings.angularjsApp.restws_csrf_token;
  provider.defaults.headers.common['X-ANGULARJS'] = 1;
}]);

angular.module('nodes', ['ngResource']).factory('Nodes', function($resource) {
  var Node = $resource(Drupal.settings.angularjsApp.basePath + '/node.json', {}, {
  });

  return Node;
 });
