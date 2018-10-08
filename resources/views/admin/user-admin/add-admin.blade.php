@extends('layouts.app')

                           
@section('content')
  <div class="row">
    <div class="col-md-12">
      <h3>Add Admin</h3>
      <div class="panel panel-default">
        <div class="panel-body">
          <form class="form-horizontal col-md-12" role="form" method="post" action="{{url('add-admin')}}" enctype="multipart/form-data">
           {{csrf_field()}}   

            <div class="form-group">
              <label class="control-label col-sm-3" for="name">
                Name
              </label>
              <div class="col-sm-9">
                <input type="text" name="name" class="form-control" placeholder="name">
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-sm-3" for="email">
                Email
              </label>
              <div class="col-sm-9">
                <input type="text" name="email" class="form-control" placeholder="email">
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-sm-3" for="password">
                Password
              </label>
              <div class="col-sm-9">
                <input type="password" name="password" class="form-control" placeholder="password">
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