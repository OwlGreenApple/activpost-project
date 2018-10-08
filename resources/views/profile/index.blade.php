@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            @if (session('status'))
                <div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{ session('status') }}
                </div>
            @endif
            <div class="panel panel-default">
                <div class="panel-heading">Change Password</div>
                <div class="panel-body">
                    <form action="{{ url('profile/update') }}" method="POST" role="form" class="row">
                        {{ csrf_field() }}
												<!--
                        <div class="form-group col-md-4">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" name="name" value="{{ Auth::user()->name }}" required="required">
                        </div>
                        <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }} col-md-4">
                            <label for="Username">Username</label>
                            <input type="text" class="form-control" name="username" value="{{ Auth::user()->username }}" required="required">
                            @if ($errors->has('username'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('username') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }} col-md-4">
                            <label for="Email">Email</label>
                            <input type="email" class="form-control" name="email" value="{{ Auth::user()->email }}" required="required">
                            @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>
												-->
                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }} col-md-6">
                            <label for="Password">Password</label>
                            <input type="password" class="form-control" name="password" placeholder="********">
                            @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }} col-md-6">
                            <label for="ConfirmPassword">Confirm Password</label>
                            <input type="password" class="form-control" name="password_confirmation" placeholder="********">
                            @if ($errors->has('password_confirmation'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-home">Ubah</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
