@extends('layouts.app')

                           
@section('content')
<form class="form-horizontal" role="form" method="post" action="{{url('proses-del-order')}} ">
<input type="hidden" name="id" value="{{ $delorder->id }}">
 {{csrf_field()}}
<p>Do You Want To Delete?</p>

     <div class="form-group">
              <input type="submit" class="btn btn-danger" value="Delete">

              <button type="button" data-dismiss="modal" class="btn btn-default">Cancel</button>
      </div>
</form>
@endsection
