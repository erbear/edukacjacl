{{ Form::open(array('url' => 'day')) }}
{{ Form::label('labelDay', 'Dzien:') }}
{{ Form::text('textDay') }}
{{ Form::submit('Wyslij!') }}
{{ Form::close() }}

@foreach ($day as $d)
    {{ $d->name }}
@endforeach