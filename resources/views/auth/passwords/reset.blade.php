@extends('layouts.app')

@section('content')
      <div class="div-black">
      </div>
      <div class="container-home-1">  

				<div class="container-home-2" >  
          <div class="div-logo">
            <a href="http://activfans.com"><div class="logo"></div></a>
          </div>

						<div class="notif-user">							
							@if ($errors->has('email'))
								<div class="alert alert-danger">
									<p align="center">{{ $errors->first('email') }}</p>
								</div>
							@endif
							@if ($errors->has('password'))
								<div class="alert alert-danger">
									<p align="center">{{ $errors->first('password') }}</p>
								</div>
							@endif
							@if ($errors->has('password_confirmation'))
								<div class="alert alert-danger">
									<p align="center">{{ $errors->first('password_confirmation') }}</p>
								</div>
							@endif
							
						</div>
								
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/password/reset') }}">
                        {{ csrf_field() }}

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="control-label">E-Mail Address</label>

                                <input id="email" type="email" class="form-control" name="email" value="{{ $email or old('email') }}" autofocus>

                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="control-label">Password</label>

                                <input id="password" type="password" class="form-control" name="password">

                        </div>

                        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                            <label for="password-confirm" class="control-label">Confirm Password</label>
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation">

                        </div>

                        <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    Reset Password
                                </button>
                        </div>
                    </form>
										
										

					</div>
        </div>

      </div>
										
@endsection
