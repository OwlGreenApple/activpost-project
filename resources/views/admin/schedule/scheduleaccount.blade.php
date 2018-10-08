@extends('layouts.app')

                           
@section('content')

    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>No</th>
          <th>Account ID</th>
          <th>Status</th>
          <th>Msg</th>
          <th width="75">Publish Time</th>
          <th width="75">Deleted Time</th>
        </tr>
        </thead>
        <tbody>
        <?php $no = $accountschedule->firstItem()  ; ?>
        @foreach ($accountschedule as $accsche)
          <tr>
            <td>{{$no ++}}</td>
            <td><a href="https://www.instagram.com/{{$accsche->username}}" target="_blank">{{$accsche->username}}</a></td>
            <td>
            @if ($accsche->status == 1)
              Pending
            @elseif ($accsche->status == 2)
              Published
            @elseif ($accsche->status == 3)
              Deleted
            @endif
            </td>
            <td>{{$accsche->msg}} </td>
            <td>{{$accsche->published_time}} </td>
            <td>{{$accsche->deleted_time}} </td>
          </tr>
        @endforeach
        </tbody>
    </table>
      {!! $accountschedule->render() !!}
@endsection