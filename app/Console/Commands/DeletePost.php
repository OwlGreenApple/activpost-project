<?php

namespace Celebpost\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

use Celebpost\Models\Schedule;
use Celebpost\Models\Proxies;
use Celebpost\Models\ScheduleAccount;
use Celebpost\Models\Users;
use Celebpost\Models\UserLog;
use Celebpost\Models\Account;

use \InstagramAPI\Instagram;
use Exception,Mail,Config;

class DeletePost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:post';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete schedule post';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
			parent::__construct();
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
			*
			*/
			$dt = Carbon::now()->setTimezone(''.env('IG_TIMEZONE').'');
			$scs = Schedule::where('status',2)
							// ->where( 'delete_at', '<=', $dt->toDateTimeString() )
							->whereDate('delete_at',$dt->format('Y-m-d'))
							->whereTime('delete_at','<=',$dt->format('H:i:s'))
							
							->orderBy('created_at', 'asc')
							->get();
			foreach ($scs as $sc) {
				$user = Users::find($sc->user_id);
				if (!is_null($user)) {
					if (!$user->is_started) {
						// continue;
					}
				}
				
				foreach ($sc->accounts as $account) {
						if ( (!$account->is_started) || (!$account->is_active) ) {
							continue;
						}
						$dt = Carbon::now();
						$last_post = Carbon::parse($account->last_post);
						if ($last_post->diffInSeconds($dt) <= 120 ) {
							continue;
						}
						if ($account->is_error) {
							continue;
						}
						if ($account->pivot->status < 3) {
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
									/*$subject_message = "[Celebpost] Error CURL, Account=".$username." schedule id = ".$sc->id;
									$emaildata = [
										"smsg" => $smsg,
									];
									Mail::queue('emails.notify-developer', $emaildata, function ($message) use ($subject_message) {
										$message->from('no-reply@activpost.net', 'Celebpost');
										$message->to("celebgramme.dev@gmail.com");
										$message->subject($subject_message);
									});*/
									continue;
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
								
								//buat log + notif di email
								/*$subject_message = "[Celebpost] IG Error Login(Delete Schedule), Account=".$username;
								$emaildata = [
									"smsg" => $smsg,
								];
								Mail::queue('emails.notify-developer', $emaildata, function ($message) use ($subject_message) {
									$message->from('no-reply@activpost.net', 'Celebpost');
									$message->to("celebgramme.dev@gmail.com");
									$message->subject($subject_message);
								});*/
								
								continue;
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
									/*$subject_message = "[Celebpost] Error CURL, Account=".$username." schedule id = ".$sc->id;
									$emaildata = [
										"smsg" => $smsg,
									];
									Mail::queue('emails.notify-developer', $emaildata, function ($message) use ($subject_message) {
										$message->from('no-reply@activpost.net', 'Celebpost');
										$message->to("celebgramme.dev@gmail.com");
										$message->subject($subject_message);
									});*/
									continue;
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
								
								//buat log + notif di email
								/*$subject_message = "[Celebpost] IG Error Delete Schedule, Account=".$username." schedule id = ".$sc->id;
								$emaildata = [
									"smsg" => $smsg,
								];
								Mail::queue('emails.notify-developer', $emaildata, function ($message) use ($subject_message) {
									$message->from('no-reply@activpost.net', 'Celebpost');
									$message->to("celebgramme.dev@gmail.com");
									$message->subject($subject_message);
								});*/
								
								continue;
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
							
						}
						
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
}
