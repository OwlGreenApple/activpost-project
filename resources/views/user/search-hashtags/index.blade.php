@extends('layouts.app')

@section('content')
<script type="text/javascript">
	$(document).ready(function() {
		$("#alert").hide();
    $('#button-search').click(function(e){
			$("#search-for").html($("#search-string").val());
      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type: 'POST',
          url: "<?php echo url('process-search-hashtags'); ?>",
          // data: $("#form-setting").serialize(),
          data: {
						searchString : $("#search-string").val(),
						sortBy : $("#select-sort").val(),
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
              // $("#alert").show();
              // $("#alert").html(data.message);
              if(data.type=='success')
              {
                $("#alert").addClass('alert-success');
                $("#alert").removeClass('alert-danger');
								$("#result-hashtags").html(data.result);
								$(".main-content").css("min-height",$(window).height()-121);
              }
              else if(data.type=='error')
              {
								$("#alert").show();
								$("#alert").html(data.message);
                $("#alert").addClass('alert-danger');
                $("#alert").removeClass('alert-success');
              }
          }
      })
    });
		
		$( "body" ).on( "click", '.button-add', function(e) {
			/*if ($(".textarea-basket").val()==""){
				$(".textarea-basket").val("#"+$(this).attr("data-hashtag"));
			} else {
				$(".textarea-basket").val($(".textarea-basket").val()+" #"+$(this).attr("data-hashtag"));
			}*/
			if (hashtagsBasketEmoji[0].emojioneArea.getText()==""){
				hashtagsBasketEmoji[0].emojioneArea.setText("#"+$(this).attr("data-hashtag"));			
			}
			else {
				hashtagsBasketEmoji[0].emojioneArea.setText(hashtagsBasketEmoji[0].emojioneArea.getText()+" #"+$(this).attr("data-hashtag"));			
			}
			$(this).prop('disabled', true);
		});
		
		$('#button-save').click(function(e){
      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type: 'POST',
          url: "<?php echo url('submit-hashtags'); ?>",
          data: {
						// hashtagsBasket : $("#input-hashtags-basket").val(),
						hashtagsBasket : hashtagsBasketEmoji[0].emojioneArea.getText(),
						hashtagsFolder : $("#input-name-folder").val(),
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
									// $("#select-collection").append('<option data-value="'+$("#input-hashtags-basket").val()+'" data-name="'+$("#input-name-folder").val()+'">'+$("#input-name-folder").val()+'</option>');
									$("#select-collection").append('<option data-value="'+hashtagsBasketEmoji[0].emojioneArea.getText()+'" data-name="'+$("#input-name-folder").val()+'">'+$("#input-name-folder").val()+'</option>');
								}
								if(data.typeSubmit=='update') {
									// $("#select-collection option[data-name='"+$("#input-name-folder").val()+"']").attr("data-value",$("#input-hashtags-basket").val());
									$("#select-collection option[data-name='"+$("#input-name-folder").val()+"']").attr("data-value",hashtagsBasketEmoji[0].emojioneArea.getText());
								}
								
              }
              else if(data.type=='error')
              {
                $("#alert").addClass('alert-danger');
                $("#alert").removeClass('alert-success');
              }
          }
      })
		});
		$('#button-edit').click(function(e){
			// $("#input-hashtags-basket").val($("#select-collection option:selected").attr("data-value"));
			hashtagsBasketEmoji[0].emojioneArea.setText($("#select-collection option:selected").attr("data-value"));
			$("#input-name-folder").val($("#select-collection option:selected").attr("data-name"));
		});
		$('#button-delete').click(function(e){
      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type: 'POST',
          url: "<?php echo url('delete-hashtags'); ?>",
          data: {
						hashtagsFolder : $("#select-collection option:selected").attr("data-name"),
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
								$("#select-collection option[data-name='"+$("#select-collection option:selected").attr("data-name")+"']").remove();
              }
              else if(data.type=='error')
              {
                $("#alert").addClass('alert-danger');
                $("#alert").removeClass('alert-success');
              }
          }
      })
		});
		
	});
