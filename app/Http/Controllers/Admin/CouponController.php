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
use Celebpost\Models\OrderMeta;
use Celebpost\Models\Coupon;
use Illuminate\Support\Facades\Auth;


use File,View, Input, Mail, Request, App, Hash, Validator, Carbon, Crypt, Redirect;

class CouponController extends Controller
{
  
 
  public function index()
  {
    $user = Auth::user();

    if ($user->is_admin == 1) {
      # code...
      $liscoupon = Coupon::
			// join('users', 'coupons.user_id', '=', 'users.id')
                            orderBy('coupons.id', "desc")
                            // ->select('coupons.*','users.name','users.email','users.active_time','users.username')
                            ->paginate(15);
       


      return view('admin.coupon.index', compact('user', 'liscoupon'));
    
    }else{

      return Redirect::to('https://activpost.net/not-authorized/');
    }

  }

  public function addusercoupon (req $request)
  {
    $user = Auth::user();
    $cekuserid = Users::where("username", "LIKE", "%".$request->name."%")
                 ->first();

		$addcoupon              = new Coupon;
		$addcoupon->coupon_code = $request->coupon_code;

		if ($request->coupon_percent == '') {
			$addcoupon->coupon_value = $request->coupon_value;
			$addcoupon->coupon_percent = 0;
		}
		else if($request->coupon_value == '') {
			$addcoupon->coupon_value = 0;
			$addcoupon->coupon_percent = $request->coupon_percent;
		}

		$addcoupon->valid_until    = $request->valid_until;
		$addcoupon->package_id = 0;
		$addcoupon->user_id = $cekuserid->id;
		$addcoupon->visible = 0;
		$addcoupon->save();

		$data = [
							"email" => $cekuserid->email,
							//"password" => $passd,
							"nama" => $request->name,
							"coupon_code" => $request->coupon_code,
							"coupon_value" => $request->coupon_value,
							"coupon_percent" => $request->coupon_percent,
							"valid_until" => $request->valid_until,
							"active_time" => $cekuserid->active_time
						];
		Mail::send('emails.register.email-coupon', $data, function ($message) use ($data) {
			$message->from('no-reply@activpost.net', 'Celebpost');
			$message->to($data['email']);
			$message->subject('[Activpost] Selamat anda mendapatkan Kupon Potongan Harga');
		});
		// $datacoupon = Coupon::join("users", "coupons.user_id", "=", "users.id")
												 // ->paginate(15);

		return response ()->json ($addcoupon);

  }

  public function generatekupon(req $request)
  {
		$user = Auth::user();
		// $cektime = Users::where("active_time", "=", 604800)
                        // ->get();

		// if(!empty($cektime) && $cektime->count()){
      // foreach ($cektime as $d) {
        $addcoupons = new Coupon;
        $chrnd=substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',5)),0,5);
        $codecu = str_replace(" ", "", $chrnd);
        $addcoupons->coupon_code = $codecu;
				$addcoupons->coupon_value = $request->coupon_value;
				$addcoupons->coupon_percent = $request->coupon_percent;
        // $today = date("Y-m-d");
        // $newdate2 = date('Y-m-d 23:59',strtotime ( '+7 day' , strtotime ( $today )));
        // $newdate2 = strtotime($request->valid_until);
        $newdate2 = Carbon::createFromFormat('Y-m-d', $request->valid_until);
        $addcoupons->valid_until = $newdate2;
        // $addcoupons->user_id = $d->id;
        $addcoupons->user_id = 0;
        $addcoupons->visible = 0;
        $addcoupons->save(); 

        /*$data = [
									"email" => $d->email,
									"nama" => $d->name,
									"coupon_code" => $codecu,
									"coupon_value" =>  $request->coupon_value,
									"coupon_percent" => $request->coupon_percent,
									"valid_until" => $newdate2,
									"active_time" => $d->active_time
								];
				Mail::send('emails.register.email-coupon', $data, function ($message) use ($data) {
					$message->from('no-reply@activpost.net', 'Activpost');
					$message->to($data['email']);
					$message->subject('[Activpost] Selamat anda mendapatkan Kupon Potongan Harga');
				});*/
      // }
    // }
    return ;
  }


