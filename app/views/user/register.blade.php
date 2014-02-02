@extends('layouts.layout')
@section('head')
    <link href="{{asset('css')}}/form-log.css" rel="stylesheet">
@stop
@section('content')
	
<div class="container">
      <div class="row">
          <div class="er-form col-sm-4 col-sm-offset-4">
              {{Form::open(array('path'=>'user.register', 'method'=>'post'));}}
                  <div class="form-group">
                    {{Form::text('login','',array(
				            'placeholder'=>'Login',
				            'class'=>'form-control',
				            'id'=>'exampleInputEmail1'
				    ));}}
                  </div>
                  <div class="form-group">
                     {{Form::password('password',array(
				            'placeholder'=>'Password',
				            'class'=>'form-control',
				            'id'=>'exampleInputPassword1'
				    ));}}
                  </div>
                  
                  {{Form::submit('Zarejestruj przez facebook',array(
				    		'class'=>'btn btn-default'
				    ));}}
                
			{{Form::close();}}
          </div>
      </div>
      
  </div>
@stop