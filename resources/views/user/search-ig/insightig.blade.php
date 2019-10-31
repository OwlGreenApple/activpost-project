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
  						<div class="col-ig-wrapper">
	  						<div class="ig-cover">
	  							<div class="col-md-3 fix-frame "><img class="igimgprofile" src="{{$rows['profile']}}"/></div>
		  						<div style="margin-right : 9px" class="col-md-7 fix-frame">
		  							<div><b><a target="_blank" href="https://www.instagram.com/{{$rows['username']}}">{{$rows['username']}}</a></b></div>
		  							<div>{{$rows['fullname']}}</div>
		  						</div>
		  						<div class="col-md-1 fix-frame ">{{$rows['time']}}</div>
		  						<div class="clearfix"></div>
		  					</div>
	  						<div class="igimgfix">
	  							<a target="_blank" href="{{$rows['code']}}"><img class="igimg" src="{{$rows['img']}}"/></a>
	  						</div>
	  						<div class="ig-cover ig-caption">{{$rows['caption']}}</div>
	  						<div class="ig-cover">
		  						<div class="col-lg-4"><span class="glyphicon glyphicon-heart-empty">{{$rows['likes']}}</span></div>
		  						<div class="col-lg-4"><span class="glyphicon glyphicon-comment"></span>{{$rows['comments']}}</div>
		  						<div class="clearfix"></div>
		  					</div>
	  					</div>
  					</div>
  					
  				@endforeach
  			@endif
  		</div>
  	</div>
 	<div class="content-tab" id="igtabs2C">
 		<div id="ighashtag">
 			<div class="table-responsive">
                    <table class="table table-striped" id="hashtag-table">
                        <thead>
                            <th>Hashtag</th>
                            <th>Hashtag Popularity</th>
                            <th>Hashtag In Post</th>
                            <th>% Usage In Post</th>
                        </thead>
                        <tbody>
                            @if(count($hashtags) > 0)
	                            @foreach($hashtags as $row)
	                                <tr>
	                                    <td>{{$row['hashtagname']}}</td>
	                                    <td><a target="_blank" href="https://www.instagram.com/explore/tags/{{str_replace('#','',$row['hashtagname'])}}/">{{number_format($row['hashtagpopularity'])}}</a></td>
	                                    <td><a>{{number_format($row['hashtaginpost'])}}</a></td>
	                                    <td>
	                                    	 <div class="c100 p{{$row['hashtaginpost']}} tiny orange">
										        <span>{{$row['hashtaginpost']}}%</span>
										        <div class="slice">
										            <div class="bar"></div>
										            <div class="fill"></div>
										        </div>
										    </div>
	                                    </td>
	                                </tr>
	                            @endforeach
                            @endif
                        </tbody>
                    </table>
                     </div>
                     <!-- end table -->
 		</div>
 	</div>
  	<div class="content-tab" id="igtabs3C">
  		<div id="igplace">
  			<div class="col-md-12 row" style="margin-bottom : 30px">
  				<div class="col-md-6 graph-bg">
  					<h3>Frequently Post</h3>
  					<h5>Post By Type</h5>
  					<div id="pieType" style="height: 300px; width: 100%;"></div>
  				</div>
  				<div class="col-md-6 graph-bg">
  					<div id="bar" style="height: 300px; width: 100%;"></div>
  				</div>
  			</div>

  			<div class="col-md-12 mt-3 graph-bg ">
  			 	<div id="chartContainer" style="height: 300px; width: 100%;"></div>
  			</div>
  		</div>
  	</div>

</div>

<script type="text/javascript">

