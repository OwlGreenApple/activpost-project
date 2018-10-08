<?php

namespace Celebpost\Models;

use Illuminate\Database\Eloquent\Model;

use Celebpost\User;
use Celebpost\Models\Package;
use Celebpost\Models\OrderMeta;

use Carbon,DB,Mail;

class Order extends Model
{
	protected $table = 'orders';
  
	protected function createOrder($cdata,$flag)
	{
        $dt = Carbon::now();
        $coupon_id = 0;$order_discount = 0;
        $coupon = Coupon::where("coupon_code","=",$cdata["coupon_code"])
                    ->where("valid_until",">=",$dt->toDateTimeString())->first();
        if (!is_null($coupon)) {
            $coupon_id = $coupon->id;
						
					if ($coupon->coupon_percent == 0 ) {
						$order_discount = $coupon->coupon_value;
					} else if ($coupon->coupon_value == 0 ) {
						$package = Package::find($cdata["package_id"]);
						$val = floor ( $coupon->coupon_percent / 100 * $package->price );
						$order_discount = $val;
					}
						
        }

				//unique code 
				$unique_code = mt_rand(1, 1000);

        $order = new Order;
    		
    		$str = 'OCLB'.$dt->format('ymdHi');
        $order_number = $this->autoGenerateID($order, 'no_order', $str, 3, '0');
        $order->no_order = $order_number;
        $order->order_type = $cdata["order_type"];
        $order->order_status = $cdata["order_status"];
        $order->user_id = $cdata["user_id"];
        $order->base_price = $cdata["order_total"];
        $order->affiliate = 0;
        $order->discount = $order_discount;
        $order->total = $cdata["order_total"] + $unique_code - $order_discount;
        $order->package_id = $cdata["package_id"];
        $order->coupon_id = $coupon_id;
        $order->save();
				
				OrderMeta::createMeta("logs","create order by ".$cdata["logs"],$order->id);

        $user = User::find($cdata["user_id"]);
        $package = Package::find($cdata["package_id"]);
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
        Mail::send('emails.register.order', $emaildata, function ($message) use ($user,$shortcode) {
          $message->from('no-reply@activfans.com', 'activfans');
          $message->to($user->email);
          $message->subject('[activfans] Order Nomor '.$shortcode);
        });

				
				//send email to admin
				$type_message="[Celebpost] Order Package";
				$type_message .= "Fullname: ".$user->fullname;
				$emaildata = [
					"user" => $user,
					"status" => "order",
				];
				Mail::send('emails.register.info-order-admin', $emaildata, function ($message) use ($type_message) {
					$message->from('no-reply@activfans.com', 'activfans');
					$message->to(array(
						"michaelsugih@gmail.com",
						"celebgramme.dev@gmail.com",
					));
					$message->subject($type_message);
				});
				
        
        return $order;
  }

  /**
   * Get generated string from 1 Database Table
   *
   * @param $model MODELS
   * @param $field STRING field name
   * @param $field STRING field name
   *
   * @return string
   */
  protected function autoGenerateID($model, $field, $search, $pad_length, $pad_string = '0')
  {
    $tb = $model->select(DB::raw("substr(".$field.", ".strval(strlen($search)+1).") as lastnum"))
								->whereRaw("substr(".$field.", 1, ".strlen($search).") = '".$search."'")
								->orderBy('id', 'DESC')
								->first();
		if ($tb == null){
			$ctr = 1;
		}
		else{
			$ctr = intval($tb->lastnum) + 1;
		}
		return $search.str_pad($ctr, $pad_length, $pad_string, STR_PAD_LEFT);
  }
	
	
}
