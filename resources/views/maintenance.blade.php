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
                <div class="panel-heading">Maintenance</div>
                <div class="panel-body">
                    <div class="row text-center">
                        <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
                            <a href="{{ url('maintenance/clearcache') }}" class="btn btn-default">Clear Cache</a>
                        </div>
                        <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
                            <a href="{{ url('maintenance/clearview') }}" class="btn btn-default">Clear View</a>
                        </div>
                        <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
                            <a href="{{ url('maintenance/clearroute') }}" class="btn btn-default">Clear Route</a>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
                            <a href="{{ url('maintenance/clearconfig') }}" class="btn btn-default">Clear Config</a>
                        </div>
                        <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
                            <a href="{{ url('maintenance/optimize') }}" class="btn btn-default">Optimize</a>
                        </div>
                        <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
                            <a data-toggle="modal" href='#delsche' class="btn btn-default">Delete All Schedule</a>
                            <div class="modal fade" id="delsche">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                            <h4 class="modal-title">Warning</h4>
                                        </div>
                                        <div class="modal-body">
                                            Are you sure want to delete all schedule?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                            <a href="{{ url('maintenance/delsche') }}" class="btn btn-primary">Yes</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
