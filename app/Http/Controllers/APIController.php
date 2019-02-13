<?php

namespace Celebpost\Http\Controllers;

use Illuminate\Http\Request as req;
use Celebpost\Models\Schedule;
use Celebpost\Models\Proxies;
use Celebpost\Models\ScheduleAccount;
use Celebpost\Models\Users;
use Celebpost\Models\UserLog;
use Celebpost\Models\Account;
use Celebpost\Jobs\PostTask;

use \InstagramAPI\Instagram;
use Carbon\Carbon;

use Config,Crypt,Log;

class APIController extends Controller
{
	/*
	API for help automation to do post task 
	*/
	public function post_ig(req $request){
    ini_set('memory_limit', '-1');
		//init 
		$check_sa = ScheduleAccount::find($request->schedule_account_id);
		$account = Account::find($request->account_id);
		$user = Users::find($request->user_id);
		$sc = Schedule::find($request->schedule_id);
		
		if (!is_null($account)) {
			if ( (!$account->is_started) || (!$account->is_active) ) {
				return "account not started or not active ";
			}
			if (!is_null($account->last_post)) {
				$dt = Carbon::now();
				$last_post = Carbon::parse($account->last_post);
				if ( ($last_post->diffInSeconds($dt) <= 120 ) && (strpos($sc->slug, 'StoryFile')<>0) ) {
					return "last post below 120 second";
				}
				if ( ($last_post->diffInSeconds($dt) <= 30 ) && (strpos($sc->slug, 'StoryFile')===0) ) {
					return "last post for story below 30 second";
				}
			}
			if ($account->is_error) {
				return "account error";
			}
			if (!is_null($check_sa)){
				if ($check_sa->status_process <> 0) {
					return "account processed";
				}
				if ($check_sa->status < 2) {
					//start
					$check_sa->status_process = 1;
					$check_sa->save();
					
					// Decrypt
					$decrypted_string = Crypt::decrypt($account->password);
					$pieces = explode(" ~space~ ", $decrypted_string);
					$pass = $pieces[0];
					
					$username = $account->username;
					$password = $pass;
					// $dir = public_path('images/uploads/'.$user->username.'-'.$user->id); 
					// $dir = base_path('../public_html/dashboard/images/uploads/'.$user->username.'-'.$user->id); 
					$dir = base_path('../public_html/vp/uploads/'.$user->username.'-'.$user->id); 
					if($sc->media_type=='video' || strpos($sc->slug, 'StoryFile')===0){
						$photo = $dir."/".$sc->slug;
					} else {
						$photo = $dir."/".$sc->slug.".jpg";
					}
					
					$caption = $sc->description;
					
					$i = new Instagram(true,true,[
						"storage"       => "mysql",
						"dbhost"       => Config::get('database.connections.mysql_celebgramme.host'),
						"dbname"   => Config::get('database.connections.mysql_celebgramme.database'),
						"dbusername"   => Config::get('database.connections.mysql_celebgramme.username'),
						"dbpassword"   => Config::get('database.connections.mysql_celebgramme.password'),
					]);
					
					//get proxy 
					$proxy = Proxies::find($account->proxy_id);
					
					// Login
					$is_error = 0 ;
					try {
						// Check Login
						if (!is_null($proxy)) {
							if($proxy->cred==""){
								$i->setProxy("http://".$proxy->proxy.":".$proxy->port);
							}
							else {
								$i->setProxy("http://".$proxy->cred."@".$proxy->proxy.":".$proxy->port);
							}
						}
						$i->login($username, $password, 300);

						// $logs = $sc->slug.'-'.$sc->media_type.", Login akun\n";
						// fwrite($myfile, $logs);
					} 
					catch (\InstagramAPI\Exception\IncorrectPasswordException $e) {
						$is_error = 1 ;
						$update_account = Account::find($account->id);
						$update_account->is_error = 1;
						$update_account->save();
						$smsg = $e->getMessage();
            Log::alert("IncorrectPasswordException ".$smsg);
						
						$subject_message = "[Activpost] Notif Post Failed";
						$emaildata = [
							"smsg" => $smsg,
							"fullname" => $user->name,
							"account_username" => $username,
							"error_message" => $e->getMessage(),
						];
						Mail::send('emails.notify-user-error', $emaildata, function ($message) use ($subject_message,$user) {
							$message->from('no-reply@activpost.net', 'Activpost');
							$message->to($user->email);
							$message->bcc("celebgramme.dev@gmail.com");
							$message->subject($subject_message);
						});									
					}
					catch (Exception $e) {
						$dt = Carbon::now();
						$dir = base_path().'/storage/error-log/'.$username; 
						if (!file_exists($dir)) {
							mkdir($dir,0755,true);
						}
						$file = $dir.'/error-1.txt';
						if (!file_exists($file)) {
							$str = "";
						} else {
							$str = file_get_contents($file);
						}
						$str .= $photo."|".$e->getMessage()."|".$e->getResponse()->printJson()."|".$dt->toDateTimeString().";";
						file_put_contents($file, $str);
						
						$is_error = 1 ;
						$smsg = $e->getMessage();
						if ( (strpos($e->getMessage(), 'Network: CURL error') !== false) || (strpos($e->getMessage(), 'No response from server') !== false) || (strpos($e->getMessage(), 'BootstrapUsersResponse') !== false) ) {
							
							//supaya diproses lagi
							if ($check_sa->counter_error <= 2 ) {
								$check_sa->counter_error += 1;
								$check_sa->status_process = 0;
								$check_sa->save();
							}
							Log::alert($username." ".$smsg);
              
							// continue;
							return "Error Login ".$smsg;
						}
						$smsg .= " Line: ".$e->getTraceAsString(); // this prints the line where the error occurs
						Log::alert($username." ".$smsg);
					}
					catch (\InstagramAPI\Exception\BadRequestException $e) {
						//supaya diproses lagi
						if ($check_sa->counter_error <= 2 ) {
							$check_sa->counter_error += 1;
							$check_sa->status_process = 0;
							$check_sa->save();
						}
						
            Log::alert($username." BadRequestException");
						// continue;
						return "Error Bad request(login)";
					}
					if ($is_error) {
							$check_sa->status = 5;
							$check_sa->media_id = $smsg;
							$check_sa->save();
						
						if (!$account->is_post_berurutan) {
								$check_sa->status_helper = 5;
								$check_sa->media_id = $smsg." asd";
								$check_sa->save();
						}
						
						//Notif Kalo Post schedule failed
						$subject_message = "[Activpost] Notif Post Failed";
						$emaildata = [
							"smsg" => $smsg,
							"fullname" => $user->name,
							"account_username" => $username,
							"error_message" => $e->getMessage(),
						];
						Mail::send('emails.notify-user-error', $emaildata, function ($message) use ($subject_message,$user) {
							$message->from('no-reply@activpost.net', 'Activpost');
							$message->to($user->email);
							$message->bcc("celebgramme.dev@gmail.com");
							$message->subject($subject_message);
						});
						
						// continue;
						return "Error ".$smsg." (login)";
					}
					// Upload
					try {
						if ($sc->media_type == "photo") {
							$caption = str_replace("\r\n", "\n", $caption);
							
							if(strpos($sc->slug, 'StoryFile')===0){
								// $logs = $sc->slug.'-'.$sc->media_type.", Pra posting story foto\n";
								// fwrite($myfile, $logs);

                $photoVar = new \InstagramAPI\Media\Photo\InstagramPhoto($photo, ['targetFeed' => \InstagramAPI\Constants::FEED_STORY]);
								$instagram = $i->story->uploadPhoto($photoVar->getFile(), ['caption' => $caption]);

								// $logs = $sc->slug.'-'.$sc->media_type.", Posting Story foto\n";
								// fwrite($myfile, $logs);
							} else {
								// $logs = $sc->slug.'-'.$sc->media_type.", Pra posting\n";
								// fwrite($myfile, $logs);

                $photoVar = new \InstagramAPI\Media\Photo\InstagramPhoto($photo, ['targetFeed' => \InstagramAPI\Constants::FEED_TIMELINE]);
								$instagram = $i->timeline->uploadPhoto($photoVar->getFile(), ['caption' => $caption]);  

								// $logs = $sc->slug.'-'.$sc->media_type.", Posting foto\n";
								// fwrite($myfile, $logs);
							}
							
							//update last post 
							$dt = Carbon::now();
							$update_account = Account::find($account->id);
							$update_account->last_post = strtotime($dt->toDateTimeString());
							$update_account->save();
						} 
						else if ($sc->media_type == "video") {
							// $i->uploadVideo($photo, $caption);
							$caption = str_replace("\r\n", "\n", $caption);
							
							if(strpos($sc->slug, 'StoryFile')===0){
								// $logs = $sc->slug.'-'.$sc->media_type.", Pra posting\n";
								// fwrite($myfile, $logs);

                $photoVar = new \InstagramAPI\Media\Video\InstagramVideo($photo, ['targetFeed' => \InstagramAPI\Constants::FEED_STORY]);
								$instagram = $i->story->uploadVideo($photoVar->getFile(), ['caption' => $caption]);

								// $logs = $sc->slug.'-'.$sc->media_type.", Posting story video\n";
								// fwrite($myfile, $logs);
							} else {
								// $logs = $sc->slug.'-'.$sc->media_type.", Pra posting\n";
								// fwrite($myfile, $logs);
								
                $photoVar = new \InstagramAPI\Media\Video\InstagramVideo($photo, ['targetFeed' => \InstagramAPI\Constants::FEED_TIMELINE]);
								$instagram = $i->timeline->uploadVideo($photoVar->getFile(), ['caption' => $caption, 'thumbnail_timestamp' => $sc->thumbnail_video]);

								// $logs = $sc->slug.'-'.$sc->media_type.", Posting video\n";
								// fwrite($myfile, $logs);
							}
							
							//update last post 
							$dt = Carbon::now();
							$update_account = Account::find($account->id);
							$update_account->last_post = strtotime($dt->toDateTimeString());
							$update_account->save();

							// $logs = $sc->slug.'-'.$sc->media_type.", Pasca posting\n";
							// fwrite($myfile, $logs);
						}
					} 
					catch (Exception $e) {
						$smsg = $e->getMessage();
						$dt = Carbon::now();
						$dir = base_path().'/storage/error-log/'.$username; 
						if (!file_exists($dir)) {
							mkdir($dir,0755,true);
						}
						$file = $dir.'/error-2.txt';
						if (!file_exists($file)) {
							$str = "";
						} else {
							$str = file_get_contents($file);
						}
						$str .= $e->getMessage()."|".$e->getResponse()->printJson()."|".$dt->toDateTimeString().";";
						file_put_contents($file, $str);
							
						if ( (strpos($e->getMessage(), 'Network: CURL error') !== false) || (strpos($e->getMessage(), 'No response from server') !== false) || (strpos($e->getMessage(), 'BootstrapUsersResponse') !== false) ) {
							//supaya diproses lagi
							if ($check_sa->counter_error <= 2 ) {
								$check_sa->counter_error += 1;
								$check_sa->status_process = 0;
								$check_sa->save();
							}
							
              Log::alert($username." ".$smsg." (posting)");
							// continue;
							return "Error ".$smsg."(posting)";
						}
						$smsg .= " Line: ".$e->getTraceAsString(); // this prints the line where the error occurs
						
						ob_start();
						$result = ob_get_clean();    
						$smsg .= " ".$result;
						
						$check_sa->status = 5;
						$check_sa->media_id = $smsg;
						$check_sa->save();
						
						if (!$account->is_post_berurutan) {
							$check_sa->status_helper = 5;
							$check_sa->save();
						}
						
						//Notif Kalo Post schedule failed
						$subject_message = "[Activpost] Notif Post Failed";
						$emaildata = [
							"smsg" => $smsg,
							"fullname" => $user->name,
							"account_username" => $username,
							"error_message" => $e->getMessage(),
						];
						Mail::send('emails.notify-user-error', $emaildata, function ($message) use ($subject_message,$user) {
							$message->from('no-reply@activpost.net', 'Activpost');
							$message->to($user->email);
							$message->bcc("celebgramme.dev@gmail.com");
							$message->subject($subject_message);
						});
						
						// continue;
						return "Error ".$smsg."(posting)";
					}
					catch (\InstagramAPI\Exception\BadRequestException $e) {
						//supaya diproses lagi
						if ($check_sa->counter_error <= 2 ) {
							$check_sa->counter_error += 1;
							$check_sa->status_process = 0;
							$check_sa->save();
						}
            
            Log::alert($username." BadRequestException (posting)");
						
						// continue;
						return "Error  badrequest (posting)";
					}
				
					$dt = Carbon::now();
					$check_sa->published_time = strtotime($dt->toDateTimeString());
					$check_sa->status = 2;
					$check_sa->status_helper = 2;
					$check_sa->media_id = $instagram->getMedia()->getId();
					$check_sa->save();
					
					
					
					//last step
					//klo uda finish semua tiap accountnya, schedule status diganti 2 
					$check_sa = ScheduleAccount::where("schedule_id","=",$sc->id)->get();
					$flag = true;
					foreach($check_sa as $data) {
						if ($data->status <> 2 ) {
							$flag = false;
						}
					}
					if ($flag) {
						$update_schedule = Schedule::find($sc->id);
						$update_schedule->status = 2;
						$update_schedule->save();
						
						if(($update_schedule->media_type=='video') || (strpos($update_schedule->slug, 'StoryFile')===0 && ($update_schedule->media_type == "photo"))){
							$photo = $dir."/".$update_schedule->slug;
							unlink($photo);
						}
					}
					
					if (!$account->is_post_berurutan) {
						//klo ada error salah satu accountnya, schedule status diganti 0 
						$flag = false;
						foreach($check_sa as $data) {
							if ($data->status == 5 ) {
								$flag = true;
							}
						}
						if ($flag) {
							$update_schedule = Schedule::find($sc->id);
							$update_schedule->status = 0; //supaya keluar dischedule list
							$update_schedule->save();
						}
					}
					//end

					
				}
			}
		}

		
	}

