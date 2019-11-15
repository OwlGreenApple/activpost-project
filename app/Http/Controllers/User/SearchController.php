<?php

namespace Celebpost\Http\Controllers\User;

use Illuminate\Http\Request;
use InstagramAPI\Instagram;
use Celebpost\caches;

class SearchController extends Controller
{

	function __construct() {
	   $this->storage = "mysql";
	   $this->dbhost = "localhost";
	   $this->dbname = "instasearch";
	   $this->dbusername = "root";
	   $this->dbpassword = "";
	   $this->login = "bungariaanastasya";
	   $this->password = "qweasdzxc123";
	}

    public function index(){
		return view('user.search-ig.index');
    }

    public function getData(Request $request){
		$query = $request->q;
		if($this->checkCache($query) == false){
			return $this->getDataAPI($query);
		} else {
		    return $this->checkCache($query);
		}
	}

	public function getDataAPI($query){
		try {
			$error_message="";
			$i = new Instagram(false,false,[
				"storage"      => $this->storage,
				"dbhost"       => $this->dbhost,
				"dbname"   	   => $this->dbname,
				"dbusername"   => $this->dbusername,
				"dbpassword"   => $this->dbpassword,
			]);	
			
			$i->login($this->login, $this->password, 300);

			
			//hashtag
			if(empty($query)){
				$data = array();
			} else {
				$hashtag = $i->hashtag->search($query)->getResults();
			}

			if(count($hashtag) > 0){
		    	$x = 0;
				foreach($hashtag as $row){
					$data['sort'][] = array('count'=>$hashtag[$x]->getMediaCount(),'name'=>$hashtag[$x]->getName());
					$x++;
				}
			}

			if(isset($data['sort'])){
				$hcount = array_column($data['sort'],'count');
				$hname = array_column($data['sort'],'name');

				array_multisort($hcount, SORT_DESC, $hname, SORT_ASC, $data['sort']);

				foreach($data['sort'] as $key => $rows){
					 $data['post'][$key] = $rows['count'];
					 $data['hashtag'][$key] = $rows['name'];
				}
			} else {
				$data['post'] = $data['hashtag'] = array();
			}

			//user
			if(empty($query)){
				$data = array();
			} else {
				$people = $i->people->search($query)->getUsers();
			}

			if(count($people) > 0){
				$p = 0;
				foreach($people as $pp){
					$data['people_username'][] = $people[$p]->getUsername();
					$data['people_image'][] = $people[$p]->getProfilePicUrl();
					$data['people_name'][] = $people[$p]->getFullName();
					$data['people_id'][] = $i->people->getUserIdForName($people[$p]->getUsername());
					$p++;
				}
			} else {
				$data['people_username'] = $data['people_image'] = $data['people_name'] = $data['people_id'] = array();
			}

			//location see model/location.php
			if(empty($query)){
				$data = array();
			} else {
				$location = $i->location->findPlaces($query)->getItems();
			}
			
			if(count($location) > 0){
				$l = 0;
				foreach($location as $loc){
					$data['location_name'][] = $location[$l]->getLocation()->getName();
					$data['location_address'][] = $location[$l]->getLocation()->getAddress();
					$data['location_pk'][] = $location[$l]->getLocation()->getPk();
					$l++;
				}
			} else {
				$data['location_name'] = $data['location_address'] = $data['location_pk'] = array();
			}

			$this->saveCache($query,$data);

			return response()->json($data);

		}  	
			catch (\InstagramAPI\Exception\IncorrectPasswordException $e) {
				//klo error password
				$error_message = $e->getMessage();
			}
			catch (\InstagramAPI\Exception\AccountDisabledException $e) {
				//klo error password
				$error_message = $e->getMessage();
			}
			catch (\InstagramAPI\Exception\CheckpointRequiredException $e) {
				//klo error email / phone verification 
				$error_message = $e->getMessage();
			}
					catch (\InstagramAPI\Exception\InstagramException $e) {
						$is_error = true;
						// if ($e->hasResponse() && $e->getResponse()->isTwoFactorRequired()) {
							// echo "2 Factor perlu dioffkan";
						// } 
						// else {
								// all other login errors would get caught here...
							echo $e->getMessage();
						// }
					}	
			catch (NotFoundException $e) {
				// echo $e->getMessage();
				echo "asd";
			}					
			catch (Exception $e) {
				$error_message = "fin ".$e->getMessage();
				if ($error_message == "InstagramAPI\Response\LoginResponse: The password you entered is incorrect. Please try again.") {
					$error_message = "fin ".$e->getMessage();
				} 
				if ( ($error_message == "InstagramAPI\Response\LoginResponse: Challenge required.") || ( substr($error_message, 0, 18) == "challenge_required") || ($error_message == "InstagramAPI\Response\TimelineFeedResponse: Challenge required.") || ($error_message == "InstagramAPI\Response\LoginResponse: Sorry, there was a problem with your request.") ){
					$error_message = "fin ".$e->getMessage();
				}
			}
		echo $error_message;
	}

