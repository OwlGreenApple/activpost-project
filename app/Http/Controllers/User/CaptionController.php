<?php

namespace Celebpost\Http\Controllers\User;

/*Models*/

use Celebpost\Http\Controllers\Controller;
use Celebpost\Http\Controllers\User\ResearchController;
use Illuminate\Support\Facades\Auth;

use Celebpost\Models\Template;

use View, Input, Mail, Request, App, Hash, Validator, Carbon, Crypt, Redirect;

class CaptionController extends Controller
{
  
	
	/*
	*Hashtags Research
	*/
	public function submit_caption()
	{
		$arr["type"] = "success";
		$arr["message"] = "Data berhasil di edit";
		$arr["typeSubmit"] = "update";
		$user = Auth::user();
		
		if (count(explode("#",Request::input("captionBox"))) - 1 > 30 ) {
			$arr["type"] = "error";
			$arr["message"] = "hashtags tidak boleh lebih dari 30";
			return $arr;
		}
		
		if (Request::input("nameTemplate")==""){
			$arr["type"] = "error";
			$arr["message"] = "Nama Template tidak boleh kosong";
			return $arr;
		}
		
		// $clean_text = ResearchController::removeEmoji(Request::input("captionBox"));
		$clean_text = Request::input("captionBox");
		
		$collection = Template::where("user_id","=",$user->id)
										->where("type","=","templates")
										->where("name","=",Request::input("nameTemplate"))
										->first();
		if (is_null($collection)){
			$collection = new Template;
			$collection->name = Request::input("nameTemplate");
			$collection->type = "templates";
			$collection->user_id = $user->id;
			$arr["message"] = "Data berhasil di add";
			$arr["typeSubmit"] = "insert";
		}
		$collection->value = $clean_text;
		$collection->save();
										
		return $arr;
	}
	
	public function delete_caption()
	{
		$user = Auth::user();
		$arr["type"] = "success";
		$arr["message"] = "Data berhasil dihapus";
		
		if (Request::input("type")=="hashtags") {
			$collection = Template::where("user_id","=",$user->id)
										->where("type","=","hashtags")
										->where("name","=",Request::input("templateName"))
										->delete();
		} 
		else if (Request::input("type")=="caption") {
			$collection = Template::where("user_id","=",$user->id)
										->where("type","=","templates")
										->where("name","=",Request::input("templateName"))
										->delete();
		}
										
		return $arr;
	}
	
	
}
