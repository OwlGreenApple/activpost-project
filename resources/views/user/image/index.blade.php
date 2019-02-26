@extends('layouts.app')

@section('content')
  <!-- Modal confirm delete-->
	<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
					<div class="modal-content">
							<div class="modal-header">
									Delete Image from folder
							</div>
							<div class="modal-body">
									Are you sure want to delete ?
							</div>
							<input type="hidden" id="id-image">
							<div class="modal-footer">
									<button type="button" data-dismiss="modal" class="btn btn-info" id="button-delete">Yes</button>
									<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
							</div>
					</div>
			</div>
	</div>	


<div class="container">
  <div class="alert alert-danger" id="alert">
    <strong>Oh snap!</strong> Change a few things up and try submitting again.
  </div>  
	@if (session('error') )
		<div class="alert alert-danger">
			<?php 
				$errors = session('error');
				$errors = explode(";",$errors);
				if ( count($errors)>0 ) {
					foreach ($errors as $error) {
						echo $error."<br>";
					}
				}
			?>
		</div>
	@endif
    <div class="row">
			<h1 align="center">Saved Images
			</h1>
			<p align="center">
			<?php if ( count($imageM) > 0 ) { echo count($imageM); } else { echo "0"; } ?> Images
				<span class="glyphicon glyphicon-question-sign hint-button tooltipPlugin" title="<div class='panel-heading'>Image lama muncul</div><div class='panel-content'>Apabila hal itu terjadi setelah upload(Image lama muncul). <br>Silahkan clear cookies browser anda, atau anda dapat menekan tombol Ctrl+Shifht+R dihalaman image ini</div>">
				</span>
			</p>
    </div>
		<?php if ( count($imageM) < 100 ) { ?>
		<div class="row">
			<div class="fl col-md-2 col-xs-3" style="margin-bottom:28px;">
				<button class="form-control btn-home" id="button-add">Upload <span class="glyphicon glyphicon-upload"></span></button>
			</div>
			<div class="fl col-md-2 col-xs-3" style="margin-bottom:28px;">
				<button class="form-control btn-home" id="button-bulk-upload">Bulk Upload <span class="glyphicon glyphicon-upload"></span></button>
				{!! Form::open(array(
										'url'=>url('multiple-upload'),
										'method'=>'POST', 
										'files'=>true,
				)) !!}
				{!! Form::file('images[]', array(
										'multiple'=>true,
										'id'=>'button-file',
										'style'=>'display:none;',
				)) !!}
				<input type="hidden" value="{{URL::current()}}" name="current_url">
				{!! Form::submit('Submit', array(
										'class'=>'send-btn',
										'style'=>'display:none;',
				)) !!}
				{!! Form::close() !!}
			</div>
			<div class="fl col-md-2 col-xs-3" style="margin-bottom:28px;">
				<button class="form-control btn-danger" id="button-delete-all" data-toggle="modal" data-target="#confirm-delete" data-id="all"><span class="glyphicon glyphicon-remove"></span>Delete All Images</button>
			</div>
			<div class="col-md-3 col-xs-3  fr" align="right" style="margin-top:15px;">
				
				<label for="ListModeButton" id="label-image">Grid View</label>
				<button type="button" class="btn btn-mode btn-mode-first btn-mode-selected" id="ListModeButton"></button><button type="button" class="btn btn-mode btn-mode-second" id="GridModeButton"></button>
				<label for="GridModeButton" id="label-caption">List View</label>
				
			</div>
			<div class="fn">
			</div>
			
		</div>
		<?php } ?>
		<div class="row" id="grid-photobox" style="margin-bottom:250px;">
			<table class="table table-bordered">  
				<thead>
					<tr>
						<th>No.</th>
						<th>Image</th>
						<th>Created</th>
						<th></th>
					</tr>      
				</thead>
				
				
				<tbody id="content">
				</tbody>
				
			</table>  
			
			<nav>
				<ul class="pagination" id="pagination">
				</ul>
			</nav>  
		</div>
		<div class="row" id="main-photobox">
		
			<?php foreach($imageM as $res) { ?>
					<div class="col-md-4 col-xs-12 container-fluid" style="margin-bottom:28px;">
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
						<div style="background-image:url('{{$file}}');" class="same-height">
							<div class="action-image hide" data-url="{{ $file}}" data-owner="{{$res->owner_post}}" data-caption="{{$res->caption}}" data-id="{{$res->id}}">
								<a href="" class="col-md-4 col-xs-4 link-home link-action-left link-edit"><span class="glyphicon glyphicon-pencil"></span> Edit</a>
								<a href="{{ $file}}" data-url="{{ $file}}" class="col-md-4 col-xs-4 link-home link-action-center link-download" download="{{'file'}}"><span class="glyphicon glyphicon-download-alt"></span> Download</a>
								<?php //if($res->is_use_caption) { ?>
									<!--<a href="" class="col-md-4 col-xs-4 link-home link-action-right link-repost">-->
									<a href="" class="col-md-4 col-xs-4 link-home link-action-right link-schedule">
								<?php //} else {?>
									<!--<label>-->
								<?php //} ?>
									<!--<span class="glyphicon glyphicon-share-alt"></span> Repost-->
									<span class="glyphicon glyphicon-time"></span> Schedule
								<?php //if($res->is_use_caption) { ?>
									</a>
								<?php //} else {?>
									<!--</label>-->
								<?php //} ?>
							</div>
							<div class="link-delete remove-icon hide" data-toggle="modal" data-target="#confirm-delete" data-id="{{$res->id}}">
							<span class="glyphicon glyphicon-remove"></span>
							</div>
						</div>
						
					</div>
			<?php } ?>
    </div>
		<div class="row">
    </div>
