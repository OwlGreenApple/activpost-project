@extends('layouts.app')

@section('content')

<div class="container">
<script type="text/javascript">
  $.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});
</script>
<div class="row"></div>
<!--
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
  Add User Coupon
</button>
-->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal2">
  Add General Coupon
</button>
<div class="row"></div>
<table class="table" id="table">
  <thead>
  <tr>
    <th>No</th>
    <!--<th>Nama</th>-->
    <th>Kode Kupon</th>
    <th>Potongan Kupon</th>
    <th>Berlaku Sampai</th>
    <th>Created</th>
  </tr>
  </thead>
  <tbody>
  
  <?php $no = $liscoupon->firstItem(); ?>
  @foreach ($liscoupon as $item)
    <tr class="item{{$item->id}}">
    <td>{{$no++}}</td>
    <!--<td>{{$item->username}}</td>-->
    <td>{{$item->coupon_code }}</td>
    <td>
      @if ($item->coupon_value == 0)
          {{$item->coupon_percent}}  %
      @else
          Rp {{number_format($item->coupon_value, 0,'.','.')}}
      @endif
    </td>
    <td>{{$item->valid_until}}</td>
    <td>{{$item->created_at}}</td>
    </tr>
  @endforeach
  </tbody>
</table>
     {!! $liscoupon->render() !!}   
</div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Add Coupon User</h4>
      </div>
      <div class="modal-body">
       
<form role="form" method="post" action="" enctype="multipart/form-data">
 {{ csrf_field() }}
<input type="hidden" name="id" value="">
  <div class="form-group row">
    <label for="nm" class="col-md-3">Name</label>
    <div class="col-md-6">
    <input type="text" class="form-control" id="nm">
    </div>
  </div>
  <div class="form-group row">
    <label for="gd" class="col-md-3">Coupon</label>
    <div class="col-md-3">
    <input type="text" class="form-control" id="coupon-code-1" maxlength="5">
    </div>
  </div>
  <div class="form-group row">
    <label for="gd" class="col-md-3">Nominal</label>
    <div class="col-md-3">
    <input type="text" class="form-control" id="coupon-value-1">
    </div>
  </div>
  <div class="form-group row">
    <label for="gd" class="col-md-3">Percent</label>
    <div class="col-md-3">
    <input type="text" class="form-control" id="coupon-percent-1">
    </div>
  </div>
  <div class="form-group row">
    <label for="pn" class="col-md-3">Date Valid Until</label>
    <div class="col-md-5">
    <div class='input-group date' >
    <input type="text"  class="form-control" id="tanggal1">
    </div>
    </div>
  </div>
</form>
       
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" id="add" class="btn btn-primary">Save</button>
      </div>
    </div>
  </div>
</div> 


<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Add Coupon</h4>
      </div>
      <div class="modal-body">
       
<form role="form" method="post" action="" enctype="multipart/form-data">
 {{ csrf_field() }}
<input type="hidden" name="id" value="">
  <!--<div class="form-group row">
    <label for="nm" class="col-md-3">Name</label>
    <div class="col-md-6">
    <input type="text" name="nama" class="form-control" id="nm">
    </div>
  </div>
  <div class="form-group row">
    <label for="gd" class="col-md-3">Coupon</label>
    <div class="col-md-3">
    <input type="text" name="coupon_code" class="form-control" id="gd" maxlength="5">
    </div>
  </div>-->
  <div class="form-group row">
    <label for="gd" class="col-md-3">Nominal</label>
    <div class="col-md-3">
    <input type="text" class="form-control" id="coupon-value-2">
    </div>
  </div>
  <div class="form-group row">
    <label for="gd" class="col-md-3">Percent</label>
    <div class="col-md-3">
    <input type="text" class="form-control" id="coupon-percent-2">
    </div>
  </div>
  <div class="form-group row">
    <label for="pn" class="col-md-3">Date Valid Until</label>
    <div class="col-md-5">
    <div class='input-group date' >
    <input type="text"  class="form-control" id="tanggal2">
    </div>
    </div>
  </div>
</form>
       
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <!--<button type="button" id="add" class="btn btn-primary">Save</button>-->
        <button type="button" id="addg" class="btn btn-primary">Generate Coupon General</button>
      </div>
    </div>
  </div>
</div> 
<script type="text/javascript">
  $("#add").click(function(){

    $.ajax({
      type:"POST",
      url :"addcoupon",
      data :{
				'name': $('#nm').val(),
				'coupon_code': $('coupon-code-1').val(),
				'coupon_value': $('#coupon-value-1').val(),
				'coupon_percent': $('#coupon-percent-1').val(),
				'valid_until': $('#tanggal2').val()
      },
      beforeSend: function (xhr) {
        // Function needed from Laravel because of the CSRF Middleware
        var token = $('meta[name="csrf_token"]').attr('content');
        if (token) {
            return xhr.setRequestHeader('X-CSRF-TOKEN', token);
        }
        $("#div-loading").show();
			},
			success: function(data){
				$("#div-loading").hide();
				$("#myModal, .fade").hide();
				location.reload();
		  }
    });
  });


  $("#addg").click(function(){
    $.ajax({
      type:"POST",
      url :"generatecoupon",
      data :{
				'coupon_value': $('#coupon-value-2').val(),
				'coupon_percent': $('#coupon-percent-2').val(),
				'valid_until': $('#tanggal2').val()
      },
      beforeSend: function (xhr) {
        var token = $('meta[name="csrf_token"]').attr('content');
        if (token) {
					return xhr.setRequestHeader('X-CSRF-TOKEN', token);
        }
        $("#div-loading").show();
      },
      success:function(data){
        $("#div-loading").hide();
        $("#myModal, .fade").hide();
				location.reload();
      }
    });
  });
/*

    $(function() {  
        $( "#nm" ).autocomplete({
         source: "lisusercoupon",  
           minLength:3, 
        });
    });*/
    $(function() {
      /*
      $('#nm').autocomplete({
        source: 'lisusercoupon',
       // minLength: 3,
        select: function(evt, data) {
            txtName.val(data.item.name);
            txtEmail.val(data.item.email);
            txtPhone.val(data.item.phone);
        }
    });
      */
    /*
    var languagesTags = [
      "PHP",
      "Javascript",
      "Ruby",
      "ASP",
      "Laravel",
      "Slim",
    ];
    var frameworksTags = [
      "Laravel",
      "Slim",
      "cakePHP"
    ];
    var ces = "{{ url('lisusercoupon') }}?term="+ $('input[name=nama]').val();
    $( "#nm" ).autocomplete({
      //source: languagesTags
      source: ces,
      //minLength: 3,
      });
    
    $( "#frameworks" ).autocomplete({
      source: frameworksTags
    });*/
    /*
    $("#nm").autocomplete({
    source: function (request, response) {
        $.getJSON("{{ url('lisusercoupon') }}?term=" + request.term, function (data) {
            response($.map(data.dealers, function (value, key) {
                return {
                    label: value,
                    value: key
                };
            }));
        });
    },
    minLength: 2,
    delay: 100
});*/
    
  });
     


  $('#tanggal1').datetimepicker({
      format: 'Y-m-d',
	});
  $('#tanggal2').datetimepicker({
      format: 'Y-m-d',
	});
</script>

<style type="text/css">
  .ui-autocomplete{
    z-index:1050;
   margin: 140px  0  0 505px;

}
</style>
  
@endsection




