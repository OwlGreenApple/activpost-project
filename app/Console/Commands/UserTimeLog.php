<?php

namespace Celebpost\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

use Celebpost\Models\Schedule;
use Celebpost\Models\Proxies;
use Celebpost\Models\ScheduleAccount;
use Celebpost\Models\Users;
use Celebpost\Models\UserMeta;
use Celebpost\Models\Coupon;
use Celebpost\Models\TimeLog;

use \InstagramAPI\Instagram;
use Exception,Mail;

class UserTimeLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'count:timelog';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Count TimeLog user time';

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
			$dt_coupon_expired = Carbon::now()->addDays(7)->toDateString();
			$users = Users::where("active_time",">",0)
								->get();
			foreach($users as $user){
				$timeLog = new TimeLog;
				$timeLog->user_id = $user->id;
				$timeLog->time = $user->active_time;
				$timeLog->description = "daily log waktu users (cron)";
				$timeLog->save();
				
        if($user->is_member_rico){
          continue;
        }

				if ( ($user->active_time>=430000) && ($user->active_time<=520000) && (UserMeta::getMeta($user->id,"email5days")<>"yes") ) {
					$temp = UserMeta::createMeta("email5days","yes",$user->id);
					$temp = UserMeta::createMeta("emailExpCoupon","",$user->id);
					$emaildata = [
							'user' => $user,
					];
					Mail::queue('emails.notif-5days', $emaildata, function ($message) use ($user) {
						$message->from('no-reply@activpost.net', 'Activpost');
						$message->to($user->email);
						$message->subject('[Activpost] 5 hari lagi nih, nggak berasa yah');
					});
				}
				if ( ($user->active_time>=43200) && ($user->active_time<=129600) && (UserMeta::getMeta($user->id,"email1days")<>"yes") ) {
					$temp = UserMeta::createMeta("email1days","yes",$user->id);
					//coupon diberi saat last day. coupon expired setelah 7 hari
					do {
						$karakter= 'abcdefghjklmnpqrstuvwxyz123456789';
						$string = '';
						for ($i = 0; $i < 5 ; $i++) {
							$pos = rand(0, strlen($karakter)-1);
							$string .= $karakter{$pos};
						}
						$coupon = Coupon::where("coupon_code","=",$string)->first();
					} while (!is_null($coupon));
					$coupon = new Coupon;
					$coupon->coupon_value = 0;
					$coupon->coupon_percent = 10;
					$coupon->package_id = 0;
					$coupon->coupon_code = $string;
					$coupon->user_id = $user->id;
					$coupon->valid_until = $dt_coupon_expired;
					$coupon->save();

					$emaildata = [
						'user' => $user,
						'code_coupon' => $string,
						'days_coupon' => 7,
						'percent_coupon' => 10,
					];
					Mail::queue('emails.notif-expired', $emaildata, function ($message) use ($user) {
						$message->from('no-reply@activpost.net', 'Activpost');
						$message->to($user->email);
						$message->subject('[Activpost] Service Activpost.net akan berakhir');
					});
				}
				
				if ( ($user->active_time>0) && ($user->active_time<=50000) ) {
					$temp = UserMeta::createMeta("email1days","exp",$user->id);
					$temp = UserMeta::createMeta("email5days","exp",$user->id);
				}
			}
			
			$now = Carbon::now();
			$coupons = Coupon::where("user_id","!=",0)
									->where("valid_until","=",$now->toDateString())
									->get();
			foreach($coupons as $coupon){
				if (UserMeta::getMeta($user->id,"emailExpCoupon")<>"yes") {
					$count_log += 1;
					$temp = UserMeta::createMeta("emailExpCoupon","yes",$user->id);
					$user = User::find($coupon->user_id);
					$emaildata = [
						'user' => $user,
						'code_coupon' => $coupon->coupon_code,
					];
					Mail::queue('emails.notif-coupon-expired', $emaildata, function ($message) use ($user) {
						$message->from('no-reply@activpost.net', 'Activpost');
						$message->to($user->email);
						// $message->bcc("celebgramme.dev@gmail.com");
						$message->subject('[Activpost] Hari ini terakhir penggunaan coupon order anda');
					});
				}
			}
			
			
    }
}