  public function generatecron()
  {
    $user = Auth::user();

    //$cekid = Users::get();
    //$arr = array(432000, 43200);

    //$val = '';

            $val = 432000;
            $cekid = Users::where("active_time", "=", $val)
                          ->get();

            if(!empty($cekid) && $cekid->count()){

              echo "<b>Sisah Waktu 5 Hari </b><br>";
              foreach ($cekid as $vel) {
               
                  
                $data = [

                    "email" => $vel->email,
                    "nama" => $vel->name,
                    

                  ];
                  Mail::send('emails.register.notif-5days', $data, function ($message) use ($data) {
                    $message->from('no-reply@celebpost.in', 'Celebpost');
                    $message->to($data['email']);
                    $message->subject('[Celebpost] Masa Berlangganan akan Berakhir 5 Hari lagi');
                  });
                  

                  echo $vel->name ." sisah waktu  ". intval(intval($vel->active_time)/(3600*24))." Hari<br>";
              }

              echo "<br><br>";

            }
          

            $val2 = 43200;
            $cekid = Users::where("active_time", "=", $val2)
                            ->get();
                            

            if(!empty($cekid) && $cekid->count()){
               echo "<b>Sisah Waktu 12 </b><br>";
              foreach ($cekid as $vel) {

                $addcoupons = new Coupon;
                $chrnd =substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',5)),0,5);
                $codecu = str_replace(" ", "", $chrnd);
                $addcoupons->coupon_code = $codecu;
                $addcoupons->coupon_value = 0;
                $addcoupons->coupon_percent = 10;
                $today = date("Y-m-d");
                $newdate2 = date('Y-m-d 23:59',strtotime ( '+5 day' , strtotime ( $today )));
                $addcoupons->valid_until = $newdate2;
                $addcoupons->user_id = $vel->id;
                $addcoupons->visible = 0 ;

                $addcoupons->save();
                $now = time();
                $datediff =  strtotime($newdate2) - $now;
                $days_coupon = floor($datediff / (60 * 60 * 24));

                $data = [

                    "email" => $vel->email,
                    "nama" => $vel->name,
                    "coupon_code" => $codecu,
                    //"coupon_value" =>  0,
                    "coupon_percent" => 10,
                    "valid_until" => $days_coupon,

                  ];
                  Mail::send('emails.register.notif-expired', $data, function ($message) use ($data) {
                    $message->from('no-reply@celebpost.in', 'Celebpost');
                    $message->to($data['email']);
                    $message->subject('[Celebpost] Selamat anda mendapatkan Kupon Potongan Harga');
                  });
                 
                  echo $vel->name ." sisah waktu  " .gmdate("H", 43200). " jam <br>";
                  //echo "Day". intval(intval($vel->active_time)/(3600*24))."<br>";
              }

              echo "<br><br>";

            }

         

        $today = date("Y-m-d");

          $cekuserc = Users::join("coupons", "users.id", "=", "coupons.user_id")
                              ->orderBy("coupons.id","desc")
                              ->where("coupons.valid_until", "LIKE" , "%".$today."%")
                              ->get();
            //echo $cekuserc ;

          if (!empty($cekuserc) && $cekuserc->count()) {
            echo "<b>Masa Aktif Kupon</b><br>";
            foreach ($cekuserc as $valu) {

              
              $data = [

                    "email" => $valu->email,
                    "nama" => $valu->name,
                    "coupon_code" => $valu->coupon_code,
                    //"coupon_value" =>  0,
                    //"coupon_percent" => 10,
                    //"valid_until" => $days_coupon,

                  ];
                  Mail::send('emails.register.notif-coupon-expired', $data, function ($message) use ($data) {
                    $message->from('no-reply@celebpost.in', 'Celebpost');
                    $message->to($data['email']);
                    $message->subject('[Celebpost] Hari Masa Aktif Kupon Anda');
                  });
                    
              echo "Cek Users".$valu->name." Masa Aktif Kupon ".$valu->valid_until."<br>";
            }

          }

  }
/*
  public function expirekupon()
  {


    echo $today = date("Y-m-d");

          $cekuserc = Users::join("coupons", "users.id", "=", "coupons.user_id")
                              ->where("coupons.valid_until", "LIKE" , "%".$today."%")
                              ->get();

          if (!empty($cekuserc) && $cekuserc->count()) {
            
            foreach ($cekuserc as $valu) {

              echo "Cek Users".$valu->name." ".$valu->valid_until."<br>";
            }

          }


  }*/


  public function listuser(req $request)
  {
    $user = Auth::user();
    //$term = "a";
    $term=$request->term;
    
    $listusers = Users::where("username","LIKE","%".$term."%")
                      ->take(10)
                      ->get();
    //$listusers = Users::get();


    $userlist = array();

    foreach ($listusers as $d) {
      $userlist[] = array('username' => $d->username);
    }

//return $userlist;

    return response ()->json ( $userlist );
  }
  

  
}
