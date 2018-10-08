@extends('layouts.app')

                           
@section('content')

<form class="form-horizontal" role="form" method="post" enctype="multipart/form-data" action="{{url('importExcel')}} ">

 {{csrf_field()}}
 <div class="form-group">
 <label class="control-label col-md-5">Attach File Excel</label>
 <div class="col-md-5">
<label class="btn btn-default btn-file">
   <input type="file" name="import_file" >
</label>
</div>
</div>
<div class="form-group">
   <label class="control-label col-md-5" for="Day">Trial Day</label>
             <div class="col-md-5">
              <input type="text" name="time_d" class="form-control" placeholder="active time">
              </div>
</div>
<div class="form-group">
   <label class="control-label col-md-5" for="Day">Max Account</label>
             <div class="col-md-5">
              <input type="text" name="max_account" class="form-control" placeholder="max account">
             </div>
</div>
     <div class="form-group">
             <button class="btn btn-primary">Import File</button>
      </div>
</form>
@endsection