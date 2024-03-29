@extends('layouts.app')

@section('content')

<?php 
	use Celebpost\Models\Schedule;
	if ($sid<>0) {
		$schedule = Schedule::find($sid);
	}

?>
<script>
	function load_image(imgData){
		$.ajax({
				headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				type: 'POST',
				url: "<?php echo url('save-image-schedule'); ?>",
				data: { 
					imgData: imgData, 
				},
				dataType: 'text',
				beforeSend: function()
				{
					$("#div-loading").show();
				},
				success: function(result) {
						$("#div-loading").hide();
						var dataR = jQuery.parseJSON(result);
						if(dataR.type=='success')
						{
							$("#alert").hide();
							$("#imguri").val(dataR.url);
							$("#image-id").val(0);
							$("#canvas-image").attr('src',imgData);
						}
						else if(dataR.type=='error')
						{
							$(window).scrollTop(0);
							$("#alert").show();
							$("#alert").html(dataR.message);
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
						url: "<?php echo url('schedule/call-action-start-schedule-akun'); ?>",
						data: $("#form-publish").serialize(),
						// data: { 
							// status: "", 
						// },
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
									$('#start-user').modal('hide');
									alert("Akun berhasil distart, silahkan publish schedule anda");
								}
						}
				});
		
	}
	function publish_post(){
		if ( $("#imguri").val() == "") {
			$(window).scrollTop(0);
			$("#alert").show();
			$("#alert").html("Silahkan Input file yang akan diupload");
			return false;
		}
		// fill hidden input before process
		$("#hidden-description").val(descriptionPostEmoji[0].emojioneArea.getText());
		
		$.ajax({
				headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				type: 'POST',
				url: "<?php echo url('schedule/publish'); ?>",
				data: $("#form-publish").serialize(),
				// data: { 
					// imgData: data, 
				// },
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
							window.location.href = "<?php echo url('schedule'); ?>";
						}
						if(data.type=='pending')
						{
							$('#start-user').modal('toggle');
						}
						if(data.type=='error')
						{
							$(window).scrollTop(0);
							$("#alert").show();
							$("#alert").html(data.message);
						}
				}
		});
	}

	$(document).ready(function() {
		$(".h-icon").css("font-size","26px");
		$('#start-user').on('hidden.bs.modal', function () {
			//window.location.href = "<?php echo url('schedule'); ?>";
		});		
		$("#button-start-user-close").click(function(e){
			//window.location.href = "<?php echo url('schedule'); ?>";
		});
		$('#button-action').click(function(e){
			action_activity();
		});
		$("#alert").hide();
		// $("#input-description-box").val('');
		var myPixie = Pixie.setOptions({
				replaceOriginal: true,
				appendTo: 'body',
				forceLaundering:true,
				onSave: function(data, img) {
					// console.log(data);
					// data //base64 encoded image data
					// img  //img element with src set to image data
					load_image(data);
					
				}
		});
		
		// Datepicker
		$('.formatted-date').datetimepicker({
			format: 'Y-m-d H:i',
			// format: 'YYYY-MM-DD HH:mm',
			minDate: myTimeZOne(rightnow, timezone),
			// maxDate: "2016-12-01 00:00",
			maxDate: "<?php echo $max_date; ?>",
		});
		$("#publish-at").val('<?php if ($sid<>0) { echo date('Y-m-d H:i',strtotime($schedule->publish_at)); } ?>');
		$("#delete-at").val('<?php if ($sid<>0) { if ($schedule->is_deleted) { echo date('Y-m-d H:i',strtotime($schedule->delete_at)); } } ?>');
	
		
		
		$('#button-upload').click(function(e){
			e.preventDefault();
			myPixie.open({
				url: '',
			});
		});
    
		$( "body" ).on( "dblclick", '.same-height', function(e) {
			$("#canvas-image").attr('src',$(this).attr("data-url"));
			$("#imguri").val($(this).attr("data-url"));
			$("#image-id").val($(this).attr("data-id"));
			$('#choose-image').modal('toggle');
			$("#alert").hide();
		});
		
		$('#button-publish').click(function(e){
			if ( $("#publish-at").val() == "") {
				$(window).scrollTop(0);
				$("#alert").show();
				$("#alert").html("Silahkan Input Waktu Publish File");
				return false;
			}
			
			$("#hidden-method").val("schedule");
			e.preventDefault();
			publish_post();
		});
		$('#button-postnow').click(function(e){
			$("#hidden-method").val("now");
			e.preventDefault();
			publish_post();
		});
		
		$('#checkbox-delete').click(function() {
			if($('#checkbox-delete').is(':checked')) {
				$("#delete-at").prop('disabled', false);
			} else {
				$("#delete-at").prop('disabled', true);
			}
		});
		if($('#checkbox-delete').is(':checked')) {
			$("#delete-at").prop('disabled', false);
		} else {
			$("#delete-at").prop('disabled', true);
		}
		
		$("#button-upload-file").on("click", function() {
				$("#file-upload").trigger("click");
		});
		
    $("#file-upload").change(function(){
			// console.log($(this).val());
			// load_image($(this).files[0]);
			
			var file    = document.querySelector('input[type=file]').files[0];
			var reader  = new FileReader();

			reader.addEventListener("load", function () {
				// preview.src = reader.result;
				load_image(reader.result);
			}, false);

			if (file) {
				reader.readAsDataURL(file);
			}
	
    }).click(function(){
        $(this).val("")
    });		
		
	});
