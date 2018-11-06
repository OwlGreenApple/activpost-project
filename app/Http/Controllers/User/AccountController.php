<?php

namespace Celebpost\Http\Controllers\User;

use Illuminate\Http\Request as reqid;
use Illuminate\Http\Request as req;
use Illuminate\Support\Facades\Crypt;

use Carbon\Carbon;

use Celebpost\Http\Requests;
use Celebpost\Models\Account;
use Celebpost\Models\ScheduleAccount;
use Celebpost\Models\Users;
use Celebpost\Models\Proxies;
use Celebpost\Models\ProxyLogin;
use Celebpost\Models\UserLog;
use Celebpost\Models\UserSetting;

use File,Auth,Exception,Request,Config;

use \InstagramAPI\Instagram;

class AccountController extends Controller
{
	public function index_test()
	{
		echo "IN";
		$user = Auth::user();
		$accounts = Account::where("user_id","=",$user->id)
							->where("is_active","=",1)
							->orderBy('username','asc')->paginate(15);
		foreach ($accounts as $account) {
			//get proxy
			$proxy = Proxies::find($account->proxy_id);
		
			// Decrypt
			$decrypted_string = Crypt::decrypt($account->password);
			$pieces = explode(" ~space~ ", $decrypted_string);
			$password = $pieces[0];
			$is_error = false;
			$ppurl = "";
			$taken_at = "-";
			try {
				$i = new Instagram(false,false,[
					"storage"       => "mysql",
					"dbhost"       => Config::get('database.connections.mysql_celebgramme.host'),
					"dbname"   => Config::get('database.connections.mysql_celebgramme.database'),
					"dbusername"   => Config::get('database.connections.mysql_celebgramme.username'),
					"dbpassword"   => Config::get('database.connections.mysql_celebgramme.password'),
				]);
				
				// Check Login
				if (!is_null($proxy)) {
					if($proxy->cred==""){
						$i->setProxy("http://".$proxy->proxy.":".$proxy->port);
					}
					else {
						$i->setProxy("http://".$proxy->cred."@".$proxy->proxy.":".$proxy->port);
					}
				}
				// $i->setUser($account->username, $password);
				$i->login($account->username, $password, 300);
			
				$self_info = $i->account->getCurrentUser();
				$self_user_feed = $i->timeline->getSelfUserFeed();
				$ppurl = $self_info->getUser()->getProfilePicUrl();
				if (count($self_user_feed->getItems()) > 0 ) {
					$temp = $self_user_feed->getItems()[0]->getTakenAt();
					$taken_at = date("Y-m-d H:i:s", $temp);
				} else {
					$taken_at = "-";
				}
				
				echo '<img src="'.$ppurl.'" class="circle-image">';
			}
			catch (Exception $e) {
				echo $e->getMessage();
			}
			echo "<br>";
		}
	}
	
	public function index()
	{
		

		$now = Carbon::now();
		$user = Auth::user();
		$user->last_seen = $now->toDateTimeString();
		$user->save();
		/*
		if ($user->is_admin == 1) {

			echo "string :". $user->is_admin;

		}elseif ($user->is_admin == 0) {

			echo "string :". $user->is_admin;
		}

  	*/

    if($user->is_admin == 1)
    { 

      $accounts = Account::where("user_id","=",$user->id)
                ->orderBy('username','asc')->paginate(15);
     

      $usern = Users::paginate(15);
  
      return view('admin.index',compact('accounts','user','usern'));

    }elseif ($user->is_admin == 0) 
    {
    	$accounts = Account::where("user_id","=",$user->id)
								->where("is_active","=",1)
                ->orderBy('username','asc')->paginate(15);
     

      $usern = Users::paginate(15);
    
    	return view('account.index',compact('accounts','user','usern'));
    }else
    {
      return 'Not Authorize';
    }
    
		
	}
	
