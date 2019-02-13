<?php

namespace Celebpost\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

use Celebpost\Models\Schedule;
use Celebpost\Models\Proxies;
use Celebpost\Models\ScheduleAccount;
use Celebpost\Models\Users;
use Celebpost\Models\Account;

use \InstagramAPI\Instagram;
use Exception,Mail,Log;

class UserTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'count:userstime';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Count Started user time';

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
      $message = "";
			$users = Users::where("is_started","=",1)
								->get();
			foreach($users as $user){
				$update_user = Users::find($user->id);
				$now = Carbon::now();
        if ( !is_null($user->running_time) ) {
          $runTime = Carbon::createFromFormat('Y-m-d H:i:s', $user->running_time);
          $timevalue = $now->diffInSeconds($runTime);
          $update_user->active_time -= $timevalue;
          if ($update_user->active_time <= 0){
            $update_user->active_time = 0;
            $update_user->is_started = 0;
            
            //mail information
          } 
          else {
            $update_user->running_time = $now->toDateTimeString();
          }
          $update_user->save();
        }
        else {
          //print ke text file email user yang waktu nya null 
          $message .= $user->username." running_time is null;";
        }
			}
      Log::error($message);
			/*
			$accounts = Account::where("is_started","=",1)
								->where("is_active","=",1)
								->get();
			foreach($accounts as $account){
				$update_user = Users::find($account->user_id);
				$now = Carbon::now();
				$runTime = Carbon::createFromFormat('Y-m-d H:i:s', $account->running_time);
				$timevalue = $now->diffInSeconds($runTime);
				$update_user->active_time -= $timevalue;
				if ($update_user->active_time <= 0){
					$update_user->active_time = 0;
					$account->is_started = 0;
					
					//mail information
				} 
				else {
					$account->running_time = $now->toDateTimeString();
				}
				$account->save();
				$update_user->save();
			}
			*/
    }
}
 