</script>

<div class="container">
	<div class="alert alert-danger" id="alert">
		<strong>Oh snap!</strong> Change a few things up and try submitting again.
	</div>  
  <!-- Modal Pick Image-->
	<div class="modal fade" id="choose-image" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
					<div class="modal-content">
							<div class="modal-header">
									Choose Image from Saved folder
							</div>
							<div class="modal-body">
							
								<div class="row" id="main-photobox">
									<?php foreach($imageM as $res) { ?>
											<div class="col-md-4 col-xs-4 container-fluid" style="margin-bottom:28px;">
											<?php 
											// $file = public_path('images/users/'.$res->file); 
											//$file = url('/images/users/'.$user->username.'-'.$user->id.'/'.$res->file); 
                      // $file = url('/images/users/'.$user->username.'-'.$user->id.'/'.$res->file.'?v='.uniqid());
                      if ($res->is_s3) {
                        $file = Storage::disk('s3')->url($res->file);
                      }
                      else {
                        $file = url('/../vp/users/'.$user->username.'-'.$user->id.'/'.$res->file.'?v='.uniqid());
                        $pieces = explode(".", $res->file);
                        $ext = $pieces[1];
                      }
											?>
												<div style="background-image:url('{{$file}}');" data-url="{{$file}}" class="same-height same-height-175" data-id="{{$res->id}}">
												</div>
												
											</div>
									<?php } ?>
								</div>
								
								
							</div>
							<input type="hidden" id="input-url">
							<!--
							<div class="modal-footer">
							</div>
							-->
					</div>
			</div>
	</div>	

  <!-- Modal START Users-->
	<div class="modal fade" id="start-user" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
					<div class="modal-content">
							<div class="modal-header">
								Anda belum start, proses publish tidak akan berjalan selama masih stop. <br>Tombol start akan menstart akun2 dalam schedule yang masih Stopped.
							</div>
							<div class="modal-body">
							
								<div class="row margin-bottom">
									<button id="button-action" data-id="{{$user->id}}" class="fl btn btn-success btn-{{$user->id}}" value="Start" style="margin-top:0px;margin-left:15px;color:#fff!important;">
										<span class='glyphicon glyphicon-play'></span> Start 
									</button>
										
										<div class="fn"></div>
								</div>
								
							</div>
							<div class="modal-footer">
									<button type="button" data-dismiss="modal" class="btn btn-info" id="button-start-user-close">OK</button>
							</div>
					</div>
			</div>
	</div>	



	<div class="row">
		<div class="col-md-6 col-xs-12">
			<h1>
				Schedules Photo
				<span class="h-icon glyphicon glyphicon-question-sign hint-button tooltipPlugin" title="<div class='panel-heading'>Schedules</div><div class='panel-content'>Schedule Post maksimum 3 Post tiap jamnya</div>">
				</span>
			</h1>
		</div>
		<div class="col-md-6 col-xs-12" align="right">
			<h3 class="pull-right"><span id="dates" style="font-weight: bold;float: left;"></span><span style="float: left;">&nbsp;</span> <span id="clock" style="color: #15b49e;"></span></h3>
		</div>
	</div>
	<hr>
	<div class="row">
		<div class="col-md-2 col-xs-5 col-sm-4 margin-bottom">
			<input type="button" value="Image Editor" class="btn btn-home" id="button-upload">
		</div>
		<div class="col-md-2 col-xs-5 col-sm-4 margin-bottom">
			<input type="button" value="Upload Image" class="btn btn-home" id="button-upload-file">
			<input type="file" style="display: none" id="file-upload" />
		</div>
		<div class="col-md-2 col-xs-5 col-sm-4 margin-bottom">
			<input type="button" value="Choose Image" class="btn btn-home" id="button-choose" data-toggle="modal" data-target="#choose-image">
		</div>
	</div>
	<div class="row margin-bottom">
			<div class="col-md-12 margin-bottom">

									<input type="hidden" name="timezone" value="{{env('IG_TIMEZONE')}}">
									<input type="hidden" name="rightnow" value="{{ Carbon\Carbon::now(''.env('IG_TIMEZONE').'')->toDateTimeString() }}">

									<form role="form" id="form-publish" enctype="multipart/form-data">
											{{ csrf_field() }}
											<input type="hidden" id="imguri" name="imguri" 
											value="<?php 
												if (!is_null($arr_repost)){ echo $arr_repost['url'];} 
												if ($sid<>0) {
                          if ($schedule->is_s3) {
                            echo rawurldecode(Storage::disk('s3')->url($schedule->image));
                          }
                          else {
                            echo $schedule->image; 
                          }
                        } 
											?>">
											<input type="hidden" name="saveuri" value="{{ url('schedule/publish') }}">
											<input type="hidden" name="ruri" value="{{ url('schedule') }}">
											<input type="hidden" name="id" value="{{ $sid }}">
											<input type="hidden" id="image-id" name="image_id" value="">
											<input type="hidden" id="slug" name="slug" value="<?php if ($sid<>0) { echo $schedule->slug; } ?>"> 
											<img id="canvas-image" class="img-responsive" src="<?php 
											if ($sid<>0) { 
                        if ($schedule->is_s3) {
                          echo Storage::disk('s3')->url($schedule->image);
                        }
                        else {
                          echo $schedule->image; 
                        }
                      } 
											else if (!is_null($arr_repost)){ echo $arr_repost['url'];}
											?>">
												
											<div class="form-group">
													<label>Caption
														<span class="glyphicon glyphicon-question-sign hint-button tooltipPlugin" title="<div class='panel-heading'>Caption</div><div class='panel-content'>Caption untuk post yang akan dischedule</div>">
														</span>														
													</label>
													<!--<textarea name="description" id="input-description-box" class="form-control" rows="5"><?php 
													// if ($sid<>0) { echo $schedule->description; } 
													// else if (!is_null($arr_repost)){ 
														// if ( ($arr_repost["caption"]<>"") && ($arr_repost["owner"]<>"") ) {
															// echo "Repost @".$arr_repost["owner"]." ".$arr_repost["caption"];
														// }
													// }  
													?></textarea>-->

													<input type="hidden" name="description" id="hidden-description">
													<div id="divInput-description-post"></div>
													<?php 
														$description = "";
														if ($sid<>0) { $description = json_encode($schedule->description); } 
														else if (!is_null($arr_repost)){ 
															if ( ($arr_repost["caption"]<>"") && ($arr_repost["owner"]<>"") ) {
																$description = json_encode("Repost @".$arr_repost["owner"]." ".$arr_repost["caption"]);
															}
														}  
													?>
													<script>
														descriptionPostEmoji = $("#divInput-description-post").emojioneArea({
															pickerPosition: "bottom",
														});
														descriptionPostEmoji[0].emojioneArea.setText(<?php echo $description; ?>);
													</script>
													
											</div>
											<div class="form-group row">
													<label class="col-md-2 col-xs-4 col-sm-3 control-label">Templates Caption
														<span class="glyphicon glyphicon-question-sign hint-button tooltipPlugin" title="<div class='panel-heading'>Templates Caption</div><div class='panel-content'>Caption-caption yang pernah disave sebelumnya(Hasil Research), dapat ditambahkan ke caption yang dischedule</div>">
														</span>														
													</label>
													<div class="col-md-3 col-xs-5 col-sm-5">
														<select class="form-control" id="select-template-name">
															<?php foreach($collections_captions as $collections_caption) { ?>
															<option data-value="{{$collections_caption->value}}" value="{{$collections_caption->value}}">
																{{$collections_caption->name}}
															</option>
															<?php } ?>
														</select>
													</div>
													<div class="col-md-1 col-xs-2 col-sm-2 margin-bottom">
														<button class="btn btn-home" id="button-add-templates" <?php 
														if (count($collections_captions)==0) {
															echo "disabled";
														}
														?>>
															<span class="glyphicon glyphicon-plus"></span>
														</button>
													</div>
											</div>
											<div class="form-group row">
													<label class="col-md-2 col-xs-4 col-sm-3 control-label">Hashtags Collection
														<span class="glyphicon glyphicon-question-sign hint-button tooltipPlugin" title="<div class='panel-heading'>Hashtags Collection</div><div class='panel-content'>Hashtag-hashtag yang pernah disave sebelumnya(Hasil Research), dapat ditambahkan ke caption yang dischedule</div>">
														</span>														
													</label>
													<div class="col-md-3 col-xs-5 col-sm-5">
														<select class="form-control" id="select-collection-hashtag">
															<?php foreach($hashtags_collections as $hashtags_collection) { ?>
															<option data-value="{{$hashtags_collection->value}}" value="{{$hashtags_collection->value}}">
																{{$hashtags_collection->name}}
															</option>
															<?php } ?>
														</select>
													</div>
													<div class="col-md-1 col-xs-2 margin-bottom">
														<button class="btn btn-home" id="button-add-hashtags"<?php 
														if (count($hashtags_collections)==0) {
															echo "disabled";
														}
														?>>
															<span class="glyphicon glyphicon-plus"></span>														
														</button>
													</div>
											</div>
											<div class="form-group">
													<label>Accounts
														<span class="glyphicon glyphicon-question-sign hint-button tooltipPlugin" title="<div class='panel-heading'>Accounts</div><div class='panel-content'>Account-account yang akan diposting</div>">
														</span>														
													</label>
													<div class="checkbox">
															<label >
																	<input type="checkbox" value="" id="checkAll">
																	Select All
															</label>
													</div>
											</div>
											<div class="form-group well">
													<div class="row">
															<?php 
																$arr_account_id = array();
																if ($sid<>0) { 
																	$check = Schedule::join("schedule_account","schedule_account.schedule_id","=","schedules.id")
																						->where("user_id","=",$user->id)
																						->where("schedule_id","=",$sid)
																						->get();
																	foreach($check as $data) {
																		$arr_account_id[] = $data->account_id;
																	}
																}
																
															?>

															@foreach ($accounts as $account)
															<div class="col-xs-6 col-sm-6 col-md-4">
																	<label><input type="checkbox" class="check" name="accounts[]" value="{{ $account->id }}"
