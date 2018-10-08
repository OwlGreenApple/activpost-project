<?php

namespace Celebpost\Http\Controllers;

/*Models*/

use Celebpost\Http\Controllers\Controller;
use Illuminate\Http\Request as req;
use Illuminate\Support\Facades\Auth;

use Celebpost\Models\Idaff;
use Celebpost\Models\Users;
use Celebpost\Models\UserLog;
use Celebpost\Models\Order;
use Celebpost\Models\Schedule;
use Celebpost\Models\Account;
use Celebpost\Models\Proxies;

use \InstagramAPI\Instagram;

use Celebpost\Helper\GeneralHelper;

use Carbon\Carbon;

use View, Input, Mail, Request, App, Hash, Validator, Crypt, DB, Image, Exception,Redirect,File,Config;

class LandingPageController extends Controller
{
  
	public function post_back_idaff(){	
		$idaff = Idaff::where("invoice","=",Input::get("invoice"))->first();
		if (is_null($idaff)){
			$idaff = new Idaff;
			$idaff->trans_id = Input::get("transid");
			$idaff->invoice = Input::get("invoice");
			$idaff->executed = 0;
		} else {
			$idaff = Idaff::where("invoice","=",Input::get("invoice"))->first();
		}
		
		$idaff->name = Input::get("cname");
		$idaff->email = Input::get("cemail");
		$idaff->phone = Input::get("cmphone");
		$idaff->status = Input::get("status");
		$idaff->grand_total = Input::get("grand_total");
		$idaff->save();
		
		if ( ($idaff->status == "SUCCESS") && (!$idaff->executed) ) {
			$flag = false;
			$user = Users::where("email","=",$idaff->email)->first();
			if (is_null($user)) {
				$flag = true;
				$karakter= 'abcdefghjklmnpqrstuvwxyz123456789';
				$string = '';
				for ($i = 0; $i < 8 ; $i++) {
					$pos = rand(0, strlen($karakter)-1);
					$string .= $karakter{$pos};
				}

				$user = new Users;
				$user->email = $idaff->email;
				$user->username = $idaff->email;
				$user->password = Hash::make($string);
				$user->name = $idaff->name;
				$user->is_confirmed = 1;
				$user->is_admin = 0;
				$user->save();
			}
			
			$dt = Carbon::now()->setTimezone('Asia/Jakarta');
			$order = new Order;
			$str = 'OCPS'.$dt->format('ymdHi');
			$order_number = GeneralHelper::autoGenerateID($order, 'no_order', $str, 3, '0');
			$order->no_order = $order_number;
			$order->order_status = "cron dari affiliate";
			
			$order->total = intval(Input::get("grand_total"));
			$order->user_id = $user->id;
			$order->order_type = "IDAFF";
			$order->base_price = 0;
			$order->sub_price = intval(Input::get("grand_total"));
			$order->potong_affiliate = intval(Input::get("grand_total"))*65/100;
			$order->affiliate = intval(Input::get("grand_total"))*35/100;
			$order->discount = 0;
			$order->package_id = 0;
			$order->month = 4;
			$order->save();
			
			if ($flag) {
				$user->active_time = 120 * 86400;
				$user->max_account = 3;
				$user->save();
				
				$emaildata = [
						'user' => $user,
						'password' => $string,
				];
				Mail::send('emails.cron.create-user', $emaildata, function ($message) use ($user) {
					$message->from('no-reply@activpost.net', 'Activpost');
					$message->to($user->email);
					$message->subject('[Activpost] Welcome to activpost.net (Info Login & Password)');
				});
			
			} else {
				$t = 120 * 86400;
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
				
				
				$user->active_time += 120 * 86400;
				$user->save();
				
				
				$emaildata = [
						'user' => $user,
				];
				Mail::send('emails.cron.adding-time-user', $emaildata, function ($message) use ($user) {
					$message->from('no-reply@activpost.net', 'Activpost');
					$message->to($user->email);
					$message->subject('[Activpost] Congratulation Pembelian Sukses, & Kredit waktu sudah ditambahkan');
				});
				
			}
			
			$idaff->executed = 1;
			$idaff->save();
		}
		
		
		
	}
		