	public function chklogin(req $request)
	{
		$user = Auth::user();
		
		$username = $request->insta_username;
		$password = $request->insta_password;
		
		
		//checking
		$account = Account::where("username","=",$username)
								->where("is_active","=",1)
								->first();
		if (!is_null($account)) {
			return response()->json([
					'login_status' => 403,
					'msg' => "username sudah pernah didaftarkan"
			]);
		}
		$count_account = Account::where("user_id","=",$user->id)
											->where("is_active","=",1)
											->count();
		if ($count_account>=$user->max_account) {
			return response()->json([
					'login_status' => 403,
					'msg' => "Jumlah Account tidak boleh lebih dari ".$user->max_account
			]);
		}
		
		
		$account = Account::where("username","=",$username)
								->first();
		if (!is_null($account)) {
			// $is_on_celebgramme = $account->is_on_celebgramme; 
		} else {
			// Save
			$string = $password.' ~space~ '.env('APP_KEY');
			$encrypted = Crypt::encrypt($string);
			$account = new Account;
			$account->user_id = $user->id;
			$account->username = $username;
			$account->password = $encrypted;
			$account->proxy_id = 0;
			$account->is_on_celebgramme = 0;
			$account->is_error = 0;
			$account->is_post_berurutan = 0;
			$account->is_active = 0;
			$account->save();
		
		}
		
		// klo ga ada sebelumnya maka proxy id dicari dari activfans
		//get proxy 
		if(!File::exists(storage_path('ig-cookies/'.$username))) {
			File::makeDirectory(storage_path('ig-cookies/'.$username), 0755, true);
		}
		
		$cookiefile = base_path('storage/ig-cookies/'.$username.'/').'cookies-celebpost-temp.txt';

    if(env('APP_ENV')=='production'){
			if ($user->is_member_rico==0) {
				$url = "https://activfans.com/dashboard/get-proxy-id/".$username;
			}
			else{
				$url = "https://activfans.com/amelia/get-proxy-id/".$username;
			}
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
    } else {
      $proxy_id = 1; 
      $is_on_celebgramme = 0; 
    }

		$account->proxy_id = $proxy_id;
		$account->is_on_celebgramme = $is_on_celebgramme;
		$account->save();
		
		
		
		
		$proxy = Proxies::find($proxy_id);
		
		
		//proxy things
		if (session()->has('proxy')) {
			$arr_proxy = session('proxy');
			if ( intval(session('attempt_count')) == 1 ) {
				//flag proxy supaya ga dipake lagi hari itu
				$proxy_login = ProxyLogin::where("proxy",$arr_proxy["proxy"])
												->where("port",$arr_proxy["port"])
												->where("cred",$arr_proxy["cred"])
												->first();
				if(!is_null($proxy_login)){
					$proxy_login->is_error = 1;
					$proxy_login->save();
				}
				
				//random dengan proxy yang lain
				$arr_proxy = $this->random_proxy();
			}
			else if ( intval(session('attempt_count')) >= 5 ) {
				//flag proxy supaya ga dipake lagi hari itu
				$proxy_login = ProxyLogin::where("proxy",$arr_proxy["proxy"])
												->where("port",$arr_proxy["port"])
												->where("cred",$arr_proxy["cred"])
												->first();
				if(!is_null($proxy_login)){
					$proxy_login->is_error = 1;
					$proxy_login->save();
				}
				
				return response()->json([
						'login_status' => 403,
						'msg' => "Mohon maaf, Anda telah melebihi batas login attempt 2x untuk menghindari akun anda di flag oleh Instagram Silahkan coba kembali setelah 3 x 24 jam",
						'proxy_id' => $proxy_id
				]);
			}
			
			session([
				'proxy' => collect($arr_proxy),
				'attempt_count' => intval(session('attempt_count')) + 1 ,
			]);
		}
		else {
			$arr_proxy = $this->random_proxy();
			session([
				'proxy' => collect($arr_proxy),
				'attempt_count' => 0,
			]);
		}
		

		$is_error = 0 ;		
		$i = new Instagram(false,false,[
			"storage"       => "mysql",
			"dbhost"       => Config::get('database.connections.mysql_celebgramme.host'),
			"dbname"   => Config::get('database.connections.mysql_celebgramme.database'),
			"dbusername"   => Config::get('database.connections.mysql_celebgramme.username'),
			"dbpassword"   => Config::get('database.connections.mysql_celebgramme.password'),
		]);

		
		try {
			// Check Login
			// if (!is_null($proxy)) {
				// if($proxy->cred==""){
				if($arr_proxy["cred"]==""){
					// $i->setProxy("http://".$proxy->proxy.":".$proxy->port);
					$i->setProxy("http://".$arr_proxy["proxy"].":".$arr_proxy["port"]);
				}
				else {
					// $i->setProxy("http://".$proxy->cred."@".$proxy->proxy.":".$proxy->port);
					$i->setProxy("http://".$arr_proxy["cred"]."@".$arr_proxy["proxy"].":".$arr_proxy["port"]);
				}
			// }
			// $i->setUser($username, $password);
			$i->login($username, $password, 300);
			
				$account->user_id = $user->id;
				$account->is_active = 1;
				$account->save();
				
				// Output
			return response()->json([
					'login_status' => 200,
					'msg' => 'Account Added'
			]);
			// $i->logout();
		}
		/*catch (Exception $e) {
			return response()->json([
					'login_status' => 403,
					'msg' => $e->getMessage(),
					'proxy_id' => $proxy_id
			]);
		}*/
		
			catch (\InstagramAPI\Exception\IncorrectPasswordException $e) {
				//klo error password
				$is_error = 1 ;
			}
			catch (\InstagramAPI\Exception\AccountDisabledException $e) {
				//klo error password
				$is_error = 1 ;
			}
			catch (\InstagramAPI\Exception\CheckpointRequiredException $e) {
				//klo error email / phone verification 
				$is_error = 2 ;
			}
			catch (\InstagramAPI\Exception\ChallengeRequiredException $e) {
				$is_error = 2 ;
			}
			catch (\InstagramAPI\Exception\BadRequestException $e) {
				return response()->json([
						'login_status' => 403,
						'msg' => "Code:400 request busy, Please try again",
						'proxy_id' => $proxy_id
				]);
			}
			catch (\InstagramAPI\Exception\ThrottledException $e) {
				return response()->json([
						'login_status' => 403,
						'msg' => "Code:399 Network busy, silahkan coba lagi",
						'proxy_id' => $proxy_id
				]);
			}
			catch (Exception $e) {
				$error_message = $e->getMessage();
				if (strpos($error_message, 'InstagramAPI\Response\LoginResponse:') !== false) {
					$is_error = 1 ;
				} 
				if ( ($error_message == "InstagramAPI\Response\LoginResponse: Challenge required.") || ( substr($error_message, 0, 18) == "challenge_required") || ($error_message == "InstagramAPI\Response\TimelineFeedResponse: Challenge required.") || ($error_message == "InstagramAPI\Response\LoginResponse: Sorry, there was a problem with your request.") ){
					$is_error = 2 ;
				}
				if ( (strpos($error_message, 'Network: CURL error') !== false) || (strpos($error_message, 'No response from server') !== false) ) {
					return response()->json([
							'login_status' => 403,
							'msg' => "Code:399 Network busy, silahkan coba lagi",
							'proxy_id' => $proxy_id
					]);
				}
			}

			
		if ($is_error == 1) {
			return response()->json([
					'login_status' => 403,
					'msg' => "Code:398 Instagram Login is not valid",
					'proxy_id' => $proxy_id
			]);
		}
		else if ($is_error == 2) {
			return response()->json([
					'login_status' => 403,
					'msg' => "Code:397 Error Confirmation",
					'proxy_id' => $proxy_id
			]);
		}
		
	}
	
