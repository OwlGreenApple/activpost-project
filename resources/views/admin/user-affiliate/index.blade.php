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
                    <div class="col-sm-5 col-md-3"> 
                    <!--<a href="{{ url('visat') }}" data-toggle="modal" id="button-add" class="btn btn-info ls-modal4" style="margin-top: 25px;" >Add User</a>-->
                    <span style="position:relative; top:14px; margin-left: 10px;">
                      Jumlah user = <?php echo $jmluser ?>      
                    </span>
                    </div>
                    <div class="col-sm-5 col-md-3 pull-right">
                    <div class="row">&nbsp</div>
                    <form action="{{ url('search-affiliate') }}" method="GET" class="navbar-form" role="search">
                    <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search" name="q" id="srch-term">
                    <div class="input-group-btn">
                    <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
                    </div>
                    </div>
                    </form>
                    </div>
        <div class="col-md-15">
        <div class="row">&nbsp</div>
        
            <table class="table">
            <thead>
            <tr>
            <th>No</th>
            <th>Username</th>
            <th>Name</th>
            <th>Email</th>
            <!--<th>Status</th>
            <th>Time</th>
            <th>Action</th>-->
            
            </tr>
           
            
            </thead>
            <tbody>


    <?php $no = $usern->firstItem()  ; ?>
    <?php $dt = count($usern) ?>
    @if ($dt > 0)
    @foreach ($usern as $usersn)
    <tr>
        <td>{{$no ++}}</td>
        <td>{{$usersn->username}}</td>
        <td>{{$usersn->name}}</td>
        <td>{{$usersn->email}}</td>

        <!--<td>        
     
{{secondsToday($usersn->active_time)}}  D {{secondsTohour($usersn->active_time)}} H {{secondsTominets($usersn->active_time)}} M

        </td>
        <td>
  

        <a href="{{action('Admin\UserController@show',['id'=>$usersn->id])}}" data-toggle="modal" id="button-add" class="btn btn-info ls-modal" >View Log</a>

         <a href="{{action('Admin\UserController@showedit',['id'=>$usersn->id])}}" data-toggle="modal" id="button-add" class="btn btn-warning ls-modal2" >Edit Time</a>

         <a href="{{action('Admin\UserController@delete',['id'=>$usersn->id])}}" id="button-add" class="btn btn-danger" >Clear Data</a> 

         <a href="{{action('Admin\UserController@showmaxaccount',['id'=>$usersn->id])}}" id="button-add" class="btn btn-primary ls-modal3" >Max Account</a> 

         <a href="{{action('Admin\UserController@loginuser',['id'=>$usersn->id])}}" id="button-add" class="btn btn-primary" >Login User</a>      
        </td>


        </td>-->

    </tr>
    @endforeach

    @else
      <div style="font-weight: bold;"> Data Tidak Ditemuka</div>
    @endif
        </tbody>
        </table>
     {!! $usern->render() !!}

        </div>


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

$('.ls-modal4').on('click', function(e){
  e.preventDefault();
  $('#myModal4').modal('show').find('.modal-body').load($(this).attr('href'));
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
        <div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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

        <div class="modal fade" id="myModal4" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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