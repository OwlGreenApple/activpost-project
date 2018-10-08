@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            @if (session('status'))
                <div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{ session('status') }}
                </div>
            @endif
            <div class="panel panel-default">
                <div class="panel-heading">
                    Schedules Video
                    <div class="pull-right">
                        <a href="{{ url('schedule-video/add') }}" class="btn btn-xs btn-success">Add</a>
                        <a class="btn btn-xs btn-default" onClick="window.location.reload()">Refresh</a>
                    </div>
                </div>
                <div class="panel-body">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Video</th>
                                <th>Caption</th>
                                <th>Created at</th>
                                <th>Schedule at</th>
                                <th>Delay</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($schedules as $schedule)
                            <tr>
                                <td>
                                    @if (!empty($schedule->image))
                                        <img src="{{$schedule->image}}" class="img-responsive zoom" data-zoom-image="{{$schedule->image}}" width="50" height="50">
                                    @endif
                                </td>
                                <td>{{ str_limit($schedule->description, 30) }}</td>
                                <td>{{ date('M d, Y H:i',strtotime($schedule->created_at)) }}</td>
                                <td>{{ date('M d, Y H:i',strtotime($schedule->publish_at)) }}</td>
                                <td>{{ number_format($schedule->delay) }} Seconds</td>
                                <td>
                                    @if ($schedule->success->count() === $schedule->accounts->count())
                                        <button type="button" class="btn btn-xs btn-success" data-toggle="collapse" data-target="#collapseme{{$schedule->id}}">Completed</button>
                                        <a class="btn btn-xs btn-danger" data-toggle="modal" href='#del-{{$schedule->id}}'>Delete</a>
                                        <div class="modal fade" id="del-{{$schedule->id}}">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                        <h4 class="modal-title">Warning!</h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        Are you sure want to delete this post?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                                        <a href="{{ url('schedule-video/delete/'.$schedule->id) }}" class="btn btn-primary">Yes</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        @if ($schedule->failed->count() === $schedule->accounts->count())
                                            <button type="button" class="btn btn-xs btn-danger" data-toggle="collapse" data-target="#collapseme{{$schedule->id}}">Failed</button>
                                        @else
                                            @if ($schedule->proccess->count() === $schedule->accounts->count())
                                                <button type="button" class="btn btn-xs btn-info" data-toggle="collapse" data-target="#collapseme{{$schedule->id}}">Processing</button>
                                            @else
                                                @if ($schedule->failed->count() > 1)
                                                    <button type="button" class="btn btn-xs btn-warning" data-toggle="collapse" data-target="#collapseme{{$schedule->id}}">Warning</button>
                                                @else
                                                    <button type="button" class="btn btn-xs btn-info" data-toggle="collapse" data-target="#collapseme{{$schedule->id}}">Processing</button>
                                                @endif
                                            @endif
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            <tr id="collapseme{{$schedule->id}}" class="collapse out">
                                <td colspan="6">
                                    <ul class="list-inline">
                                        @foreach ($schedule->accounts as $account)
                                            <li>
                                                @if ($account->pivot->status === 0)
                                                    <span class="label label-default" data-toggle="tooltip" data-placement="top" title="Processing" style="cursor:help">{{$account->username}}</span>
                                                @elseif ($account->pivot->status === 1)
                                                    <span class="label label-danger" data-toggle="tooltip" data-placement="top" title="{{$account->pivot->msg}}" style="cursor:help">{{$account->username}}</span>
                                                @elseif ($account->pivot->status === 2)
                                                    <a href="https://www.instagram.com/{{$account->username}}" target="_blank"><span class="label label-success" data-toggle="tooltip" data-placement="top" title="Success">{{$account->username}}</span></a>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="text-center">
                        {{ $schedules->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
