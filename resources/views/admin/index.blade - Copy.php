@extends('layouts.app')

@section('content')
<div class="container">
 <h1>
  List User
  </h1>
  <div class="row">
    <p class="fl" style="margin-left:15px;">Max User : {{$user->max_account}}</p>
    <a data-toggle="modal" href='#add' id="button-add" class="btn btn-sm btn-info" style="margin-left:15px;margin-top:-5px;">Add</a>
    
    <a id="button-buy-more" class="btn btn-sm btn-home fl" style="margin-left:15px;margin-top:-5px;">Buy More</a>
    <button id="button-action" data-id="{{$user->id}}" class="fr btn btn-sm <?php if (!$user->is_started) { echo 'btn-home'; } else {echo 'btn-danger';} ?> btn-{{$user->id}}" value="<?php if (!$user->is_started) { echo 'Start'; } else {echo 'Stop';}?>" style="margin-top:-5px;margin-right:15px; color:#fff!important;">
      <?php if (!$user->is_started) { echo "<span class='glyphicon glyphicon-play'></span> Start"; } else {echo "<span class='glyphicon glyphicon-stop'></span> Stop";}?> 
    </button>
    
    
    
    <div class="fn"></div>
  </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">

                    <div class="col-sm-5 col-md-3 pull-right">
                    <div class="row">&nbsp</div>
                    <form action="{{ url('search-user') }}" method="GET" class="navbar-form" role="search">
                    <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search" name="q" id="srch-term">
                    <div class="input-group-btn">
                    <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
                    </div>
                    </div>
                    </form>
                    </div>
        <div class="col-md-11">
        <div class="row">&nbsp</div>
        
            <table class="table">
            <thead>
            <tr>
            <th>No</th>
            <th>Username</th>
            <th>Name</th>
            <th>Email</th>
            <th>Time</th>
            <th>Action</th>
            
            </tr>
           
            
            </thead>
            <tbody>


    <?php $no = $usern->firstItem()  ; ?>

    @foreach ($usern as $usersn)
    <tr>
        <td>{{$no ++}}</td>
        <td>{{$usersn->username}}</td>
        <td>{{$usersn->name}}</td>
        <td>{{$usersn->email}}</td>
        <td>        
     
{{secondsToday($usersn->active_time)}}  D {{secondsTohour($usersn->active_time)}} H {{secondsTominets($usersn->active_time)}} M

        </td>
        <td>
  

        <a href="{{action('Admin\HomeController@show',['id'=>$usersn->id])}}" data-toggle="modal" id="button-add" class="btn btn-info ls-modal" >View Log</a>

         <a href="{{action('Admin\HomeController@showedit',['id'=>$usersn->id])}}" data-toggle="modal" id="button-add" class="btn btn-info ls-modal2" >Edit Time</a>

         <a href="{{action('Admin\HomeController@delete',['id'=>$usersn->id])}}" id="button-add" class="btn btn-info" >Clear Data</a>
       
        </td>

    </tr>

    @endforeach
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