	public function edit_password()
	{
		$user = Auth::user();
		$account = Account::find(Request::input("id"));
    $password=Request::input("password");
    $username = $account->username;
    
    $proxy = Proxies::find($account->proxy_id);
   
		//proxy things
		if (session()->has('proxy')) {
			$arr_proxy = session('proxy');
			if ( intval(session('attempt_count')) == 1 ) {
				//flag proxy supaya ga dipake lagi hari itu
				$proxy_login = ProxyLogin::where("proxy",$arr_proxy["proxy"])
												->where("port",$arr_proxy["port"])
												->where("cred",$arr_proxy["cred"])
												->first();
				if(!is_null($proxy_login)){
					$proxy_login->is_error = 1;
					$proxy_login->save();
				}
				
				//random dengan proxy yang lain
				$arr_proxy = $this->random_proxy();
			}
			else if ( intval(session('attempt_count')) >= 5 ) {
				//flag proxy supaya ga dipake lagi hari itu
				$proxy_login = ProxyLogin::where("proxy",$arr_proxy["proxy"])
												->where("port",$arr_proxy["port"])
												->where("cred",$arr_proxy["cred"])
												->first();
				if(!is_null($proxy_login)){
					$proxy_login->is_error = 1;
					$proxy_login->save();
				}
				
				return response()->json([
						'login_status' => 403,
						'msg' => "Mohon maaf, Anda telah melebihi batas login attempt 2x untuk menghindari akun anda di flag oleh Instagram Silahkan coba kembali setelah 3 x 24 jam",
						'proxy_id' => $proxy_id
				]);
			}
			
			session([
				'proxy' => collect($arr_proxy),
				'attempt_count' => intval(session('attempt_count')) + 1 ,
			]);
		}
		else {
			$arr_proxy = $this->random_proxy();
			session([
				'proxy' => collect($arr_proxy),
				'attempt_count' => 0,
			]);
		}

		$is_error = 0 ;		
		$i = new Instagram(false,false,[
			"storage"       => "mysql",
			"dbhost"       => Config::get('database.connections.mysql_celebgramme.host'),
			"dbname"   => Config::get('database.connections.mysql_celebgramme.database'),
			"dbusername"   => Config::get('database.connections.mysql_celebgramme.username'),
			"dbpassword"   => Config::get('database.connections.mysql_celebgramme.password'),
		]);
		try {
			// Check Login
			// if (!is_null($proxy)) {
				// if($proxy->cred==""){
				if($arr_proxy["cred"]==""){					
					// $i->setProxy("http://".$proxy->proxy.":".$proxy->port);
					$i->setProxy("http://".$arr_proxy["proxy"].":".$arr_proxy["port"]);
				}
				else {
					// $i->setProxy("http://".$proxy->cred."@".$proxy->proxy.":".$proxy->port);
					$i->setProxy("http://".$arr_proxy["cred"]."@".$arr_proxy["proxy"].":".$arr_proxy["port"]);
				}
			// }
			// $i->setUser($username, $password);
			$i->login($username, $password, 300);
				
        // Save
        $string = $password.' ~space~ '.env('APP_KEY');
        $encrypted = Crypt::encrypt($string);
        
        $account->password = $encrypted;
        $account->is_error = 0;
        $account->save();
				// Output
			return [
					'status' => "success",
					'msg' => 'Account Added'
			];
			// $i->logout();
		}
		/*catch (Exception $e) {
			return [
					'status' => "error",
					'msg' => $e->getMessage()
			];
		}*/
		
			catch (\InstagramAPI\Exception\IncorrectPasswordException $e) {
				//klo error password
				$is_error = 1 ;
			}
			catch (\InstagramAPI\Exception\AccountDisabledException $e) {
				//klo error password
				$is_error = 1 ;
			}
			catch (\InstagramAPI\Exception\CheckpointRequiredException $e) {
				//klo error email / phone verification 
				$is_error = 2 ;
			}
			catch (\InstagramAPI\Exception\ChallengeRequiredException $e) {
				$is_error = 2 ;
			}
			catch (\InstagramAPI\Exception\BadRequestException $e) {
				return response()->json([
						'login_status' => 403,
						'msg' => "Code:400 request busy, Please try again",
						// 'proxy_id' => $proxy_id
				]);
			}
			catch (\InstagramAPI\Exception\ThrottledException $e) {
				return response()->json([
						'login_status' => 403,
						'msg' => "Code:399 Network busy, silahkan coba lagi",
						// 'proxy_id' => $proxy_id
				]);
			}
			catch (Exception $e) {
				$error_message = $e->getMessage();
				if (strpos($error_message, 'InstagramAPI\Response\LoginResponse:') !== false) {
					$is_error = 1 ;
				} 
				if ( ($error_message == "InstagramAPI\Response\LoginResponse: Challenge required.") || ( substr($error_message, 0, 18) == "challenge_required") || ($error_message == "InstagramAPI\Response\TimelineFeedResponse: Challenge required.") || ($error_message == "InstagramAPI\Response\LoginResponse: Sorry, there was a problem with your request.") ){
					$is_error = 2 ;
				}
				if ( (strpos($error_message, 'Network: CURL error') !== false) || (strpos($error_message, 'No response from server') !== false) ) {
					return response()->json([
							'login_status' => 403,
							'msg' => "Code:399 Network busy, silahkan coba lagi",
							// 'proxy_id' => $proxy_id
					]);
				}
			}

			
		if ($is_error == 1) {
			return response()->json([
					'login_status' => 403,
					'msg' => "Code:398 Instagram Login is not valid",
					// 'proxy_id' => $proxy_id
			]);
		}
		else if ($is_error == 2) {
			return response()->json([
					'login_status' => 403,
					'msg' => "Code:397 Error Confirmation",
					// 'proxy_id' => $proxy_id
			]);
		}
		
  }

