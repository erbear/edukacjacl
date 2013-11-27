<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Starter Template for Bootstrap</title>

    <!-- Bootstrap core CSS -->
    <link href="{{asset('css')}}/bootstrap.css" rel="stylesheet">

    <!-- Add custom CSS here -->
    <link href="{{asset('css')}}simple-sidebar.css" rel="stylesheet">
    <link href="{{asset('css')}}calendar.css" rel="stylesheet">
    <link href="{{asset('font-awesome')}}/css/font-awesome.min.css" rel="stylesheet">

    @yield('head')
  </head>

  <body>
    @yield('content')
    
  </body>
</html>