</div>

<script type="text/javascript">
	function refresh_page(page)
	{
		$.ajax({                                      
			url: '<?php echo url('load-image-list'); ?>',
			type: 'get',
			data: {
				// search : $("#search-text").val(),
				page: page,
			},
			beforeSend: function()
			{
				$("#div-loading").show();
			},
			dataType: 'text',
			success: function(result)
			{
				$('#content').html(result);
				$("#div-loading").hide();
			}
		});
	}
	function create_pagination(page)
	{
		$.ajax({
			url: '<?php echo url('pagination-image-list'); ?>',
			type: 'get',
			data: {
				page : page,
				// search : $("#search-text").val(),
			},
			beforeSend: function()
			{
				$("#div-loading").show();
			},
			dataType: 'text',
			success: function(result)
			{
				$('#pagination').html(result);
				
				$('#pagination a').click(function(e){
					e.preventDefault();
					e.stopPropagation();
					if ($(this).html() == "«") {
						page -= 1; 
					} else 
					if ($(this).html() == "»") {
						page += 1; 
					} else {
						page = parseInt($(this).html());
					}
					create_pagination(page);
					refresh_page(page);
				});
				
				// $("#div-loading").hide();
			}
		});
	}
	function hover_image(){
			$( ".same-height" ).hover(
			// $("body").on('click', '.tree-link',function(e) {
			// $('body').on('hover','.container-fluid',
				function() {
					$(this).find( ".action-image" ).addClass("show");
					$(this).find( ".action-image" ).removeClass("hide");
					$(this).find( ".remove-icon" ).addClass("show");
					$(this).find( ".remove-icon" ).removeClass("hide");
				}
				, function() {
					$(this).find( ".action-image" ).addClass("hide");
					$(this).find( ".action-image" ).removeClass("show");
					$(this).find( ".remove-icon" ).addClass("hide");
					$(this).find( ".remove-icon" ).removeClass("show");
				}
			);	
	}
	$(document).ready(function() {
		hover_image();
		$("#alert").hide();
		refresh_page(1);
		create_pagination();
		
		// $('.same-height').matchHeight(false);
		
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
										location.reload();
									} else if(data.type=='error') {
										$("#alert").show();
										$("#alert").html(data.message);
									}

							}
					});
				}
    });
		
		$( "body" ).on( "click", '.link-edit', function(e) {
			e.preventDefault();
			console.log();
			myPixie.open({
				url: $(this).parent().attr("data-url"),
			});
		});
		
		
		
		$( "body" ).on( "click", '.link-download', function(e) {
			// e.preventDefault();
			// $("#input-url").val($(this).parent().attr("data-url"));
			// $(this).parent().toDataURL();
		});
		$('#button-add').click(function(e){
			e.preventDefault();
			myPixie.open({
				url: '',
			});
		});
		
		$('#button-delete-all').click(function(e){
			$("#id-image").val($(this).attr("data-id"));
		});
		$('#button-delete').click(function(e){
			e.preventDefault();
      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type: 'POST',
          url: "<?php echo url('delete-image'); ?>",
          // data: $("#form-setting").serialize(),
          data: {
						inputId : $("#id-image").val(),
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
								location.reload();
              } else 
							if(data.type=='error')
							{
								$("#alert").addClass("alert-danger");
								$("#alert").removeClass("alert-success");
							}
          }
      });
		});
		

		$( "body" ).on( "click", '.link-delete', function(e) {
			$("#id-image").val($(this).attr("data-id"));
		});
		
		
		/*$( "body" ).on( "click", '.link-repost', function(e) {
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
								// call schedule add with image caption, owner 
								window.location.href = "<?php echo url('schedule/repost'); ?>"+"/"+data.imageId;
							}
					}
			});
			
		});*/
		
		$( "body" ).on( "click", '.link-schedule', function(e) {
			e.preventDefault();
			console.log("wqqee");
			window.location.href = "<?php echo url('schedule/repost'); ?>"+"/"+$(this).parent().attr("data-id");
		});
		
		// $('#button-file').hide();
		$('#button-bulk-upload').click(function() {
			$('#button-file').click();
		});
		$('#button-file').change(function() {
			console.log("a");
			$(this).closest('form').submit();
		});		
		
		$("#grid-photobox").hide();
		$('#ListModeButton').click(function(e){
			$("#grid-photobox").hide();
			$("#main-photobox").show();
			$('#ListModeButton').addClass("btn-mode-selected");
			$('#GridModeButton').removeClass("btn-mode-selected");
		});		
		$('#GridModeButton').click(function(e){
			$("#grid-photobox").show();
			$("#main-photobox").hide();
			$('#ListModeButton').removeClass("btn-mode-selected");
			$('#GridModeButton').addClass("btn-mode-selected");
		});		
	});
</script>

@endsection
