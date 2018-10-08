@extends('layouts.app')

                           
@section('content')
  <div class="row">
    <div class="col-md-12">
      <h3>Edit Time</h3>
      <div class="panel panel-default">
        <div class="panel-body">
          <form class="form-horizontal col-md-12" role="form" method="post" action="{{url('update-time')}}" enctype="multipart/form-data">
          <input type="hidden" name="id" value="{{ $cruds->id }}">
           {{csrf_field()}}   

             <div class="form-group">
             <label class="control-label col-sm-2" for="Day">Day</label>
             <div class="col-sm-10">
              <input type="text" name="time_d" class="form-control" placeholder="active time" value="{{secondsToday($cruds->active_time)}}">
              </div>
            </div>
            <div class="form-group">
            <label class="control-label col-sm-2" for="hour">Hour:</label>
            <div class="col-sm-10">
            <input type="text" name="time_h" class="form-control"  placeholder="hour" value="{{secondsTohour($cruds->active_time)}}">
            </div>
            </div>
            <div class="form-group">
            <label class="control-label col-sm-2" for="hour">Menit:</label>
            <div class="col-sm-10">
            <input type="text" name="time_m" class="form-control"  placeholder="menit" value="{{secondsTominets($cruds->active_time)}}">
            </div>
            </div>
            
            <div class="form-group">
              <input type="submit" class="btn btn-primary" value="Save">
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

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
@endsection