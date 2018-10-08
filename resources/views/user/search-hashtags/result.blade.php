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
                <div class="panel-heading">
                    List Account
                    <div class="pull-right">
                        <a data-toggle="modal" href='#add' class="btn btn-sm btn-success">Add</a>
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
                                    {{-- Loading --}}
                                    <div id="showloading" style="display: none;">
                                        <img src="{{ asset('public/images/loading.gif') }}" class="img-responsive center-block" width="50" height="50">
                                    </div>
                                    {{-- Alert --}}
                                    <div id="successmsg" class="alert alert-success" style="display: none;">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <strong>Login Success!</strong>
                                    </div>
                                    <div class="form-group">
                                        <label for=Username"">Username</label>
                                        <input type="text" class="form-control" name="username" required="required">
                                    </div>
                                    <div class="form-group">
                                        <label for="Password">Password</label>
                                        <input type="password" class="form-control" name="password" required="required">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Add</button>
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