$(function () {

	var album = [];
	$.each(<?php echo json_encode($graph['Album'], JSON_NUMERIC_CHECK); ?>, function( i, item ) {
		album.push({'x': new Date(item.x), 'y':item.y, 'z':item.z,'type':item.type ,'image':item.image, 'link':item.link});
 	});

	var image = [];
	$.each(<?php echo json_encode($graph['Image'], JSON_NUMERIC_CHECK); ?>, function( i, item ) {
		image.push({'x': new Date(item.x), 'y':item.y, 'z':item.z,'type':item.type ,'image':item.image, 'link':item.link});
 	});

 	var video = [];
	$.each(<?php echo json_encode($graph['Video'], JSON_NUMERIC_CHECK); ?>, function( i, item ) {
		video.push({'x': new Date(item.x), 'y':item.y, 'z':item.z,'type':item.type ,'image':item.image, 'link':item.link});
 	});

	$("#chartContainer").CanvasJSChart({ //Pass chart options
		title:{
       		text: ""
      	},
	  	toolTip:{
	       backgroundColor: "rgba(0,0,0,.5)",
	       fontColor: "white",
	    },
	    legend: {
           cursor:"pointer",
       	   horizontalAlign: "center",
           verticalAlign: "top",
           fontSize: 15,
	       /*itemclick: function(e){
	          alert( "Legend item clicked with type : " + e.dataSeries.type );
	       }*/
     	},
     	axisX:{      
            valueFormatString: "DD-MMM-YYYY" ,
            labelAngle: 0
        },
		data: [
			//Image
			{
				cursor:"pointer",
				color: "blue",
				type: "bubble", //change it to column, spline, line, pie, etc
				name: "image",
	        	legendText: "Image",
		        showInLegend: true,
		        legendMarkerType: "circle",
		 		toolTipContent: "<div class='\"'row fixtooltip'\"'><div class='\"'col-md-4 fixcol'\"'><img src={image} style='\"'width:100px'\"' /></div><div class='\"'col-md-8 fixcol'\"'>Engagement Rate: {y}<br/> Posted on: {x}<br /> Type : {type}<br/> Click to jump the post</div></div>",
				dataPoints: image,
				click : onClick
			},
			//Album
			{
				cursor:"pointer",
				color: "orange",
				type: "bubble", //change it to column, spline, line, pie, etc
				name: "album",
	        	legendText: "Album",
		        showInLegend: true,
		        legendMarkerType: "circle",
		 		toolTipContent: "<div class='\"'row fixtooltip'\"'><div class='\"'col-md-4 fixcol'\"'><img src={image} style='\"'width:100px'\"' /></div><div class='\"'col-md-8 fixcol'\"'>Engagement Rate: {y}<br/> Posted on: {x}<br /> Type : {type}<br/> Click to jump the post</div></div>",
				dataPoints: album,
				click : onClick
			},
			//Video
			{
				cursor:"pointer",
				color: "green",
				type: "bubble", //change it to column, spline, line, pie, etc
				name: "video",
	        	legendText: "Video",
		        showInLegend: true,
		        legendMarkerType: "circle",
		 		toolTipContent: "<div class='\"'row fixtooltip'\"'><div class='\"'col-md-4 fixcol'\"'><img src={image} style='\"'width:100px'\"' /></div><div class='\"'col-md-8 fixcol'\"'>Engagement Rate: {y}<br/> Posted on: {x}<br /> Type : {type}<br/> Click to jump the post</div></div>",
				dataPoints: video,
				click : onClick
			}
		]
	});

});

function onClick(e) {
	window.open( e.dataPoint.link, '_blank');
	//alert(  e.dataSeries.type + ", dataPoint { x:" + e.dataPoint.x + ", y: "+ e.dataPoint.y + " }" );
}


/* pie diagram */
$(function() {
	$("#pieType").CanvasJSChart({ //Pass chart options
        data: [{
			type: "pie",
			startAngle: 240,
			yValueFormatString: "##0.00\"%\"",
			indexLabel: "{label} {y}",
			dataPoints: [
				{y: 79.45, label: "Google"},
				{y: 7.31, label: "Bing"},
				{y: 7.06, label: "Baidu"},
				{y: 4.91, label: "Yahoo"},
				{y: 1.26, label: "Others"}
			]
		}]

	});
});

/* Bar diagram */
$(function(){
	$("#bar").CanvasJSChart({
		animationEnabled: true,
		title:{
			text: "Crude Oil Reserves vs Production, 2016"
		},	
		axisY: {
			title: "Billions of Barrels",
			titleFontColor: "#4F81BC",
			lineColor: "#4F81BC",
			labelFontColor: "#4F81BC",
			tickColor: "#4F81BC"
		},
		axisY2: {
			title: "Millions of Barrels/day",
			titleFontColor: "#C0504E",
			lineColor: "#C0504E",
			labelFontColor: "#C0504E",
			tickColor: "#C0504E"
		},	
		toolTip: {
			shared: true
		},
		legend: {
			cursor:"pointer",
			itemclick: toggleDataSeries
		},
		data: [{
			type: "column",
			name: "Proven Oil Reserves (bn)",
			legendText: "Proven Oil Reserves",
			showInLegend: true, 
			dataPoints:[
				{ label: "Saudi", y: 266.21 },
				{ label: "Venezuela", y: 302.25 },
				{ label: "Iran", y: 157.20 },
				{ label: "Iraq", y: 148.77 },
				{ label: "Kuwait", y: 101.50 },
				{ label: "UAE", y: 97.8 }
			]
		},
		{
			type: "column",	
			name: "Oil Production (million/day)",
			legendText: "Oil Production",
			axisYType: "secondary",
			showInLegend: true,
			dataPoints:[
				{ label: "Saudi", y: 10.46 },
				{ label: "Venezuela", y: 2.27 },
				{ label: "Iran", y: 3.99 },
				{ label: "Iraq", y: 4.45 },
				{ label: "Kuwait", y: 2.92 },
				{ label: "UAE", y: 3.1 }
			]
		}]
	})

	function toggleDataSeries(e) {
		if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
			e.dataSeries.visible = false;
		}
		else {
			e.dataSeries.visible = true;
		}
	}

});

</script>
<script type="text/javascript">
	$(document).ready(function() {    
		getTabs();
		table();
	});

	function table()
	{
        $("#hashtag-table").dataTable({
            'pageLength':10,
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

</script>

@endsection
