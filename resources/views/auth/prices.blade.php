@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Prices</div>
                <div class="panel-body">
								
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/checkout') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Package Name</label>

                            <div class="col-md-6">
															<select name="select-package" class="form-control">
																<?php foreach($packages as $package) { ?>
																	<option value="{{$package->id}}">{{$package->package_name." - ".$package->price}}</option>
																<?php } ?>
															</select>

                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Kode Kupon</label>
                            <div class="col-md-6">
															<input type="text" placeholder="Kode Kupon" class="form-control" name="coupon-code">
															<input type="button" value="Apply">
                            </div>
                        </div>
												
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Metode Pembayaran</label>
                            <div class="col-md-6">
															<select name="payment-method" class="form-control">
																	<option value="Transfer Bank">Transfer Bank</option>
															</select>

                            </div>
                        </div>
												
												
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Submit
                                </button>
                            </div>
                        </div>
												
                    </form>
										
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
