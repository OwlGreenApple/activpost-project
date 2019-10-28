@extends('layouts.app')

@section('content')

<div class="container">

	<ul id="igtabs">
	      <li><a id="igtabs1">User</a></li>
	      <li><a id="igtabs2">Hashtag</a></li>
	      <li><a id="igtabs3">Place</a></li>
	</ul>

	<div>
		<form id="search">
			<input type="text" class="form-control" id="searchig" />
		</form>
	</div>

  	<div class="content-tab" id="igtabs1C">
  		<div id="iguser"></div>
  	</div>
 	<div class="content-tab" id="igtabs2C">
 		<div id="ighashtag"></div>
 	</div>
  	<div class="content-tab" id="igtabs3C">
  		<div id="igplace"></div>
  	</div>

</div>

<script type="text/javascript">
	$(document).ready(function() {    
		getTabs();
		searchIg();
		putHashTag();
	});


	function getTabs(){
		$('#igtabs li a:not(:first)').addClass('inactive');
		$('.content-tab').hide();
		$('.content-tab:first').show();
		    
		$('#igtabs li a').click(function(){
		    var t = $(this).attr('id');
		  if($(this).hasClass('inactive')){ //this is the start of our condition 
		    $('#igtabs li a').addClass('inactive');           
		    $(this).removeClass('inactive');
		    
		    $('.content-tab').hide();
		    $('#'+ t + 'C').fadeIn('slow');
		 }
		});
	}

	function searchIg(){
		$("#search").submit(function(e){
			e.preventDefault();
			var hashtag = '';
			var people = '';
			var place = '';
			var query = $("#searchig").val();
			$("#ighashtag").html('');
			$("#iguser").html('');
			$("#igplace").html('');
			$.ajaxSetup({
		        headers: {
	              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		        }
		     });
			$.ajax({
				type : 'POST',
				url : '{{route("igdata")}}',
				data : {"q":query},
				dataType : 'json',
				success : function(result){
					/* hashtag */
					$.each(result.hashtag,function(key, value){
						hashtag += '<div class="col-result row"><div class="col-lg-6"><a class="hashtag_input">'+value+'</a></div><div id="pos-'+key+'" class="col-lg-6"></div><div class="clearfix"></div></div>';
					});

					$("#ighashtag").html(hashtag);

					//post count
					$.each(result.post,function(key, value){
						$("#pos-"+key).html('<b>'+value+'</b>');
					});

					/* people */
					$.each(result.people_username,function(key, value){
						people += '<div class="col-result row"><div class="col-lg-3" id="img-'+key+'"></div><div class="col-lg-6"><div><a target="_blank" href="https://www.instagram.com/'+value+'">'+value+'</a></div><small id="fnm-'+key+'"></small></div><div class="col-lg-3" id="igt-'+key+'"></div><div class="clearfix"></div></div>';
					});

					console.log(people);
					$("#iguser").html(people);

					//image
					$.each(result.people_image,function(key, value){
						$("#img-"+key).html('<img class="igimage" src="'+value+'"/>');
					});

					//full name
					$.each(result.people_name,function(key, value){
						$("#fnm-"+key).text(value);
					});

					//id for insight
					$.each(result.people_id,function(key, value){
						$("#igt-"+key).html('<a class="btn btn-default" href="{{url("insightigdata")}}/'+value+'">Insight</a>');
					});

					/* place */
					$.each(result.location_name,function(key, value){
						place+= '<div class="col-result"><div><a target="_blank" id="pk-'+key+'">'+value+ '</a></div><small id="addr-'+key+'"></small></div>';
					});
					$("#igplace").html(place);

					//address
					$.each(result.location_address,function(key, value){
						$("#addr-"+key).text(value);
					});

					//link
					$.each(result.location_pk,function(key, value){
						$("#pk-"+key).attr('href','https://www.instagram.com/explore/locations/'+value)
					});

				}
			});
		});
	}

	function putHashTag(){
		$("body").on("click",".hashtag_input",function(){
			var value = $(this).text();
			$("#searchig").val(value);
		});
	}

</script>

@endsection
