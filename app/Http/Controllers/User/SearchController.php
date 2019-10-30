<?php

namespace Celebpost\Http\Controllers\User;

use Illuminate\Http\Request;
use InstagramAPI\Instagram;
use Celebpost\caches;

class SearchController extends Controller
{

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
				"storage"      => "mysql",
				"dbhost"       => "localhost",
				"dbname"   	   => "instasearch",
				"dbusername"   => "root",
				"dbpassword"   =>  "",
			]);	
			
			$i->login("bungariaanastasya", "qweasdzxc123", 300);

			
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
				$data = array();
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
				$data = array();
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
				$data = array();
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
				"storage"      => "mysql",
				"dbhost"       => "localhost",
				"dbname"   	   => "instasearch",
				"dbusername"   => "root",
				"dbpassword"   =>  "",
			]);	

			$i->login("bungariaanastasya", "qweasdzxc123", 300);

			$totalpost = $i->people->getInfoById($userId)->getUser()->getMediaCount();
			$follower = $i->people->getInfoById($userId)->getUser()->getFollowerCount();

			$maxId = null;
			$today = Date('d-m-Y');
			$timeline = $i->timeline->getUserFeed($userId,$maxId);
			$nextMaxId = $timeline->getNextMaxId();
			$countTimeline = count($timeline->getItems());
			$maxid[0] = $maxId;
			$maxid[] = $nextMaxId;

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
						}
						else
						{
							$hashtagposts[] = array();
						}
				
						$posts[] = array(
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

						#data for graph

						$division = $follower * 20;
						$engagement = ($item->getCommentCount()+$item->getLikeCount()) / $division;

						//print('<pre>'.print_r($engagement,true).' '.print_r($taken,true).'</pre>');
						$dataPoints[] = array(
						 	"x" => $taken,  //date when posting created
						 	"y" => $engagement, // engagement rate
						 	"z" => 10,  	// size of bubble
						 	"image" => $img, //image of post code
						 	"link" => 'https://www.instagram.com/p/'.$item->getCode().'/', //go to post link when user click on bubble
						);

			    	}

					#end foreach insight

				 # end foreach maxid  
				#}
			} 
			else
			{
				$posts = array();
			}

			#graph data
			$datagraph = json_encode($dataPoints,JSON_NUMERIC_CHECK);

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
				}
			}
			else
			{
				$hashtags = array();
			}
			
			//print('<pre>'.print_r(round($percenthashtag),true).'</pre>');
			//die('');

			$data['post'] = $posts;
			$data['hashtags'] = $hashtags;
			$this->saveCacheInsight($userId,$data);

			return view('user.search-ig.insightig',['data'=>$posts,'hashtags'=>$hashtags,'graph'=>$datagraph]);

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

	 public function checkCacheInsight($userId){
		$cache = caches::where([['keyword','=',$userId]])->first();
		if(!is_null($cache)){
			$getdata = $cache->data;
			$db = json_decode($getdata,true);

			//post
    		$posts = $db['postinsight'];

	    	//hashtag
	    	$hashtags = $db['hashtaginsight'];

			return view('user.search-ig.insightig',['data'=>$posts,'hashtags'=>$hashtags]);
		} else {
			return false;
		}
    }

	 private function saveCacheInsight($userId,$data)
	 {
    	//post
    	$save['postinsight'] = $data['post'];

    	//hashtag
    	$save['hashtaginsight'] = $data['hashtags'];

    	$json = json_encode($save);
		$caches = new caches;
		$caches->keyword = $userId;
		$caches->data = $json;
		$caches->save();
    }


/* end search controller */
}