	public function update_publish_schedule(){		
		// ini_set("gd.jpeg_ignore_warning", 1);
		$schedules = Schedule::where(function ($query) {
										$query->where("status","=",2);
										$query->orWhere("status","=",3);
									})
									->whereNull("thumbnail")
									// ->where("user_id","=",6)
									->orderBy("id","desc")
									->get();
		foreach($schedules as $schedule){
			/* ga perlu. ??
			if ( ($schedule->is_deleted) && ($schedule->status == 2) ) {
				// tunggu status 3 baru diresize
				continue;
			}*/
			echo $schedule->id." - ".$schedule->user_id."<br>";
			$user = Users::find($schedule->user_id);
			// $dir = public_path('images/uploads/'.$user->username.'-'.$user->id); 
			$dir = public_path('../vp/uploads/'.$user->username.'-'.$user->id); 
			if (!file_exists($dir)) {
				mkdir($dir,0741,true);
			}
			try {
				/*
				$arr_size = getimagesize($dir."/".$schedule->slug.".jpg");
				// if ( ($arr_size[0]>1400) && ($arr_size[1]>1400) ) {
				if ( ( ($arr_size[0]>4000) && ($arr_size[1]>4000) ) || ( ($arr_size[0]>4000) && ($arr_size[1]>280) ) || ( ($arr_size[0]>280) && ($arr_size[1]>4000) ) ) {
					continue;
				}*/
				
				Image::make($dir."/".$schedule->slug.".jpg")
							->resize(50, null, function ($constraint) {
									$constraint->aspectRatio();
							})
							->save($dir."/".$schedule->slug.".jpg");
			} catch (Exception $e) {
				// return $e->getMessage();
				continue;
			}

			$update_schedule = Schedule::find($schedule->id);
			// $update_schedule->thumbnail = url('/images/uploads/'.$user->username.'-'.$user->id.'/'.$schedule->slug.".jpg");
			$update_schedule->thumbnail = url('/../vp/uploads/'.$user->username.'-'.$user->id.'/'.$schedule->slug.".jpg");
			$update_schedule->save();
		}
		
		// return "";
	}
	
	public function verifyEmail($cryptedcode)
	{
		try {
			$decryptedcode = Crypt::decrypt($cryptedcode);
			$data = json_decode($decryptedcode);
			// return $data->email."-".$data->verification_code;
			$user = Users::where("username","=",$data->email)->first();
			if (!is_null($user)) {
				// Check customer email and status
				if (!$user->is_confirmed){
					// Check Verification Code
					if ($user->verificationcode == $data->verification_code){
						// $reg_date = Carbon::createFromFormat('Y-m-d H:i:s', $data->register_time);
							// Change customer status to verified, then redirect to Home
							$user->is_confirmed = 1;
							$user->save();

							return Redirect::to("https://activpost.net/selamat/");
					}
					else{
						return redirect(404);
					}
				}
				else{
					return redirect(404);
				}
			}
			else{
				return redirect(404);
			}
		} catch (DecryptException $e) {
			return redirect(404);
		}
	}

  public function resendEmailActivation()
  {
    $user = Auth::user();
    
    $register_time = Carbon::now()->toDateTimeString();
    $verificationcode = Hash::make($user->email.$register_time);
    $user->verificationcode = $verificationcode;
    $user->save();
    if (App::environment() == 'local'){
      $url = 'http://localhost/laravel-instagram/verifyemail/';
    }
    else if (App::environment() == 'production'){
      $url = 'https://activpost.net/dashboard/verifyemail/';
    }
    $secret_data = [
      'email' => $user->email,
      'register_time' => $register_time,
      'verification_code' => $verificationcode,
    ];
    $emaildata = [
      'url' => $url.Crypt::encrypt(json_encode($secret_data)),
			'user' => $user,
			'password' => "",
    ];
    Mail::send('emails.register.confirm-email', $emaildata, function ($message) use ($user) {
      $message->from('no-reply@activpost.net', 'Activpost');
      $message->to($user->email);
      $message->subject('[Activpost] Verify Email');
    });
    $arr = array (
      "message"=>"Email aktivasi berhasil dikirim",
      "type"=>"success",
      );
    return $arr;
  }

	public function update_cookie_account(){
		$accounts = Account::where("is_active","=",1)->get();
		foreach ($accounts as $data_account) {
			$account = Account::find($data_account->id);
			
			$decrypted_string = Crypt::decrypt($data_account->password);
			$pieces = explode(" ~space~ ", $decrypted_string);
			$pass = $pieces[0];
			$password = $pass;
			
			$username = $account->username;
			
			$proxy = Proxies::find($account->proxy_id);
			
			$i = new Instagram(false,false,[
				"storage"       => "mysql",
				"dbhost"       => Config::get('database.connections.mysql_celebgramme.host'),
				"dbname"   => Config::get('database.connections.mysql_celebgramme.database'),
				"dbusername"   => Config::get('database.connections.mysql_celebgramme.username'),
				"dbpassword"   => Config::get('database.connections.mysql_celebgramme.password'),
			]);
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
			} catch (Exception $e) {
				echo "login failed :".$username."  -  ".$e->getMessage()."<br>";
			}
			
			
			
			
		}
	}

}
