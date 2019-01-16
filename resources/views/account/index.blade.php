@extends('layouts.app')

@section('content')
<?php 
use \InstagramAPI\Instagram;
use Celebpost\Models\Proxies;
?>
<style type="text/css">
  @media only screen and (max-width: 570px) {
    label {
      font-size:11px;
    }
  }
</style>
<script>
	buttonAction ="";
	action_all = "";
		function getTimeRemaining(endtime){
			var t = endtime;
			var seconds = Math.floor( (t) % 60 );
			var minutes = Math.floor( (t/60) % 60 );
			var hours = Math.floor( (t/(60*60)) % 24 );
			var days = Math.floor( t/(60*60*24) );
			return {
				'total': t,
				'days': days,
				'hours': hours,
				'minutes': minutes,
				'seconds': seconds
			};
		}

		function initializeClock(id, endtime){
			var clock = document.getElementById(id);
			var daysSpan = clock.querySelector('.days');
			var hoursSpan = clock.querySelector('.hours');
			var minutesSpan = clock.querySelector('.minutes');
			var secondsSpan = clock.querySelector('.seconds');

			function updateClock(){
				var t = getTimeRemaining(endtime);

				daysSpan.innerHTML = t.days;
				hoursSpan.innerHTML = ('0' + t.hours).slice(-2);
				minutesSpan.innerHTML = ('0' + t.minutes).slice(-2);
				secondsSpan.innerHTML = ('0' + t.seconds).slice(-2);

				// if(t.total<=0){
					// clearInterval(timeinterval);
				// }
			}

			updateClock();
			//var timeinterval = setInterval(updateClock,1000);
		}
	function action_activity_all() {
				$.ajax({
						headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						},
						type: 'POST',
						url: "<?php echo url('account/call-action-all'); ?>",
						// data: $("#form-setting").serialize(),
						data: { 
							status: action_all, 
						},
						dataType: 'text',
						beforeSend: function()
						{
							$("#div-loading").show();
						},
						success: function(result) {
								$("#div-loading").hide();
								var data = jQuery.parseJSON(result);
								if(data.type=='success')
								{
									if (action_all == "Stop") { //last position stopped
										$(".button-action").val("Start");
										$(".button-action").html("<span class='glyphicon glyphicon-play'></span> Start");
										$(".button-action").addClass("btn-success");
										$(".button-action").removeClass("btn-danger");
										
										$(".stopped-div").show();
										$(".started-div").hide();
										// $("#status-activity").html('Status : <span class="glyphicon glyphicon-stop"></span> <span style="color:#c12e2a; font-weight:Bold;">Stopped</span>');
									} else if (action_all == "Start") { //last position started
										$(".button-action").val("Stop");
										$(".button-action").html("<span class='glyphicon glyphicon-stop'></span> Stop");
										$(".button-action").addClass("btn-danger");
										$(".button-action").removeClass("btn-success");
										
										$(".stopped-div").hide();
										$(".started-div").show();
										// $("#status-activity").html('Status : <span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> <span style="color:#5cb85c; font-weight:Bold;">Started</span>');
									}
								}
								else if(data.type=='error')
								{
								}								
						}
				});
	}

	function action_activity() {
				$.ajax({
						headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						},
						type: 'POST',
						url: "<?php echo url('account/call-action'); ?>",
						// data: $("#form-setting").serialize(),
						data: { 
							status: buttonAction.val(), 
							id: $("#account-id").val(), 
						},
						dataType: 'text',
						beforeSend: function()
						{
							$("#div-loading").show();
						},
						success: function(result) {
								$("#div-loading").hide();
								var data = jQuery.parseJSON(result);
								if(data.type=='success')
								{
									if (buttonAction.val()== "Stop") { //last position stopped
										buttonAction.val("Start");
										buttonAction.html("<span class='glyphicon glyphicon-play'></span> Start");
										buttonAction.addClass("btn-success");
										buttonAction.removeClass("btn-danger");
										$(".stopped-div-"+$("#account-id").val()).show();
										$(".started-div-"+$("#account-id").val()).hide();
										// $("#status-activity").html('Status : <span class="glyphicon glyphicon-stop"></span> <span style="color:#c12e2a; font-weight:Bold;">Stopped</span>');
									} else if (buttonAction.val()== "Start") { //last position started
										buttonAction.val("Stop");
										buttonAction.html("<span class='glyphicon glyphicon-stop'></span> Stop");
										buttonAction.addClass("btn-danger");
										buttonAction.removeClass("btn-success");
										$(".stopped-div-"+$("#account-id").val()).hide();
										$(".started-div-"+$("#account-id").val()).show();
										// $("#status-activity").html('Status : <span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> <span style="color:#5cb85c; font-weight:Bold;">Started</span>');
									}
								}
								else if(data.type=='error')
								{
								}								
						}
				});
		
	}
	$(document).ready(function() {
		$("#alert-main").hide();
		initializeClock('clockdiv', <?php echo $user->active_time ?>);
		
		mode_action = "";
		$('#button-stop').click(function(e){
			if (mode_action=="1") {
				action_activity();
			} 
			else if (mode_action=="all") {
				action_activity_all();
			}
		});
		$('#button-start-all').click(function(e){
			action_all = "Start";
			action_activity_all();
		});
		$('#button-stop-all').click(function(e){
			mode_action = "all";
			action_all = "Stop";
			$('#confirm-stop').modal('toggle');
		});
		// $('.button-action').click(function(e){
		$("body").on('click', '.button-action',function(e) {
			mode_action = "1";
			buttonAction = $(this);
			$("#account-id").val($(this).attr("data-id"));
			if ($(this).val()== "Stop") { // last position stopped
				$('#confirm-stop').modal('toggle');
			} else if ($(this).val()== "Start") { //last position started
				action_activity();
			}
		});
    
    
		$('#button-edit-password').click(function(){
				$.ajax({
						headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						},
						type: 'POST',
						url: "<?php echo url('account/edit-password'); ?>",
						// data: $("#form-setting").serialize(),
						data: { 
							id: $("#hidden-id").val(), 
							password: $("#edit-insta-password").val(), 
						},
						dataType: 'text',
						beforeSend: function()
						{
							$("#div-loading").show();
						},
						success: function(result) {
								$("#div-loading").hide();
								var data = jQuery.parseJSON(result);
								if(data.status=='success')
								{
                  alert("Login success");
                  window.location.href = "<?php echo url(''); ?>";
								}
								if(data.status=='error')
								{
                  alert(data.msg);
								}
						}
				});

		});
    
		$("body").on('click', '.link-edit-password',function(e) {
			e.preventDefault();
			$("#hidden-id").val($(this).attr("data-id"));
			$('#edit-password').modal('toggle');
		});


	
		$("body").on('click', '.radio-account-postBerurutan',function(e) {
				$.ajax({
						headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						},
						type: 'POST',
						url: "<?php echo url('account/post-berurutan'); ?>",
						// data: $("#form-setting").serialize(),
						data: { 
							id: $(this).attr("data-id"), 
							isPB: $(this).attr("data-isPB"), 
						},
						dataType: 'text',
						beforeSend: function()
						{
							$("#div-loading").show();
						},
						success: function(result) {
								$("#div-loading").hide();
								var data = jQuery.parseJSON(result);
								if(data.status=='success')
								{
                  alert("Saved");
								}
								if(data.status=='error')
								{
                  alert("Invalid Error");
								}
						}
				});
		});
    
		$('#link-activation').click(function(e){
			e.preventDefault();
			$.ajax({
					type: 'GET',
					url: "<?php echo url('resend-email-activation'); ?>",
					data: {},
					dataType: 'text',
					beforeSend: function()
					{
						$("#div-loading").show();
					},
					success: function(result) {
							// $('#result').html(data);
							$("#div-loading").hide();
							var data = jQuery.parseJSON(result);
							$("#alert-main").show();
							$("#alert-main").html(data.message);
							if(data.type=='success')
							{
								$("#alert-main").addClass('alert-success');
								$("#alert-main").removeClass('alert-danger');
							}
							else if(data.type=='error')
							{
								$("#alert-main").addClass('alert-danger');
								$("#alert-main").removeClass('alert-success');
							}
					}
			})
		});
		
		
    
	});
