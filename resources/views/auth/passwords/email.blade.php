@extends('layouts.app')

<!-- Main Content -->
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
									<p align="center">{{$errors->first('email')}}</p>
								</div>
							@endif
							@if (session('status'))
								<div class="alert alert-success">
									<p align="center">{{session('status')}}</p>
								</div>
							@endif
						</div>

                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/password/email') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="label-home control-label">E-Mail Address</label>

                            <input id="email" type="email" class="input-text-home form-control" name="email" value="{{ old('email') }}">
                        </div>

                        <div class="form-group">
                                <button type="submit" class="btn btn-home form-control">
                                    Send Password Reset Link
                                </button>
                        </div>
                        <div class="form-group">
                                <a class="btn btn-link" href="{{ url('') }}">
                                    Back to login
                                </a>
                        </div>
                    </form>

										
					</div>
        </div>

      </div>
@endsection
