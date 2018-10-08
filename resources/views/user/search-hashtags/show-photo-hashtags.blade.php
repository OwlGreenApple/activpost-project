@extends('layouts.app')

@section('content')
  <!-- Modal confirm SAVE IMAGE-->
	<div class="modal fade" id="confirm-download" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
					<div class="modal-content">
							<div class="modal-header">
									Save Image to folder
							</div>
							<div class="modal-body">
									Are you sure want to Save ?
							</div>
							<input type="hidden" id="input-url">
							<input type="hidden" id="input-owner">
							<input type="hidden" id="input-caption">
							<div class="modal-footer">
									<button type="button" data-dismiss="modal" class="btn btn-info" id="button-download">Yes</button>
									<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
							</div>
					</div>
			</div>
	</div>	

  <!-- Modal confirm SAVE TEMPLATE-->
	<div class="modal fade" id="confirm-save-template" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
					<div class="modal-content">
							<div class="modal-header">
									Save Template to folder
							</div>
							<div class="modal-body">
									Are you sure want to Save ?
							</div>
							<input type="hidden" id="input-name-template">
							<input type="hidden" id="input-caption-box">
							<div class="modal-footer">
									<button type="button" data-dismiss="modal" class="btn btn-info" id="button-save-template">Yes</button>
									<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
							</div>
					</div>
			</div>
	</div>	


<div class="container">
  <div class="alert alert-danger" id="alert">
    <strong>Oh snap!</strong> Change a few things up and try submitting again.
  </div>  

    <div class="row">
			<h1 align="center">#{{$input_hashtags}}</h1>
			<p align="center">{{$media_count}} Kiriman</p>
    </div>
		<div class="row">
			<div class="col-md-3 col-xs-12 margin-bottom" role="group">
				<!--
				<select class="form-control" id="select-sort">
					<option value="1">Image</option>
					<option value="2">Caption</option>
				</select>
				-->
				<label for="ImageModeButton" id="label-image">Image</label>
				<button type="button" class="btn btn-mode btn-mode-first btn-mode-selected" id="ImageModeButton"></button><button type="button" class="btn btn-mode btn-mode-second" id="CaptionModeButton"></button>
				<label for="CaptionModeButton" id="label-caption">Caption</label>
			</div>
    </div>
		<div class="row" id="main-photobox">
			<?php 
			$result = unserialize($result); 
			if (!is_null($result)) foreach($result as $res) { 
				
				$username = "";
				if (!is_null($res->getUser())) {
					$username = $res->getUser()->getUsername();
				}
				
				$url = "";
				if (!is_null($res->getImageVersions2())) {
					$url =$res->getImageVersions2()->getCandidates()[0]->getUrl();
				}
				
				$caption = "";
				if (!is_null($res->getCaption())){
					$caption = $res->getCaption()->getText();
				}			
			?>
					<div class="col-md-4 col-xs-12 container-fluid same-height container-content" style="margin-bottom:28px;" data-container="image">
						<div style="background-image:url({{$url}});" class="same-height image-div">
							<div class="description-bar hide">
								<a href="https://instagram.com/{{$username}}" target="_blank" class="col-md-12 col-xs-12 link-home link-action-right"> @ : {{$username}}</a>
								<label class="col-md-12 col-xs-12 link-home link-action-right" style="margin-top:5px;"><span class="glyphicon glyphicon-heart"></span> : {{$res->getLikeCount()}}</label>
								<label class="col-md-12 col-xs-12 link-home link-action-right"><span class="glyphicon glyphicon glyphicon-comment"></span> : {{$res->getCommentCount()}}</label>
							</div>
							<div class="action-image hide" data-url="{{ Crypt::encrypt($url)}}" data-owner="{{$username}}" data-caption="{{$caption}}" >
								<a href="" class="col-md-4 col-xs-4 link-home link-action-left link-edit"><span class="glyphicon glyphicon-pencil"></span> Edit</a>
								<a href="" class="col-md-4 col-xs-4 link-home link-action-center link-download" data-toggle="modal" data-target="#confirm-download"><span class="glyphicon glyphicon-download-alt"></span> Save</a>
								<a href="" class="col-md-4 col-xs-4 link-home link-action-right link-repost"><span class="glyphicon glyphicon-share-alt"></span> Repost</a>
							</div>
						</div> 
						<div class="same-height caption-div">
							<textarea style="width:100%;" class="same-height">{{$caption}}</textarea>
							<div class="action-image hide">
								<input type="text" class="fl input-name-template" placeholder="Name Templates"> 
								<a href="" class="col-md-5 col-xs-5 link-home link-action-center link-download-template" data-toggle="modal" data-target="#confirm-save-template"><span class="glyphicon glyphicon-download-alt"></span> Save Caption</a>
							</div>
						</div> 
					</div>
			<?php } ?>
			<!--<img id="edit-me" src="/laravel-instagram/images/uploads/asd.jpg"/>-->

    </div>
		<div class="row">
			<p align="center"><a href="#" id="button-show-more" class="link-home">Muat lainnya</a></p>
    </div>
