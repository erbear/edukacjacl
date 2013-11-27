{{Form::open(array('path'=>'user.register', 'method'=>'post'));}}
        {{Form::text('login','',array(
                'placeholder'=>'login'
        ));}}
        {{Form::password('password',array(
                'placeholder'=>'haslo'
        ));}}
        {{Form::submit('Zarejestruj!');}}
{{Form::close();}}