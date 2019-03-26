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
use Celebpost\Models\Refund;
use Celebpost\Models\AdminLog;
use Celebpost\Models\TimeLog;
use Illuminate\Support\Facades\Auth;


use File,View, Input, Mail, Request, App, Hash, Validator, Carbon, Crypt, Redirect, Excel;

class UserController extends Controller
{
  
  public function test(){
  }

  /**
   * Menampilkan halaman utama
   *
   * @return response
   */
  public function index(){
    
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
      return Redirect::to('https://activpost.net/not-authorized/');
    }
  }

  public function listuser()
  {

    $user = Auth::user();

    if ($user->is_admin == 1) {
      
      $accounts = Account::where("user_id", "=" , $user->id)
                  ->orderBy('username', 'asc')->paginate(15);

      $usern = Users::where('is_admin',0)->paginate(15);

      return view('admin.index', compact('accounts', 'usern','user'));

    }else{

        return Redirect::to('https://activpost.net/not-authorized/');
    }

  }

  public function listadmin()
  {

    $user = Auth::user();

    if ($user->is_admin == 1 && ($user->email=='admin@demo.com' || $user->email=='puspita.celebgramme@gmail.com' || $user->email=='it.axiapro@gmail.com')) {
      
      $accounts = Account::where("user_id", "=" , $user->id)
                  ->orderBy('username', 'asc')->paginate(15);

      $usern = Users::where('is_admin',1)->paginate(15);

      $jmluser = Users::where('is_admin',1)->count();

      return view('admin.user-admin.index')
              ->with('accounts',$accounts)
              ->with('user',$user)
              ->with('usern',$usern)
              ->with('jmluser',$jmluser);

    } else {

        return Redirect::to('https://activpost.net/not-authorized/');
    }

  }

  public function searchadmin(req $request)
  {
      $user = Auth::user();
      //$query  = $request->get('q');

      if ($user->is_admin == 1) {
       $q = $request->q;
  /*
       $accounts = Account::where("user_id","=",$user->id)
                ->orderBy('username','asc')->paginate(15);
    */
      $usern = Users::leftJoin('accounts', 'accounts.user_id', '=', 'users.id' )
                          ->where('users.is_admin',1)
                          ->where('users.email', 'LIKE', '%'.$q.'%')
                          ->orWhere('accounts.username', 'LIKE', '%'.$q.'%')
                          ->select('users.username', 'users.name', 'users.email', 'users.active_time', 'users.id')
                          
                          ->paginate(15);
                          
        return view('admin.user-admin.index',compact('accounts','user','usern'));

      }else{

        return Redirect::to('https://activpost.net/not-authorized/');

      }
  }  

  public function listuseraffiliate()
  {

    $user = Auth::user();

    if ($user->is_admin == 1) {
      
      $accounts = Account::where("user_id", "=" , $user->id)
                  ->orderBy('username', 'asc')->paginate(15);

      $usern = Users::where('is_admin',0)
                ->where('is_member_rico',1)->paginate(15);

      $jmluser = Users::where('is_admin',0)
                  ->where('is_member_rico',1)->count();

      return view('admin.user-affiliate.index')
              ->with('accounts',$accounts)
              ->with('user',$user)
              ->with('usern',$usern)
              ->with('jmluser',$jmluser);

    } else{

        return Redirect::to('https://activpost.net/not-authorized/');
    }

  }

  public function searchaffiliate(req $request)
  {
      $user = Auth::user();
      //$query  = $request->get('q');

      if ($user->is_admin == 1) {
       $q = $request->q;
  /*
       $accounts = Account::where("user_id","=",$user->id)
                ->orderBy('username','asc')->paginate(15);
    */
      $usern = Users::leftJoin('accounts', 'accounts.user_id', '=', 'users.id' )
                          ->where('users.is_member_rico',1)
                          ->where('users.is_admin',0)
                          ->where('users.email', 'LIKE', '%'.$q.'%')
                          ->orWhere('accounts.username', 'LIKE', '%'.$q.'%')
                          ->select('users.username as username1', 'users.name', 'users.email', 'users.active_time', 'users.id')
                          
                          ->paginate(15);
                          
        return view('admin.search',compact('accounts','user','usern'));

      }else{

        return Redirect::to('https://activpost.net/not-authorized/');

      }
  }  
  
  public function searchrefund(req $request)
  {
      $user = Auth::user();
      //$query  = $request->get('q');

      if ($user->is_admin == 1) {
       $q = $request->q;
  /*
       $accounts = Account::where("user_id","=",$user->id)
                ->orderBy('username','asc')->paginate(15);
    */
      $usern = Refund::join('users','refund.user_id','=','users.id')
                  ->where('users.email', 'LIKE', '%'.$q.'%')
                  ->where('users.is_admin',0)
                  ->select('refund.*','users.name','users.email')        
                  ->paginate(15);
                
        return view('admin.user-refund.index',compact('accounts','user','usern'));

      }else{

        return Redirect::to('https://activpost.net/not-authorized/');

      }
      

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

      if ($user->is_admin == 1) {
        $q = $request->q;
        $usern = Users::
                      // leftJoin('accounts', 'accounts.user_id', '=', 'users.id' )  
                      where('is_admin',0)
                      ->where('users.email', 'LIKE', '%'.$q.'%')
                      // ->orWhere('accounts.username', 'LIKE', '%'.$q.'%')
                      ->select('users.username as username1', 'users.name', 'users.email', 'users.active_time', 'users.id')
                      // ->groupBy('users.username', 'users.name', 'users.email', 'users.active_time', 'users.id')
                      ->paginate(15); ////INI MASI SLOW
        return view('admin.search',compact('user','usern'));
      }else{
        return Redirect::to('https://activpost.net/not-authorized/');
      }
      

  }  

  public function show(req $request)
  {
        $user = Auth::user();

        if ($user->is_admin == 1) {
          
          $id= $request->get('id');
        //$id = 1;
        //$blog = Users::where('id','=',$id)

        //$blog = Users::find($id);
         //$logs = UserLog::find($id);
        $logs = UserLog::where('user_id', '=' , $id)
                    ->orderBy('created_at','desc')
                    ->get();
          //$logs = DB::table('user_logs')->where('user_id', '=', 1)->get();

        return view('admin.show',compact('blog','user','logs'))->renderSections()['content'];

        }else{

          return Redirect::to('https://activpost.net/not-authorized/');
        }
        
  }

  public function show_addadmin(){
    $user = Auth::user();

    if ($user->is_admin == 1) {

      return view('admin.user-admin.add-admin')->renderSections()['content'];

    } else {

      return Redirect::to('https://activpost.net/not-authorized/');
    }
  }


  public function showemail(req $request){
    $user = Auth::user();

    if ($user->is_admin == 1) {

      $id= $request->get('id');
      $cruds = Users::findOrFail($id);
      return view('admin.showeditemail', compact('cruds','user'))->renderSections()['content'];

    } else {

      return Redirect::to('https://activpost.net/not-authorized/');
    }
  }

  public function showtimelog(req $request){
    $logs = TimeLog::where('user_id', '=' , $request->id)
                ->orderBy('created_at','desc')
                ->get();

    $arr['view'] = (string) view('admin.showtimelog')
                    ->with('logs',$logs);
    return $arr;
  }

  public function showedit(req $request)
  {

        $user = Auth::user();

        if ($user->is_admin == 1) {

        $id= $request->get('id');
        $cruds = Users::findOrFail($id);
        return view('admin.showedit', compact('cruds','user'))->renderSections()['content'];

        }else{

          return Redirect::to('https://activpost.net/not-authorized/');
        }

  }
  
  public function addadmin(req $request)
  {
        $user = Auth::user();

        if ($user->is_admin == 1) {
          $admin = new Users;
          $admin->username = $request->email;
          $admin->email = $request->email;
          $admin->name = $request->name;
          $admin->password = Hash::make($request->password);
          $admin->active_time = 0;
          $admin->is_started = 0;
          $admin->timezone = 'Asia/Jakarta';
          $admin->max_account = 0;
          $admin->is_admin = 1;
          $admin->save();

          return back()->with('message', 'Admin berhasil ditambah');
          
        } else{
          return Redirect::to('https://activpost.net/not-authorized/');

        }   
  }

  public function updatetime(req $request)
  {

        $user = Auth::user();

        if ($user->is_admin == 1) {
          $active_d = strtotime(''.$request->time_d.' day 0 second', 0);
          $active_h = strtotime(''.$request->time_h.' hour 0 second', 0);
          $active_m = strtotime(''.$request->time_m.' minutes 0 second', 0);

          $timeactive = $active_d + $active_h + $active_m;

          $cruds = Users::findOrFail($request->id);
          $cruds->active_time = $timeactive;
          $cruds->save();

          $adminlog = new AdminLog;
          $adminlog->user_id = $user->id;
          $adminlog->description = 'Tambah waktu, '.$cruds->email.', '.$request->time_d.'D '.$request->time_h.'H '.$request->time_m.'M';
          $adminlog->save();

        //$savelog = UsersLog::findOrFail($request->id);
        //$savelog->
       
         //return Redirect::to('/');
          return back()->with('message', 'Time Success Update');

        }else{

          return Redirect::to('https://activpost.net/not-authorized/');

        }
  }

  public function updateemail(req $request)
  {
        $user = Auth::user();

        if ($user->is_admin == 1) {
          $cekemail = Users::where('email',$request->email)->first();

          if(is_null($cekemail)){
            $cruds = Users::findOrFail($request->id);

            $adminlog = new AdminLog;
            $adminlog->user_id = $user->id;
            $adminlog->description = 'Ubah email, '.$cruds->email.'->'.$request->email;
            $adminlog->save();  

            $cruds->email = $request->email;
            $cruds->username = $request->email;
            $cruds->save();

            return back()->with('message', 'Email Success Update');
          } else {
            return back()->with('message', 'Email sudah terdaftar');
          }   
        } else{
          return Redirect::to('https://activpost.net/not-authorized/');

        }   
  }
  

  public function delete(req $request)
  {

    $user = Auth::user();

    if ($user->is_admin == 1) {
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
    }else{

      return Redirect::to('https://activpost.net/not-authorized/');

    }
    
  
  }


  public function showmaxaccount(req $request)
  {
      $user = Auth::user();

      if ($user->is_admin ==  1) {
        
         $id = $request->get('id');

  
        $id= $request->get('id');
        $cruds = Users::findOrFail($id);

        return view('admin.showeditmaxaccount', compact('cruds','user'))->renderSections()['content'];

      }else{

        return Redirect::to('https://activpost.net/not-authorized/');
      }
          
  }


  public function updatemaxaccount(req $request)
  {
      $user = Auth::user();

      if ($user->is_admin == 1) {

        $cruds = Users::findOrFail($request->id);
        $cruds->max_account = $request->max_account;
        $cruds->save();

        $adminlog = new AdminLog;
        $adminlog->user_id = $user->id;
        $adminlog->description = 'Tambah max account, '.$cruds->email.', '.$request->max_account.' akun';
        $adminlog->save();

        return back()->with('message', 'Max Account Success Update');

      }else{

        return Redirect::to('https://activpost.net/not-authorized/');

      }

      
      
  }

  public function viatt()
  {
      $user = Auth::user();

      if ($user->is_admin == 1) {
        
        return view('admin.viatx', compact('user'))->renderSections()['content'];

      }else{

        return Redirect::to('https://activpost.net/not-authorized/');
      }

      
  }

  public function importxls(req $request)
  {
      //$path = $request->import_file->getRealPath();
      //echo $days = $request->time_d;
    $admin = Auth::user();

    if ($admin->is_admin == 1) {

        $active_d = strtotime(''.$request->time_d.' day 0 second', 0);
      // $data = Excel::load(Input::file('import_file'), function($reader) {
      $data = Excel::load($request->import_file, function($reader) {

      })->get();

     
    
               
                    

      if(!empty($data) && $data->count()){
        foreach ($data as $key) {
					foreach ($key as $value) {
						//echo $value->email;
						if (!filter_var($value->email, FILTER_VALIDATE_EMAIL) === false) {
							/*
							$insert[] = ['username' => $value->email, 'name' => $value->name, 'email' => $value->email, 'password' => $pass, 'is_confirmed' => 1, 'is_admin' => 0, 'is_started' => 0, 'active_time' => $active_d, 'remember_token' => $value->remember_token, 'created_at' => $value->created_at, 'updated_at' => $value->updated_at, 'timezone' => $value->timezone, 'verificationcode' => $value->verificationcode, 'max_account' => $value->max_account, 'valid_until' => $value->valid_until, 'last_seen' => $value->last_seen];*/
							$ad = $value->email;
							$userer = Users::where('email', '=', $ad)->first();
								$user_log = new UserLog;
								// $user_log->user_id = $userer->id;
								// $user_log->admin_id = $admin->id;
								$user_log->description = "Adding time from excel. ";
								$user_log->save();

							
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
							else {
								// dd($request->time_d);
								// $t = $request->time_d * 86400;
								// $days = floor($t / (60*60*24));
								// $hours = floor(($t / (60*60)) % 24);
								// $minutes = floor(($t / (60)) % 60);
								// $seconds = floor($t  % 60);
								// $time = $days."D ".$hours."H ".$minutes."M ".$seconds."S ";

								// $user_log = new UserLog;
								// $user_log->user_id = $userer->id;
								// $user_log->admin_id = $admin->id;
								// $user_log->description = "Adding time from excel. ".$time;
								// $user_log->save();
									// echo $value->email." Email sudah ada  <br>";
								// $userer->active_time += $active_d ;
								// $userer->save();
								
								// $emaildata = [
										// 'user' => $userer,
								// ];
								// Mail::send('emails.notify-adding-time-user', $emaildata, function ($message) use ($userer) {
									// $message->from('no-reply@activpost.net', 'Activpost');
									// $message->to($userer->email);
									// $message->subject('[Activpost] Bonus Activpost');
								// });
									
							}
						}
					}
        }
        if(!empty($insert)){
					Users::insert($insert);
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
  
  public function submit_refund(req $request){
    $refund = new Refund;
    $refund->user_id = $request->id_refund;
    $refund->total = $request->total;
    $refund->save();

    $user = Users::find($request->id_refund);

    $adminlog = new AdminLog;
    $adminlog->user_id = Auth::user()->id;
    $adminlog->description = 'Refund, '.$user->email.', total = Rp.'.number_format($request->total);
    $adminlog->save();

    $arr['type'] = 'success';
    $arr['message'] = 'Refund berhasil dilakukan';

    return $arr;
  }

  public function user_refund(req $request){
    $user = Auth::user();

    if ($user->is_admin == 1) {
      
      $accounts = Account::where("user_id", "=" , $user->id)
                  ->orderBy('username', 'asc')->paginate(15);

      $usern = Refund::join('users','refund.user_id','=','users.id')
                  ->select('refund.*','users.name','users.email')
                  ->where('users.is_admin',0)
                  ->paginate(15);

      //$jmluser = Users::where('is_member_rico',1)->count();

      return view('admin.user-refund.index')
              ->with('accounts',$accounts)
              ->with('user',$user)
              ->with('usern',$usern);
              //->with('jmluser',$jmluser);

    } else {

        return Redirect::to('https://activpost.net/not-authorized/');
    }
  }

  public function show_log(req $request){
    $logs = AdminLog::where('user_id',$request->id_log)
                  ->whereDate('created_at','>=',$request->from_log)
                  ->whereDate('created_at','<=',$request->to_log)
                  ->where('description','like','%'.$request->description.'%')
                  ->get();

    $arr['view'] = (string) view('admin.user-admin.content-log')
                    ->with('logs',$logs);
    return $arr;
  }
}
