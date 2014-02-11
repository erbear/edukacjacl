'use strict';


// Declare app level module which depends on filters, and services
angular.module('ZapisyApp', [
	'ui.router',
	'ZapisyControllers',
	'ZapisyDirectives',
	'ZapisyServices'
])
.run(['$rootScope','$state', function($rootScope, $state){
	$rootScope.$state = $state;
}])
.config(['$stateProvider','$urlRouterProvider', function($stateProvider, $urlRouterProvider) {
  $urlRouterProvider.otherwise("/");

	$stateProvider
		.state('plan', {
			url: "/",
			templateUrl: "partials/ed-plan.html"
		})
		.state('kurs', {
			url: "/kurs/{kursid:[0-9]{1,3}}",
			templateUrl: "partials/ed-prowadzacy.html",
			controller: "KursController"
		})
		.state('kurs.prowadzacy', {
			url: "/prowadzacy/{prowadzacyid}",
			templateUrl: "partials/ed-kalendarz.html",
			controller: "ProwadzacyController"
		})

}]);
