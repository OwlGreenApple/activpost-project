<?php

namespace Celebpost\Http\Controllers\User;

/*Models*/

use Illuminate\Http\Request as req;
use Celebpost\Http\Controllers\Controller;
use Celebpost\Models\Account;
use Celebpost\Models\Users;
use Celebpost\Models\UserAffiliate;
use Celebpost\Models\UserLog;
use Celebpost\Models\Schedule;
use Celebpost\Models\ScheduleAccount;
use Celebpost\Models\Order;
use Celebpost\Models\OrderCLB;
use Celebpost\Models\OrderAffiliate;
use Celebpost\Models\OrderUserAffiliate;
use Celebpost\Models\Coupon;
use Celebpost\Models\CouponAffiliate;
use Celebpost\Models\Package;
use Celebpost\Models\PackageAffiliate;
use Celebpost\Models\OrderMeta;
use Illuminate\Support\Facades\Auth;


use File,View, Input, Mail, Request, App, Hash, Validator, Carbon, Crypt, Redirect, GeneralHelper, Storage,Image;

class OrderController extends Controller
{
  
 
  public function index()
  {

    $user = Auth::user();
    //Auth::logout();

    if($user->is_member_rico==1){
      return Redirect::to('https://amelia.id/order.php');
    }

    $packages = null;
    if(env('APP_PROJECT')=='Amelia'){
      $packages = PackageAffiliate::where('owner_id',1)
                    ->where('is_shown',1)
                    ->orderBy('akun')
                    ->orderBy('paket')
                    ->get();
    }

    return view('order.orders', compact('user'))
            ->with('packages',$packages);
  }

  public function orderkupon(req $request)
  {

    $user = Auth::user();
    $id = $request->get('user_id');
    $kupon = $request->get('coupon_code');
    
		$today = date("Y-m-d H:i:s");

    if(env('APP_PROJECT')=='Celebgramme'){
      $listordercoupon = Coupon::where('coupon_code', '=', $kupon)
                        // ->where('user_id', '=', $id)
                        ->where('valid_until', '>=', $today)
                        ->first();
    } else {
      $listordercoupon = CouponAffiliate::where('kodekupon', $kupon)
                        ->first();
    }
		

    return response ()->json ( $listordercoupon );
  }


  public function save_order_amelia($useraffiliate,$total,$no_order,$paket_id,$type,$coupon,$diskon){
    if(!is_null($useraffiliate)){
      $owner = UserAffiliate::where('is_admin',$useraffiliate->owner_id)
              ->first();

      $order = new OrderAffiliate;
      $order->no_order = $no_order;
      $order->type = 'extend';
      $order->owner_id = $owner->is_admin;
      $order->total = $total - $diskon;
      $order->tagihan = $order->total*$owner->komisi_new/100;
      $order->save();

      $orderuser = new OrderUserAffiliate;
      $orderuser->order_id = $order->id;
      $orderuser->user_id = $useraffiliate->id;
      $orderuser->harga = $total;

      if($type!='max-account' && !is_null($coupon)){
        $orderuser->coupon_id = $coupon->id;
        $orderuser->diskon = $diskon;
      }

      if($type == "max-account"){
        $orderuser->paket_id = 0;
      } else {
        $paket = explode('999', $paket_id);  
        $orderuser->paket_id = $paket[1];
      }
      
      $orderuser->save();
    }
  }

