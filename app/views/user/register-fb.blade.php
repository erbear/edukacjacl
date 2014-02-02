@extends('layouts.layout')
@section('head')
    <link href="{{asset('css')}}/form-log.css" rel="stylesheet">
@stop
@section('content')
	
<div class="container">
      <div class="row">
          <div class="er-form col-sm-4 col-sm-offset-4">
              {{Form::open(array('path'=>'user.register-fb', 'method'=>'post'));}}
                  {{Form::submit('Połącz z facebook\'iem!',array(
                    'class'=>'btn btn-lg btn-primary'));
                  }}                
			       {{Form::close();}}
          </div>
      </div>
      
  </div>
@stop