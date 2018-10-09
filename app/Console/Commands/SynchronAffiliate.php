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
use Celebpost\Models\Order;
use Celebpost\Models\OrderMeta;

use Celebpost\Helper\GeneralHelper;

use \InstagramAPI\Instagram;
use Exception,Mail,Validator,DB,Hash;

class SynchronAffiliate extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'synchron:affiliate';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Synchron dashboard with wp affiliate';

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
		$datas = DB::connection('mysqlAffiliate')->select("select p.*,u.user_email,u.display_name from wp_posts p inner join wp_users u on u.id=p.post_author where post_title like 'CPS%' and post_content='' and post_status='publish' and post_type='wuoysales'");		
		// dd($datas);
		// echo $datas[0]->ID;
		foreach ($datas as $data) {
			// echo $data->post_status."<br>";
			// if ($data->post_status=="publish") {
				
				//kirim email create user
				$temp = array (
					"email" => $data->user_email,
				);
				$validator = Validator::make($temp, [
					'email' => 'required|email|max:255',
					// 'email' => 'required|email|max:255|unique:users',
				]);
				if ($validator->fails()){
					continue;
				}

				$flag = false;
				$user = Users::where("email","=",$data->user_email)->first();
				if (is_null($user)) {
					$flag = true;
					$karakter= 'abcdefghjklmnpqrstuvwxyz123456789';
					$string = '';
					for ($i = 0; $i < 8 ; $i++) {
						$pos = rand(0, strlen($karakter)-1);
						$string .= $karakter{$pos};
					}

					$user = new Users;
					$user->email = $data->user_email;
					$user->username = $data->user_email;
					$user->password = Hash::make($string);
					$user->name = $data->display_name;
					$user->is_confirmed = 1;
					$user->is_admin = 0;
					$user->save();
				}
				
				//cek order&user uda pernah ada didatabase
				$new_base_price = 0;
				$tothari = 30; 
				$data_meta = DB::connection('mysqlAffiliate')->select("select meta_value from wp_postmeta where meta_key='price' and post_id = ".$data->ID);		
				if (!is_null($data_meta)) {
					$new_base_price = intval($data_meta[0]->meta_value); //harga baru				
				}


				
				$dt = Carbon::now();
				$order = new Order;
				$str = 'OCPS'.$dt->format('ymdHi');
				$order_number = GeneralHelper::autoGenerateID($order, 'no_order', $str, 3, '0');
				$order->no_order = $order_number;
				$order->order_status = "cron dari affiliate";
				$data_meta = DB::connection('mysqlAffiliate')->select("select meta_value from wp_postmeta where meta_key='price' and post_id = ".$data->ID);		
				if (!is_null($data_meta)) {
					$order->total = intval($data_meta[0]->meta_value);
					$order->user_id = $user->id;
					$order->order_type = "Transfer Bank";
					$order->base_price = intval($data_meta[0]->meta_value); //harus diisi buat buy morenya
					$order->sub_price = intval($data_meta[0]->meta_value);
					if ( ($new_base_price >=450000) && ($new_base_price <=460000) ) { //klo harga affiliate
						$order->potong_affiliate = intval($data_meta[0]->meta_value)*65/100;
						$order->affiliate = intval($data_meta[0]->meta_value)*35/100;
					}
					else {
						$order->potong_affiliate = 0;
						$order->affiliate = 0;
					}
				}
				$order->discount = 0;
				$order->package_id = 0;
				if ( ($new_base_price >=450000) && ($new_base_price <=460000) ) { 
					$month = 4;
				} else {
					$month = 1;
				}
				$order->month = $month;
				$order->save();
				
				OrderMeta::createMeta("logs","create order from affiliate",$order->id);

				//calculate max_account
				$max_account = 3;
				if ( ( ($new_base_price >=450000) && ($new_base_price <=460000) ) || ($new_base_price<=200000) ) { 
					$max_account = 3;
				}
				else if ( ($new_base_price >=290000) && ($new_base_price <=300000) ) {
					$max_account = 5;
				}
				else if ( ($new_base_price >=490000) && ($new_base_price <=500000) ) {
					$max_account = 10;
				}
				else if ( ($new_base_price >=690000) && ($new_base_price <=700000) ) {
					$max_account = 15;
				}
				$user->max_account = $max_account;
				$user->save();

				if ($flag) {
					if ( ($new_base_price >=450000) && ($new_base_price <=460000) ) {
						$user->active_time = 120 * 86400;
					}
					else {
						$user->active_time = $tothari * 86400;
					}
					$user->save();
					
					$affected = DB::connection('mysqlAffiliate')->update('update wp_posts set post_content = "registered" where id="'.$data->ID.'"');
					
					$emaildata = [
							'user' => $user,
							'password' => $string,
					];
					Mail::queue('emails.cron.create-user', $emaildata, function ($message) use ($user) {
						$message->from('no-reply@activpost.net', 'Activpost');
						$message->to($user->email);
						$message->subject('[Activpost] Welcome to activpost.net (Info Login & Password)');
					});
				
				} else {
					if ( ($new_base_price >=450000) && ($new_base_price <=460000) ) {
						$t = 120 * 86400;
					} 
					else {
						$t = $tothari * 86400;
					}
					$days = floor($t / (60*60*24));
					$hours = floor(($t / (60*60)) % 24);
					$minutes = floor(($t / (60)) % 60);
					$seconds = floor($t  % 60);
					$time = $days."D ".$hours."H ".$minutes."M ".$seconds."S ";

					$user_log = new UserLog;
					$user_log->user_id = $user->id;
					$user_log->admin_id = 0;
					$user_log->description = "Adding time from cron. ".$time;
					$user_log->save();

					
					if ( ($new_base_price >=450000) && ($new_base_price <=460000) ) {
						$user->active_time += 120 * 86400;
					}
					else {
						$user->active_time += $tothari * 86400;
					}
					
					
					$user->save();
					
					$affected = DB::connection('mysqlAffiliate')->update('update wp_posts set post_content = "registered" where id="'.$data->ID.'"');
					
					$emaildata = [
							'user' => $user,
					];
					Mail::queue('emails.cron.adding-time-user', $emaildata, function ($message) use ($user) {
						$message->from('no-reply@activpost.net', 'Activpost');
						$message->to($user->email);
						$message->subject('[Activpost] Congratulation Pembelian Sukses, & Kredit waktu sudah ditambahkan');
					});
					
				}

				
		}
	}
}
