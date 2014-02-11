<!DOCTYPE html>
<html ng-app="ZapisyApp"lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Zapisy EdukacjaCL</title>

    <!-- Bootstrap core CSS -->
<!--
    <link href="lib/bootstrap/bootstrap.css" rel="stylesheet">

     Add custom CSS here 
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet">
-->
    <link href="{{{asset('css')}}}/global.css" rel="stylesheet">
    <link href="{{{asset('css')}}}/calendar.css" rel="stylesheet">
    <link href="{{{asset('css')}}}/layout.css" rel="stylesheet">
  </head>

  <body ng-controller="MainController" ng-init="path = '{{{action('PlanController@getAllTerminy')}}}';templatePlan = '{{{asset('partials/ed-plan.html')}}}'">
      
      <div id="ed-sidebar">
      <div id="legenda">
          <ul>
              <li ui-sref="plan">Plan</li>
              <li ng-repeat="dana in dane" ui-sref="kurs({kursid: $index})" ng-class="[services.type(dana.kind.id), isDone(plan, dana) ? 'type6' : '']">{{dana.name}}</li>
          </ul>   
          <div><p class="type1">wykład</p><p class="type2">laboratoria</p><p class="type3">ćwiczenia</p><p class="type5">projekt</p></div>
        <div><span class="TP">zajęty TP</span><span class="TN">zajęty TN</span></div>
          </div>
      </div>
      <div ui-view></div>
    <script src="{{{asset('lib')}}}/angular/angular.js"></script>
    <script src="{{{asset('lib')}}}/angular-ui/angular-ui-router.js"></script>
    <script src="{{{asset('lib')}}}/angular/angular-resource.js"></script>
    <script src="{{{asset('js')}}}/app.js"></script>
    <script src="{{{asset('js')}}}/services.js"></script>
    <script src="{{{asset('js')}}}/controllers.js"></script>
    <script src="{{{asset('js')}}}/filters.js"></script>
    <script src="{{{asset('js')}}}/directives.js"></script>
  </body>
  </html>