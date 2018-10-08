@extends('layouts.app')

@section('content')

<script type="text/javascript">

	$(document).ready(function() {
		$("#alert").hide();		
		$('#button-save').click(function(e){
      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type: 'POST',
          url: "<?php echo url('submit-caption'); ?>",
          data: {
						// captionBox : $("#input-caption-box").val(),
						captionBox : captionBoxEmoji[0].emojioneArea.getText(),
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
								$("#result-hashtags").html(data.result);
								$(".main-content").css("min-height",$(window).height()-121);
								if(data.typeSubmit=='insert') {
									// $("#select-collection-caption").append('<option data-value="'+$("#input-caption-box").val()+'" data-name="'+$("#input-name-template").val()+'">'+$("#input-name-template").val()+'</option>');
									$("#select-collection-caption").append('<option data-value="'+captionBoxEmoji[0].emojioneArea.getText()+'" data-name="'+$("#input-name-template").val()+'">'+$("#input-name-template").val()+'</option>');
								}
								if(data.typeSubmit=='update') {
									// $("#select-collection-caption option[data-name='"+$("#input-name-template").val()+"']").attr("data-value",$("#input-caption-box").val());
									$("#select-collection-caption option[data-name='"+$("#input-name-template").val()+"']").attr("data-value",captionBoxEmoji[0].emojioneArea.getText());
								}


								
              }
              else if(data.type=='error')
              {
                $("#alert").addClass('alert-danger');
                $("#alert").removeClass('alert-success');
              }

              // $("#preview-caption-box").val($("#select-collection-caption option:selected").attr("data-value"));
							previewCaptionEmoji[0].emojioneArea.setText($("#select-collection-caption option:selected").attr("data-value"));
          }
      })
		});
		$('#button-edit').click(function(e){
			// $("#input-caption-box").val($("#select-collection-caption option:selected").attr("data-value"));
			captionBoxEmoji[0].emojioneArea.setText($("#select-collection-caption option:selected").attr("data-value"));
			$("#input-name-template").val($("#select-collection-caption option:selected").attr("data-name"));
		});
		$('#button-delete').click(function(e){
			if ($("#id-delete").val()=="hashtags") {
				inputData = $("#select-collection-hashtag option:selected").attr("data-name");
			} else if ($("#id-delete").val()=="caption") {
				inputData = $("#select-collection-caption option:selected").attr("data-name");
			}
      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type: 'POST',
          url: "<?php echo url('delete-caption'); ?>",
          data: {
						templateName : inputData,
						type:$("#id-delete").val(),
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
								$("#result-hashtags").html(data.result);
								$(".main-content").css("min-height",$(window).height()-121);
								if ($("#id-delete").val()=="hashtags") {
									$("#select-collection-hashtag option[data-name='"+$("#select-collection-hashtag option:selected").attr("data-name")+"']").remove();
									$("#select-collection-hashtag").find('option:selected').val();
									
									var myDropDown=$("#select-collection-hashtag");
									var length = $('#select-collection-hashtag> option').length;
									//open dropdown
									myDropDown.attr('size',length);
									//close dropdown
									myDropDown.attr('size',0);									
									
								} else if ($("#id-delete").val()=="caption") {
									$("#select-collection-caption option[data-name='"+$("#select-collection-caption option:selected").attr("data-name")+"']").remove();
									$("#select-collection-caption").find('option:selected').val();
									
									var myDropDown=$("#select-collection-caption");
									var length = $('#select-collection-caption> option').length;
									//open dropdown
									myDropDown.attr('size',length);
									//close dropdown
									myDropDown.attr('size',0);								
									
								}
              }
              else if(data.type=='error')
              {
                $("#alert").addClass('alert-danger');
                $("#alert").removeClass('alert-success');
              }

              // $("#preview-caption-box").val($("#select-collection-caption option:selected").attr("data-value"));
							previewCaptionEmoji[0].emojioneArea.setText($("#select-collection-caption option:selected").attr("data-value"));
          }
      })
		});
		
    
    //$("#preview-caption-box").val($("#select-collection-caption option:selected").attr("data-value"));
		$( "#select-collection-caption" ).change(function() {
			// $("#preview-caption-box").val($("#select-collection-caption option:selected").attr("data-value"));
			previewCaptionEmoji[0].emojioneArea.setText($("#select-collection-caption option:selected").attr("data-value"));
		});

		$( "#select-collection-caption" ).change();
		
		$('#btn-delete-hashtags').click(function(e){
			$("#id-delete").val("hashtags");
		});

		$('#btn-delete-caption').click(function(e){
			$("#id-delete").val("caption");
		});
		
		$('#button-add-collection').click(function(e){
			
			// $("#input-caption-box").val($("#input-caption-box").val()+" "+$("#select-collection-hashtag option:selected").attr("data-value"));
			captionBoxEmoji[0].emojioneArea.setText(captionBoxEmoji[0].emojioneArea.getText()+" "+$("#select-collection-hashtag option:selected").attr("data-value"));
		});
	});
