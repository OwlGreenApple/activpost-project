@extends('layouts.app')

@section('content')
<?php 
use \InstagramAPI\Instagram;
?>
<div class="container">
    <div class="row">
        <!--<div class="col-md-8 col-md-offset-2">-->
        <div class="col-md-12">
            @if (session('status'))
                <div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{ session('status') }}
                </div>
            @endif
            <div class="panel panel-default">
                <div class="panel-heading">
                    List Account
                    <div class="pull-right">
                        <a data-toggle="modal" href='#add' id="button-add" class="btn btn-sm btn-home">Add</a>
                    </div>
                </div>
                {{-- Add Modal --}}
                <div class="modal fade" id="add">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form id="addaccount" role="form">
                                {{ csrf_field() }}
                                <input type="hidden" name="uri" value="{{ url('account/chklogin') }}">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title">Add Account</h4>
                                </div>
                                <div class="modal-body">
                                    {{-- Alert --}}
                                    <div id="successmsg" class="alert alert-success" style="display: none;">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <strong>Login Success!</strong>
                                    </div>
                        
																		<div class="row">
																			<div class="col-sm-12 col-md-12">
																				<input type="checkbox" class="checkbox-term" id="terms-add-account1"> <label for="terms-add-account1" class="control-label">UMUR akun Instagram minimal 10 hari</label>
																			</div>
																			<div class="col-sm-12 col-md-12">
																				<input type="checkbox" class="checkbox-term" id="terms-add-account4"> <label for="terms-add-account4" class="control-label">Email & No HP sudah terhubung dengan Account Instagram ini</label>
																			</div>
																			<div class="col-sm-12 col-md-12">
																				<input type="checkbox" class="checkbox-term" id="terms-add-account5"> <label for="terms-add-account5" class="control-label">PUNYA AKSES ke Email & No HP tersebut</label>
																			</div>
																			<div class="col-sm-12 col-md-12">
																				<input type="checkbox" class="checkbox-term" id="terms-add-account6"> <label for="terms-add-account6" class="control-label">Akun Instagram memiliki 10 Post Photo / Video</label>
																			</div>
																			<div class="col-sm-12 col-md-12">
																				<input type="checkbox" class="checkbox-term" id="terms-add-account7"> <label for="terms-add-account7" class="control-label">Turn OFF 2 Factor Authentications ( Khusus followers >1000 ) <a href="https://activfans.freshdesk.com/solution/articles/9000093394--instagram-error-instagram-selalu-minta-verifikasi-no-telp"> >> Link Help << </a> </label>
																			</div>
																			<div class="col-sm-12 col-md-12">
																				<input type="checkbox" class="checkbox-term" id="terms-add-account9"> <label for="terms-add-account9" class="control-label">Saya sudah membaca dan mempelajari <a href="https://docs.google.com/document/d/1GWfW5kU5yvchCZlqPxEksK7NrMqgQGP0saqCG2VNGlQ"> Tutorial Celebpost </a> </label>
																			</div>
																			<div class="col-sm-12 col-md-12">
																				<input type="checkbox" class="checkbox-term" id="terms-add-account8"> <label for="terms-add-account8" class="control-label">Saya sudah membaca & menyetujui <a href="http://activfans.com/terms-conditions">TERMS & CONDITIONS</a> Celebpost </label>
																			</div>
																		</div>


                                    <div class="form-group">
                                        <label for="insta_username">Username</label>
                                        <input type="text" class="form-control" name="insta_username" required="required" id="insta_username">
                                    </div>
                                    <div class="form-group">
                                        <label for="insta_password">Password</label>
                                        <input type="password" class="form-control" name="insta_password" required="required" id="insta_password">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary" id="button-process">Add</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="well well-sm">
                        NOTE: You can't edit account, if you need to change password or username, please delete first, then recreate account.
                    </div>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Profile Pic</th>
                                <th>Username</th>
                                <th>Proccessing Post</th>
                                <th>Failed Post</th>
                                <th>Success Post</th>
                                <th>Total Post</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($accounts as $account)
                            <tr>
                                <td><?php 
																	// Decrypt
																	$decrypted_string = Crypt::decrypt($account->password);
																	$pieces = explode(" ~space~ ", $decrypted_string);
																	$pass = $pieces[0];
																	$IGDataPath = base_path('storage/ig/'.$account->username.'/');
																	$i = new Instagram($account->username, $pass, false, $IGDataPath);
																	
																	$ppurl = $i->getProfileData()->getProfilePicUrl();
																	// echo $ppurl;
																	
																?>
																	<img src="{{$ppurl}}" class="circle-image">
																</td>
                                <td>{{ $account->username }}</td>
                                <td>{{ $account->proccess->count() }}</td>
                                <td>{{ $account->failed->count() }}</td>
                                <td>{{ $account->success->count() }}</td>
                                <td>
                                    {{ number_format($account->schedules->count()) }}
                                </td>
                                <td>
                                    {{-- Delete --}}
                                    <a data-toggle="modal" href='#delete-{{$account->id}}' class="btn btn-xs btn-danger">Delete</a>
                                    <a href="https://www.instagram.com/{{ $account->username }}" target="_blank" class="btn btn-xs btn-info">View</a>
                                    <div class="modal fade" id="delete-{{$account->id}}">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                    <h4 class="modal-title">Warning!</h4>
                                                </div>
                                                <div class="modal-body">
                                                    Delete <strong>{{ $account->username }}</strong> Account?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                                    <a href="{{ url('/account/delete/'.$account->id) }}" class="btn btn-primary">Yes</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="text-center">
                        {{ $accounts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