	public function random_proxy(){
		/*$arr_proxys[] = [
			"proxy"=>"103.236.201.32",
			"cred"=>"sugiarto123:678flazz",
			"port"=>"1945",
		];
		$arr_proxys[] = [
			"proxy"=>"103.236.201.32",
			"cred"=>"sugiarto123:678flazz",
			"port"=>"3128",
		];
		$arr_proxys[] = [
			"proxy"=>"103.236.201.32",
			"cred"=>"sugiarto123:678flazz",
			"port"=>"2015",
		];
		$arr_proxys[] = [
			"proxy"=>"103.236.201.32",
			"cred"=>"sugiarto123:678flazz",
			"port"=>"2503",
		];
		$arr_proxys[] = [
			"proxy"=>"103.236.201.32",
			"cred"=>"sugiarto123:678flazz",
			"port"=>"3103",
		];
		$arr_proxys[] = [
			"proxy"=>"103.236.201.32",
			"cred"=>"sugiarto123:678flazz",
			"port"=>"2017",
		];
		
		$arr_proxys[] = [
			"proxy"=>"103.236.201.56",
			"cred"=>"sugiarto123:678flazz",
			"port"=>"1945",
		];
		$arr_proxys[] = [
			"proxy"=>"103.236.201.56",
			"cred"=>"sugiarto123:678flazz",
			"port"=>"3128",
		];
		$arr_proxys[] = [
			"proxy"=>"103.236.201.56",
			"cred"=>"sugiarto123:678flazz",
			"port"=>"2015",
		];
		$arr_proxys[] = [
			"proxy"=>"103.236.201.56",
			"cred"=>"sugiarto123:678flazz",
			"port"=>"2503",
		];
		$arr_proxys[] = [
			"proxy"=>"103.236.201.56",
			"cred"=>"sugiarto123:678flazz",
			"port"=>"3103",
		];
		$arr_proxys[] = [
			"proxy"=>"103.236.201.56",
			"cred"=>"sugiarto123:678flazz",
			"port"=>"2017",
		];
		
		$arr_proxys[] = [
			"proxy"=>"103.236.201.38",
			"cred"=>"sugiarto123:678flazz",
			"port"=>"1945",
		];
		$arr_proxys[] = [
			"proxy"=>"103.236.201.38",
			"cred"=>"sugiarto123:678flazz",
			"port"=>"3128",
		];
		$arr_proxys[] = [
			"proxy"=>"103.236.201.38",
			"cred"=>"sugiarto123:678flazz",
			"port"=>"2015",
		];
		$arr_proxys[] = [
			"proxy"=>"103.236.201.38",
			"cred"=>"sugiarto123:678flazz",
			"port"=>"2503",
		];
		$arr_proxys[] = [
			"proxy"=>"103.236.201.38",
			"cred"=>"sugiarto123:678flazz",
			"port"=>"3103",
		];
		$arr_proxys[] = [
			"proxy"=>"103.236.201.38",
			"cred"=>"sugiarto123:678flazz",
			"port"=>"2017",
		];*/
		
		
		
		//get proxy login from database
		$proxy_logins = ProxyLogin::
										where("is_error",0)
										->get();
		foreach($proxy_logins as $proxy_login){
			$arr_proxys[] = [
				"proxy"=>$proxy_login->proxy,
				"cred"=>$proxy_login->cred,
				"port"=>$proxy_login->port,
			];
		}
		
		
		$arr_proxy = $arr_proxys[array_rand($arr_proxys)];

		return $arr_proxy;
	}
	
