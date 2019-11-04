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

  			<h3>Activity</h3>

  			<div class="col-md-12 row" style="margin-bottom : 30px">
  				<div class="col-md-6 graph-bg">
  					<h3>Posting Time</h3>
  					<h5>Posts distribution by hours</h5>
  					<div id="avghour" style="height: 300px; width: 100%;"></div>
  				</div>
  				<div class="col-md-6 graph-bg">
  					<h3>Average Post</h3>
  					<h5>Posts distribution by day of the week</h5>
  					<div id="avgweek" style="height: 300px; width: 100%;"></div>
  				</div>
  			</div>

  			<h3>Hashtag</h3>

  			<div class="col-md-12 row" style="margin-bottom : 30px">
  				<div class="col-md-6 graph-bg">
  					<h3>Number of Hashtags per Post</h3>
  					<h5>Posts distribution by hashtags in caption</h5>
  					<div id="barchart" style="height: 300px; width: 100%;"></div>
  				</div>
  				<div class="col-md-6 graph-bg">
  					<h3>Hashtags Variation</h3>
  					<h5>Hashtags usage by popularity</h5>
  					<div id="column" style="height: 300px; width: 100%;"></div>
  				</div>
  			</div>

  			<h3>Content</h3>

  			<div class="col-md-12 row" style="margin-bottom : 30px">
  				<div class="col-md-6 graph-bg">
  					<h3>Frequently Post</h3>
  					<h5>Post By Type</h5>
  					<div id="pieType" style="height: 300px; width: 100%;"></div>
  				</div>
  				<div class="col-md-6 graph-bg">
  					<h3>Most Engaging Content Type</h3>
  					<h5>Average engagements by post type</h5>
  					<div id="bar" style="height: 300px; width: 100%;"></div>
  				</div>
  			</div>

  			<div class="col-md-12 mt-3 graph-bg">
  				<h4>Video's Viewer</h4>
  			 	<div id="chartViewer" style="height: 300px; width: 100%;"></div>
  			</div>

  			<div class="col-md-12 mt-3 graph-bg">
  				<h4>Post Performance</h4>
  			 	<div id="chartContainer" style="height: 300px; width: 100%;"></div>
  			</div>
  		</div>
  	</div>

</div>

<script type="text/javascript">
/* Performance post */
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
        dataPointWidth: 20,
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

/* Video viewer by date */
$(function() {

	var dataviews = [];
	$.each(<?php echo json_encode($totalvideoview, JSON_NUMERIC_CHECK);?>,function(i, data){
		dataviews.push({ 'x': new Date(data.date_posting), 'y': data.views, 'link': data.link});
		//console.log(views.date_posting);
	});

	$("#chartViewer").CanvasJSChart({ //Pass chart options
	    axisY2: {
	        title: "Total Viewer"
	    },
	    toolTip: {
	      	contentFormatter : function(e){
			  var content = "";
	          for (var i = 0; i < e.entries.length; i++){
	            content = 'Date : '+CanvasJS.formatDate(e.entries[i].dataPoint.x, "DD-MMM-YYYY")+'<br/> Total Views : '+addCommas(e.entries[i].dataPoint.y)+'<br/>(Click to visit post)';       
	          }       
	          return content;
			}
	    },
	    data: [
	      {
	      	cursor:"pointer",
	        axisYType: "secondary",
	        type: "line",
	        click : onClick,
	        dataPoints: dataviews
	      }
	    ]
	});
});

function addCommas(nStr)
{
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}

/* Frequently Post */
$(function() {
	$("#pieType").CanvasJSChart({ //Pass chart options
        data: [{
			type: "pie",
			startAngle: 240,
			//yValueFormatString: "##0.00\"%\"",
			indexLabel: "{label} {y}",
			dataPoints: [
				{y: <?php echo $piedata['image'];?>, label: "Image", color : "blue"},
				{y: <?php echo $piedata['album'];?>, label: "Album", color : "orange"},
				{y: <?php echo $piedata['video'];?>, label: "Video", color : "green"},
			]
		}]

	});
});

