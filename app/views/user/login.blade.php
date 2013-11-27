{{Form::open(array('path'=>'user.login', 'method'=>'post'));}}
        {{Form::text('login','',array(
                'placeholder'=>'login'
        ));}}
        {{Form::password('password',array(
                'placeholder'=>'haslo'
        ));}}
        {{Form::submit('Zaloguj!');}}
{{Form::close();}}