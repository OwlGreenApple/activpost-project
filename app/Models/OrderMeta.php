<?php namespace Celebpost\Models;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class OrderMeta extends Model {

	protected $table = 'order_metas';
  public $timestamps = false;
  
	protected function createMeta($name,$value,$id)
	{
    $order_meta = new OrderMeta;
    $order_meta->meta_name = $name;
    $order_meta->meta_value = $value;
    $order_meta->order_id = $id;
    $order_meta->save();
  }
  
	protected function getMeta($id,$meta_name)
	{
		$order_meta = OrderMeta::where('order_id','=',$id)->where("meta_name","=",$meta_name)->first();
		if (!is_null($order_meta)) {
			return $order_meta->meta_value;
		} else {
			return "";
		}
	}
}