/* Most Engaging Content Type */
$(function(){
	$("#bar").CanvasJSChart({
		animationEnabled: true,
		axisY: {
			title: "Average Likes",
			titleFontColor: "#4F81BC",
			lineColor: "#4F81BC",
			labelFontColor: "#4F81BC",
			tickColor: "#4F81BC"
		},
		axisY2: 
		{
			title: "Average Comments",
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
		dataPointWidth: 20,
		data: [
			{
				type: "column",
				name: "Average Like",
				legendText: "Average Likes",
				showInLegend: true, 
				dataPoints:[
					{ label: "Image", y: <?php echo $avgdata['imagelike'] ;?> },
					{ label: "Album", y: <?php echo $avgdata['albumlike'] ;?> },
					{ label: "Video", y: <?php echo $avgdata['videolike'] ;?> },
				]
			},
			{
				type: "column",	
				name: "Average Comments",
				legendText: "Average Comments",
				axisYType: "secondary",
				axisYIndex: 1, //When axisYType is secondary, axisYIndex indexes to secondary Y axis & not to primary Y axis
				showInLegend: true,
				dataPoints:[
					{ label: "Image", y: <?php echo $avgdata['imagecomments'] ;?> },
					{ label: "Album", y: <?php echo $avgdata['albumcomments'] ;?> },
					{ label: "Video", y: <?php echo $avgdata['videocomments'] ;?> },
				]
			},
		]
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

/* Number of Hashtags per Post */
$(function(){

	var hashtagpost = [];
	$.each(<?php echo json_encode( $totalhashtaginpost, JSON_NUMERIC_CHECK);?>,function(i, item){
		hashtagpost.push({'y': item,'label': i});
	});

	$("#barchart").CanvasJSChart({
		animationEnabled: true,
		axisX:{
			title: "Hashtags",
			interval: 1
		},
		axisY2:{
			//interlacedColor: "rgba(1,77,101,.2)",
			//gridColor: "rgba(1,77,101,.1)",
			title: "Total Post"
		},
		data: [{
			type: "bar",
			name: "companies",
			axisYType: "secondary",
			color: "green",
			toolTipContent: "Hashtags : {y}<br/> Total Post : {label}",
			dataPoints: hashtagpost
		}]
	})
});

/* Hashtags Variation By Popularity Hashtag */
$(function() {
	$("#column").CanvasJSChart({ //Pass chart options
       animationEnabled: true,
		theme: "light2", // "light1", "light2", "dark1", "dark2"
		axisY: {
			title: "Hashtags"
		},
		axisX: {
			title: "Popularity"
		},
		dataPointWidth: 20,
		data: [{        
			type: "column",  
			showInLegend: false, 
			//legendMarkerColor: "transparent",
			//legendText: "Popularity",
			color : "blue",
			dataPoints: [      
				{ y: <?php echo $hashtagspopularity['x_popular']; ?>, label: "Extremely Popular" },
				{ y: <?php echo $hashtagspopularity['very_popular']; ?>,  label: "Very Popular" },
				{ y: <?php echo $hashtagspopularity['popular']; ?>,  label: "Popular" },
				{ y: <?php echo $hashtagspopularity['medium']; ?>,  label: "Medium" },
				{ y: <?php echo $hashtagspopularity['specific']; ?>,  label: "Specific" },
			]
		}]
	});
});

/* Average Post By week */
$(function() {
	$("#avgweek").CanvasJSChart({ //Pass chart options
       animationEnabled: true,
		theme: "light2", // "light1", "light2", "dark1", "dark2"
		axisY: {
			title: "Posts",
		},
		dataPointWidth: 30,
		data: [{        
			type: "column",  
			showInLegend: false, 
			//legendMarkerColor: "transparent",
			//legendText: "Popularity",
			color : "orange",
			dataPoints: [      
				{ y: <?php echo $totaldaypost['Mon']; ?>, label: "Mon" },
				{ y: <?php echo $totaldaypost['Tue']; ?>,  label: "Tue" },
				{ y: <?php echo $totaldaypost['Wed']; ?>,  label: "Wed" },
				{ y: <?php echo $totaldaypost['Thu']; ?>,  label: "Thu" },
				{ y: <?php echo $totaldaypost['Fri']; ?>,  label: "Fri" },
				{ y: <?php echo $totaldaypost['Sat']; ?>,  label: "Sat" },
				{ y: <?php echo $totaldaypost['Sun']; ?>,  label: "Sun" },
			]
		}]
	});
});

/* Average Post By Hour */

$(function() {
	var totalclock = [];
	var parse, hour, minute;
	$.each(<?php echo json_encode($totalclock, JSON_NUMERIC_CHECK); ?>, function( hour, total ) {
		parse = hour.split(':');
		hour = parseInt(parse[0],10);
		minute = parseInt(parse[1],10);
		totalclock.push({'x': new Date(0000,00,00,hour,minute), 'y':total});
 	});

	$("#avghour").CanvasJSChart({ //Pass chart options
       animationEnabled: true,
		theme: "light2", // "light1", "light2", "dark1", "dark2"
		axisY: {
			title: "Post"
		},
		axisX: {
			valueFormatString: "HH:mm"
		},
		toolTip: {
	      	contentFormatter : function(e){
			  var content = "";
	          for (var i = 0; i < e.entries.length; i++){
	            content = 'Hour : '+CanvasJS.formatDate(e.entries[i].dataPoint.x, "HH:mm")+'<br/> Total Post : '+e.entries[i].dataPoint.y;       
	          }       
	          return content;
			}
	     },
	    dataPointWidth: 20,
		data: [{        
			type: "column",  
			showInLegend: false, 
			//toolTipContent: "Total Post : {y}",
			//legendMarkerColor: "transparent",
			//legendText: "Popularity",
			color : "#999",
			dataPoints: totalclock,
		}]
	});
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