	 public function checkCache($query){
		$cache = caches::where([['keyword','=',$query]])->get();
		if($cache->count() > 0){
			foreach($cache as $row){
				$getdata = $row->data;
			}
			$db = json_decode($getdata,true);

			//hashtag
			$data['hashtag'] = $db['hashtagig']['hashtag'];
			$data['post'] = $db['hashtagig']['post'];

			//user
			$data['people_username'] = $db['userig']['people_username'];
			$data['people_image'] = $db['userig']['people_image'];
			$data['people_name'] = $db['userig']['people_name'];
			$data['people_id'] = $db['userig']['people_id'];

			//place
			$data['location_name'] = $db['placeig']['location_name'];
			$data['location_address'] = $db['placeig']['location_address'];
			$data['location_pk'] = $db['placeig']['location_pk'];
			return response()->json($data);
		} else {
			return false;
		}

		/*
		$db = json_decode($cache->data,true);
		dd($db['hashtagig']['post']);
		*/
    }

    private function saveCache($query,$data){
    	//hashtag
    	$save['hashtagig']['hashtag'] = $data['hashtag'];
    	$save['hashtagig']['post'] = $data['post'];

    	//user
    	$save['userig']['people_username'] = $data['people_username'];
    	$save['userig']['people_image'] = $data['people_image'];
    	$save['userig']['people_name'] = $data['people_name'];
    	$save['userig']['people_id'] = $data['people_id'];

    	//place
    	$save['placeig']['location_name'] = $data['location_name'];
    	$save['placeig']['location_address'] = $data['location_address'];
    	$save['placeig']['location_pk'] = $data['location_pk'];

    	$json = json_encode($save);
		$caches = new caches;
		$caches->keyword = $query;
		$caches->data = $json;
		$caches->save();
    }

    /* insight */

    public function getInsight($userId)
    {
		if($this->checkCacheInsight($userId) == false){
			return $this->getDataInsight($userId);
		} else {
		    return $this->checkCacheInsight($userId);
		}
    }