  public function addorder(req $request)
  {
    set_time_limit(0);
    $user = Auth::user();

    if(env('APP_PROJECT')=='Amelia'){
      $type = 'daily-activity';

      if($request->added_max_account>0){
        $type='max-account';
      }

      $paket_id = '999'.$request->paket_id;

      $useraffiliate = UserAffiliate::where('user_id_celebpost',$user->id)
                        ->first();
      $coupon = CouponAffiliate::where('kodekupon',$request->coupon_code)->first();

      if(!is_null($useraffiliate)){ 
        $data = array (
          "type" => $type,
          "maximum_account" => $request->added_max_account,
          "order_type" => 'rico-extend',
          "order_status" => "pending",
          "user_idclb" => $useraffiliate->user_id_celebgramme,
          "user_id" => $user->id,
          "base_price" => $request->base_price,
          "order_total" => $request->total,
          "coupon" => $coupon,
          "package_manage_id" => $paket_id,
          "logs" => "EXISTING MEMBER",
        );

        $order = OrderCLB::createOrder($data,true);

        $this->save_order_amelia($useraffiliate,$order['order']->total,$order['order']->no_order,$paket_id,$type,$coupon,$order['order']->discount);

        return response ()->json ($order);
      }
    } else {
      $userer = Order::where('user_id', '=', $request->user_id)
                ->where(function ($query) {
                  $query->where("order_status","=","Confirmed");
                  $query->orWhere("order_status","=","cron dari affiliate");
                })
                ->orderBy("id","desc")
                ->first();
                
      $today = date("Y-m-d H:i:s");
      $coupon_id = 0; 
      $coupon = Coupon::where('coupon_code', '=', $request->coupon_code)
                          ->where('valid_until', '>=', $today)
                          ->first();
      if (!is_null($coupon)) {
        $coupon_id = $coupon->id; 
      }



      $orderadd                 = new Order;
      $time                     = new \DateTime();
      $str                      = 'OCPS'.$time->format('ymdHi');
      $order_number             = GeneralHelper::autoGenerateID($orderadd, 'no_order', $str, 3, '0');
      $orderadd->no_order       = $order_number;
      $orderadd->user_id        = $request->user_id;
      $orderadd->order_type     = 'TS';
      $orderadd->order_status   = 'Pending';
      $orderadd->base_price     = $request->base_price;
      $orderadd->affiliate      = '0';
      $orderadd->package_id     = '0';
      $orderadd->added_account  = $request->added_max_account;
      
      $orderadd->coupon_id    = $coupon_id;
      $orderadd->discount     = $request->base_price - $request->total;


      $orderadd->total          = $request->total;
      
      $sisahari = Users::where("id", "=", $user->id)->first();
      $days = intval(intval($sisahari->active_time) / (3600*24));

      //pengecekan order waktu
      // $tothari = ($request->month*30) + $days;//???? baru ditambah rr terakir, blm dicek
      $tothari = $request->days;//???? baru ditambah rr terakir, blm dicek

      $orderadd->total          = $request->total;
      $orderadd->month          = 0;
      $orderadd->sub_price      = $request->sub_price;
      $orderadd->save();

      $torder = Users::join("orders","users.id", '=', "orders.user_id")
                    ->where('orders.no_order', '=', $order_number)
                    ->first();
      $conpay = OrderMeta::createMeta('total_hari',$tothari,$torder->id);
      //$metamaxaccount = OrderMeta::createMeta('max_account',$request->max_account,$torder->id);
      $metamaxaccount = OrderMeta::createMeta('max_account',$request->added_max_account,$torder->id);
    

      $data = [
                    "no_order" => $torder->no_order,
                    "username" => $torder->name,
                    "order_status" => $torder->order_status,
                    "total" => $torder->total,
                    "discount" => $torder->discount,
                    "email" => $torder->email,
              ];

      Mail::send('emails.register.email-order', $data, function ($message) use ($data) {
        $message->from('no-reply@activpost.net', 'Activpost'); 
        $message->to($data['email']);
        $message->subject('[Activpost] ORDER '.str_replace('OCPS', '', $data['no_order']));
      });
      $queryorder = Order::join("order_metas","orders.id", "=", "order_metas.order_id")
                          ->where([
                            ['orders.no_order','=', $order_number],
                            ['order_metas.meta_name','=', 'total_hari']
                            ])->first();

                 
      return response ()->json ($queryorder);
      //return $totals;  
    }
		
  }


  public function confirpay(req $request)
  {

    $user = Auth::user();
    $orno = $request->get('id');
    //$orno = 12;
    $tipe = $request->get('tipe');

    if($tipe=='clbp'){
      $orderdis = Order::join("order_metas","orders.id", "=", "order_metas.order_id")
                ->join("users","users.id", '=', "orders.user_id")
                ->where([
                  ['orders.no_order','=', "OCPS".$orno],
                  ['order_metas.meta_name','=', 'total_hari']
                ])
                ->orderBy("orders.id","desc")
                ->first();

      return view('order.confirpayment', compact('user','orderdis'));

    } else {
      $orderdis = OrderCLB::where('no_order','OCLB'.$orno)->first();

      if($orderdis->package_manage_id==0){
        $package = null;
      } else {
        $paket = explode('999', $orderdis->package_manage_id);
        $package = PackageAffiliate::find($paket[1]);
      }

      return view('amelia.confirpayment', compact('user','orderdis'))
              ->with('package',$package);
    }
	  

		// if () {
			//redirect ke order
		// }
  }