	/*
	API for help automation to do delete task 
	*/
	public function delete_post_ig(req $request){
		//init 
		// $account = Account::find($request->account_id);
		$user = Users::find($request->user_id);
		$sc = Schedule::find($request->schedule_id);
		$account = Account::find($request->account_id);
		$account_pivot_status = $request->account_pivot_status;
		if ( (!$account->is_started) || (!$account->is_active) ) {
			return "account not started or not active ";
		}
		$dt = Carbon::now();
		$last_post = Carbon::parse($account->last_post);
		if ($last_post->diffInSeconds($dt) <= 120 ) {
			return "last post below 120 second";
		}
		if ($account->is_error) {
			return "account error";
		}
		if ($account_pivot_status < 3) {
			// Decrypt
			$decrypted_string = Crypt::decrypt($account->password);
			$pieces = explode(" ~space~ ", $decrypted_string);
			$pass = $pieces[0];
			
			$username = $account->username;
			$password = $pass;
			$photo = $sc->image;
			$caption = $sc->description;
			$i = new Instagram(false,false,[
				"storage"       => "mysql",
				"dbhost"       => Config::get('database.connections.mysql_celebgramme.host'),
				"dbname"   => Config::get('database.connections.mysql_celebgramme.database'),
				"dbusername"   => Config::get('database.connections.mysql_celebgramme.username'),
				"dbpassword"   => Config::get('database.connections.mysql_celebgramme.password'),
			]);
			
			//get proxy 
			$proxy = Proxies::find($account->proxy_id);
			
			// Login
			$is_error = 0 ;
			try {
				if (!is_null($proxy)) {
					if($proxy->cred==""){
						$i->setProxy("http://".$proxy->proxy.":".$proxy->port);
					}
					else {
						$i->setProxy("http://".$proxy->cred."@".$proxy->proxy.":".$proxy->port);
					}
				}
				// $i->setUser($username, $password);
				$i->login($username, $password, 300);
			} 
			catch (\InstagramAPI\Exception\IncorrectPasswordException $e) {
				$is_error = 1 ;
				$update_account = Account::find($account->id);
				$update_account->is_error = 1;
				$update_account->save();
				$smsg = $e->getMessage();
			}
			catch (Exception $e) {
				$is_error = 1 ;
				$smsg = $e->getMessage();
				if ( (strpos($e->getMessage(), 'Network: CURL error') !== false) || (strpos($e->getMessage(), 'No response from server') !== false) ) {
					return "error ".$smsg." (login)";
				}
				$smsg .= " Line: ".$e->getTraceAsString(); // this prints the line where the error occurs                  
				
			}
			if ($is_error) {
				// ob_start();
				// var_dump($e);
				// $result = ob_get_clean();    
				// $smsg .= " ".$result;
					
				$userlog = new UserLog;
				$userlog->user_id = $sc->user_id;
				$userlog->description = "Error Cron Posting (Login Delete Schedule), instagram account=".$username." detail=".$smsg;
				$userlog->admin_id = 0;
				$userlog->save();
				
				return "error ".$smsg." (login)";
			}
			$is_error = 0;
			// Upload
			try {
				$media_id = "";
				$sched_account = ScheduleAccount::where("schedule_id","=",$sc->id)
													->where("account_id",$account->id)
													->first();
				if (!is_null($sched_account)) {
					$media_id = $sched_account->media_id;
				}
				
				//update last post 
				$dt = Carbon::now();
				$update_account = Account::find($account->id);
				$update_account->last_post = strtotime($dt->toDateTimeString());
				$update_account->save();
				
				$i->media->delete($media_id);
			} catch (Exception $e) {
				$smsg = $e->getMessage();
				if ( (strpos($e->getMessage(), 'Network: CURL error') !== false) || (strpos($e->getMessage(), 'No response from server') !== false) ) {
					return "error ".$smsg." (delete)";
				}
				$smsg .= " Line: ".$e->getTraceAsString(); // this prints the line where the error occurs                  
				
					// ob_start();
					// var_dump($e);
					// $result = ob_get_clean();    
					// $smsg .= " ".$result;
					
				//biar ga error dikasi ini dulu
				$sc->accounts()->syncWithoutDetaching([
					$account->id => [
						'status' => 3,
					]
				]);
				
				$userlog = new UserLog;
				$userlog->user_id = $sc->user_id;
				$userlog->description = "Error Cron Posting (Deleting Delete Schedule), instagram account=".$username." schedule id = ".$sc->id." detail=".$smsg;
				$userlog->admin_id = 0;
				$userlog->save();
				
				return "error ".$smsg." (delete)";
			}
			
			$dt = Carbon::now();
			$sc->accounts()->syncWithoutDetaching([
				$account->id => [
					'status' => 3,
				]
			]);
			$sa = ScheduleAccount::where("account_id","=",$account->id)
						->where("schedule_id","=",$sc->id)
						->first();
			if (!is_null($sa)){
				$sa->deleted_time = strtotime($dt->toDateTimeString());
				$sa->save();
			}



			//last step
			//klo uda finish semua tiap accountnya, schedule status diganti 3 
			$check_sa = ScheduleAccount::where("schedule_id","=",$sc->id)->get();
			$flag = true;
			foreach($check_sa as $data) {
				if ($data->status < 3 ) {
					$flag = false;
				}
			}
			if ($flag) {
				$update_schedule = Schedule::find($sc->id);
				$update_schedule->status = 3;
				$update_schedule->save();
			}
		}
	}
}
