<?php

namespace Celebpost\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Foundation\Bus\DispatchesJobs;

use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

use Celebpost\Models\Schedule;
use Celebpost\Models\Proxies;
use Celebpost\Models\ScheduleAccount;
use Celebpost\Models\Users;
use Celebpost\Models\UserLog;
use Celebpost\Models\Account;
use Celebpost\Models\UserMeta;

// use Celebpost\Jobs\PostingTask;

use \InstagramAPI\Instagram;
use Exception,Mail,Config,DB;

class PostInstagram extends Command
{
		use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:instagram';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Post to Instagram';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
			parent::__construct();
			$this->waktu = Carbon::now()->setTimezone(''.env('IG_TIMEZONE').'');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
			/*
			*
			*	SCHEDULE HANDLER
			* status_helper -> untuk membantu pengecekan post harus berurutan
			* status_process -> untuk membantu pengecekan klo post tersebut sudah di process(biar ga di process berulang kali)
			*
			*/
      $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
			$scs = Schedule::
							join("schedule_account","schedules.id","=","schedule_account.schedule_id")
							// ->join("accounts","accounts.id","=","schedule_account.account_id")
							->select("schedules.*",'schedule_account.account_id',DB::raw('schedule_account.id as said'))
							->where('schedule_account.status_helper',0)
							->where('schedule_account.status_process',0)
							// ->where( 'schedules.publish_at', '<=', $this->waktu->toDateTimeString() )
							// ->whereDate('schedules.publish_at','>=',$this->waktu->toDateString())
							->whereDate('schedules.publish_at',$this->waktu->format('Y-m-d'))
							->whereTime('schedules.publish_at','<=',$this->waktu->format('H:i:s'))
							->groupBy('schedule_account.account_id')
							->orderBy('schedules.publish_at', 'asc')
							->get();
			$smsg = '';

