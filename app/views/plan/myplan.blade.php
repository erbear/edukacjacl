<table>
@foreach ($lectures as $lecture)
<tr>
	<td>
	{{$lecture->name}}
	</td>
	<td>
	{{$lecture->day->name}}
	</td>
	<td>
	{{$lecture->hour->start}}-{{$lecture->hour->finish}}
	</td>
	<td>
	{{$lecture->teacher->name}}
	</td>
	<td>
	{{$lecture->place->bulding}} {{$lecture->place->room}}
	</td>
</tr>
@endforeach