@foreach($logs as $log)
  <tr>
    <td>{{$log->created_at}}</td>
    <td>{{$log->description}}</td>
  </tr>
@endforeach