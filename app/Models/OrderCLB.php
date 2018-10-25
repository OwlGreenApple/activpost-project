<?php

namespace Celebpost\Models;

use Illuminate\Database\Eloquent\Model;
use Celebpost\Models\OrderMeta;
use Celebpost\Models\Users;
use Celebpost\Models\PackageAffiliate;
use GeneralHelper,Carbon,Mail;

class OrderCLB extends Model
{
    protected $connection = 'mysql_celebgramme';
    protected $table = 'orders';

    protected function createOrder($cdata,$flag)
    {
        $dt = Carbon::now();
        
        //unique code 
        $unique_code = mt_rand(1, 1000);
        $order = new OrderCLB;
        $str = 'OCLB'.$dt->format('ymdHi');
        $order_number = GeneralHelper::autoGenerateID($order, 'no_order', $str, 3, '0');
        $order->no_order = $order_number;

        $order->order_type = $cdata["order_type"];
        $order->order_status = $cdata["order_status"];
        $order->user_id = $cdata["user_idclb"];
        $order->total = $cdata["order_total"] + $unique_code;
        $order->discount = 0;
        $order->package_id = 0;
        $order->coupon_id = 0;
        
        $order->type = $cdata["type"];
        $order->is_remind_email = 0;
        
        if ($cdata["type"] == "daily-activity" ) {
          $order->package_manage_id = $cdata["package_manage_id"];
          $order->added_account = 0;
          $paket = explode('999', $cdata["package_manage_id"]);
          $package = PackageAffiliate::find($paket[1]);
        }
        else if ($cdata["type"] == "max-account" ) {
          $order->package_manage_id = 0;
          $order->added_account = $cdata["maximum_account"];
          $package = null;
        }
        $order->save();
        
        OrderMeta::createMeta("logs","create order by ".$cdata["logs"],$order->id);

        $user = Users::find($cdata["user_id"]);
        
        $shortcode = str_replace('OCLB', '', $order_number);
        //send email order
        $emaildata = [
            'order' => $order,
            'user' => $user,
            'package' => $package,
            'no_order' => $shortcode,
        ];
        if ( $flag ) {
            $emaildata['status'] = "Belum lunas";
        } else {
            $emaildata['status'] = "Lunas";
        }
        Mail::send('emails.order', $emaildata, function ($message) use ($user,$shortcode) {
          $message->from('no-reply@activpost.net', 'activpost');
          $message->to($user->email);
          $message->subject('[activpost] Order Nomor '.$shortcode);
        });

        
        //send email to admin
        $type_message="[activpost] Order Package";
        $type_message .= "Fullname: ".$user->fullname;
        $emaildata = [
          "user" => $user,
          "status" => "order",
        ];
        Mail::send('emails.info-order-admin', $emaildata, function ($message) use ($type_message) {
          $message->from('no-reply@activpost.net', 'activpost');
          $message->to(array(
            "activfans@gmail.com",
            // "celebgramme.dev@gmail.com",
          ));
          $message->subject($type_message);
        });
        
        $arr['order'] = $order;
        $arr['paket'] = $package;

        return $arr;
  }
}
