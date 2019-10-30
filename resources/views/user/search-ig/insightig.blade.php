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
  		<div id="igplace" class="col-md-12">
  			 <div id="chartContainer" style="height: 300px; width: 100%;"></div>
  		</div>
  	</div>

</div>

<script type="text/javascript">
$(function () {
	$("#chartContainer").CanvasJSChart({ //Pass chart options
		title:{
       		text: ""
      	},
	  	toolTip:{
	       backgroundColor: "rgba(0,0,0,.8)",
	       fontColor: "yellow",
	    },
	    legend: {
           cursor:"pointer",
       	   horizontalAlign: "left",
           verticalAlign: "bottom",
           fontSize: 15,
	       itemclick: function(e){
	          alert( "Legend item clicked with type : " + e.dataSeries.type );
	       }
     	},
     	axisX:{      
            valueFormatString: "DD-MMM-YYYY" ,
            labelAngle: -50
        },
		data: [
			{
			cursor:"pointer",
			color: "LightSeaGreen",
			type: "bubble", //change it to column, spline, line, pie, etc
			name: "series1",
        	legendText: "Apples",
	        showInLegend: true,
	        legendMarkerType: "circle",
	 		toolTipContent: "<strong>{name}</strong> <br/> Fetility Rate: {y}<br/> Life Expectancy: {x} yrs<br/> Population: {z} mn",
			dataPoints: {!! $graph !!}
			}
			/*,{
			color: "orange",
			type: "bubble", //change it to column, spline, line, pie, etc
			name: "series2",
        	legendText: "orange",
	        showInLegend: true,
	        legendMarkerType: "circle",
	 		toolTipContent: "<strong>{name}</strong> <br/> Fetility Rate: {y}<br/> Life Expectancy: {x} yrs<br/> Population: {z} mn",
			dataPoints: [
			     { x: 64.8, y: 2.66, z:50, name: "India", click : onClick},
			     { x: 73.1, y: 1.61, z:50, name: "China"},
			     { x: 78.1, y: 2.00, z:50, name: "US" },
			     { x: 72.5, y: 1.86, z:50, name: "Brazil"},
			     { x: 76.5, y: 2.36, z:50, name: "Mexico"},
			     { x: 82.9, y: 1.37, z:50, name: "Japan" },
			     { x: 79.8, y: 1.36, z:50, name:"Australia" },
			     { x: 72.7, y: 2.78, z:50, name: "Egypt"},
			     { x: 80.1, y: 1.94, z:50, name:"UK" },
			     { x: 81.5, y: 1.93, z:50, name:"Australia" },
		     ]
			}
			*/
		]
	});

});

function onClick(e) {
	alert(  e.dataSeries.type + ", dataPoint { x:" + e.dataPoint.x + ", y: "+ e.dataPoint.y + " }" );
}

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
