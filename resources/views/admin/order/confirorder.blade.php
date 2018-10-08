@extends('layouts.app')

                           
@section('content')
<form class="form-horizontal" role="form" method="post" action="{{url('proses-confir-order')}}">
<input type="hidden" name="id" value="{{ $ordercon->id }}">
 {{csrf_field()}}   
<p>Do You Want To Confirmed?</p>

     <div class="form-group">
              <input type="submit" class="btn btn-danger" value="Confirmed">

              <button type="button" data-dismiss="modal" class="btn btn-default">Cancel</button>
      </div>
</form>
@endsection