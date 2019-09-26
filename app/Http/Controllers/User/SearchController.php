<?php

namespace Celebpost\Http\Controllers\User;

use Illuminate\Http\Request;
use InstagramAPI\Instagram;
use Celebpost\caches;

class SearchController extends Controller
{

	public function getHashtag(Request $request){
		$query = $request->q;
		$type = 'hashtag';
		
		if($this->checkCache($query,$type) == false){
			return $this->getHashtagAPI($query,$type);;
		} else {
		    return $this->checkCache($query,$type);
		}
	}

	public function getHashtagAPI($query,$type){
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
				exit();
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

				//dd($data['sort']);

				foreach($data['sort'] as $key => $rows){
					 $data['post'][$key] = $rows['count'];
					 $data['hashtag'][$key] = $rows['name'];
				}
			} else {
				$data = array();
			}

			$this->saveCache($query,$type,$data);

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

	public function getUser(Request $request){
		$query = $request->q;
		$type = 'user';

		if($this->checkCache($query,$type) == false){
			return $this->getUserAPI($query,$type);
		} else {
		    return $this->checkCache($query,$type);
		}
	}
	
	public function getUserAPI($query,$type){
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

			//user
			if(empty($query)){
				exit();
			} else {
				$people = $i->people->search($query)->getUsers();
			}

			if(count($people) > 0){
				$p = 0;
				foreach($people as $pp){
					$data['people_username'][] = $people[$p]->getUsername();
					$data['people_image'][] = $people[$p]->getProfilePicUrl();
					$data['people_name'][] = $people[$p]->getFullName();
					$p++;
				}
			} else {
				$data = array();
			}

			$this->saveCache($query,$type,$data);

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

	public function getPlace(Request $request){
		$query = $request->q;
		$type = 'place';

		if($this->checkCache($query,$type) == false){
			return $this->getPlaceAPI($query,$type);
		} else {
		    return $this->checkCache($query,$type);
		}
		
	}

	public function getPlaceAPI($query,$type){
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

			//location see model/location.php
			if(empty($query)){
				exit();
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

			$this->saveCache($query,$type,$data);

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

    public function index(){
		return view('user.search-ig.index');
    }

    /* Test */

    public function getData(Request $request){
		$query = $request->q;
		return $this->getDataAPI($query);
		
		/*if($this->checkCacheTest($query) == false){
			return $this->getDataAPI($query);
		} else {
		    return $this->checkCacheTest($query);
		}*/
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

			$this->saveCacheTest($query,$data);

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

	 public function checkCacheTest(){
		$cache = caches::select('data')->where('id','=',1)->first();
		dd($cache->data);
    }

    public function saveCacheTest($query,$data){
    	//convert json to string
		$json_save = json_encode($data,JSON_FORCE_OBJECT );
		$caches = new caches;
		$caches->type = 'nodata';
		$caches->keyword = $query;
		$caches->data = $json_save;
		$caches->save();
    }

    /* end Test */

    public function checkCache($query,$type){
    	$cache = 
		$cache = caches::where([['keyword','=',$query],['type','=',$type]])->get();
		if($cache->count() > 0){
			foreach($cache as $row){
				$data = $row->data;
			}
			return $data;
		} else {
			return false;
		}
		//$arr = explode(" ",$query);
		//$arr = array_slice($arr,0,5);
		/*foreach($arr as $rows){
			$caches[] = caches::where('keyword','=',$rows)
		->orWhere('keyword', 'LIKE', '%'.$rows.'%')->get();
		}*/

		/*foreach($caches as $col)
		{
			$filter[] = $col->count();
		}*/

		//$count = count(array_keys($filter, 1)); //count how many that contain count() that return 1

    }

    public function saveCache($query,$type,$data){
    	//convert json to string
		$json_save = json_encode($data,JSON_FORCE_OBJECT );
		$caches = new caches;
		$caches->type = $type;
		$caches->keyword = $query;
		$caches->data = $json_save;
		$caches->save();
    }
}
