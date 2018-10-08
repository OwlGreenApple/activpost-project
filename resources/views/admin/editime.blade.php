@extends('layouts.app')
@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-12">
    
      <div class="panel panel-default">
        <div class="panel-body">
          <form action="{{route('update', $cruds->id)}}" method="post">
          <input name="_method" type="hidden" value="PATCH">
          {{csrf_field()}}
            <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
              <input type="text" name="username" class="form-control" placeholder="Username" value="{{$cruds->username}}">
              {!! $errors->first('username', '<p class="help-block">:message</p>') !!}
            </div>
            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
              <input type="text" name="name" class="form-control" placeholder="Name" value="{{$cruds->name}}">
              {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
            </div>
            <div class="form-group">
              <input type="submit" class="btn btn-primary" value="Simpan">
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection