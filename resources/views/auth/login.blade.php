<!--
<html>
    <head>
      <title>Celebpost</title>
      <link rel="shortcut icon" type="image/x-icon" href="{{ asset('/images/celebpost-favicon.png') }}">
      <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">
      <link href="{{ asset('/css/bootstrap.min.css') }}" rel="stylesheet">
      <link href="{{ asset('/css/bootstrap-theme.min.css') }}" rel="stylesheet">
      <link href="{{ asset('/css/sign-in.css') }}" rel="stylesheet">
    </head>
    <body>
      <div class="div-black">
      </div>
      <div class="container">  
        <div class="container2" >  
          <div class="div-logo">
            <a href="http://activpost.net"><div class="logo"></div></a>
          </div>
					
-->
@extends('layouts.app')

@section('content')
      <div class="div-black">
      </div>
      <div class="container-home-1">  

				<div class="container-home-2" >  
          <div class="div-logo">
            <a href="https://activfans.com"><div class="logo"></div></a>
          </div>

						<div class="notif-user">
							@if ($errors->has('username'))
								<div class="alert alert-danger">
									<p align="center">{{ $errors->first('username') }}</p>
								</div>
							@endif

							@if ($errors->has('password'))
								<div class="alert alert-danger">
									<p align="center">{{ $errors->first('password') }}</p>
								</div>
							@endif
							@if (session('error') )
								<div class="alert alert-danger">
									<p align="center">{{session('error')}}</p>
								</div>
							@endif
							@if (session('success') )
								<div class="alert alert-success">
									<p align="center">{{session('success')}}</p>
								</div>
							@endif
						</div>
					
					
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                            <label for="username" class="label-home control-label">Email Address</label>

                                <input id="username" type="text" class="form-control input-text-home" name="username" value="{{ old('username') }}" autofocus placeholder="Email">

                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="label-home control-label">Password</label>

                                <input id="password" type="password" class="form-control input-text-home" name="password" placeholder="password">

                        </div>

                        <div class="form-group">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember"> Remember Me
                                    </label>
                                </div>
                        </div>

                        <div class="form-group">
                                <button type="submit" class="btn btn-home form-control">
                                    Sign in
                                </button>

                                <a class="btn btn-link" href="{{ url('/password/reset') }}">
                                    Forgot Your Password?
                                </a>
                        </div>
												
												
												
												
                    </form>
					

					</div>
        </div>

      </div>
@endsection
					
					
<!--					
					<div class="notif-user">
						@if (session('error') )
							<div class="alert alert-danger">
								<p align="center">{{session('error')}}</p>
							</div>
						@endif
						@if (session('success') )
							<div class="alert alert-success">
								<p align="center">{{session('success')}}</p>
							</div>
						@endif
					</div>
        </div>

      </div>
    </body>
</html>

-->