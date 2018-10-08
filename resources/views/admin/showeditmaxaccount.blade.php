@extends('layouts.app')

                           
@section('content')
  <div class="row">
    <div class="col-md-12">
      <h3>Edit Time</h3>
      <div class="panel panel-default">
        <div class="panel-body">
          <form class="form-horizontal col-md-12" role="form" method="post" action="{{url('update-max-account')}}" enctype="multipart/form-data">
          <input type="hidden" name="id" value="{{$cruds->id}}">
           {{csrf_field()}}   

             <div class="form-group">
             <label class="control-label col-sm-5" for="Day">Max Account</label>
             <div class="col-sm-5">
              <input type="text" name="max_account" class="form-control" placeholder="max account" value="{{$cruds->max_account}}">
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
@endsection