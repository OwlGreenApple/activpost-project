@extends('layouts.app')

@section('content')

<style>
	#igtabs {

   width: 100%;
    height:30px; 
   border-bottom: solid 1px #CCC;
   padding-right: 2px;
   margin-top: 30px;
   

}
a {cursor:pointer;}

#igtabs li {
    float:left; 
    list-style:none; 
    border-top:1px solid #ccc; 
    border-left:1px solid #ccc; 
    border-right:1px solid #ccc; 
    margin-right:5px; 
    border-top-left-radius:3px;  
    border-top-right-radius:3px;
      outline:none;
}

#igtabs li a {

    font-family:Arial, Helvetica, sans-serif; 
    font-size: small;
    font-weight: bold; 
    color: #5685bc;;
   padding-top: 5px;
   padding-left: 7px;
   padding-right: 7px;
    padding-bottom: 8px; 
    display:block; 
    background: #FFF;
    border-top-left-radius:3px; 
    border-top-right-radius:3px; 
    text-decoration:none;
    outline:none;
  
}

#igtabs li a.inactive{
    padding-top:5px;
    padding-bottom:8px;
  padding-left: 8px;
  padding-right: 8px;
    color:#666666;
    background: #EEE;
   outline:none;
   border-bottom: solid 1px #CCC;

}

#igtabs li a:hover, #igtabs li a.inactive:hover {


    color: #5685bc;
      outline:none;
}

.igimage {
	width  :100%;
	max-width : 30px;
}

.hashtag_input {
	cursor : pointer;
}

.content-tab {
    clear:both;           
    width:100%; 
    text-align:left;
}

.content-tab h2 { margin-left: 15px;  margin-right: 15px;  margin-bottom: 10px; color: #5685bc; }

.content-tab p { margin-left: 15px; margin-right: 15px;  margin-top: 10px; margin-bottom: 10px; line-height: 1.3; font-size: small; }

.content-tab ul { margin-left: 25px; font-size: small; line-height: 1.4; list-style-type: disc; }

.content-tab li { padding-bottom: 5px; margin-left: 5px;}

.col-result {
	padding : 10px;
	border-bottom : 1px solid #999;
}
</style>

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
  	<button id="test">Virol</button>
</div>

<script type="text/javascript">
	$(document).ready(function() {    
		//getTabs();
		//searchIg();
		//putHashTag();
		testvirol();
	});

	function testvirol()
	{
		$("#test").click(function(){
			/*$.ajax({
				type: "GET",
			    url: "https://i.instagram.com/api/v1/tags/search",
			    data: {'q':'burger'},
			    dataType : 'json',
			    success: function (result, status, xhr) {
			        console.log(result);
			    },
			})*/
			/*$.ajax({
				type: "GET",
			    url: "https://i.instagram.com/api/v1/tags/search",
			    data: {'timezone_offset':25200, 'lat':-7.306216299999999,'lng':112.6906303,'q':'buku'},
			    dataType : 'json',
			    success: function (result, status, xhr) {
			        console.log(result);
			    },
			})
			*/

			/* Test if cors is disable */
			$.ajax({
				crossDomain:true,
				headers: {'X-Requested-With': 'XMLHttpRequest'},
				type: "GET",
			    url: "https://i.instagram.com/api/v1/users/search",
			    data: {
			    	q : 'bola',
			    },
			    dataType : 'json',
			    success: function (result, status, xhr) {
			        console.log(xhr);
			    },
			})
			
		});
	}

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