</div>

<script type="text/javascript">
	end_cursor = "<?php echo $end_cursor; ?>";
	
	function hover_image(){
		$( ".same-height" ).hover(
		// $("body").on('click', '.tree-link',function(e) {
		// $('body').on('hover','.same-height',
			function() {
				$(this).find( ".action-image" ).addClass("show");
				$(this).find( ".action-image" ).removeClass("hide");
				
				$(this).find( ".description-bar" ).addClass("show");
				$(this).find( ".description-bar" ).removeClass("hide");
			}
			, function() {
				$(this).find( ".action-image" ).addClass("hide");
				$(this).find( ".action-image" ).removeClass("show");
				
				$(this).find( ".description-bar" ).addClass("hide");
				$(this).find( ".description-bar" ).removeClass("show");
			}
		);	
	}
	
	$(document).ready(function() {
		
		delay = 250;
		
		$( "#select-sort" ).change(function() {
			if ($(this).val()=="1") {
				$(".container-content").attr("data-container","image");
				$(".image-div").removeClass("hide");
				$(".caption-div").addClass("hide");
			} else {
				$(".container-content").attr("data-container","caption");
				$(".image-div").addClass("hide");
				$(".caption-div").removeClass("hide");
			}
		});		
		
		$(".caption-div").hide();
		$('#ImageModeButton').click(function(e){
				$(".container-content").attr("data-container","image");
				// $(".image-div").removeClass("hide");
				$(".image-div").fadeIn(delay);
				// $(".caption-div").addClass("hide");
				$(".caption-div").fadeOut(delay);
				
				$('#ImageModeButton').addClass("btn-mode-selected");
				$('#CaptionModeButton').removeClass("btn-mode-selected");
		});
		
		$('#CaptionModeButton').click(function(e){
				$(".container-content").attr("data-container","caption");
				// $(".image-div").addClass("hide");
				// $(".caption-div").removeClass("hide");
				$(".image-div").fadeOut(delay);
				$(".caption-div").fadeIn(delay);
				
				$('#CaptionModeButton').addClass("btn-mode-selected");
				$('#ImageModeButton').removeClass("btn-mode-selected");
		});
		
		$("#alert").hide();
		asset_folder = "<?php echo asset('/pixie'); ?>";
    var myPixie = Pixie.setOptions({
        replaceOriginal: true,
        appendTo: 'body',
				forceLaundering:true,
				onSave: function(data, img) {
					// data //base64 encoded image data
					// img  //img element with src set to image data
					
					
					$.ajax({
							headers: {
									'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
							},
							type: 'POST',
							url: "<?php echo url('save-image'); ?>",
							// data: $("#form-setting").serialize(),
							data: { 
								imgData: data,
								captionData: "",
								ownerData: "",
								decryptData: "0",
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
									}
							}
					});
					
					
					
					
				}
				
    });
		
		hover_image();
		// $('.same-height').matchHeight(false);
/*    $('#edit-me').on('click', function(e) {
			console.log(e.target.src);
			console.log(e.target);
        myPixie.open({
            url: e.target.src,
            // image: e.target
				});
    });*/
				
		$('.container-content').click(function(e){
		});
		
		$( "body" ).on( "dblclick", '.container-content', function(e) {
		// $('.container-content').dblclick(function(e){
			if ($(this).attr("data-container")=="image"){
				$(this).attr("data-container","caption");
				// $(this).find(".image-div").addClass("hide");
				// $(this).find(".caption-div").removeClass("hide");
				$(this).find(".image-div").fadeOut(delay);
				$(this).find(".caption-div").fadeIn(delay);
			} else
			if ($(this).attr("data-container")=="caption"){
				$(this).attr("data-container","image");
				// $(this).find(".image-div").removeClass("hide");
				// $(this).find(".caption-div").addClass("hide");
				$(this).find(".image-div").fadeIn(delay);
				$(this).find(".caption-div").fadeOut(delay);
			}
		});
		
		$( "body" ).on( "click", '.link-edit', function(e) {
			e.preventDefault();
      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type: 'GET',
          url: "<?php echo url('save-temp-image'); ?>",
          // data: $("#form-setting").serialize(),
          data: {
						url : $(this).parent().attr("data-url"),
					},
          dataType: 'text',
          beforeSend: function()
          {
            $("#div-loading").show();
          },
          success: function(result) {
						// window.location.href = "<?php echo url('image-editor'); ?>";
						var data = jQuery.parseJSON(result);
						console.log(data.url);
						if(data.type=='success')
						{
							myPixie.open({
								url: data.url,
								// image: '<img id="edit-me" src="/laravel-instagram/images/uploads/asd.jpg">'
							});
						}
						$("#div-loading").hide();
          }
      });
		});
		
		$('#button-show-more').click(function(e){
			e.preventDefault();
      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type: 'POST',
          url: "<?php echo url('more-photo'); ?>",
          // data: $("#form-setting").serialize(),
          data: {
						inputHashtags : "<?php echo $input_hashtags; ?>",
						endCursor : end_cursor,
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
								$("#main-photobox").append(data.view);
								end_cursor = data.endCursor;
              }
							hover_image();
          }
      });
		});
		
		
		$( "body" ).on( "click", '.link-download', function(e) {
			e.preventDefault();
			$("#input-url").val($(this).parent().attr("data-url"));
			$("#input-owner").val($(this).parent().attr("data-owner"));
			$("#input-caption").val($(this).parent().attr("data-caption"));
		});
		$('#button-download').click(function(e){
			e.preventDefault();
      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type: 'POST',
          url: "<?php echo url('save-image-IG'); ?>",
          // data: $("#form-setting").serialize(),
          data: {
						inputUrl : $("#input-url").val(),
						inputCaption : $("#input-caption").val(),
						inputOwner : $("#input-owner").val(),
					},
          dataType: 'text',
          beforeSend: function()
          {
            $("#div-loading").show();
          },
          success: function(result) {
              $("#div-loading").hide();
              var data = jQuery.parseJSON(result);
							$("#alert").show();
							$("#alert").html(data.message);
              if(data.type=='success')
              {
								$("#alert").addClass("alert-success");
								$("#alert").removeClass("alert-danger");
              } else 
							if(data.type=='error')
							{
								$("#alert").addClass("alert-danger");
								$("#alert").removeClass("alert-success");
							}
							$(window).scrollTop(0);
          }
      });
		});
		
		$( "body" ).on( "click", '.link-download-template', function(e) {
			$("#input-name-template").val($(this).parent().find(".input-name-template").val());
			$("#input-caption-box").val($(this).parent().parent().find("textarea").val());
		});
		$('#button-save-template').click(function(e){
			
      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type: 'POST',
          url: "<?php echo url('submit-caption'); ?>",
          data: {
						captionBox : $("#input-caption-box").val(),
						nameTemplate : $("#input-name-template").val(),
					},
          dataType: 'text',
          beforeSend: function()
          {
            $("#div-loading").show();
          },
          success: function(result) {
              // $('#result').html(data);
              // console.log(result);return false;
              window.scrollTo(0, 0);
              $("#div-loading").hide();
              var data = jQuery.parseJSON(result);
              $("#alert").show();
              $("#alert").html(data.message);
              if(data.type=='success')
              {
                $("#alert").addClass('alert-success');
                $("#alert").removeClass('alert-danger');
								$(".main-content").css("min-height",$(window).height()-121);
								
              }
              else if(data.type=='error')
              {
                $("#alert").addClass('alert-danger');
                $("#alert").removeClass('alert-success');
              }
          }
      })
			
			
		});
		
		$( "body" ).on( "click", '.link-repost', function(e) {
			e.preventDefault();
			$.ajax({
					headers: {
							'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					type: 'POST',
					url: "<?php echo url('save-image'); ?>",
					// data: $("#form-setting").serialize(),
					data: { 
						imgData: $(this).parent().attr("data-url"),
						captionData: $(this).parent().attr("data-caption"),
						ownerData: $(this).parent().attr("data-owner"),
						decryptData: "1",
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
								// call schedule add with image caption, owner 
								window.location.href = "<?php echo url('schedule/repost'); ?>"+"/"+data.imageId;
							}
							else if(data.type=='error') {
								$("#alert").show();
								$("#alert").html(data.message);
							}
					}
			});
			
		});

	});
</script>

@endsection
