@extends('layouts.app')

@section('content')
<?php 
use Illuminate\Support\Facades\Crypt;
use Celebpost\Models\Proxies;
?>
<div class="container">


    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">

                    <div class="col-sm-5 col-md-3 pull-right">
                    <div class="row">&nbsp</div>
                    <form action="{{ url('search-eacount') }}" method="GET" class="navbar-form" role="search">
                    <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search" name="q" id="q">
                    <div class="input-group-btn">
                    <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
                    </div>
                    </div>
                    </form>
                    </div>
        <div class="col-md-10">
        <div class="row">&nbsp</div>
            <table class="table">
            <thead>
            <tr>
            <th>No</th>
            <th>Username</th>
            <th>Proxy</th>
						<th>Is started</th>
            <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = $searchid2->firstItem()  ; ?>
            <?php $dt = count($searchid2) ?>
            @if ($dt > 0)
            @foreach ($searchid2 as $listaccountn)
            <tr>
            <td>{{$no ++}}</td>
             <td><a href="https://www.instagram.com/{{$listaccountn->username2}}" target="_blank">{{$listaccountn->username2}}</a>/{{$listaccountn->username1}}</td>
            <!--<td>
							<?php 
								// Decrypt
              
								/*$decrypted_string = Crypt::decrypt($listaccountn->password1);
								$pieces = explode(" ~space~ ", $decrypted_string);
								$pass = $pieces[0];
								echo $pass;
                */
							?>
						</td>
						-->
            <td>
						<?php 
							$proxy = Proxies::find($listaccountn->proxy_id);
							if (!is_null($proxy)){
								if($proxy->cred==""){
									echo $proxy->proxy.":".$proxy->port;
								}
								else {
									echo $proxy->proxy.":".$proxy->port.":".$proxy->cred;
								}
								if ($proxy->is_error){
									echo "<br> Proxy Error";
								}
							}
						?>
						</td>
            <td>
							<?php if ($listaccountn->is_started) {
								echo "yes";
							}
							else {
								echo "no";
							}?>
						</td>
            <td>
                <a href="{{action('Admin\AccountController@delaccount',['id'=>$listaccountn->user_id])}}" id="button-add" class="btn btn-danger" >Clear Data</a> 
								<a href="#" class="btn btn-info btn-check-login" data-id="{{$listaccountn->id}}">Refresh Auth</a> 
								<a href="#" class="btn btn-info btn-valid-account" data-id="{{$listaccountn->id}}">Valid Account</a> 
            </td>
            </tr>
            @endforeach

            @else
                <div style="font-weight: bold;">Data Tidak Ditemukan</div>
            @endif
            </tbody>
            </table>
           {!! $searchid2->render() !!}
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
		$( "body" ).on( "click", '.btn-check-login', function(e) {
      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type: 'get',
          url: "<?php echo url('check-login-ig'); ?>",
          // data: $("#form-setting").serialize(),
          data: {
						inputId : $(this).attr("data-id"),
					},
          dataType: 'text',
          beforeSend: function()
          {
            $("#div-loading").show();
          },
          success: function(result) {
						// location.reload();
            $("#div-loading").hide();
            alert("akun berhasil direfresh");
          }
      });
		});
		$( "body" ).on( "click", '.btn-valid-account', function(e) {
      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type: 'get',
          url: "<?php echo url('process-valid-account'); ?>",
          // data: $("#form-setting").serialize(),
          data: {
						inputId : $(this).attr("data-id"),
					},
          dataType: 'text',
          beforeSend: function()
          {
          },
          success: function(result) {
						location.reload();
          }
      });
		});
    </script>
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

{{-- Add Modal --}}
        <div class="modal fade" id="add">
            <div class="modal-dialog">
                    <div class="modal-content">
                            <form id="addaccount" role="form">
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