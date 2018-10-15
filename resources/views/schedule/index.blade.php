@extends('layouts.app')

@section('content')
<?php 
use Celebpost\Models\Account;

?>
<script>
	main_content_page = 1;
	totalMainSchedulePage = <?php echo $totalMainSchedulePage; ?>;
	action_all = "";
  var table;
 
	$(function() {
    table = $('#tableSchedule').DataTable({
                searching: false,
                destroy: true,
                "order": [],
                "columnDefs": [ {
                  "targets": [0,1,7],
                  "orderable": false
                } ],
            });
    $.fn.dataTable.moment( 'YYYY-MM-DD HH:mm:ss' );

    //supaya nomor table nya nggak berubah
    table.on( 'order.dt search.dt', function () {
      table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
        cell.innerHTML = i+1;
      });
    }).draw();

		$("#from").datepicker({
			dateFormat: 'dd-mm-yy',
			onSelect: function(d) {
				var from = $('#from').datepicker('getDate');
				var to = $('#to').datepicker('getDate');
				if (from.getTime() > to.getTime()){
					$("#from").datepicker('setDate', to);
				}
				refresh_page(1);
				// create_pagination();
			}
		});
		$("#to").datepicker({
			dateFormat: 'dd-mm-yy',
			onSelect: function(d) {
				var from = $('#from').datepicker('getDate');
				var to = $('#to').datepicker('getDate');
				if (from.getTime() > to.getTime()){
					$("#to").datepicker('setDate', from);
				}
				refresh_page(1);
				// create_pagination();
			}
		});
		// $("#from").datepicker('setDate', new Date());
		// $("#to").datepicker('setDate', new Date());
		$("#from").datepicker('setDate', new Date('<?php echo $from->toDateString(); ?>'));
		$("#to").datepicker('setDate', new Date('<?php echo $to->toDateString(); ?>'));
		
	});
	
	function refresh_main_content(page)
	{
		$.ajax({
			url: '<?php echo url('load-main-schedule'); ?>',
			type: 'get',
			data: {
				page: page,
			},
			beforeSend: function()
			{
				$("#div-loading").show();
			},
			dataType: 'text',
			success: function(result)
			{
				var data = jQuery.parseJSON(result);
				$('#list-view').append(data.content);
				if (totalMainSchedulePage<=main_content_page) {
					$('#button-show-more').parent().parent().hide();
				}
				$("#div-loading").hide();
			}
		});
	}
	
	function refresh_page(page)
	{
    table.destroy();
		$.ajax({
			url: '<?php echo url('load-schedule-list'); ?>',
			type: 'get',
			data: {
				sortBy : $("#sort-by").val(),
				showStatus : $("#show-status").val(),
				from: ($('#from').datepicker('getDate').getTime()/1000+(3600*24+1)),
				to: ($('#to').datepicker('getDate').getTime()/1000+(3600*24+1)),
				fromSort : $("#from-sort").val(),
				page: page,
			},
			beforeSend: function()
			{
				$("#div-loading").show();
			},
			dataType: 'text',
			success: function(result)
			{
				var data = jQuery.parseJSON(result);
				$('#content').html(data.content);
				//$('#pagination').html(data.pagination);
				
        table = $('#tableSchedule').DataTable({
                searching: false,
                destroy: true,
                "order": [],
                "columnDefs": [ {
                  "targets": [0,1,7],
                  "orderable": false
                } ],
            });

				/*$('#pagination a').click(function(e){
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
					// create_pagination(page);
					refresh_page(page);
				});*/
				
				$("#div-loading").hide();
			}
		});
	}
	/*function create_pagination(page)
	{
		$.ajax({
			url: '<?php echo url('pagination-schedule-list'); ?>',
			type: 'get',
			data: {
				page : page,
				sortBy : $("#sort-by").val(),
				showStatus : $("#show-status").val(),
				from: ($('#from').datepicker('getDate').getTime()/1000+(3600*24+1)),
				to: ($('#to').datepicker('getDate').getTime()/1000+(3600*24+1)),
				fromSort : $("#from-sort").val(),
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
	}*/
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
									if (action_all== "Stop") { //last position stopped
										// $("#button-action").val("Start");
										// $("#button-action").html("<span class='glyphicon glyphicon-play'></span> Start");
										// $("#button-action").addClass("btn-success");
										// $("#button-action").removeClass("btn-danger");
										$("#status-activity").html('Status : <span class="glyphicon glyphicon-stop"></span> <span style="color:#c12e2a; font-weight:Bold;">Stopped</span>');
									} else if (action_all== "Start") { //last position started
										// $("#button-action").val("Stop");
										// $("#button-action").html("<span class='glyphicon glyphicon-stop'></span> Stop");
										// $("#button-action").addClass("btn-danger");
										// $("#button-action").removeClass("btn-success");
										$("#status-activity").html('Status : <span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> <span style="color:#5cb85c; font-weight:Bold;">Started</span>');
									}
								}
						}
				});
		
	}
	
	
	$(document).ready(function() {
		$("#alert").hide();
		refresh_main_content(main_content_page);
		refresh_page(1);
		// create_pagination();
		
		$('#button-show-more').click(function(e){
			e.preventDefault();
			main_content_page += 1;
			refresh_main_content(main_content_page);
		});
		$("#sort-by").change(function() {
			refresh_page(1);
			// create_pagination();
		});
		$("#from-sort").change(function() {
			refresh_page(1);
			// create_pagination();
		});
		$("#show-status").change(function() {
			refresh_page(1);
			// create_pagination();
		});

		$('#button-stop').click(function(e){
			action_activity_all();
		});
		/*$('#button-action').click(function(e){
			if ($("#button-action").val()== "Stop") { // last position stopped
				$('#confirm-stop').modal('toggle');
			} else if ($("#button-action").val()== "Start") { //last position started
				action_activity();
			}
		});*/
		$('#button-start-all').click(function(e){
			action_all = "Start";
			action_activity_all();
		});
		$('#button-stop-all').click(function(e){
			action_all = "Stop";
			$('#confirm-stop').modal('toggle');
		});
		

		$("#grid-view").hide();
		$('#ListModeButton').click(function(e){
			$("#grid-view").hide();
			$("#list-view").show();
			$('#ListModeButton').addClass("btn-mode-selected");
			$('#GridModeButton').removeClass("btn-mode-selected");
		});		
		$('#GridModeButton').click(function(e){
			// refresh_page(1);
			$("#grid-view").show();
			$("#list-view").hide();
			$('#ListModeButton').removeClass("btn-mode-selected");
			$('#GridModeButton').addClass("btn-mode-selected");
		});		
		
		
		$("body").on('click', '.link-read-more',function(e) {
			e.preventDefault();
			$(this).parent().html($(this).parent().attr("data-full-description"));
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
		
		
	});
</script>

<style type="text/css">
  #description {
    max-width: 200px;
  }
</style>
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


	<h1>
			Schedules
	</h1>
	
<h3><div id="dates" style="font-weight: bold;float: left;"></div><div style="float: left;">&nbsp;</div> <div id="clock" style="color: #15b49e;"></div></h3>


	<div class="row margin-bottom">
		<!--<button id="button-start-all" data-id="{{$user->id}}" class="fl btn btn-md btn-success btn-{{$user->id}}" value="Start" style="margin-top:0px;margin-left:15px;color:#fff!important;">
			<span class='glyphicon glyphicon-play'></span> Start All
		</button>
		<button id="button-stop-all" data-id="{{$user->id}}" class="fl btn btn-md btn-danger btn-{{$user->id}}" value="Stop" style="margin-top:0px;margin-left:15px;color:#fff!important;">
			<span class='glyphicon glyphicon-stop'></span> Stop All
		</button>
		-->
		<a href="{{ url('schedule/add') }}" class="btn btn-home fl" style="margin-left:15px;margin-top:0px;"><span class="glyphicon glyphicon-time"></span> Schedule Photo</a>
    <a href="{{ url('schedule/video') }}" class="btn btn-home fl" style="margin-left:15px;margin-top:0px;"><span class="glyphicon glyphicon-time"></span> Schedule Video</a>
		<!--<a id="button-buy-more" class="btn btn-sm btn-home-light fl" style="margin-left:15px;margin-top:-5px;" href="{{url('/order')}}">Buy More</a>-->
			
			<div class="fn"></div>
	</div>
	<div class="row margin-bottom">
	

			<!--<div class="col-md-5 col-xs-9 col-sm-9">
				<p id="status-activity" class="" style="">Status : <?php 
					if (!$user->is_started) {
						echo '<span class="glyphicon glyphicon-stop"></span> <span style="color:#c12e2a; font-weight:Bold;">Stopped</span>';
					} else {
						echo '<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> <span style="color:#5cb85c; font-weight:Bold;">Started</span>';
					}
				?></p>
				<p style="font-size:10px;margin-top: -14px;font-weight:Bold;">*Schedules tidak akan dijalankan jika status Stopped</p>
			</div>-->
		
			<div class="col-md-3 col-xs-6 col-sm6 fr" align="right" style="margin-top:5px;">
				
				<label for="ListModeButton" id="label-image">Grid View</label>
				<button type="button" class="btn btn-mode btn-mode-first btn-mode-selected" id="ListModeButton"></button><button type="button" class="btn btn-mode btn-mode-second" id="GridModeButton"></button>
				<label for="GridModeButton" id="label-caption">List View</label>
				
			</div>
		
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

	<!--
	Grid VIEW
	-->
	<div class="row" id="list-view">
	</div>
	<div class="row">
		<p align="center"><a href="#" id="button-show-more" class="link-home">Muat lainnya</a></p>
	</div>

	
	<!--
	List VIEW
	-->
	<div id="grid-view" style="margin-bottom:250px;">
		<div class="row margin-bottom">
			<div class="col-md-2 col-xs-5 col-sm-5">
				<label for="sort-by">Sort By</label>
				<select id="sort-by" class="form-control">
					<option value="1">Created</option>
					<option value="2">Scheduled At</option>
				</select>
			</div>  
			<div class="col-md-2 col-xs-5 col-sm-5">
				<label for="from-sort">Sort Order</label>
				<select id="from-sort" class="form-control">
					<option value="2">Latest</option>
					<option value="1">Oldest</option>
				</select>
			</div>  
			<div class="col-md-2 col-xs-5 col-sm-5">
				<label for="show-status">Status</label>
				<select id="show-status" class="form-control">
					<option value="1">All</option>
					<option value="2">Published</option>
					<option value="3">Deleted</option>
					<option value="4">Pending</option>
				</select>
			</div>  
			<div class="col-md-2 col-xs-5 col-sm-5">
				<label for="from">From</label>
				<input type="text" id="from" class="form-control"> 
			</div>  
			<div class="col-md-2 col-xs-5 col-sm-5">
				<label for="to">To</label>
				<input type="text" id="to" class="form-control"> 
			</div>  
		</div>
	
		<div class="row">
			<div class="table-responsive" style="margin-left:15px;">
				<table class="table table-bordered" id="tableSchedule">  
					<thead>
							<th style="vertical-align: top">No.</th>
							<th style="vertical-align: top">Image</th>
							<th style="vertical-align: top">Accounts</th>
							<th style="vertical-align: top">Description</th>
							<th style="vertical-align: top">Created</th>
							<th style="vertical-align: top">Scheduled At</th>
							<th style="vertical-align: top">Deleted At</th>
							<th style="vertical-align: top"></th>
					</thead>
					
					
					<tbody id="content">
					</tbody>
					
				</table>  
				
				<nav>
					<ul class="pagination" id="pagination">
					</ul>
				</nav>  
				
			</div>
		</div>
	</div>
	
</div>
@endsection