</script>

<div class="container">

  <!-- Modal confirm stop-->
	<div class="modal fade" id="confirm-stop" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
					<div class="modal-content">
							<div class="modal-header">
									Stop Activity
							</div>
							<div class="modal-body">
									Schedule will not be working, are you sure want to stop ?
							</div>
							<input type="hidden" id="id-image">
							<div class="modal-footer">
									<button type="button" data-dismiss="modal" class="btn btn-info" id="button-stop">Yes</button>
									<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
							</div>
					</div>
			</div>
	</div>	

  <!-- Modal edit password-->
	<div class="modal fade" id="edit-password" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
					<div class="modal-content">
							<div class="modal-header">
									Edit Password
							</div>
							<div class="modal-body">
								<div class="form-group">
									<label for="" class="control-label">Silahkan login dahulu ke Instagram.com via browser, & jangan di logout sebelum berhasil add account <br>
									STOP Activfans dahulu(Jika Activfans anda dalam posisi belum distop), & dapat di START lagi setelah berhasil Edit Password</label>
								</div>
                <div class="form-group">
                    <label for="insta_password">Password</label>
                    <input type="password" class="form-control" name="" required="required" id="edit-insta-password">
                </div>
                <input type="hidden" id="hidden-id">
							</div>
							<div class="modal-footer">
									<button type="button" data-dismiss="modal" class="btn btn-info" id="button-edit-password">Edit</button>
									<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
							</div>
					</div>
			</div>
	</div>	


	<h1 <?php if(env('APP_PROJECT')=='Amelia') echo 'style="color:#000000"';?>>
	List Account 
	</h1>
	<div class="row">
		<p class="fl" style="margin-left:15px; <?php if(env('APP_PROJECT')=='Amelia') echo 'color:#3a3a3a' ?>">
      Max Account : {{$user->max_account}}
    </p>
		
		<div class="fn"></div>
	</div>
	<div class="row margin-bottom">
		<input type="hidden" id="account-id">
		<!--
		<button id="button-start-all" data-id="{{$user->id}}" class="fl btn btn-md btn-success btn-{{$user->id}}" value="Start" style="margin-top:0px;margin-left:15px;margin-bottom:10px;color:#fff!important;">
			<span class='glyphicon glyphicon-play'></span> Start All
		</button>
		<button id="button-stop-all" data-id="{{$user->id}}" class="fl btn btn-md btn-danger btn-{{$user->id}}" value="Stop" style="margin-top:0px;margin-left:15px;margin-bottom:10px;color:#fff!important;">
			<span class='glyphicon glyphicon-stop'></span> Stop All
		</button>
		-->
		<a data-toggle="modal" href='#add' id="button-add" class="btn btn-home fl" style="margin-left:15px;margin-top:0px;"><span class='glyphicon glyphicon-plus'></span> Add IG Account</a>
		
