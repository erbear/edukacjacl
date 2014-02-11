<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>erbear.com - EdukacjaCL do smieci</title>

    <!-- Bootstrap core CSS -->

    <!-- <link href="lib/bootstrap/dist/css/bootstrap.css" rel="stylesheet"> -->
<!-- 
     Add custom CSS here 
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet">
 -->
    <link href="{{{asset('css')}}}/index-layout.css" rel="stylesheet">


  </head>
  <body>
  <div id="fb-root"></div>
    <script>(function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = "//connect.facebook.net/pl_PL/all.js#xfbml=1&appId=286087371540581";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>
    <div ><img id="image" href="data/edu-trash.png"  alt="Responsive image">
    </div>
    <div id="content">
      <div >
        <div class="fb-like" data-href="http://erbear.com/" data-colorscheme="dark" data-layout="button" data-action="like" data-show-faces="false" data-share="false"></div>
        <h1>Zapisy na kursy to udręka.</h1>
        <p>Ale zbliża się alternatywa.</p>
        {{{Form::open(array('path'=>'user.login', 'method'=>'post'));}}}
		        {{{Form::text('login','',array(
		                'placeholder'=>'Login'
		        ));}}}
		        {{{Form::password('password',array(
		                'placeholder'=>'Hasło'
		        ));}}}
		        {{{Form::submit('Zaloguj!','',array(
		                'id'=>'submit'
		        ));}}}
		{{{Form::close();}}}
      </div>
    </div>
    <script type="text/javascript">
    var changeHeight = function(){
      var h;
      if(self.innerHeight)
        h = window.innerHeight
      else if(document.documentElement && document.documentElement.clientHeight)
        h = document.documentElement.clientHeight;
      else if(document.body)
        h= document.body.clientHeight;
      var element = document.getElementById('image');
      element.style.height = h-15 +"px";
      var element = document.getElementById('content');
      element.style.height = h-15 +"px";
    }
    window.addEventListener('resize', function(event){
      changeHeight();
    });
      changeHeight();
    </script>
  </body>
  </html>