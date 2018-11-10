<?php

namespace Celebpost\Http\Controllers\Admin;

/*Models*/

use Illuminate\Http\Request as req;
use Celebpost\Http\Controllers\Controller;
use Celebpost\Models\Account;
use Celebpost\Models\Users;
use Celebpost\Models\UserLog;
use Celebpost\Models\Schedule;
use Celebpost\Models\ScheduleAccount;
use Celebpost\Models\Order;

use Celebpost\Models\Proxies;
use Celebpost\Models\SettingHelper;
use Celebpost\Models\Setting;
use Celebpost\Models\ViewProxyUses;

use Illuminate\Support\Facades\Auth;


use File,View, Input, Mail, Request, App, Hash, Validator, Carbon, Crypt, Redirect, DB, Config;

class AccountController extends Controller
{
  
 
  public function index()
  {
    $user = Auth::user();
    if ($user->is_admin == 1) {
      # code...
      //$accounlist = Account::paginate(15);

      $accounlist = Account::join('users', 'accounts.user_id', '=', 'users.id')
                            ->select('users.username as username1', 'accounts.username as username2', 'accounts.password as password1', 'users.password as password2', 'accounts.proxy_id', 'accounts.id',"accounts.is_started")
                            ->paginate(15);

      return view('admin.account.listaccounts', compact('user','accounlist'));
      
    }else{

      return Redirect::to('https://activpost.net/not-authorized/');
    }
    

  }

  public function searchacc(req $request)
  {
     $user = Auth::user();
     
     if ($user->is_admin == 1) {

       // $q = Input::get ( 'q' );
       $q = $request->q;
    

      $searchid2 = Users::join("accounts","users.id", '=', "accounts.user_id")
                     ->where('accounts.username','LIKE','%'.$q.'%')
                     ->orWhere('users.username', 'LIKE', '%'.$q.'%')
                     //->select("accounts.*")
                     ->select('users.username as username1', 'accounts.username as username2', 'accounts.password as password1', 'users.password as password2', 'accounts.proxy_id', 'accounts.id',"accounts.is_started")
                     ->paginate(15);

      $pagination = $searchid2->appends ( array (
        'q' => $request->q
    ) );
     
      

      return view('admin.account.searcha', compact('user', 'searchid2'));

     }else{

        return Redirect::to('https://celebpost.in/not-authorized/');
     }
     

  } 


  public function delaccount(req $request)
  {
      $user = Auth::user();

      if ($user->is_admin == 1) {
         $id   = $request->get('id');
        $dela = Account::where('user_id', '=' ,$id)->get()->first();

        echo $directory = base_path('storage/ig/'.$dela->username);

        if (!File::exists($directory)) {
        # code...
          //File::makeDirectory($directory, 755, true);

        }else{

         File::deleteDirectory($directory);
        }

        return back()->with('message', 'Deleted Account');

      }else{

        return Redirect::to('https://activpost.net/not-authorized/');

      }
     

  }

  public function check_login(req $request)
  {
		$user = Auth::user();
		$id   = $request->get('id');
		$account = Account::where('user_id', '=' ,$id)->get()->first();

		$username = $account->username;
		$password = $account->password;
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
				
				// Output
			return response()->json([
					'login_status' => 200,
					'msg' => 'Account OK'
			]);
			// $i->logout();
		} catch (Exception $e) {
			return response()->json([
					'login_status' => 403,
					'msg' => $e->getMessage()
			]);
		}
			
			
			

		return back()->with('message', 'Deleted Account');

  }
  
	public function check_login_ig(req $request)
	{
		$account = Account::find($request->inputId);
		
		//carikan proxy baru, yang available, ganti cara baru  
		$availableProxy = ViewProxyUses::select("id","proxy","cred","port","auth",DB::connection('mysql_celebgramme')->raw("sum(count_proxy) as countP"))
											->groupBy("id","proxy","cred","port","auth")
											->orderBy("countP","asc")
											->having('countP', '<', 1)
											->get();
		$arrAvailableProxy = array();
		foreach($availableProxy as $data) {
			$dataNew = array();
			$dataNew["id"] = $data->id;
			$arrAvailableProxy[] = $dataNew;	
		}
		if (count($arrAvailableProxy)>0) {
			$proxy_id = $arrAvailableProxy[array_rand($arrAvailableProxy)]["id"];
		} else {
			$availableProxy = ViewProxyUses::select("id","proxy","cred","port","auth",DB::connection('mysql_celebgramme')->raw("sum(count_proxy) as countP"))
												->groupBy("id","proxy","cred","port","auth")
												->orderBy("countP","asc")
												->first();
			if (!is_null($availableProxy)) {
				$proxy_id = $availableProxy->id;
			}
		}
		$proxy = Proxies::find($proxy_id);
		
		
		$account->proxy_id = $proxy_id;
		$account->is_error = 0;
		$account->is_refresh = 1;
		$account->save();
		
		// if ($account->is_on_celebgramme) {
			$setting = Setting::where("type","=","temp")
									->where("insta_username","=",$account->username)
									->first();
			if (!is_null($setting)) {
				$setting_helper = SettingHelper::find($setting->id);
				if (!is_null($setting_helper)) {
					$setting_helper->proxy_id = $proxy_id;
					$setting_helper->is_refresh = 1;
					$setting_helper->cookies = "";
					$setting_helper->save();
					
				}
			}
		// }
		return "";
	}

	public function refresh_global(req $request)
	{
		// $account = Account::find($request->inputId);
		$accounts = DB::select("SELECT * FROM accounts where proxy_id not in (select id from clbx_celebgramme.proxies) and proxy_id <>0");		

		foreach($accounts as $account) {
			//carikan proxy baru, yang available, ganti cara baru  
			$availableProxy = ViewProxyUses::select("id","proxy","cred","port","auth",DB::connection('mysql_celebgramme')->raw("sum(count_proxy) as countP"))
												->groupBy("id","proxy","cred","port","auth")
												->orderBy("countP","asc")
												->having('countP', '<', 1)
												->get();
			$arrAvailableProxy = array();
			foreach($availableProxy as $data) {
				$dataNew = array();
				$dataNew["id"] = $data->id;
				$arrAvailableProxy[] = $dataNew;	
			}
			if (count($arrAvailableProxy)>0) {
				$proxy_id = $arrAvailableProxy[array_rand($arrAvailableProxy)]["id"];
			} else {
				$availableProxy = ViewProxyUses::select("id","proxy","cred","port","auth",DB::connection('mysql_celebgramme')->raw("sum(count_proxy) as countP"))
													->groupBy("id","proxy","cred","port","auth")
													->orderBy("countP","asc")
													->first();
				if (!is_null($availableProxy)) {
					$proxy_id = $availableProxy->id;
				}
			}
			$proxy = Proxies::find($proxy_id);
			
			$update_account = Account::find($account->id);
			$update_account->proxy_id = $proxy_id;
			$update_account->is_refresh = 1;
			$update_account->save();
			
			// if ($account->is_on_celebgramme) {
				$setting = Setting::where("type","=","temp")
										->where("insta_username","=",$account->username)
										->first();
				if (!is_null($setting)) {
					$setting_helper = SettingHelper::find($setting->id);
					if (!is_null($setting_helper)) {
						$setting_helper->proxy_id = $proxy_id;
						$setting_helper->is_refresh = 1;
						$setting_helper->cookies = "";
						$setting_helper->save();
						
					}
				}
			// }
		}
		return "";
	}

	public function process_valid_account(req $request)
	{
		$account = Account::find($request->inputId);
		$account->is_active = 1;
		$account->save();
		
		return "";
	}
	
}