    public function getDataInsight($userId)
    {

		try {	
			$error_message="";
			$i = new Instagram(false,false,[
				"storage"      => $this->storage,
				"dbhost"       => $this->dbhost,
				"dbname"   	   => $this->dbname,
				"dbusername"   => $this->dbusername,
				"dbpassword"   => $this->dbpassword,
			]);	
			
			$i->login($this->login, $this->password, 300);

			$totalpost = $i->people->getInfoById($userId)->getUser()->getMediaCount();
			$follower = $i->people->getInfoById($userId)->getUser()->getFollowerCount();

			$maxId = null;
			$today = Date('d-m-Y');
			$timeline = $i->timeline->getUserFeed($userId,$maxId);
			$nextMaxId = $timeline->getNextMaxId();
			$countTimeline = count($timeline->getItems());
			$maxid[0] = $maxId;
			$maxid[] = $nextMaxId;
			$viewVideo = $hashtagposts = $totalhours = $totalweek = $hashtag_popularity = $average = $dataPoints['Image'] = $dataPoints['Album'] = $dataPoints['Video'] = array();

			
			#get max id for pagination
			/*if($nextMaxId <> null)
			{
				for($x=0;$x<=5;$x++)
				{
					$timeline = $i->timeline->getUserFeed($userId,$nextMaxId);
					$nextMaxId = $timeline->getNextMaxId();

					if($nextMaxId <> null)
					{
						$maxid[] = $nextMaxId;
					}
					else
					{
						break;
					}
				}
			}
			*/

			if($countTimeline > 0)
			{
				#foreach($maxid as $idmax)
				#{
					#$timeline = $i->timeline->getUserFeed($userId,$idmax);

					#foreach insight

					foreach ($timeline->getItems() as $item) 
					{   

						$taken = Date('d-m-Y',$item->getTakenAt());
						$converting_date = Date('Y-m-d',strtotime($taken)); // convert to new format so that not causing 'invalid date' on javascript

						$time =  $this->datediff('ww',$taken,$today,false).'w';

						if($time > 52)
						{
							$time =  $this->datediff('yyyy',$taken,$today,false).'y';
						}


						if(is_null($item->getImageVersions2()))
						{
							$img = $item->getCarouselMedia()[0]->getImageVersions2()->getCandidates()[1]->getUrl();
						}
						else
						{
							$img = $item->getImageVersions2()->getCandidates()[1]->getUrl();
						}

						if(is_null($item->getCaption()))
						{
							$caption = null;
						}
						else
						{
							$caption = $item->getCaption()->getText();
						}

						if($caption !== null)
						{
							preg_match_all("/(#\w+)/", $caption, $hashtagpost);
							$hashtagposts[] = $hashtagpost[0];
							$hashtagperposts[$item->getPk()] = $hashtagpost[0];
						}
						
						$posts[$item->getPk()] = array(
							'profile'=> $item->getUser()->getProfilePicUrl(),
							'username' =>$item->getUser()->getUsername(),
							'fullname' =>$item->getUser()->getFullName(),
							'code' => 'https://www.instagram.com/p/'.$item->getCode().'/',
							'comments' =>$item->getCommentCount(),
							'likes' =>$item->getLikeCount(),
							'img' => $img,
							'time'=> $time,
							'caption'=>$caption
						);

						#media type
						/*
							1 = image
							2 = video / igtv
							8 = album
						*/
						$mediatype = $item->getMediaType();

						if($mediatype == 8)
						{
							$typemedia = 'Album';
						}
						else if($mediatype == 2)
						{
							$typemedia = 'Video';
							#data video view
							$viewVideo[$item->getPk()] = array(
								'views'=> $item->getViewCount(),
								'date_posting'=>$converting_date,
								'link'=>'https://www.instagram.com/p/'.$item->getCode().'/'
							);
						}
						else
						{
							$typemedia = 'Image';
						}

						#data total view for graph
						$engagement = $item->getCommentCount()+$item->getLikeCount();

						//print('<pre>'.print_r($engagement,true).' '.print_r($taken,true).'</pre>');
						$dataPoints[$typemedia][$item->getPk()] = array(
						 	"x" => $converting_date,  //date when posting created
						 	"y" => $engagement, // engagement rate
						 	"z" => $engagement,  // size of bubble
						 	"type"=> $typemedia,
						 	"image" => $img, //image of post code
						 	"link" => 'https://www.instagram.com/p/'.$item->getCode().'/', //go to post link when user click on bubble
						 	"like" => $item->getLikeCount(),
						 	"comments" => $item->getCommentCount(),
						);


						#average post by week
						$totalweek[$item->getPk()] = Date('D',$item->getTakenAt());
						$totalhours[$item->getPk()] = Date('H:00',$item->getTakenAt());

			    	}

					#end foreach insight

				 # end foreach maxid  
				#}
			} 
			else
			{
				$posts = array();
			}

			#average post by day
			$totalclock = array_count_values($totalhours);
			
			#average post by day
			$totalday = array_count_values($totalweek);

			if(!isset($totalday['Mon'])){$totalday['Mon'] = 0;}
			if(!isset($totalday['Tue'])){$totalday['Tue'] = 0;}
			if(!isset($totalday['Wed'])){$totalday['Wed'] = 0;}
			if(!isset($totalday['Thu'])){$totalday['Thu'] = 0;}
			if(!isset($totalday['Fri'])){$totalday['Fri'] = 0;}
			if(!isset($totalday['Sat'])){$totalday['Sat'] = 0;}
			if(!isset($totalday['Sun'])){$totalday['Sun'] = 0;}

			#graph data "Most Engaging Content Type"
			$datagraph = $dataPoints;

			#piegraphdata
			$piedata['image'] = count($datagraph['Image']);
			$piedata['album'] = count($datagraph['Album']);
			$piedata['video'] = count($datagraph['Video']);

			#bar column graph data

			#image like and comment
			$totalimagelike = 0;
			$totalimagecomments = 0;
			if(count($datagraph['Image']) > 0)
            {
                foreach($datagraph['Image'] as $rows)
                {
                    $totalimagelike += $rows['like'];
                    $totalimagecomments += $rows['comments'];
                }
            }
           
            $average['imagelike'] = $sc->divisionLikeComments($totalimagelike,$piedata['image']);
            $average['imagecomments'] = $sc->divisionLikeComments($totalimagecomments,$piedata['image']);

            #album like and comment
            $totalalbumlike = 0;
            $totalalbumcomments = 0;

            if(count($datagraph['Album']) > 0)
            {
                foreach($datagraph['Album'] as $rows)
                {
                    $totalalbumlike += $rows['like'];
                    $totalalbumcomments += $rows['comments'];
                }
            }

            $average['albumlike'] = $sc->divisionLikeComments($totalalbumlike,$piedata['album']);
            $average['albumcomments'] = $sc->divisionLikeComments($totalalbumcomments,$piedata['album']);

            #video like and comment
            $totalvideolike = $totalvideocomments = $totalvideoviews = 0;
            if(count($datagraph['Video']) > 0)
            {
                foreach($datagraph['Video'] as $rows)
                {
                    $totalvideolike += $rows['like'];
                    $totalvideocomments += $rows['comments'];
                }
            }

			$average['videolike'] = $this->divisionLikeComments($totalalbumlike,$piedata['video']);
			$average['videocomments'] = $this->divisionLikeComments($totalalbumcomments,$piedata['video']);

			#hashtag post
			$hashtags_temp = array();
			$count = count($hashtagposts);
			if($count > 0)
			{
				foreach($hashtagposts as $arr)
				{
					foreach ($arr as $value) {
						$hashtags_temp[] = $value;
					}
				}
				$hashtag_name = array_count_values($hashtags_temp);
			}
			else
			{
				$hashtag_name =  array();
			}

			#hashtag column
			$hashtag_per_post = $hashtag_popularity = [];
			if(count($hashtag_name) > 0)
			{
				foreach($hashtag_name as $hashtag=>$totalhashtag)
				{
					$hashtagkey = str_replace('#','',$hashtag);
					$hashtagpopularity = $i->hashtag->getInfo($hashtagkey)->getMediaCount();
					$percenthashtag = ($totalhashtag/$totalpost) * 100;
					
					$hashtags[] = array(
						'hashtagname'=>$hashtag,
						'hashtagpopularity'=>$hashtagpopularity,
						'hashtaginpost'=>$totalhashtag,
						'hashtagpercent'=> round($percenthashtag)
					);
					#hashtag by popularity
					$hashtag_popularity[] = $hashtagpopularity;
				}
			}
			else
			{
				$hashtags = array();
			}

			#for graph 'Number of Hashtags per Post

			if(count($hashtagperposts) > 0)
			{
				 foreach($hashtagperposts as $row=>$val)
	            {
	                $totalhashtagsperpost[$row] = count($val);
	            }
	            $totalhashtaginpost = array_count_values($totalhashtagsperpost);
			}
			
			#hashtag by popularity
			$arr = $hashtag_popularity;		
			$hash = $hash['specific'] = $hash['medium'] = $hash['popular'] = $hash['very_popular'] = $hash['x_popular'] = array();

	    	$hash['specific'] = array_filter($arr, function($value) {
	    		if($value < pow(10,5))
	    		{
	    			return $value;
	    		}
			});

			$hash['medium'] = array_filter($arr, function($value) {
	    		if($value >= pow(10,5) && $value < pow(10,6))
	    		{
	    			return $value;
	    		}
			});

			$hash['popular'] = array_filter($arr, function($value) {
	    		if($value >= pow(10,6) && $value < pow(10,7))
	    		{
	    			return $value;
	    		}
			});

			$hash['very_popular'] = array_filter($arr, function($value) {
	    		if($value >= pow(10,7) && $value < pow(10,8))
	    		{
	    			return $value;
	    		}
			});

			$hash['x_popular'] = array_filter($arr, function($value) {
	    		if($value > pow(10,8))
	    		{
	    			return $value;
	    		}
			});
		
			$hash['specific'] = count($hash['specific']);
			$hash['medium'] = count($hash['medium']);
			$hash['popular'] = count($hash['popular']);
			$hash['very_popular'] = count($hash['very_popular']);
			$hash['x_popular'] = count($hash['x_popular']);

			//print('<pre>'.print_r(round($percenthashtag),true).'</pre>');

			$data = array(
				'post'=>$posts,
				'hashtags'=>$hashtags,
				'graph'=>$datagraph,
				'piedata'=>$piedata, 
				'avgdata'=>$average, 
				'totalhashtaginpost'=>$totalhashtaginpost,
				'hashtagspopularity'=>$hash, 
				'totaldaypost'=>$totalday, 
				'totalclock'=>$totalclock,
				'totalvideoview' => $viewVideo
			);

			$this->saveCacheInsight($userId,$data,$nextMaxId);

			return view('user.search-ig.insightig',['data'=>$posts,'hashtags'=>$hashtags,'graph'=>$datagraph,'piedata'=>$piedata, 'avgdata'=>$average, 'totalhashtaginpost'=>$totalhashtaginpost, 'hashtagspopularity'=>$hash, 'totaldaypost'=>$totalday, 'totalclock'=>$totalclock, 'totalvideoview' => $viewVideo]);

		}  	
			catch (\InstagramAPI\Exception\IncorrectPasswordException $e) {
				//klo error password
				$error_message = $e->getMessage();
			}
			catch (\InstagramAPI\Exception\AccountDisabledException $e) {
				//klo error password
				$error_message = $e->getMessage();
			}
			catch (\InstagramAPI\Exception\CheckpointRequiredException $e) {
				//klo error email / phone verification 
				$error_message = $e->getMessage();
			}
					catch (\InstagramAPI\Exception\InstagramException $e) {
						$is_error = true;
						// if ($e->hasResponse() && $e->getResponse()->isTwoFactorRequired()) {
							// echo "2 Factor perlu dioffkan";
						// } 
						// else {
								// all other login errors would get caught here...
							echo $e->getMessage();
						// }
					}	
			catch (NotFoundException $e) {
				// echo $e->getMessage();
				echo "asd";
			}					
			catch (Exception $e) {
				$error_message = "fin ".$e->getMessage();
				if ($error_message == "InstagramAPI\Response\LoginResponse: The password you entered is incorrect. Please try again.") {
					$error_message = "fin ".$e->getMessage();
				} 
				if ( ($error_message == "InstagramAPI\Response\LoginResponse: Challenge required.") || ( substr($error_message, 0, 18) == "challenge_required") || ($error_message == "InstagramAPI\Response\TimelineFeedResponse: Challenge required.") || ($error_message == "InstagramAPI\Response\LoginResponse: Sorry, there was a problem with your request.") ){
					$error_message = "fin ".$e->getMessage();
				}
			}
		echo $error_message;
	}

