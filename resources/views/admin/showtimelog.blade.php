@foreach ($logs as $vlog)
	<tr>
    <td>{{ $vlog->created_at }}</td>
		<td>
      {{secondsToday($vlog->time)}} D {{secondsTohour($vlog->time)}} H {{secondsTominets($vlog->time)}} M
    </td>
	</tr>
@endforeach

<?php
function secondsToday($seconds) {
  $dtF = new DateTime("@0");
  $dtT = new DateTime("@$seconds");
  return $dtF->diff($dtT)->format('%a');
}

function secondsTohour($seconds) {
  $dtF = new DateTime("@0");
  $dtT = new DateTime("@$seconds");
  return $dtF->diff($dtT)->format('%h');
}

function secondsTominets($seconds) {
  $dtF = new DateTime("@0");
  $dtT = new DateTime("@$seconds");
  return $dtF->diff($dtT)->format('%i');
}
?>