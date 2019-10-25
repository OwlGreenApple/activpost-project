@extends('layouts.app')

@section('content')

<div class="container">

	<ul id="igtabs">
	      <li><a id="igtabs1">Post</a></li>
	      <li><a id="igtabs2">Hashtags</a></li>
	      <li><a id="igtabs3">Analytics</a></li>
	</ul>

  	<div class="content-tab" id="igtabs1C">
  		<div id="iguser" class="col-md-12 row">
  			@if(count($data) > 0)
  				@foreach($data as $rows)
  					<div class="col-sm-3 col-ig">
  						<div class="ig-cover">
  							<div class="col-md-3 fix-frame "><img class="igimgprofile" src="{{$rows['profile']}}"/></div>
	  						<div style="margin-right : 9px" class="col-md-7 fix-frame">
	  							<div><b>{{$rows['username']}}</b></div>
	  							<div>{{$rows['fullname']}}</div>
	  						</div>
	  						<div class="col-md-1 fix-frame ">{{$rows['time']}}</div>
	  						<div class="clearfix"></div>
	  					</div>
  						<div class="igimgfix">
  							<a target="_blank" href="{{$rows['code']}}"><img class="igimg" src="{{$rows['img']}}"/></a>
  						</div>
  						<div class="ig-cover">
	  						<div class="col-lg-4"><span class="glyphicon glyphicon-heart-empty">{{$rows['likes']}}</span></div>
	  						<div class="col-lg-4"><span class="glyphicon glyphicon-comment"></span>{{$rows['comments']}}</div>
	  						<div class="clearfix"></div>
	  					</div>
  					</div>
  					
  				@endforeach
  			@endif
  		</div>
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
		//searchIg();
		//putHashTag();
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
						people += '<div class="col-result row"><div class="col-lg-3" id="img-'+key+'"></div><div class="col-lg-6"><div><a target="_blank" href="https://www.instagram.com/'+value+'">'+value+'</a></div><small id="fnm-'+key+'"></small></div><div class="col-lg-3">follower</div><div class="clearfix"></div></div>';
					});
					$("#iguser").html(people);

					//image
					$.each(result.people_image,function(key, value){
						$("#img-"+key).html('<img class="igimage" src="'+value+'"/>');
					});

					//full name
					$.each(result.people_name,function(key, value){
						$("#fnm-"+key).text(value);
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
