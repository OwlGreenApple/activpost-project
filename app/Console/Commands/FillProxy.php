<?php

namespace Celebpost\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;

use Celebpost\Models\Account;
use Celebpost\Models\Proxies;
use Celebpost\Models\Users;

use \InstagramAPI\Instagram;
use Exception,Mail,File,Config;

class FillProxy extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'fill:proxy';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Fill Proxy yang ga ada';

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
		/*$accounts = Account::where('proxy_id', '=', 0)
								->orWhereNull('proxy_id')
								->get();
		foreach($accounts as $account){
			//cariin proxy baru
			if(!File::exists(storage_path('ig-cookies/'.$account->username))) {
					File::makeDirectory(storage_path('ig-cookies/'.$account->username), 0755, true);
			}
			
			$cookiefile = base_path('storage/ig-cookies/'.$account->username.'/').'cookies-celebpost-temp.txt';


			$url = "https://activfans.com/dashboard/get-proxy-id/".$account->username;
			$c = curl_init();

			curl_setopt($c, CURLOPT_URL, $url);
			curl_setopt($c, CURLOPT_REFERER, $url);
			curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($c, CURLOPT_COOKIEFILE, $cookiefile);
			curl_setopt($c, CURLOPT_COOKIEJAR, $cookiefile);
			$page = curl_exec($c);
			curl_close($c);

			$arr_res = json_decode($page,true);
			
			$proxy_id = $arr_res["proxy_id"]; 
			$is_on_celebgramme = $arr_res["is_on_celebgramme"]; 
			
			$update_account = Account::find($account->id);
			if (!is_null($update_account)) {
				$update_account->proxy_id = $proxy_id;
				$update_account->is_on_celebgramme = $is_on_celebgramme;
				$update_account->save();
			}
			
		}*/
		
		//check refresh account yang proxynya ganti 
		$accounts = Account::where('is_refresh', '=', 1)
								->get();
		foreach($accounts as $account){
			//cek klo diganti proxy masalah ga..
			$proxy = Proxies::find($account->proxy_id);
			
			
			// Decrypt
			$decrypted_string = Crypt::decrypt($account->password);
			$pieces = explode(" ~space~ ", $decrypted_string);
			$password = $pieces[0];
			
			$i = new Instagram(false,false,[
				"storage"       => "mysql",
				"dbhost"       => Config::get('database.connections.mysql_celebgramme.host'),
				"dbname"   => Config::get('database.connections.mysql_celebgramme.database'),
				"dbusername"   => Config::get('database.connections.mysql_celebgramme.username'),
				"dbpassword"   => Config::get('database.connections.mysql_celebgramme.password'),
			]);
			try {
				// Check Login
				// $i->setUser($account->username, $password);
				$i->login($account->username, $password, 300);
				if (!is_null($proxy)) {
					if($proxy->cred==""){
						$i->setProxy("http://".$proxy->proxy.":".$proxy->port);
					}
					else {
						$i->setProxy("http://".$proxy->cred."@".$proxy->proxy.":".$proxy->port);
					}
				}
			} catch (Exception $e) {
				$error_type = str_replace(" ","",$e->getMessage());
				$error_type = trim(preg_replace('/\s+/', ' ', $error_type));
				if ($error_type=='login_required') {
					$msg = "Mengalami perubahan Login Username / Password anda";
				} else if ($error_type=="checkpoint_required") {
					$msg = "Memerlukan konfirmasi email / sms. Silahkan Cek IG anda";
				} else {
					$msg = $error_type;
				}
				
				$user = Users::find($account->user_id);
				//email ke user klo error 
				$emaildata = [
						'user' => $user,
						'msg' => $msg,
						'account_username' => $account->username,
				];
				Mail::queue('emails.notify-user', $emaildata, function ($message) use ($user) {
					$message->from('no-reply@activpost.net', 'Activpost');
					$message->to($user->email);
					$message->bcc("celebgramme.dev@gmail.com");
					$message->subject('[Activpost] Please Check your Account on Dashboard');
				});
				
			}
			
			$update_account = Account::find($account->id);
			$update_account->is_refresh = 0;
			$update_account->save();
		}
		
	}
}
