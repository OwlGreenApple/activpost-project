@extends('layouts.app')

                           
@section('content')
  <div class="row">
    <div class="col-md-12">
      <h3>Edit Email</h3>
      <div class="panel panel-default">
        <div class="panel-body">
          <form class="form-horizontal col-md-12" role="form" method="post" action="{{url('update-email')}}" enctype="multipart/form-data">
          <input type="hidden" name="id" value="{{$cruds->id}}">
           {{csrf_field()}}   

             <div class="form-group">
             <label class="control-label col-sm-2" for="Day">Email</label>
             <div class="col-sm-10">
              <input type="text" name="email" class="form-control" placeholder="email" value="{{$cruds->email}}">
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