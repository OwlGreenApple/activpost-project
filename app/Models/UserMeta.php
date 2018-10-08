<?php namespace Celebpost\Models;

use Illuminate\Database\Eloquent\Model;

class UserMeta extends Model {

	protected $table = 'user_metas';
  public $timestamps = false;
	protected function createMeta($name,$value,$id)
	{
		$user_meta = UserMeta::
											where("user_id","=",$id)
											->where("meta_name","=",$name)
											->first();
		if (is_null($user_meta)) {
			$user_meta = new UserMeta;
		}
    $user_meta->meta_name = $name;
    $user_meta->meta_value = $value;
    $user_meta->user_id = $id;
    $user_meta->save();
  }
  
	protected function getMeta($id,$meta_name)
	{
		$user_meta = UserMeta::where('user_id','=',$id)->where("meta_name","=",$meta_name)->first();
		if (!is_null($user_meta)) {
			return $user_meta->meta_value;
		} else {
			return "0";
		}
	}
}