<!--		<a id="button-buy-more" class="btn btn-sm btn-home-light fl" style="margin-left:15px;margin-top:-5px;" href="{{url('/order')}}">Buy More</a>
		-->
		<!--<div class="col-md-5 col-xs-9 col-sm-9">
			<p id="status-activity" class="" style="margin-left:15px;">Status : <?php 
				if (!$user->is_started) {
					echo '<span class="glyphicon glyphicon-stop"></span> <span style="color:#c12e2a; font-weight:Bold;">Stopped</span>';
				} else {
					echo '<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> <span style="color:#5cb85c; font-weight:Bold;">Started</span>';
				}
			?></p>
			<p style="font-size:10px;margin-left:15px;margin-top: -14px;font-weight:Bold;">*Schedules tidak akan dijalankan jika status Stopped</p>
		</div>
		-->
		<div class="fn"></div>
	</div>
	<!--
	<div class="row"> 
		<div class="col-md-12 col-sm-12 col-xs-12">
			<input type="radio" style="display:inline-block;" 
			class="fl radio-account-postBerurutan" 
			id="radio-account-postBerurutanTrue" name="radio-account-postBerurutan" 
			data-isPB="1"
			<?php 
			if ($user->is_post_berurutan) { echo "checked"; }
			?>
			> 
			<label style="font-size:11px;display:inline-block;margin-top: 3px;margin-left: 5px;" class="fl" for="radio-account-postBerurutanTrue">Post Harus Berurutan
				<span class="glyphicon glyphicon-question-sign hint-button tooltipPlugin" title="<div class='panel-heading'>Post Harus Berurutan</div><div class='panel-content'>Mencegah dan tidak melakukan posting(untuk schedule berikutnya) jika ada schedule yang gagal terposting. <br>Anda akan kami kirim email notifikasi, supaya dapat memperbaiki  & reschedule kembali </div>">
				</span>
			</label>
			<div class="fn"></div>
		</div>
	</div>
	<div class="row"> 
		<div class="col-md-12 col-sm-12 col-xs-12">
			<input type="radio" style="display:inline-block;" 
			class="fl radio-account-postBerurutan" 
			id="radio-account-postBerurutanFalse" name="radio-account-postBerurutan"
			data-isPB="0"
			<?php 
			if (!$user->is_post_berurutan) { echo "checked"; }
			?>
			> 
			<label style="font-size:11px;display:inline-block;margin-top: 3px;margin-left: 5px;" class="fl" for="radio-account-postBerurutanFalse">Post Tidak Harus Berurutan
				<span class="glyphicon glyphicon-question-sign hint-button tooltipPlugin" title="<div class='panel-heading'>Post Tidak Harus Berurutan</div><div class='panel-content'>Post schedule berikutnya tetap akan diposting jika ada schedule yang gagal terposting. <br>Anda tetap akan kami kirim email notifikasi, supaya dapat memperbaiki & reschedule kembali.</div>">
				</span>
			</label>
			<div class="fn"></div>
		</div>
	</div>
		-->

	
	<div class="row">
		<div class="col-sm-12 col-md-12">
				<h3 <?php if(env('APP_PROJECT')=='Amelia') echo 'style="color:#3a3a3a"' ?>>Total Waktu Berlangganan &nbsp <span class="glyphicon glyphicon-question-sign hint-button tooltipPlugin" title="<div class='panel-heading'>Fitur Start Stop waktu</div><div class='panel-content'><?php if(env('APP_PROJECT')=='Amelia') {echo 'Perhitungan Waktu Berlangganan<br>Waktu berlangganan akan berjalan otomatis saat pertama kali login di Amelia Post.'; } else {echo 'Waktu berlangganan akan terbagi sesuai dengan akun IG yang Start saja <br>contoh: apabila anda mengaktifkan 3 akun sekaligus, maka waktu akan terbagi 3 <br>Jika salah satu akun di Stop maka waktu tersisa akan dibagi rata dengan akun lain <br>';} ?></div>">

				</span></h3>
				<div id="clockdiv" class="fl">
					<div class="fl">
						<span class="days"></span>
						<div class="smalltext">Days</div>
					</div>
					<div class="fl">
						<span class="hours"></span>
						<div class="smalltext">Hours</div>
					</div>
					<div class="fl">
						<span class="minutes"></span>
						<div class="smalltext">Minutes</div>
					</div>
					<div class="fl">
						<span class="seconds"></span>
						<div class="smalltext">Seconds</div>
					</div>
					<i class="fn">
					</i>
				</div>
				<div class="fn">
				</div>
		</div>
	</div>

	<div class="row">
  <?php if (!$user->is_confirmed) { ?> 
    <div class="col-sm-12 col-md-12">            
      <div class="alert alert-danger col-sm-18 col-md-18">
        Silahkan konfirmasi email terlebih dahulu. Klik <a href="" id="link-activation">disini</a> untuk kirim email konfirmasi ulang.
      </div>  
    </div>          
  <?php } ?>
    <div class="col-sm-12 col-md-12">            
      <div class="alert alert-danger col-sm-18 col-md-18" id="alert-main">
      </div>  
    </div>          
	
	</div>          
	
	
	
	<div class="row" style="margin-bottom:30px;">
		@foreach ($accounts as $account)
		<div class="col-md-6 col-xs-12 col-sm-12" style="border:1px solid #fff;padding:5px;">
			<div class="col-md-6 col-xs-6 col-sm-6 margin-bottom">
				<div class="row"> 
				&nbsp
				</div>
				<div class="row"> 
					<?php 
            
            //new supaya, ga ada yang proxy_id 0 untuk akun amelia
            if ($account->proxy_id == 0){
              $cookiefile = base_path('storage/ig-cookies/'.$account->username.'/').'cookies-celebpost-temp.txt';
              if ($user->is_member_rico==0) {
                $url = "https://activfans.com/dashboard/get-proxy-id/".$account->username;
              }
              else{
                $url = "https://activfans.com/amelia/get-proxy-id/".$account->username;
              }
              $c = curl_init();

              curl_setopt($c, CURLOPT_URL, $url);
              curl_setopt($c, CURLOPT_REFERER, $url);
              curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
              curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
              curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
              curl_setopt($c, CURLOPT_COOKIEFILE, $cookiefile);
              curl_setopt($c, CURLOPT_COOKIEJAR, $cookiefile);
              $page = curl_exec($c);
              curl_close($c);

              $arr_res = json_decode($page,true);  

              $proxy_id = $arr_res["proxy_id"]; 
              $is_on_celebgramme = $arr_res["is_on_celebgramme"]; 
              $account->proxy_id = $proxy_id;
              $account->is_on_celebgramme = $is_on_celebgramme;
              $account->save();
            }
            
						//get proxy
						$proxy = Proxies::find($account->proxy_id);
					 
            $password='';
            if(App::environment()!="local"){
						  // Decrypt
  						$decrypted_string = Crypt::decrypt($account->password);
  						$pieces = explode(" ~space~ ", $decrypted_string);
  					 	$password = $pieces[0];
            }
						$is_error = false;
						// $ppurl = "";
						$ppurl = 'images/profile-default.png';
						$taken_at = "-";
						if(App::environment()<>"local"){
							try {
								$i = new Instagram(false,false,[
									"storage"       => "mysql",
									"dbhost"       => Config::get('database.connections.mysql_celebgramme.host'),
									"dbname"   => Config::get('database.connections.mysql_celebgramme.database'),
									"dbusername"   => Config::get('database.connections.mysql_celebgramme.username'),
									"dbpassword"   => Config::get('database.connections.mysql_celebgramme.password'),
								]);
								
								if ($account->proxy_id <> 0){
									// Check Login
									if (!is_null($proxy)) {
										if($proxy->cred==""){
											$i->setProxy("http://".$proxy->proxy.":".$proxy->port);
										}
										else {
											$i->setProxy("http://".$proxy->cred."@".$proxy->proxy.":".$proxy->port);
										}
									}
									$i->login($account->username, $password);
									
									//DIROMBAK
									$self_info = $i->account->getCurrentUser();
									$self_user_feed = $i->timeline->getSelfUserFeed();
									$ppurl = $self_info->getUser()->getProfilePicUrl();
									// $ppurl = str_replace("http", "https", $ppurl);
									if (count($self_user_feed->getItems()) > 0 ) {
										$temp = $self_user_feed->getItems()[0]->getTakenAt();
										$taken_at = date("Y-m-d H:i:s", $temp);
									} else {
										$taken_at = "-";
									}
								}
							} 
							catch (\InstagramAPI\Exception\IncorrectPasswordException $e) {
								$is_error = true;
								echo 'Silahkan Edit password anda <a href="#" class="link-edit-password" data-id="'.$account->id.'">disini</a>';
							}
							catch (\InstagramAPI\Exception\CheckpointRequiredException $e) {
								$is_error = true;
								echo 'Silahkan Konfirmasi Lewat Email / SMS Account IG anda';
							}
							catch (\InstagramAPI\Exception\FeedbackRequiredException $e) {
								$is_error = true;
								echo 'Silahkan Konfirmasi Lewat Email / SMS Account IG anda';
							}
							catch (\InstagramAPI\Exception\LoginRequiredException $e) {
								$is_error = true;
								echo 'Silahkan Edit password anda <a href="#" class="link-edit-password" data-id="'.$account->id.'">disini</a>';
							}
							catch (\InstagramAPI\Exception\InstagramException $e) {
								$is_error = true;
								echo $e->getMessage();
							}	
							catch (\InstagramAPI\Exception\BadRequestException $e) {
								$is_error = true;
								echo $e->getMessage();
							}
							catch (\InstagramAPI\Exception\ThrottledException $e) {
								$is_error = true;
								echo $e->getMessage();
							}
							catch (Exception $e) {
								$is_error = true;
								// echo $e->getMessage();
								$error_type = str_replace(" ","",$e->getMessage());
								$error_type = trim(preg_replace('/\s+/', ' ', $error_type));
								if ( ($error_type=='login_required') || ($error_type=="Notloggedin") ) {
									echo 'Silahkan Edit password anda <a href="#" class="link-edit-password" data-id="'.$account->id.'">disini</a>';
								} else if ($error_type=="checkpoint_required") {
									echo 'Silahkan Konfirmasi Lewat Email / SMS Account IG anda';
								} 
								else {
									echo $e->getMessage();
								}
							}
						}
						
						if ($is_error) {
						}
						
						// echo $ppurl;
						if (!$is_error) {
					?>
						<img src="{{$ppurl}}" class="circle-image">
					<?php } 
					if(App::environment()=="local"){
					?>
					<img src="{{url('images/fav-ico-del.png')}}" class="circle-image">
					<?php } ?>
					
				</div>
				<!--
				<div class="row" >
				
					<button data-id="{{$account->id}}" class="button-action btn btn-md <?php if (!$account->is_started) { echo 'btn-success'; } else {echo 'btn-danger';} ?> btn-{{$account->id}}" value="<?php if (!$account->is_started) { echo 'Start'; } else {echo 'Stop';}?>" style="display:block;margin-top: 30px; margin-left:auto;margin-right:auto;color:#fff!important;">
					<?php if (!$account->is_started) { echo "<span class='glyphicon glyphicon-play'></span> Start "; } else {echo "<span class='glyphicon glyphicon-stop'></span> Stop";}?> 
					</button>
					
				</div>
				-->
			</div>
			<div class="col-md-6 col-xs-6 col-sm-6 margin-bottom">

				{{-- Delete --}}
				<div class="row"> 
					<div class="col-md-10 col-sm-10 col-xs-10"></div>
					<div class="col-md-2 col-sm-2 col-xs-2">
						<a data-toggle="modal" href='#delete-{{$account->id}}' class="">
						<span data-id="" class="delete-button glyphicon glyphicon-remove" style="cursor:pointer;" aria-hidden="true" data-toggle="modal" data-target="#confirm-delete" ></span> 
						</a>
					</div> 
				</div>
				
					<h5 style="font-weight:bold;">{{ $account->username }} </h5>
					<!--
					<p style="margin-bottom:0px;font-weight:bold;">Status : </p>
					<div class="started-div started-div-{{$account->id}}" <?php if (!$account->is_started) { echo 'style="display:none;"'; } ?>>
						<span style="color:#009688;"><img src="{{url('images/startIcon.png')}}" class="img-responsive" style="animation: spin 2s infinite linear;display:inline-block;"> Started</span>
					</div>
					<div class="stopped-div stopped-div-{{$account->id}}" <?php if ($account->is_started) { echo 'style="display:none;"'; } ?>>
						<span style="color:#E91E63;"><img src="{{url('images/stopIcon.png')}}" class="img-responsive" style="display:inline-block;"> Stopped</span>
					</div>
					-->
					<p>
					Total Post : 
					<?php 
					try {
						// echo number_format($account->schedules->count());
						echo number_format($account->success->count());
					}
					catch (Exception $e) {
						echo $e->getMessage();
					}
					?>
					<br>
					Last Post Date: <br>{{$taken_at}}<br>
					Scheduled Post : {{ $account->proccess->count() }} <br>
					<a href="https://www.instagram.com/{{ $account->username }}" target="_blank" class="">My Instagram Post</a>
					</p>
					
					<div class="row"> 
						<div class="col-md-12 col-sm-12 col-xs-12">
							<input type="radio" style="display:inline-block;" 
							class="fl radio-account-postBerurutan" 
							id="radio-account-postBerurutanTrue-{{$account->id}}" name="radio-account-postBerurutan-{{$account->id}}" 
							data-id="{{$account->id}}" data-isPB="1"
							<?php 
							if ($account->is_post_berurutan) { echo "checked"; }
							?>
							> 
							<label style="font-size:11px;display:inline-block;margin-top: 3px;margin-left: 5px;" class="fl" for="radio-account-postBerurutanTrue-{{$account->id}}">Post Harus Berurutan
								<span class="glyphicon glyphicon-question-sign hint-button tooltipPlugin" title="<div class='panel-heading'>Post Harus Berurutan</div><div class='panel-content'>Mencegah dan tidak melakukan posting(untuk schedule berikutnya) jika ada schedule yang gagal terposting. <br>Anda akan kami kirim email notifikasi, supaya dapat memperbaiki  & reschedule kembali </div>">
								</span>
							</label>
							<div class="fn"></div>
						</div>
					</div>
					<div class="row"> 
						<div class="col-md-12 col-sm-12 col-xs-12">
							<input type="radio" style="display:inline-block;" 
							class="fl radio-account-postBerurutan" 
							id="radio-account-postBerurutanFalse-{{$account->id}}" name="radio-account-postBerurutan-{{$account->id}}"
							data-id="{{$account->id}}" data-isPB="0"
							<?php 
							if (!$account->is_post_berurutan) { echo "checked"; }
							?>
							> 
							<label style="font-size:11px;display:inline-block;margin-top: 3px;margin-left: 5px;" class="fl" for="radio-account-postBerurutanFalse-{{$account->id}}">Post Tidak Harus Berurutan
								<span class="glyphicon glyphicon-question-sign hint-button tooltipPlugin" title="<div class='panel-heading'>Post Tidak Harus Berurutan</div><div class='panel-content'>Post schedule berikutnya tetap akan diposting jika ada schedule yang gagal terposting. <br>Anda tetap akan kami kirim email notifikasi, supaya dapat memperbaiki & reschedule kembali.</div>">
								</span>
							</label>
							<div class="fn"></div>
						</div>
					</div>
					
							<div class="modal fade" id="delete-{{$account->id}}">
									<div class="modal-dialog">
											<div class="modal-content">
													<div class="modal-header">
															<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
															<h4 class="modal-title">Warning!</h4>
													</div>
													<div class="modal-body">
															Delete <strong>{{ $account->username }}</strong> Account?<br>
															Silahkan Stop akun Activfans terlebih dulu apabila anda akan melakukan <br>
															delete & add akun ulang(refresh Session IG akun).<br>
															*Abaikan pesan diatas apabila akun ini tidak sedang aktif memakai Activfans
													</div>
													<div class="modal-footer">
															<button type="button" class="btn btn-default" data-dismiss="modal">No</button>
															<a href="{{ url('/account/delete/'.$account->id) }}" class="btn btn-primary">Yes</a>
													</div>
											</div>
									</div>
							</div>
					
			</div>
		</div>
		@endforeach
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
											<div class="alert alert-danger" id="alert">
												<strong>Oh snap!</strong> Change a few things up and try submitting again.
											</div>  
					
											<div class="row" id="terms">
												<div class="col-sm-12 col-md-12">
													<label for="terms-add-account1" class="control-label">
                            <input type="checkbox" class="checkbox-term" id="terms-add-account1">
                            UMUR akun Instagram minimal 10 hari
                          </label>
												</div>
												<div class="col-sm-12 col-md-12">
													<label for="terms-add-account4" class="control-label">
                            <input type="checkbox" class="checkbox-term" id="terms-add-account4">
                            Email & No HP sudah terhubung dengan Account Instagram ini
                          </label>
												</div>
												<div class="col-sm-12 col-md-12">
													<label for="terms-add-account5" class="control-label">
                            <input type="checkbox" class="checkbox-term" id="terms-add-account5">
                            PUNYA AKSES ke Email & No HP tersebut
                          </label>
												</div>
												<div class="col-sm-12 col-md-12">
													<label for="terms-add-account6" class="control-label">
                            <input type="checkbox" class="checkbox-term" id="terms-add-account6">
                            Akun Instagram memiliki 10 Post Photo / Video
                          </label>
												</div>
												<div class="col-sm-12 col-md-12">
													<label for="terms-add-account7" class="control-label">
                            <input type="checkbox" class="checkbox-term" id="terms-add-account7">
                            Turn OFF 2 Factor Authentications ( Khusus followers >1000 ) 
                          </label>
												</div>
												<div class="col-sm-12 col-md-12">
													<label for="terms-add-account2" class="control-label">
                            <input type="checkbox" class="checkbox-term" id="terms-add-account2">
                            Max Schedule Post = 3 post/jam atau 72 post/hari.
                          </label>
												</div>
												<div class="col-sm-12 col-md-12">
													<label for="terms-add-account8" class="control-label">
                            <input type="checkbox" class="checkbox-term" id="terms-add-account8">
                            Saya sudah membaca dan mempelajari  <a href="https://docs.google.com/document/d/1CA7hxRL-3DTQiR8CoEX7yw58mx4LNRmfLKahaHtKFic/edit" target="_blank">Tutorial Activpost </a>
                          </label>
												</div>
												<div class="col-sm-12 col-md-12">
													<label for="terms-add-account9" class="control-label">
                            <input type="checkbox" class="checkbox-term" id="terms-add-account9">
                            Saya sudah mempelajari  
                            <?php if(env('APP_PROJECT')=='Celebgramme') { ?>
                              <a href="https://youtu.be/muraXXVnq5Y" target="_blank"> Video Tutorial Activpost 1</a>
                              <a href="https://youtu.be/F3WzEJYnrHk" target="_blank"> Video Tutorial Activpost 2</a>
                              <a href="https://youtu.be/DBse29qDnKg" target="_blank"> Video Tutorial Activpost 3</a>
                            <?php } if(env('APP_PROJECT')=='Amelia') { ?>
                              <a href="https://youtu.be/-0yI4BvsTZo" target="_blank"> Video Tutorial Activpost 1</a>
                              <a href="https://youtu.be/jF8LSy7mU7g" target="_blank"> Video Tutorial Activpost 2</a>
                            <?php } ?>
                          </label>
												</div>
												<div class="col-sm-12 col-md-12">
													<label for="terms-add-account10" class="control-label">
                            <input type="checkbox" class="checkbox-term" id="terms-add-account10">
                            Saya sudah membaca & menyetujui <a href="https://activpost.net/terms-conditions/">TERMS & CONDITIONS</a> Activpost 
                          </label>
												</div>
												<div class="col-sm-12 col-md-12">
													<label for="terms-add-account12" class="control-label">
                            <input type="checkbox" class="checkbox-term" id="terms-add-account12"> 
                            STOP Activfans dahulu(Jika Activfans anda dalam posisi belum distop), & dapat di START lagi setelah berhasil add account 
                          </label>
												</div>
												<div class="col-sm-12 col-md-12">
													<label for="terms-add-account11" class="control-label">
                            <input type="checkbox" class="checkbox-term" id="terms-add-account11"> 
                            Silahkan login dahulu ke Instagram.com via browser, & jangan di logout sebelum &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;berhasil add account 
                          </label>
													<p style="font-size:11px; margin-left:20px;"> PS : <br>
													1. Stop Activpost terlebih dahulu apabila akan melakukan activity Instagram secara manual <br> (Follow / Like / Comment / Unfollow / Post) atau bisa melakukan activity diluar jam schedule Activpost <br>
													2. Jika anda sedang aktif menggunakan Activfans, sebaiknya jangan menggunakan fitur post now pada Activpost. Beri waktu setidaknya 15 menit kemudian dari waktu post now.</p>
													
												</div>
											</div>


											<div class="form-group">
													<label for="insta_username">Username Instagram</label>
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