  public function proconpay(req $request)
  {
    $user = Auth::user();
    //$no_order = 16112215380012;

    if(env('APP_PROJECT')=='Amelia'){
      $orderid = OrderCLB::where("no_order","=","OCLB".$request->no_order)->first();  
    } else {
      $orderid = Order::where("no_order","=","OCPS".$request->no_order)->first();  
    }
    
    $maxacc  = Users::where("id","=",$user->id)->first();
    //$userer = Users::where('email', '=', $ad)->first();

		
    // if (!Input::file('gambar')->isValid()) {
    if (!$request->gambar->isValid()) {
      $arr["message"]= "Upload bukti transfer tidak valid";
      $arr["type"]= "error";
      return $arr;
    }
		
    if (Request::input("atas_nama")=="") {
      $arr["message"]= "Nama Pemilik Rekening tidak boleh kosong";
      $arr["type"]= "error";
      return $arr;
    }
		
    if (Request::input("nama_bank")=="") {
      $arr["message"]= "Nama Bank tidak boleh kosong";
      $arr["type"]= "error";
      return $arr;
    }
		
    if (Request::input("no_rekening")=="") {
      $arr["message"]= "Nomor Rekening tidak boleh kosong";
      $arr["type"]= "error";
      return $arr;
    }
		
		
    
    $file       = $request->file('gambar');

    $rules = array('file' => 'max:2048'); 
    $validator = Validator::make(array('file'=> $file), $rules);

    if(!$validator->passes()){
      $arr["type"] = "error";
      $arr["message"] = "Ukuran file Maksimum 2MB";
      return $arr;
    }else{

      

      if(env('APP_PROJECT')=='Celebgramme'){
        $fileName   = $file->getClientOriginalName();
        //$destinationPath = base_path("storage/images/");
        $destinationPath = "storage/images/";
        // Input::file('gambar')->move($destinationPath, $fileName);
        $request->gambar->move($destinationPath, $fileName);

        $conpay = OrderMeta::createMeta("no_order",$request->no_order,$orderid->id);
        $conpay = OrderMeta::createMeta("nama_bank",$request->nama_bank,$orderid->id);
        $conpay = OrderMeta::createMeta("no_rekening",$request->no_rekening,$orderid->id);
        $conpay = OrderMeta::createMeta("atas_nama",$request->atas_nama,$orderid->id);
        $conpay = OrderMeta::createMeta("image",$fileName,$orderid->id);
        $conpay = OrderMeta::createMeta("keterangan",$request->keterangan,$orderid->id);
        //$conpay = OrderMeta::createMeta("max_account",$maxacc->max_account,$orderid->id);
        
        //$conorder = Order::where("no_order", "=", "OCLB".$request->no_order);
        $conorder = Order::findOrFail($orderid->id);
        $conorder->order_status = "Not Confirmed";
        $conorder->image = url("storage/images/".$fileName);
        $conorder->save();
        

        $conpayment = Users::join("orders", "users.id", "=", "orders.user_id")
                      ->where("orders.no_order", "=", "OCPS".$request->no_order)
                      ->first();

        $data = [
          "no_order" => $request->no_order,
          "nama_bank" => $request->nama_bank,
          "no_rekening" => $request->no_rekening,
          "atas_nama" => $request->atas_nama,
          "keterangan" => $request->keterangan,
          "email" => $conpayment->email,
          "total" => "Rp. ".number_format($conorder->total,0,'','.'),
        ];

        Mail::send('emails.register.email-payment', $data, function ($message) use ($data) {
          $message->from('no-reply@activpost.net', 'Activpost');
          $message->to($data['email']);
          $message->bcc(array(
            "celebgramme.dev@gmail.com",
            "celebgramme@gmail.com",
          ));
          $message->subject('[Activpost] Order Confirmation');
        });
      } else {
        // $fileName   = $orderid->no_order.".".Input::file('gambar')->getClientOriginalExtension();
        $fileName   = $orderid->no_order.".".$request->gambar->getClientOriginalExtension();

        //Storage::disk('s3')->put('confirm-payment/'.$fileName,File::get(Input::file('gambar')),'public');

        // $image = Image::make(Input::file('gambar')->getRealPath())
        $image = Image::make($request->gambar->getRealPath())
                ->resize(600, null, function ($constraint) {
                    $constraint->aspectRatio();
                })                
                ->stream();
        Storage::disk('s3')->put('confirm-payment/'.$fileName,$image->__toString(),'public');
    
        $orderid->image = $fileName;
        $orderid->save();

        $conpay = null;

        $data = [
          "no_order" => $request->no_order,
          "nama_bank" => $request->nama_bank,
          "no_rekening" => $request->no_rekening,
          "atas_nama" => $request->atas_nama,
          "keterangan" => $request->keterangan,
          "email" => $user->email,
          "total" => "Rp. ".number_format($orderid->total),
        ];

        Mail::send('emails.register.email-payment', $data, function ($message) use ($data) {
          $message->from('no-reply@activpost.net', 'Activpost');
          $message->to($data['email']);
          $message->bcc(array(
            "celebgramme.dev@gmail.com",
            "activfans@gmail.com",
            "support@amelia.id",
          ));
          $message->subject('[Amelia] Order Confirmation');
        });
      }

			return $conpay;
		}
  
  }


