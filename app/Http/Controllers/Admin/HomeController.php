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
use Illuminate\Support\Facades\Auth;


use File,View, Input, Mail, Request, App, Hash, Validator, Carbon, Crypt, Redirect, Excel;

class HomeController extends Controller
{
  
	public function test(){
  }

	/**
	 * Menampilkan halaman utama
	 *
	 * @return response
	 */
	public function index(){
    
    $user = Auth::user();

		return view('member.auto-manage.index')->with(array('user'=>$user,));
	}
  
  /**
   * Mengirim ulang email aktivasi
   *
   * @return response
   */
  public function resendEmailActivation()
  {
    $user = Auth::user();
    
    $register_time = Carbon::now()->toDateTimeString();
    $verificationcode = Hash::make($user->email.$register_time);
    $user->verification_code = $verificationcode;
    $user->save();
		
    $secret_data = [
      'email' => $user->email,
      'register_time' => $register_time,
      'verification_code' => $verificationcode,
    ];
    $emaildata = [
      "url" => url("verifyemail")."/".Crypt::encrypt(json_encode($secret_data)),
			'user' => $user,
			'password' => "",
    ];
    Mail::send('emails.confirm-email', $emaildata, function ($message) use ($user) {
      $message->from('no-reply@activfans.com', 'activfans');
      $message->to($user->email);
      $message->subject('Email Confirmation');
    });
    $arr = array (
      "message"=>"Email aktivasi berhasil dikirim",
      "type"=>"success",
      );
    return $arr;
  }
	
	
	public function research(){
		return view('user.search-hashtags.index');
  }


  public function search(req $request)
  {
      $user = Auth::user();
      
      //$query  = Input::get('q');
      //$query = Request::get('q');
       $accounts = Account::where("user_id","=",$user->id)
                ->orderBy('username','asc')->paginate(15);

      if ($query  = $request->get('q')) {
        # code...
        $usern = Users::where('username','LIKE','%' . $query. '%')->paginate(15);
        return view('admin.search',compact('accounts','user','usern'));
      }else
      {
        $usern = Users::paginate(15);
        return view('admin.search',compact('accounts','user','usern'));
      }

  }  

  public function show(req $request)
  {
        $user = Auth::user();
        $id= $request->get('id');
        //$id = 1;
        //$blog = Users::where('id','=',$id)

        $blog = Users::find($id);
         //$logs = UserLog::find($id);
        $logs = UserLog::where('user_id', '=' , $id)->get()->first();
          //$logs = DB::table('user_logs')->where('user_id', '=', 1)->get();

        return view('admin.show',compact('blog','user','logs'))->renderSections()['content'];
  }

  public function showedit(req $request)
  {

        $user = Auth::user();
        $id= $request->get('id');
        $cruds = Users::findOrFail($id);
        return view('admin.showedit', compact('cruds','user'))->renderSections()['content'];
  }


  public function updatetime(req $request)
  {

        $user = Auth::user();
  
        $active_d = strtotime(''.$request->time_d.' day 0 second', 0);
        $active_h = strtotime(''.$request->time_h.' hour 0 second', 0);
        $active_m = strtotime(''.$request->time_m.' minutes 0 second', 0);

        $timeactive = $active_d + $active_h + $active_m;

        $cruds = Users::findOrFail($request->id);
        $cruds->active_time = $timeactive;
        $cruds->save();

        //$savelog = UsersLog::findOrFail($request->id);
        //$savelog->
       
         //return Redirect::to('/');
         return back()->with('message', 'Time Success Update');
     
  }
  

  public function delete(req $request)
  {

    $user = Auth::user();
    $id = $request->get('id');


  $account = Account::where('user_id', '=' , $id)->get();

  foreach ($account as $acct) {

    $directory = base_path('storage/ig/'.$acct->username);

    
    if (!File::exists($directory))
    {
      
      //File::makeDirectory($directory, 755, true);

    }else
    {
      
      File::deleteDirectory($directory);
      //Account::where('user_id', '=', $acct->user_id)->delete();
     
    }
      
    
    }

    return back()->with('message','Account Deleted!');
    //return Redirect::to('/');
  
  }


  public function showmaxaccount(req $request)
  {
      $user = Auth::user();
      $id = $request->get('id');

  
      $id= $request->get('id');
      $cruds = Users::findOrFail($id);
       return view('admin.showeditmaxaccount', compact('cruds','user'))->renderSections()['content'];
      
  }


