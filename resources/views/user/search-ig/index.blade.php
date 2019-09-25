@extends('layouts.app')

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
			var people = '';
			var hashtag = '';
			var place = '';
			var query = $("#searchig").val();
			$.ajax({
				type : 'GET',
				url : '{{route("getHashtag")}}',
				data : {"q":query},
				dataType : 'json',
				success : function(result){

					/* people */
					$.each(result.people_username,function(key, value){
						people += '<div class="col-result row"><div class="col-lg-3">image</div><div class="col-lg-6"><div>Username</div><small>Full Name</small></div><div class="col-lg-3></div>"<div class="clearfix"></div></div>';
					});
					$("#iguser").html(people);

					/* hashtag */
					$.each(result.hashtag,function(key, value){
						hashtag += '<div class="col-result row"><div class="col-lg-6">'+value+'</div><div id="pos-'+key+'" class="col-lg-6"></div><div class="clearfix"></div></div>';
					});
					$("#ighashtag").html(hashtag);

					//post count
					$.each(result.post,function(key, value){
						$("#pos-"+key).html('<b>'+value+'</b>');
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
</script>

@endsection