</script>
  <!-- Modal confirm delete-->
	<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
					<div class="modal-content">
							<div class="modal-header">
									Delete Selected Hashtag Collection
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
    <div class="row">
			<div class="col-md-3 col-xs-12 margin-bottom">
				<input type="text" placeholder="Cari" class="form-control" id="search-string">
			</div>
			<div class="col-md-3 col-xs-12 margin-bottom">
				<select class="form-control" id="select-sort">
					<option value="1">Trending Hashtags</option>
					<option value="2">Number of Posts</option>
				</select>
			</div>
			<div class="col-md-1 col-xs-12 margin-bottom">
				<input type="button" value="Search" class="btn btn-home" id="button-search">
			</div>
    </div>
			<h1>Search Result for "<span id="search-for"></span>"</h1>
		<div class="row">
			<div class="col-md-8 col-xs-12 margin-bottom">
				<table class="table table-striped">
					<thead>
						<th>Hashtags</th>
						<th>Posts</th>
						<th> </th>
					</thead>
					<tbody id="result-hashtags">
						<tr><td colspan="3">Tidak ada data</td></tr>
					</tbody>
				</table>
			</div>
			<div class="col-md-4 col-xs-12 margin-bottom">
				<div class="row">
					<p class="colected-tags">Hashtags Basket
						<span class="glyphicon glyphicon-question-sign hint-button tooltipPlugin" title="<div class='panel-heading'>Hashtags Basket</div><div class='panel-content'>Tempat menampung Hashtags yang dapat disimpan dengan cara : <br> 1. Menambahkan hashtags <br> 2. Memasukkan nama folder hashtags collection <br> 3. Tekan tombol Save </div>">
						</span>														
					
					</p>
					<div class="col-md-12 col-xs-12">
						<!--<textarea class="form-control textarea-basket" id="input-hashtags-basket"></textarea> -->
						<div id="hashtags-basket-box"></div>
						<script>
							hashtagsBasketEmoji = $("#hashtags-basket-box").emojioneArea({
																	pickerPosition: "bottom",
																});
							// hashtagsBasketEmoji[0].emojioneArea.setText("");
							// hashtagsBasketEmoji[0].emojioneArea.getText();
						</script>
					</div>
					<div class="col-md-12 col-xs-12">
					&nbsp
					</div>
					<div class="col-md-8 col-xs-8">
						<input type="text" class="form-control" placeholder="Name Folder" id="input-name-folder">
					</div>
					<div class="col-md-3 col-xs-3">
						<input type="button" value="Save" class="form-control btn-home" id="button-save">
					</div>
					<div class="col-md-12 col-xs-12">
					&nbsp
					</div>
					<p class="colected-tags">Hashtags Collection
						<span class="glyphicon glyphicon-question-sign hint-button tooltipPlugin" title="<div class='panel-heading'>Hashtags Collection</div><div class='panel-content'>Hashtags Collection merupakan kumpulan dari hashtags-hashtags yang pernah disave sebelumnya. <br>Pilih Hashtags Collection yang akan diedit, tekan tombol edit untuk mengedit <br>atau tekan tombol delete untuk menghapus hashtags collection</div>">
						</span>														
					</p>
					<div class="col-md-8 col-xs-8">
						<select class="form-control" id="select-collection">
							<?php foreach($collections as $collection) { ?>
								<option value="{{$collection->id}}" data-value="{{$collection->value}}" data-name="{{$collection->name}}">{{$collection->name}}</option>
							<?php } ?>
						</select>
					</div>
					<div class="col-md-4 col-xs-4">
						<input type="button" value="Edit" class="btn btn-home" id="button-edit">
						<button value="Delete" class="btn btn-home" data-toggle="modal" data-target="#confirm-delete" id="">
							<span class="glyphicon glyphicon-remove"></span>
						</button>
					</div>
				</div>
			</div>
    </div>
</div>
@endsection