  public function checknoorder(req $request)
  {
    $arr["message"]= "Silahkan tunggu konfirmasi admin maksimal 1x24 jam (jam kerja) ";
    $arr["type"]= "success";
    
    $no_order = $request->get('no_order');
    
    if(env('APP_PROJECT')=='Celebgramme'){
      $user = Auth::user();
      $order = Order::where("no_order", "=", "OCPS".$no_order)
                      ->first();

      if (is_null($order)) { 
        $arr["message"]= "No order tidak ada pada database";
        $arr["type"]= "error";
        return response ()->json ( $arr );
      }

      if ($order->order_status=="Not Confirmed") {
        $arr["message"]= "No order menunggu confirmasi dari admin";
        $arr["type"]= "error";
        return response ()->json ( $arr );
      }

      if ( ($order->order_status=="Confirmed") || ($order->order_status=="cron dari affiliate") ) {
        $arr["message"]= "No order sudah lunas";
        $arr["type"]= "error";
        return response ()->json ( $arr );
      }

      if ($order->user_id <> $user->id) {
        $arr["message"]= "Bukan order yang anda buat, silahkan masukkan no order lain";
        $arr["type"]= "error";
        return response ()->json ( $arr );
      }

    } else {
      $userclbp = Auth::user();
      $user = UserAffiliate::where('user_id_celebpost',$userclbp->id)->first();

      $order = OrderCLB::where("no_order", "=", "OCLB".$no_order)->first();

      if (is_null($order)) { 
        $arr["message"]= "No order tidak ada pada database";
        $arr["type"]= "error";
        return response ()->json ( $arr );
      }

      if ($order->order_status=="pending" && $order->image!='') {
        $arr["message"]= "No order menunggu confirmasi dari admin";
        $arr["type"]= "error";
        return response ()->json ( $arr );
      }

      if ( ($order->order_status=="success") ) {
        $arr["message"]= "No order sudah lunas";
        $arr["type"]= "error";
        return response ()->json ( $arr );
      }

      if ($order->user_id <> $user->user_id_celebgramme) {
        $arr["message"]= "Bukan order yang anda buat, silahkan masukkan no order lain";
        $arr["type"]= "error";
        return response ()->json ( $arr );
      }
    }
										
    return response ()->json ( $arr );
  }


  public function listorderuser (){

    $user = Auth::user();

    if($user->is_member_rico==1){
      return Redirect::to('https://amelia.id/order.php');
    }
    
    $orderlistuser = Users::join("orders","users.id", '=', "orders.user_id")
                   ->where('orders.user_id', '=', $user->id)
                   ->orderBy('orders.created_at', 'desc')
                   ->paginate(15);

    $orderclb = null;
    if (env('APP_PROJECT')=='Amelia') {
      $useraffiliate = UserAffiliate::where('user_id_celebpost',$user->id)->first();
      $orderclb = OrderCLB::where('user_id',$useraffiliate->user_id_celebgramme)->paginate(15);
    }

    return view('order.storieorder', compact('user', 'orderlistuser'))
            ->with('orderclb',$orderclb);
     //return view('admin.order.listorders', compact('user', 'listorder'));

  }



  public function index_confirm_payment()
  {
    $user = Auth::user();
    //Auth::logout();

    return view('order.confirm-payment', compact('user'));
  }
  
}

