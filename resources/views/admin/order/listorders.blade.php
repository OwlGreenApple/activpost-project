@extends('layouts.app')

@section('content')
<div class="container">

           @if(Session::has('message'))
        <span class="labe label-succes">{{Session::get('message')}}</span>
    @endif
   
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="col-sm-5 col-md-7">
                    <div class="row">&nbsp</div>
                    <form action="{{ url('search-orders') }}" method="GET" class="navbar-form" role="search">
                     
                    <div class="input-group">
                    <label class="radio-inline"><input type="radio" name="q2" value="all" checked>All</label>
                    <label class="radio-inline"><input type="radio" name="q2" value="Confirmed">Confirmed</label>
                    <label class="radio-inline"><input type="radio" name="q2" value="Not Confirmed">Not Confirmed
														<span class="glyphicon glyphicon-question-sign hint-button tooltipPlugin" title="<div class='panel-heading'>Status</div><div class='panel-content'> Confirmed = Pembayaran sudah di confirm admin <br>
														Not Confirmed = Pembayaran sudah di confirm user, belum di confirm admin <br>
														Pending = Pembayaran sudah di order user, user tsb belum melakukan Confirmasi Pembayaran</div>">
														</span>														
										</label>
                    </div>
                    <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search" name="q" id="srch-term">
                    <div class="input-group-btn">
                    <button class="btn btn-default" type="submit" id="df"><i class="glyphicon glyphicon-search"></i></button>
                    </div>
                    </div>
                    </form>
                    </div>
       
        <div class="row">&nbsp</div>
            <table class="table" >
            <thead>
            <tr>
            <th>No</th>
            <th>No Order/Email</th>
            <th>Order Status</th>
            <th>Bulanan</th>
            <!--<th>Sub Total</th>-->
            <th>Discount</th>
            <th>Total</th>
            <th>Created</th>
            <th>Image</th>
            <th>Action</th>
            </tr>
            </thead>
            <tbody>
              
                 <?php $no = $listorder->firstItem()  ; ?>
                 <?php $dt = count($listorder);?>
                 @if($dt > 0)
            @foreach ($listorder as $orderlist)
            <tr>
                <td>{{$no ++}}</td>
                <td>{{$orderlist->no_order}}/{{$orderlist->username}}</td>
                <td 
                  <?php if ($orderlist->order_status == "Confirmed"): ?>

                  style="font-weight: bold;color:#2b9984;"

                  <?php elseif($orderlist->order_status == "Not Confirmed") : ?>

                  style="font-weight: bold;color:#ada4a4;"

                  <?php elseif($orderlist->order_status == "Pending") : ?>
                  style="font-weight: bold;color:#f90625;"
                  <?php endif ?>

                >{{$orderlist->order_status}} 
                </td>
                <td>{{number_format($orderlist->base_price, 0,'.','.')}} </td>
                <!--<td>{{number_format($orderlist->sub_price, 0,'.','.')}} </td>-->
                <td>{{$orderlist->discount}} </td>
                <td>{{number_format($orderlist->total, 0,'.','.')}} </td>
                 <td>{{date('M d, Y',strtotime($orderlist->created_at))}} <strong>{{date('H:i',strtotime($orderlist->created_at))}}</strong></td>

                 @if ($orderlist->image == '')
                 <td> - </td>

                 @else
                 <td><a href="" class="popup-newWindow"><img src="{{$orderlist->image}}" width="75" height="75"> </a></td>
                @endif
                <td>
                  @if($orderlist->order_status !== 'Confirmed' && $orderlist->order_status !== 'cron dari affiliate')
                  <a href="{{action('Admin\OrderController@deleteorder',['id'=>$orderlist->id])}}" data-toggle="modal" id="button-add" class="btn btn-danger ls-modal2" title="Delete">  <span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a> 
                 
                  <a href="{{action('Admin\OrderController@confirorders',['id'=>$orderlist->id])}}" data-toggle="modal" id="button-add" class="btn btn-home ls-modal3" title="Confirmed">
                    <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                  </a> 
                  @endif
                </td>
               
            </tr>
            @endforeach
            @else
            Data tidak ditemukan
            @endif
            </tbody>
            </table>
              {!! $listorder->render() !!}
   


                </div>

                <div class="panel-body">
                    <div id="totalpost"></div>
                </div>
            </div>
        </div>
    </div>