  public function updatemaxaccount(req $request)
  {
      $user = Auth::user();

      $cruds = Users::findOrFail($request->id);
      $cruds->max_account = $request->max_account;
      $cruds->save();

      return back()->with('message', 'Max Account Success Update');
      
  }

  public function viatt()
  {
      $user = Auth::user();

      return view('admin.viatx', compact('user'))->renderSections()['content'];
  }

  public function importxls(req $request)
  {
    $admin = Auth::user();

    if ($admin->is_admin == 1) {
			//$path = Input::file('import_file')->getRealPath();
			//echo $days = $request->time_d;
			$active_d = strtotime(''.$request->time_d.' day 0 second', 0);
			$data = Excel::load(Input::file('import_file'), function($reader) {

			})->get();


			if(!empty($data) && $data->count()){
				foreach ($data as $key) {
					foreach ($key as $value) {
						if (!filter_var($value->email, FILTER_VALIDATE_EMAIL) === false) {						
							/*
							$insert[] = ['username' => $value->email, 'name' => $value->name, 'email' => $value->email, 'password' => $pass, 'is_confirmed' => 1, 'is_admin' => 0, 'is_started' => 0, 'active_time' => $active_d, 'remember_token' => $value->remember_token, 'created_at' => $value->created_at, 'updated_at' => $value->updated_at, 'timezone' => $value->timezone, 'verificationcode' => $value->verificationcode, 'max_account' => $value->max_account, 'valid_until' => $value->valid_until, 'last_seen' => $value->last_seen];*/
							$ad = $value->email;
							$userer = Users::where('email', '=', $ad)->first();

							

							if ( is_null($userer) ) {
								$user = new Users;
								$user->username = $value->email;
								$user->name = $value->name;
								$user->email = $value->email;
								$pas = $value->username.$value->name;
								$gh = substr($pas, 0,6);
								$chrnd =substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',5)),0,5);
							 //echo $passd = 'Password : '.$gh.$chrnd.'<br>';
								$passd = str_replace(' ','', $gh.$chrnd) ;
								$user->password = Hash::make($passd);
								$user->is_confirmed = 1;
								$user->is_admin = 0;
								$user->is_started = 0;
								$user->active_time = $active_d;
								$user->remember_token = $value->remember_token;
								$user->created_at = $value->created_at;
								$user->updated_at = $value->updated_at;
								$user->timezone = $value->timezone;
								$user->verificationcode = $value->verificationcode;
								$user->max_account = $request->max_account;
								$user->last_seen = $value->last_seen;
								$user->save();
								
								
								$data = [
									"email" => $user->username,
									"password" => $passd,
								];
								Mail::send('emails.register.welcome', $data, function ($message) use ($data) {
									$message->from('no-reply@activpost.net', 'Activpost');
									$message->to($data['email']);
									$message->subject('[Activpost] Verify Email');
								});
								
									


							}
							else{
								$t = $request->time_d * 86400;
								$days = floor($t / (60*60*24));
								$hours = floor(($t / (60*60)) % 24);
								$minutes = floor(($t / (60)) % 60);
								$seconds = floor($t  % 60);
								$time = $days."D ".$hours."H ".$minutes."M ".$seconds."S ";

								$user_log = new UserLog;
								$user_log->user_id = $userer->id;
								$user_log->admin_id = $admin->id;
								$user_log->description = "Adding time from excel. ".$time;
								$user_log->save();
								$userer->active_time += $active_d ;
								$userer->save();
								
								$emaildata = [
										'user' => $userer,
								];
								Mail::send('emails.notify-adding-time-user', $emaildata, function ($message) use ($userer) {
									$message->from('no-reply@activpost.net', 'Activpost');
									$message->to($userer->email);
									$message->subject('[Activpost] Bonus Activpost');
								});
						
						 
							}
							
						}
					}
				}

				if(!empty($insert)){

				
				// Users::insert($insert);

				}

			}

			return back();
		}else{

      return Redirect::to('https://activpost.net/not-authorized/');      
    }
  }

  public function loginuser(req $request)
  {

     $user = Auth::user();
     
     if ($user->is_admin == 1) {

       $user = Auth::loginUsingId($request->get('id'));
     }
    

    return Redirect::to('/');

  }
	
}
