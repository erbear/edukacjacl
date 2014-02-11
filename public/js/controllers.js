'use strict';

/* Controllers */

angular.module('ZapisyControllers', [])
  .controller('MainController', ['$scope', '$http','CalendarService', function($scope, $http, CalendarService) {
    $scope.$watch('initialData', function(data){
      var newData = CalendarService.customizeJSON(data);
      $scope.dane = newData;
      $scope.nauczyciele = [{nazwa: "Twoj Plan"}]
      $scope.plan = {1: new Array(), 2: new Array(), 3: new Array(), 4: new Array(), 5: new Array() };
      $scope.przedmioty = $scope.plan;
      $scope.isPlan = true;
    })
    
    $scope.services = CalendarService;
    $scope.isPlan = true;
    
  	$scope.changeSubject = function(data){
      $scope.nauczyciele = data.teachers
      $scope.przedmioty = data.teachers[0];
      $scope.isPlan = false;
  	}
    $scope.changeTeacher = function(nauczyciel){
      $scope.przedmioty = nauczyciel;
    }
    $scope.changeToPlan = function(nauczyciel){
      $scope.przedmioty = $scope.plan;
      $scope.nauczyciele = [{nazwa: "Twoj Plan"}];
      $scope.isPlan = true;
    }
    //usuwa z tablicy obiekt na podanej pozycji
    Array.prototype.remove = function(from, to) {
      var rest = this.slice((to || from) + 1 || this.length);
      this.length = from < 0 ? this.length + from : from;
      return this.push.apply(this, rest);
    };
  	
    $scope.isDone = function(plan, przedmiot){
      var tmp = $scope.services.findInPlan(plan, przedmiot.id);//szuka przedmiotu w planie
      if( tmp.dzien != -1){//jak znajdzie to git
        return true;
      }
      return false; 
    }
    $scope.sendPlan = function(){
      console.log('klik');
      $http.post('/plan/zapisz-plan', $scope.plan).success(function(data){
        console.log(data);

      });
    }
  }])
  .controller('KursController', ['$scope', 'CalendarService', '$stateParams','$state', function($scope, CalendarService, $stateParams, $state){
    $scope.nauczyciele = $scope.dane[$stateParams.kursid].teachers;
    $scope.przedmiot = $scope.dane[$stateParams.kursid].name;

    $scope.$on('$stateChangeSuccess', 
      function(event, toState, toParams, fromState, fromParams){ 
          console.log(toParams);
          if (toState.url == "/kurs/{kursid:[0-9]{1,3}}"){
            $state.go('.prowadzacy', {prowadzacyid: 0});
          }
    })
  }])
  .controller('ProwadzacyController', ['$scope', 'CalendarService', '$stateParams', function($scope, CalendarService, $stateParams){
    if ($stateParams.prowadzacyid!= null){
      $scope.przedmioty = $scope.nauczyciele[$stateParams.prowadzacyid];
    }else {
      $scope.przedmioty = $scope.nauczyciele[0];
    }
  }]);