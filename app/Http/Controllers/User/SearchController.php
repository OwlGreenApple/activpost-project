<?php

namespace Celebpost\Http\Controllers\User;

use Illuminate\Http\Request;
use InstagramAPI\Instagram;

class SearchController extends Controller
{

	public function getHashtag(Request $request){
		$query = $request->q;

		if(empty($query)){
			$query = '';
		} 
		
		try {
			$error_message="";
			$i = new Instagram(false,false,[
				"storage"      => "mysql",
				"dbhost"       => "localhost",
				"dbname"   	   => "instasearch",
				"dbusername"   => "root",
				"dbpassword"   =>  "",
			]);	
			
					// JANGAN LUPA DILOGIN TERLEBIH DAHULU
					// $i->setProxy('http://208.115.112.100:9999');
					
					$i->login("bungariaanastasya", "qweasdzxc123", 300);

					//user
					$people = $i->people->search('thekingofrandom')->getUsers();
					$p = 0;
					foreach($people as $pp){
						$data['people_username'][] = $people[$p]->getUsername();
						$p++;
					}

					//hashtag
					$hashtag = $i->hashtag->search('surabaya')->getResults();
			    	$x = 0;
					foreach($hashtag as $row){
						$data['hashtag'][] = $hashtag[$x]->getName();
						$data['post'][] = $hashtag[$x]->getMediaCount();
						$x++;
					}

					dd($hashtag);

					die('');

					//location see model/location.php
					$location = $i->location->findPlaces($query)->getItems();
					$l = 0;
					foreach($location as $loc){
						$data['location_name'][] = $location[$l]->getLocation()->getName();
						$data['location_address'][] = $location[$l]->getLocation()->getAddress();
						$data['location_pk'][] = $location[$l]->getLocation()->getPk();
						$l++;
					}
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
}