	function divisionLikeComments($totalsubject,$totalpost)
	{
		$result = round($totalsubject/max($totalpost,1));
		return $result;
	}

	function datediff($interval, $datefrom, $dateto, $using_timestamps = false)
	{
	    /*
	    $interval can be:
	    yyyy - Number of full years
	    q    - Number of full quarters
	    m    - Number of full months
	    y    - Difference between day numbers
	           (eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".)
	    d    - Number of full days
	    w    - Number of full weekdays
	    ww   - Number of full weeks
	    h    - Number of full hours
	    n    - Number of full minutes
	    s    - Number of full seconds (default)
	    */

	    if (!$using_timestamps) {
	        $datefrom = strtotime($datefrom, 0);
	        $dateto   = strtotime($dateto, 0);
	    }

	    $difference        = $dateto - $datefrom; // Difference in seconds
	    $months_difference = 0;

	    switch ($interval) {
	        case 'yyyy': // Number of full years
	            $years_difference = floor($difference / 31536000);
	            if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom)+$years_difference) > $dateto) {
	                $years_difference--;
	            }

	            if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto)-($years_difference+1)) > $datefrom) {
	                $years_difference++;
	            }

	            $datediff = $years_difference;
	        break;

	        case "q": // Number of full quarters
	            $quarters_difference = floor($difference / 8035200);

	            while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($quarters_difference*3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
	                $months_difference++;
	            }

	            $quarters_difference--;
	            $datediff = $quarters_difference;
	        break;

	        case "m": // Number of full months
	            $months_difference = floor($difference / 2678400);

	            while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
	                $months_difference++;
	            }

	            $months_difference--;

	            $datediff = $months_difference;
	        break;

	        case 'y': // Difference between day numbers
	            $datediff = date("z", $dateto) - date("z", $datefrom);
	        break;

	        case "d": // Number of full days
	            $datediff = floor($difference / 86400);
	        break;

	        case "w": // Number of full weekdays
	            $days_difference  = floor($difference / 86400);
	            $weeks_difference = floor($days_difference / 7); // Complete weeks
	            $first_day        = date("w", $datefrom);
	            $days_remainder   = floor($days_difference % 7);
	            $odd_days         = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder?

	            if ($odd_days > 7) { // Sunday
	                $days_remainder--;
	            }

	            if ($odd_days > 6) { // Saturday
	                $days_remainder--;
	            }

	            $datediff = ($weeks_difference * 5) + $days_remainder;
	        break;

	        case "ww": // Number of full weeks
	            $datediff = floor($difference / 604800);
	        break;

	        case "h": // Number of full hours
	            $datediff = floor($difference / 3600);
	        break;

	        case "n": // Number of full minutes
	            $datediff = floor($difference / 60);
	        break;

	        default: // Number of full seconds (default)
	            $datediff = $difference;
	        break;
	    }

	    return $datediff;
	}

	 public function checkCacheInsight($userId)
	 {
		$cache = caches::where([['keyword','=',$userId]])->first();
		if(!is_null($cache))
		{
			$getdata = file_get_contents(storage_path('jsondata').'/'.$userId.'.json');
			$db = json_decode($getdata,true);

    		$posts = $db['post'];
	    	$hashtags = $db['hashtags'];
	    	$datagraph = $db['graph'];
			$piedata = $db['piedata']; 
			$average = $db['avgdata']; 
			$totalhashtaginpost = $db['totalhashtaginpost'];
			$hash = $db['hashtagspopularity']; 
			$totalday = $db['totaldaypost']; 
			$totalclock = $db['totalclock'];
			$viewVideo = $db['totalvideoview'];

			return view('user.search-ig.insightig',['data'=>$posts,'hashtags'=>$hashtags,'graph'=>$datagraph,'piedata'=>$piedata, 'avgdata'=>$average, 'totalhashtaginpost'=>$totalhashtaginpost, 'hashtagspopularity'=>$hash, 'totaldaypost'=>$totalday, 'totalclock'=>$totalclock, 'totalvideoview' => $viewVideo]);
		} 
		else 
		{
			return false;
		}
    }

    private function saveCacheInsight($userId,$data,$nextMaxId)
	 {

		$save = array(
			'post'=>$data['post'],
			'hashtags'=>$data['hashtags'],
			'graph'=>$data['graph'],
			'piedata'=>$data['piedata'], 
			'avgdata'=>$data['avgdata'], 
			'totalhashtaginpost'=>$data['totalhashtaginpost'],
			'hashtagspopularity'=>$data['hashtagspopularity'], 
			'totaldaypost'=>$data['totaldaypost'], 
			'totalclock'=>$data['totalclock'],
			'totalvideoview' => $data['totalvideoview']
		);

    	$json = json_encode($save);
		$caches = new caches;
		$caches->type = 1;
		$caches->keyword = $userId;
		$caches->data = 0;
		$caches->nextmaxid = $nextMaxId;
		$caches->save();

		if($caches->save() == true)
		{
			file_put_contents(storage_path('jsondata').'/'.$userId.'.json', $json);
		}

    }

    # TO SUM CLOCK WHEN USER USUALLY UPLOAD POST
    public function array_merge_numeric_values()
	{
		$arrays = func_get_args();
		$merged = array();
		foreach ($arrays as $array)
		{
			foreach ($array as $key => $value)
			{
				if ( ! is_numeric($value))
				{
					continue;
				}
				if ( ! isset($merged[$key]))
				{
					$merged[$key] = $value;
				}
				else
				{
					$merged[$key] += $value;
				}
			}
		}
		return $merged;
	}

	public function test()
	{
		$userId = 515588497;
		$getdata = file_get_contents(storage_path('jsondata').'/'.$userId.'.json');
		$db = json_decode($getdata,true);

		$a2 = array('text'=>'aaaa','rubber'=>'bbbbb');
		$source = array(
			'2176176520861881664'=> array
				 (
				 'profile' => 'https://instagram.fsub8-1.fna.fbcdn.net/vp/8f6750afa2146895c714071c51abfd50/5E57F4BA/t51.2885-19/s150x150/12093436_159206814427747_546960802_a.jpg?_nc_ht=instagram.fsub8-1.fna.fbcdn.net&_nc_cat=107',
				 'username' => 'bungariaanastasya',
				 'fullname' => 'anastasya',
				 'code' => 'https://www.instagram.com/p/B4zVQQOhJVA/',
				 'comments' => 0,
				 'likes' => 4,
				 'img' => 'https://instagram.fsub8-1.fna.fbcdn.net/vp/d859e411c2661308049ac9c0b8004d27/5E8806B1/t51.2885-15/e15/s480x480/75590889_751700708632733_368283279574312339_n.jpg?_nc_ht=instagram.fsub8-1.fna.fbcdn.net&_nc_cat=105&ig_cache_key=MjE3NjE3NjUyMDg2MTg4MTY2NA%3D%3D.2',
				 'time' => '0w',
				 'caption' => 'testupload',
				  ),
			 '2176119684376676241' => array
                (
                    'profile' => 'https://instagram.fsub8-1.fna.fbcdn.net/vp/8f6750afa2146895c714071c51abfd50/5E57F4BA/t51.2885-19/s150x150/12093436_159206814427747_546960802_a.jpg?_nc_ht=instagram.fsub8-1.fna.fbcdn.net&_nc_cat=107',
                    'username' => 'bungariaanastasya',
                    'fullname' => 'anastasya',
                    'code' => 'https://www.instagram.com/p/B4zIVLIAceR/',
                    'comments' => 10,
                    'likes' => 7,
                    'img' => 'https://instagram.fsub8-1.fna.fbcdn.net/vp/7a29bd0e92fac046d5f17cdf68a67b42/5E4E989D/t51.2885-15/e15/s480x480/73387332_504043800185866_2802116812744979681_n.jpg?_nc_ht=instagram.fsub8-1.fna.fbcdn.net&_nc_cat=110&ig_cache_key=MjE3NjExOTY4NDM3NjY3NjI0MQ%3D%3D.2',
                    'time' => '0w',
                    'caption' => 'testaaaa'
                )
		); 
              
		#$arrmerge['post'] = array_merge($db['post'],$a2);
		#$json = json_encode($a2);
		#file_put_contents(storage_path('jsondata').'/'.$userId.'.json', $json);

		 $a1 = array(
	         "a" => 2
	        ,"b" => 0
	        ,"c" => 5
	    );

	    $a2 = array(
	         "a" => 3
	        ,"b" => 9
	        ,"c" => 7
	        ,"d" => 10
	    );


	    $result = $this->array_merge_numeric_values($a1,$a2);
		$db['post'] = $source + $db['post'];
		dd($db['hashtagspopularity']);
		//print('<pre>'.print_r($db['post'],true).'</pre>');
    }

    

/* end search controller */
}