</div>

  <script language="javascript">
    //JS script
$('.ls-modal').on('click', function(e){
  e.preventDefault();
  $('#myModal').modal('show').find('.modal-body').load($(this).attr('href'));
});

$('.ls-modal2').on('click', function(e){
  e.preventDefault();
  $('#myModal2').modal('show').find('.modal-body').load($(this).attr('href'));
});

$('.ls-modal3').on('click', function(e){
  e.preventDefault();
  $('#myModal3').modal('show').find('.modal-body').load($(this).attr('href'));
});
      $( "body" ).on( "click", ".popup-newWindow", function() {
        event.preventDefault();
        window.open($(this).find("img").attr("src"), "popupWindow", "width=600,height=600,scrollbars=yes");
      });
    </script>


{{-- Add Modal --}}
<div class="modal fade" id="myModal" >
          <div class="modal-dialog">
              <div class="modal-content">
                  <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title" id="modalMdTitle"> User Log</h4>
                  </div>
                  <div class="modal-body">
                      
                  </div>
              </div>
          </div>
        </div>
    <!--
       <div class="modal fade" id="modalMd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title" id="modalMdTitle"></h4>
                  </div>
                  <div class="modal-body">
                      <div class="modalError"></div>
                      <div id="modalMdContent"></div>
                  </div>
              </div>
          </div>
        </div>
    -->
    
        <!-- Edit modal -->
        <div class="modal fade" id="myModal2" >
          <div class="modal-dialog" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title" id="modalMdTitle"></h4>
                  </div>
                  <div class="modal-body">
                     
                  </div>
              </div>
          </div>
        </div>


        <!-- Edit modal max account -->
        <div class="modal fade" id="myModal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title" id="modalMdTitle"></h4>
                  </div>
                  <div class="modal-body">
                      <div class="modalError"></div>
                      <div id="modalMdContent"></div>
                  </div>
              </div>
          </div>
        </div>         
      

        <div class="modal fade" id="add">
            <div class="modal-dialog">
                    <div class="modal-content">
                            <form id="addaccount" role="form" action="">
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
                                            <button type="submit" class="btn btn-home" id="button-process">Add</button>
                                    </div>
                            </form>
                    </div>
            </div>
        </div>
@endsection
@section('javascript')
<script>



   // $(document).on('click', '.edit-modal', function() {
    $('.edit-modal').on('click', function(e){
        e.preventDefault();
       // $('#id-edit').val($(this).data('id'));
        //$('#name-edit').val($(this).data('name'));
        //$('#username-edit').val($(this).data('username'));
        $('#edittime').modal('show');
    });

    $("#edit").click(function() {
        $.ajax({
            type: 'post',
            url: '/update',
            data: {
                '_token': $('input[name=_token]').val(),
                'id' : $('input[name=id]').val(),
                'name': $('input[name=name-edit]').val(),
                'username': $('input[name=username-edit]').val()
            },
            success: function(data) {
                $('.item' + data.id).replaceWith("<tr class='item" + data.id + "'><td>" + data.id + "</td><td>" + data.nama + "</td><td>" + data.phone + "</td><td><button class='edit-modal btn btn-info btn-sm' data-id='" + data.id + "' data-nama='" + data.nama + "' data-phone='" + data.phone + "'>Edit</button> <button class='delete-modal btn btn-danger btn-sm' data-id='" + data.id + "' data-name='" + data.name + "'>Delete</button></td></tr>");
                toastr.success("Data Berhasil Diubah.");
            },
        });
    });
</script>

@endsection
<?php
function secondsToday($seconds) {
  $dtF = new DateTime("@0");
  $dtT = new DateTime("@$seconds");
  return $dtF->diff($dtT)->format('%a');
}

function secondsTohour($seconds) {
  $dtF = new DateTime("@0");
  $dtT = new DateTime("@$seconds");
  return $dtF->diff($dtT)->format('%h');
}

function secondsTominets($seconds) {
  $dtF = new DateTime("@0");
  $dtT = new DateTime("@$seconds");
  return $dtF->diff($dtT)->format('%i');
}
?>