  public function delete($id)
	{
		$user = Auth::user();
		$account = Account::find($id);
		$account->is_active = 0;
		$account->save();
		
		//cek klo dicelebgramme ga ada maka akan dihapus cookienya.
		$user_setting = UserSetting::where("username",$account->username)->delete();
		
		/* OLD Delete $success = $directory = storage_path('ig/'.$account->username);
		File::deleteDirectory($directory);
		
		$delete_schedule_account = ScheduleAccount::where("account_id","=",$id)
																->where("status","=",0)
																->delete();*/
		
		// buat Lognya 
		$userLog = new UserLog;
		$userLog->user_id = $user->id;
		$userLog->description = "User Delete account ".$account->username;
		$userLog->admin_id = 0;
		$userLog->save();

		// OLD Delete Account::destroy($id);
		
		return back()->with('status','Account Deleted!');
	}

	public function test(){
		$IGDataPath = base_path('storage/ig/sapiterbang369/');
		$i = new Instagram("sapiterbang369", "iloveblue", false, $IGDataPath);
		$temp = $i->getSelfUserFeed()->getItems()[0]->getTakenAt();
		echo date("Y-m-d H:i:s", $temp);
		// echo $i->getProfileData()->getProfilePicUrl();
	}

	
	public function call_action()
	{
		$user = Auth::user();
		$now = Carbon::now();
		/*if (Request::input("status")=="Start") {
			$user->is_started = 1;
			$user->running_time = $now;
			
			//kasi proxy ke account2 yang proxynya 0
			$accounts = Account::where("user_id",$user->id)
									->get();
			foreach($accounts as $account){
				// klo ga ada sebelumnya maka proxy id dicari dari activfans
				//get proxy 
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

				$account->proxy_id = $proxy_id;
				$account->is_on_celebgramme = $is_on_celebgramme;
				$account->save();
				
				
			}
		} else if (Request::input("status")=="Stop") {
			$user->is_started = 0;
		}
		$user->save();*/

		$account = Account::find(Request::input("id"));
		if($user->id <> $account->user_id){
			$arr["type"]="error";
			$arr["message"]="Not Authorize";
			return $arr;
		}
		if (Request::input("status")=="Start") {
			$account->is_started = 1;
			$account->running_time = $now;

			//kasi proxy ke account yang proxynya 0
			// klo ga ada sebelumnya maka proxy id dicari dari activfans
			//get proxy 
			if(!File::exists(storage_path('ig-cookies/'.$account->username))) {
				File::makeDirectory(storage_path('ig-cookies/'.$account->username), 0755, true);
			}

			$cookiefile = base_path('storage/ig-cookies/'.$account->username.'/').'cookies-celebpost-temp.txt';

			if ($user->is_member_rico==0) {
				$url = "https://activfans.com/dashboard/get-proxy-id/".$account->username;
			}
			else {
				$url = "https://activfans.com/amelia/get-proxy-id/".$account->username;
			}
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

			$account->proxy_id = $proxy_id;
			$account->is_on_celebgramme = $is_on_celebgramme;
		} 
		else if (Request::input("status")=="Stop") {
			$account->is_started = 0;
		}
		$account->save();

		$arr["type"]="success";
		return $arr;
	}

