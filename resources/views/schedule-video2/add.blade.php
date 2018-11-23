@extends('layouts.app')

@section('content')

<?php 
	use Celebpost\Models\Schedule;
	if ($sid<>0) {
		$schedule = Schedule::find($sid);
	}

?>
<script>
  var temp_file = "<?php if (!is_null($arr_repost)){ echo $arr_repost['url'];} 
                         if ($sid<>0) { echo $schedule->image; } 
                    ?>";
	var isChrome = !!window.chrome; 
	var isIE = /*@cc_on!@*/false;

  function set_thumbnail(){
    var vid = document.getElementById("video-preview");
    $('#thumbnail').val(vid.currentTime);
    $('#span-thumbnail').html(Math.round(vid.currentTime)+' seconds');
    alert('Thumbnail berhasil di set');
  }

	function load_image(imgData,fileType){
    // var form = $('#form-publish')[0];
    // var formData = new FormData(form);
  
		$.ajax({
				headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				type: 'POST',
				url: "<?php echo url('schedule/save-video'); ?>",
        // data: formData, 
        data: {
					duration_video : $('#duration_video').val(),
					width : $('#width_video').val(),
					height : $('#height_video').val()
				}, 
				dataType: 'text',
        // cache: false,
        // contentType: false,
        // processData: false,
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
							$("#video-preview").show();
							// if( isChrome ) {
								// $("#video-preview").replaceWith($('<video id="video-preview" autoplay="autoplay" controls="controls" width="100%"><source src="'+imgData+'" type="'+fileType+'"></video>'));
								// $("#video-preview").replaceWith($('<video id="video-preview" src="'+imgData+'" width="100%"></video>'));
							// }
							// else {
								$("#video-preview").attr('src',imgData);
							// }
              $('.div-thumbnail').show();
						}
						else if(dataR.type=='error')
						{
              //location.reload();
							$(window).scrollTop(0);
							$("#alert").show();
							$("#alert").html(dataR.message);

              $("#imguri").val(temp_file);
              $("#video-preview").attr('src',temp_file);

              <?php if($sid==0) { ?>
                $("#video-preview").hide();
                $(".div-thumbnail").hide();
              <?php } ?>

              $("#file-upload").val(null);
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
		
    var form = $('#form-publish')[0];
    var formData = new FormData(form);
		$.ajax({
				headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				type: 'POST',
				url: "<?php echo url('schedule/publish-video'); ?>",
				//data: $("#form-publish").serialize(),
        data: formData,
				dataType: 'text',
        cache: false,
        contentType: false,
        processData: false,
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
    $("#btn-thumbnail").click(function(e){
      set_thumbnail();
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
		
    $("#button-upload-file").on("click", function() {
        $("#file-upload").trigger("click");
    });

    $("#file-upload").change(function(){
      var file    = document.querySelector('input[type=file]').files[0];
      var reader  = new FileReader();

      reader.addEventListener("load", function () {
        // preview.src = reader.result;
        console.log(file.type);

				//MP4 WebM Ogg 
        if(file.type.match("^video")){
					console.log("a");
          var videoId = "videoMain";
          var $videoEl = $('<video id="' + videoId + '"></video>');
          $videoEl.attr('src', reader.result);
          var videoTagRef = $videoEl[0];
          videoTagRef.addEventListener('loadedmetadata', function(e){
            $('#width_video').val(videoTagRef.videoWidth);
            $('#height_video').val(videoTagRef.videoHeight);
            $('#duration_video').val(videoTagRef.duration);
						
						load_image(reader.result,file.type);
          });
        } else {
          $(window).scrollTop(0);
          $("#alert").show();
          $("#alert").html("File yang diupload harus dalam format video");
        }
      }, false);

      if (file) {
        reader.readAsDataURL(file);
      }
			// load_image(document.querySelector('input[type=file]').files[0]);
    }).click(function(){
        $(this).val("")
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
	});
</script>

<div class="container">
	<div class="alert alert-danger" id="alert">
		<strong>Oh snap!</strong> Change a few things up and try submitting again.
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
				Schedules Video
				<span class="h-icon glyphicon glyphicon-question-sign hint-button tooltipPlugin" title="<div class='panel-heading'>Schedule Video</div><div class='panel-content'>Schedule Maksimum 10 Video yang bisa dishare(schedule story dan schedule post video) <br>File harus berupa MP4</div>">
				</span>
			</h1>
		</div>
		<div class="col-md-6 col-xs-12" align="right">
			<h3 class="pull-right"><span id="dates" style="font-weight: bold;float: left;"></span><span style="float: left;">&nbsp;</span> <span id="clock" style="color: #15b49e;"></span></h3>
		</div>
	</div>
	<hr>
	<div class="row">
		<div class="col-md-2 col-xs-4 col-sm-4 margin-bottom">
			<input type="button" value="Upload Video" class="btn btn-home" id="button-upload-file">
      <!--<form id="form-uploadfile" enctype="multipart/form-data">
            <input type="file" name="imgData" style="display: none" id="file-upload" />  
          </form>-->
		</div>
	</div>
	<div class="row margin-bottom">
			<div class="col-md-12 margin-bottom">

									<input type="hidden" name="timezone" value="{{env('IG_TIMEZONE')}}">
									<input type="hidden" name="rightnow" value="{{ Carbon\Carbon::now(''.env('IG_TIMEZONE').'')->toDateTimeString() }}">

									<form role="form" id="form-publish" enctype="multipart/form-data">
											{{ csrf_field() }}
                      <input type="hidden" name="width_video" id="width_video">
                      <input type="hidden" name="height_video" id="height_video">
                      <input type="hidden" name="duration_video" id="duration_video">
                      <input type="hidden" name="thumbnail" id="thumbnail" value="0">
											<input type="hidden" id="imguri" name="imguri" 
											value="<?php 
												if (!is_null($arr_repost)){ echo $arr_repost['url'];} 
												if ($sid<>0) { echo $schedule->image; } 
											?>">
                      <input type="file" name="imgData" style="display: none" id="file-upload" />  
											<input type="hidden" name="saveuri" value="{{ url('schedule/publish') }}">
											<input type="hidden" name="ruri" value="{{ url('schedule') }}">
											<input type="hidden" name="id" value="{{ $sid }}">
											<input type="hidden" id="image-id" name="image_id" value="">
											<input type="hidden" id="slug" name="slug" value="<?php if ($sid<>0) { echo $schedule->slug; } ?>">

											<div class="form-group row">
												<div class="col-md-6 col-xs-12 col-sm-12">
													<video id="video-preview" <?php if($sid==0) echo 'style="display: none;"' ?> src="<?php 
													if ($sid<>0) { echo $schedule->image; } 
													else if (!is_null($arr_repost)){ echo $arr_repost['url'];}
													?>" width="320" height="240" controls></video>
												</div>
											</div>

                      <div class="div-thumbnail" <?php if($sid==0) echo 'style="display: none;"' ?>>
                        <span style="font-size: 12px">Silahkan play video, kemudian pause dan klik Set Thumbnail untuk mengeset thumbnail video</span> <br>
                        <span id="span-thumbnail" style="font-weight: bold"><?php if($sid!=0) echo round($schedule->thumbnail_video).' seconds'; ?></span> <br>
                        <input type="button" value="Set Thumbnail" class="btn btn-home" id="btn-thumbnail"> <br> <br>
                      </div>
												
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
											<!--<h3>
                        <div id="dates2" style="font-weight: bold;float: left;"></div>
                        <div style="float: left;">&nbsp;</div> 
                        <div id="clock2" style="color: #15b49e;"></div>
                      </h3>-->
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
    @if (Request::is('schedule/add') || Request::is('schedule/edit*') || Request::is('schedule/repost*') || Request::is('schedule/video*'))
        <script src="{{ asset('/js/schedule.js') }}"></script>
				
    @endif

@endsection
