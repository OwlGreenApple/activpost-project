<?php
namespace Celebpost\Helper;

use DB;
/**
* 
*/
class GeneralHelper 
{
  
  public static function autoGenerateID($model, $field, $search, $pad_length, $pad_string = '0')
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

?>