<?php if (in_array($account->id, $arr_account_id)) { echo "checked";} ?>> {{$account->username}}</label>
															</div>
															@endforeach
													</div>
											</div>
											<hr>
											<div class="row">
											<!--
											<h3><div id="dates2" style="font-weight: bold;float: left;"></div><div style="float: left;">&nbsp;</div> <div id="clock2" style="color: #15b49e;"></div></h3>
											-->
													<div class="form-group col-md-5 col-sm-12 col-xs-12">
															<label>Schedule At <span class="glyphicon glyphicon-time"></span></label>
															<input type="text" id="publish-at" name="publish_at" class="form-control formatted-date" required="required">
													</div>
													<div class="form-group col-md-5 col-sm-12 col-xs-12">
															<label>Delete At <span class="glyphicon glyphicon-time"></span></label>
															<input type="checkbox" id="checkbox-delete" name="checkbox_delete" <?php 
																if ($sid<>0) { if($schedule->is_deleted) { echo "checked"; } }
															?>>
															<input type="text" id="delete-at" name="delete_at" class="form-control formatted-date" required="required" disabled>
													</div>
											</div>
											<button type="button" id="button-publish" class="btn btn-home" style="margin-bottom : 75px;">Schedule <span class="glyphicon glyphicon-time"></span></button>
											<button type="button" id="button-postnow" class="btn btn-home" style="margin-bottom : 75px;">Post Now <span class="glyphicon glyphicon-time"></span></button>
											<input type="hidden" id="hidden-method" name="hidden_method">
									</form>
			</div>
	</div>
</div>
    @if (Request::is('schedule/add') || Request::is('schedule/edit*') || Request::is('schedule/repost*'))
        <script src="{{ asset('/js/schedule.js') }}"></script>
				
    @endif

@endsection