</script>

  <!-- Modal confirm delete-->
	<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
					<div class="modal-content">
							<div class="modal-header">
									Delete Selected Caption
							</div>
							<div class="modal-body">
									Are you sure want to delete ?
							</div>
							<input type="hidden" id="id-delete">
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
	<div class="col-lg-9 col-md-9 col-xs-12">
		<div class="row">
			<p class="colected-tags">Caption Box
				<span class="glyphicon glyphicon-question-sign hint-button tooltipPlugin" title="<div class='panel-heading'>Caption Box</div><div class='panel-content'>Merupakan textbox untuk menyimpan caption-caption untuk schedule</div>">
				</span>														
			</p>
			<div class="col-md-12 col-xs-12">
				<!--<textarea class="form-control textarea-basket" id="input-caption-box"></textarea>-->
				<div id="divInput-caption-box"></div>
				<script>
					captionBoxEmoji = $("#divInput-caption-box").emojioneArea({
															pickerPosition: "bottom",
														});
					// captionBoxEmoji[0].emojioneArea.setText("");
					// captionBoxEmoji[0].emojioneArea.getText();
				</script>
				
			</div>
			<div class="col-md-12 col-xs-12">
			&nbsp
			</div>
			<div class="col-md-8 col-xs-7">
				<input type="text" class="form-control" placeholder="Name Templates" id="input-name-template">
			</div>
			<div class="col-md-3 col-xs-4">
				<input type="button" value="Save Template" class="form-control btn-home" id="button-save">
			</div>
			<div class="col-md-1 col-xs-1">
			&nbsp
			</div>
		</div>

		<div class="row">
			<div class="col-lg-12 col-md-12 col-xs-12">
				<br>
			</div>
			<p class="colected-tags">Add Hashtags Collection
				<span class="glyphicon glyphicon-question-sign hint-button tooltipPlugin" title="<div class='panel-heading'>Add Hashtags Collection</div><div class='panel-content'>Berfungsi untuk menambahkan hashtags yang pernah disave sebelumnya pada caption box</div>">
				</span>														
			</p>
			<div class="col-md-8 col-xs-8">
				<select class="form-control" id="select-collection-hashtag">
					<?php foreach($collections_hashtags as $collection) { ?>
						<option value="{{$collection->id}}" data-value="{{$collection->value}}" data-name="{{$collection->name}}">{{$collection->name}}</option>
					<?php } ?>
				</select>
			</div>
			<div class="col-md-4 col-xs-4">
				<input type="button" value="Add" class="btn btn-home" id="button-add-collection">
				<button value="Delete" class="btn btn-home" data-toggle="modal" data-target="#confirm-delete" id="btn-delete-hashtags">
					<span class="glyphicon glyphicon-remove"></span>
				</button>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-12 col-md-12 col-xs-12">
				<br>
			</div>
			<p class="colected-tags">Caption Templates
				<span class="glyphicon glyphicon-question-sign hint-button tooltipPlugin" title="<div class='panel-heading'>Caption Templates</div><div class='panel-content'>Berfungsi mengedit templates collection</div>">
				</span>														
			</p>
			<div class="col-md-8 col-xs-8">
				<select class="form-control" id="select-collection-caption">
					<?php foreach($collections_captions as $collection) { ?>
						<option value="{{$collection->id}}" data-value="{{$collection->value}}" data-name="{{$collection->name}}">{{$collection->name}}</option>
					<?php } ?>
				</select>
			</div>
			<div class="col-md-4 col-xs-4">
				<input type="button" value="Edit" class="btn btn-home" id="button-edit">
				<button value="Delete" class="btn btn-home" data-toggle="modal" data-target="#confirm-delete" id="btn-delete-caption">
					<span class="glyphicon glyphicon-remove"></span>
				</button>
			</div>
		</div>
		
		<div class="row">
			<p class="colected-tags">Preview Box
				<span class="glyphicon glyphicon-question-sign hint-button tooltipPlugin" title="<div class='panel-heading'>Preview Box</div><div class='panel-content'>Berfungsi untuk melihat isi dari templates collection</div>">
				</span>														
			</p>
			<div class="col-md-12 col-xs-12">
				<!--<textarea class="form-control textarea-basket" id="preview-caption-box" readonly></textarea>-->
				<div id="preview-caption-box"></div>
				<script>
					previewCaptionEmoji = $("#preview-caption-box").emojioneArea({
															pickerPosition: "bottom",
														});
					previewCaptionEmoji[0].emojioneArea.disable();
					// previewCaptionEmoji[0].emojioneArea.setText("");
					// previewCaptionEmoji[0].emojioneArea.getText();
				</script>
			</div>
			<div class="col-md-12 col-xs-12">
			&nbsp
			</div>
		</div>
		
	</div>
</div>
@endsection
