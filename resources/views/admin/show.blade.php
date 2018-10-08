@extends('layouts.app')

                           
@section('content')

		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					
					<th>Description</th>
					<th>Created</th>
					
				</tr>
				</thead>
				<tbody>
				 @foreach ($logs as $vlog)
					<tr>
						<td>{{ $vlog->description }}</td>
						<td>{{ $vlog->created_at }}</td>
					</tr>
					@endforeach
				</tbody>
		</table>

@endsection