			foreach ($scs as $sc) {
        $logs = date("Y-m-d h:i:sa").' '.$sc->slug.'-'.$sc->media_type.", Looping schedule\n";
        fwrite($myfile, $logs);

				$user = Users::find($sc->user_id);
				if (!is_null($user)) {
					// if (!$user->is_started) {
						// continue;
					// }
				}
				
				// foreach ($sc->accounts as $account) {
				$account = Account::find($sc->account_id);

				if (!is_null($account)) {
						if ( (!$account->is_started) || (!$account->is_active) ) {
							continue;
						}
						if (!is_null($account->last_post)) {
							$dt = Carbon::now();
							$last_post = Carbon::parse($account->last_post);
							if ($last_post->diffInSeconds($dt) <= 120 ) {
								continue;
							}
						}
						if ($account->is_error) {
							continue;
						}
						// if ($account->pivot->status < 2) {
						// $check_sa = ScheduleAccount::where("account_id","=",$account->id)
									// ->where("schedule_id","=",$sc->id)
									// ->first();
						$check_sa = ScheduleAccount::find($sc->said);
						if (!is_null($check_sa)){
							if ($check_sa->status_process <> 0) {
								continue;
							}
							if ($check_sa->status < 2) {
							
								//do PostingTask on queue, ga jadi dulu queue nya harus rush UP productionnya
								/*$serializeAccount = serialize($account);
								$serializeSc = serialize($sc);
								$this->dispatch(new PostingTask($serializeAccount,$serializeSc));
								*/
								
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
								// $photo = $sc->image;
                /*if($sc->media_type='photo'){
                  $photo = $dir."/".$sc->slug.".jpg";
                } else {
                  $photo = $dir."/".$sc->slug;
                }*/
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
									// $i->setUser($username, $password);
									$i->login($username, $password, 300);

                  $logs = $sc->slug.'-'.$sc->media_type.", Login akun\n";
                  fwrite($myfile, $logs);
								} 
								catch (\InstagramAPI\Exception\IncorrectPasswordException $e) {
									$is_error = 1 ;
									$update_account = Account::find($account->id);
									$update_account->is_error = 1;
									$update_account->save();
									$smsg = $e->getMessage();
									
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
										/*$subject_message = "[Activpost] Error CURL, Account=".$username." schedule id = ".$sc->id;
										$emaildata = [
											"smsg" => $smsg,
										];
										Mail::send('emails.notify-developer', $emaildata, function ($message) use ($subject_message) {
											$message->from('no-reply@activpost.net', 'Activpost');
											$message->to("celebgramme.dev@gmail.com");
											$message->subject($subject_message);
										});*/
										
										//supaya diproses lagi
										if ($check_sa->counter_error <= 2 ) {
											$check_sa->counter_error += 1;
											$check_sa->status_process = 0;
											$check_sa->save();
										}
										
										continue;
									}
									$smsg .= " Line: ".$e->getTraceAsString(); // this prints the line where the error occurs
									
                  // ob_start();
                  // var_dump($e);
                  // $result = ob_get_clean();    
									// $smsg .= " ".$result;
                  
									//new, klo error jgn di schedule lagi 
									// $sc->accounts()->syncWithoutDetaching([
										// $account->id => [
											// 'status' => 5, 
										// ]
									// ]);
								}
								catch (\InstagramAPI\Exception\BadRequestException $e) {
									//supaya diproses lagi
									if ($check_sa->counter_error <= 2 ) {
										$check_sa->counter_error += 1;
										$check_sa->status_process = 0;
										$check_sa->save();
									}
									
									continue;
								}
								if ($is_error) {
									// $sa = ScheduleAccount::where("account_id","=",$account->id)
												// ->where("schedule_id","=",$sc->id)
												// ->first();
									// if (!is_null($check_sa)){
										$check_sa->status = 5;
										$check_sa->media_id = $smsg;
										$check_sa->save();
									// }
									
									if (!$account->is_post_berurutan) {
										// $sc->accounts()->syncWithoutDetaching([
											// $account->id => [
												// 'status_helper' => 5, 
											// ]
										// ]);
										// $sa = ScheduleAccount::where("account_id","=",$account->id)
													// ->where("schedule_id","=",$sc->id)
													// ->first();
										// if (!is_null($sa)){
											$check_sa->status_helper = 5;
											$check_sa->media_id = $smsg." asd";
											$check_sa->save();
										// }
									}
									//buat log + notif di email
									/*$subject_message = "[Activpost] IG Error Login, Account=".$username;
									$emaildata = [
										"smsg" => $smsg,
									];
									Mail::send('emails.notify-developer', $emaildata, function ($message) use ($subject_message) {
										$message->from('no-reply@activpost.net', 'Activpost');
										$message->to("celebgramme.dev@gmail.com");
										$message->subject($subject_message);
									});*/
									
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
									
									continue;
								}
								// Upload
								try {
                  if ($sc->media_type == "photo") {
										// $caption = str_replace(chr(13),"\n",$caption);
										// $caption = str_replace(chr(13).chr(10),"\n"."\n",$caption);
										// $caption = str_replace(chr(126),"."."\n"."\n",$caption);
										$caption = str_replace("\r\n", "\n", $caption);
										
                    if(strpos($sc->slug, 'StoryFile')===0){
                      $logs = $sc->slug.'-'.$sc->media_type.", Pra posting story foto\n";
                      fwrite($myfile, $logs);

                      $instagram = $i->story->uploadPhoto($photo, ['caption' => $caption]);

                      $logs = $sc->slug.'-'.$sc->media_type.", Posting Story foto\n";
                      fwrite($myfile, $logs);
                    } else {
                      $logs = $sc->slug.'-'.$sc->media_type.", Pra posting\n";
                      fwrite($myfile, $logs);

                      $instagram = $i->timeline->uploadPhoto($photo, ['caption' => $caption]);  

                      $logs = $sc->slug.'-'.$sc->media_type.", Posting foto\n";
                      fwrite($myfile, $logs);
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
                      $logs = $sc->slug.'-'.$sc->media_type.", Pra posting\n";
                      fwrite($myfile, $logs);

                      $instagram = $i->story->uploadVideo($photo, ['caption' => $caption]);

                      $logs = $sc->slug.'-'.$sc->media_type.", Posting story video\n";
                      fwrite($myfile, $logs);
                    } else {
                      $logs = $sc->slug.'-'.$sc->media_type.", Pra posting\n";
                      fwrite($myfile, $logs);
                      
                      $instagram = $i->timeline->uploadVideo($photo, ['caption' => $caption, 'thumbnail_timestamp' => $sc->thumbnail_video]);

                      $logs = $sc->slug.'-'.$sc->media_type.", Posting video\n";
                      fwrite($myfile, $logs);
                    }
                    
                    //update last post 
                    $dt = Carbon::now();
                    $update_account = Account::find($account->id);
                    $update_account->last_post = strtotime($dt->toDateTimeString());
                    $update_account->save();

                    $logs = $sc->slug.'-'.$sc->media_type.", Pasca posting\n";
                    fwrite($myfile, $logs);
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
										/*$subject_message = "[Celebpost] Error CURL, Account=".$username." schedule id = ".$sc->id;
										$emaildata = [
											"smsg" => $smsg,
										];
										Mail::send('emails.notify-developer', $emaildata, function ($message) use ($subject_message) {
											$message->from('no-reply@activpost.net', 'Activpost');
											$message->to("celebgramme.dev@gmail.com");
											$message->subject($subject_message);
										});*/
										
										//supaya diproses lagi
										if ($check_sa->counter_error <= 2 ) {
											$check_sa->counter_error += 1;
											$check_sa->status_process = 0;
											$check_sa->save();
										}
										
										continue;
									}
									$smsg .= " Line: ".$e->getTraceAsString(); // this prints the line where the error occurs
									
                  ob_start();
                  // var_dump($e);
                  $result = ob_get_clean();    
									$smsg .= " ".$result;
                  
									//new
									// $sc->accounts()->syncWithoutDetaching([
										// $account->id => [
											// 'status' => 5, 
										// ]
									// ]);
									// $sa = ScheduleAccount::where("account_id","=",$account->id)
												// ->where("schedule_id","=",$sc->id)
												// ->first();
									// if (!is_null($sa)){
										$check_sa->status = 5;
										$check_sa->media_id = $smsg;
										$check_sa->save();
									// }
									
									if (!$account->is_post_berurutan) {
										// $sc->accounts()->syncWithoutDetaching([
											// $account->id => [
												// 'status_helper' => 5, 
											// ]
										// ]);
										// $sa = ScheduleAccount::where("account_id","=",$account->id)
													// ->where("schedule_id","=",$sc->id)
													// ->first();
										// if (!is_null($sa)){
											$check_sa->status_helper = 5;
											$check_sa->save();
										// }
									}
									//buat log + notif di email
									/*$subject_message = "[Celebpost] IG Error Publish, Account=".$username." schedule id = ".$sc->id;
									$emaildata = [
										"smsg" => $smsg,
									];
									Mail::send('emails.notify-developer', $emaildata, function ($message) use ($subject_message) {
										$message->from('no-reply@activpost.net', 'Activpost');
										$message->to("celebgramme.dev@gmail.com");
										$message->subject($subject_message);
									});*/
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
									
									continue;
								}
								catch (\InstagramAPI\Exception\BadRequestException $e) {
									//supaya diproses lagi
									if ($check_sa->counter_error <= 2 ) {
										$check_sa->counter_error += 1;
										$check_sa->status_process = 0;
										$check_sa->save();
									}
									
									continue;
								}
							
							
							
								$dt = Carbon::now();
								// $sc->accounts()->syncWithoutDetaching([
									// $account->id => [
										// 'status' => 2, 
										// 'status_helper' => 2, 
										// 'media_id' => $instagram->getMediaId(),
									// ]
								// ]);
								// $sa = ScheduleAccount::where("account_id","=",$account->id)
											// ->where("schedule_id","=",$sc->id)
											// ->first();
								// if (!is_null($sa)){
									$check_sa->published_time = strtotime($dt->toDateTimeString());
									$check_sa->status = 2;
									$check_sa->status_helper = 2;
									$check_sa->media_id = $instagram->getMedia()->getId();
									$check_sa->save();
								// }
								
							}
						}


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
						}
						
						if (!$account->is_post_berurutan) {
							//klo ada error salah satu accountnya, schedule status diganti 0 
							// $check_sa = ScheduleAccount::where("schedule_id","=",$sc->id)->get();
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

						
				}
			}
			
			

			
				// if ($user->username=="ss080189@gmail.com") {
					// $userMeta = new UserMeta;
					// $userMeta->user_id = $user->id;
					// $userMeta->meta_name = $user->username;
					// $userMeta->meta_value = "testing ".$sc->id;
					// $userMeta->save();
				// }

				// $userlog = new UserLog;
				// $userlog->user_id = $sc->user_id;
				// $userlog->description = "Cron Success Posting (Upload Photo), instagram account=".$username." schedule id = ".$sc->id;
				// $userlog->admin_id = 0;
				// $userlog->save();

      fclose($myfile);	
			
    }
		
								
		
}