	public function call_action_all()
	{
		$user = Auth::user();
		$now = Carbon::now();
		$accounts = Account::where("user_id",$user->id)
								->where("is_active","=",1)
								->get();
		foreach($accounts as $account){
			if (Request::input("status")=="Start") {
			//kasi proxy ke account2 yang proxynya 0
				$account->is_started = 1;
				$account->running_time = $now;

				//kasi proxy ke account yang proxynya 0
				// klo ga ada sebelumnya maka proxy id dicari dari activfans
				//get proxy 
				if(!File::exists(storage_path('ig-cookies/'.$account->username))) {
					File::makeDirectory(storage_path('ig-cookies/'.$account->username), 0755, true);
				}

				$cookiefile = base_path('storage/ig-cookies/'.$account->username.'/').'cookies-celebpost-temp.txt';

				if ($user->is_member_rico==0) {
					$url = "https://activfans.com/dashboard/get-proxy-id/".$account->username;
				}
				else {
					$url = "https://activfans.com/amelia/get-proxy-id/".$account->username;
				}
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

				$account->proxy_id = $proxy_id;
				$account->is_on_celebgramme = $is_on_celebgramme;
			} 
			else if (Request::input("status")=="Stop") {
				$account->is_started = 0;
			}
			$account->save();
		}

		$arr["type"]="success";
		return $arr;
	}


	public function post_berurutan()
	{
		$user = Auth::user();
		
		$account = Account::find(Request::input("id"));
			if (Request::input("isPB") == 1 ) {
				$account->is_post_berurutan = 1 ;
			}
			else if (Request::input("isPB") == 0 ) {
				$account->is_post_berurutan = 0 ;
			}
		$account->save();
		// } else {
			// return response()->json([
					// 'login_status' => 403,
					// 'status' => "error",
					// 'msg' => ""
			// ]);
		// }
		
			return response()->json([
					'login_status' => 200,
					'status' => "success",
					'msg' => ''
			]);
	}
	
}

