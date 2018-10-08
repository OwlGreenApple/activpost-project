@extends('layouts.app')

@section('content')
<div class="modal fade" id="defaultloading">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Please Wait</h4>
            </div>
            <div class="modal-body">
                <img src="{{ asset('public/images/loading.gif') }}" class="img-responsive center-block" width="50" height="50">
            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="alert alert-info">
                <strong>NOTE:</strong> Make sure your data is correct before publish, because you cannot be edit after publish!
            </div>
            @if (session('status'))
                <div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{ session('status') }}
                </div>
            @endif
            <div class="panel panel-default">
                <div class="panel-heading">
                    Schedules
                    <div class="pull-right">
                        <a href="{{ url('schedule') }}" class="btn btn-sm btn-danger">Back</a>
                    </div>
                </div>
                <div class="panel-body">
                    <input type="hidden" name="timezone" value="{{env('IG_TIMEZONE')}}">
                    <input type="hidden" name="rightnow" value="{{ Carbon\Carbon::now(''.env('IG_TIMEZONE').'')->toDateTimeString() }}">
                    {{-- Image --}}
                    <form name="uploader" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" value="{{ $schedule->id }}">
                        <input type="hidden" name="height" value="">
                        <input type="hidden" name="width" value="">
                        <input type="hidden" name="x" value="">
                        <input type="hidden" name="y" value="">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>Landscape Image</label>
                                <input type="file" name="file[]" id="LfileInput" accept=".jpg, .jpeg">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Portrait Image</label>
                                <input type="file" name="file[]" id="fileInput" accept=".jpg, .jpeg">
                            </div>
                        </div>
                        <div id="fileDisplayArea"></div>
                        @if (!empty($schedule->image))
                            <img src="{{ $schedule->image }}" class="img-responsive center-block" id="showimguri">
                        @else
                            <img src="" class="img-responsive center-block" id="showimguri" style="display: none;">
                        @endif
                        
                        <br>
                        <button type="submit" class="btn btn-primary center-block" id="pubimage" style="display: none;">Crop and Save</button>
                        <br>
                    </form>
                    <form role="form">
                        {{ csrf_field() }}
                        <input type="hidden" name="imguri" value="{{ url('schedule/saveimage') }}">
                        <input type="hidden" name="saveuri" value="{{ url('schedule/publish') }}">
                        <input type="hidden" name="ruri" value="{{ url('schedule') }}">
                        <input type="hidden" name="id" value="{{ $schedule->id }}">
                        <div class="form-group">
                            <label>Caption</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Accounts</label>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" value="" id="checkAll">
                                    <span style="color:blue;">Select All</span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group well">
                            <div class="row">
                                @foreach ($accounts as $account)
                                <div class="col-xs-6 col-sm-6 col-md-4">
                                    <label><input type="checkbox" class="check" name="accounts[]" value="{{ $account->id }}"> {{$account->username}}</label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>Post</label>
                                <input type="text" name="publish_at" class="form-control wsdate" required="required">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Delay</label>
                                <select name="delay" class="form-control" required="required">
                                    <option value="60">60 Seconds</option>
                                    <option value="120">120 Seconds</option>
                                    <option value="180">180 Seconds</option>
                                    <option value="240" selected="selected">240 Seconds</option>
                                    <option value="300">300 Seconds</option>
                                </select>
                            </div>
                        </div>
                        <button type="button" id="publishschedule" class="btn btn-primary">Publish</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
