<?php

namespace Celebpost\Http\Controllers\User;

/*Models*/

use Celebpost\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use Celebpost\Models\Image as ImageModel;
use Celebpost\Models\Template;


use View, Input, Mail, Request, App, Hash, Validator, Carbon, Crypt, Redirect;

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

		return view('user.index')->with(array('user'=>$user,));
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
		$user = Auth::user();
		if (!$user->is_confirmed) {
			return "Please Confirm Your Email";
		}
		
		$collections = Template::where("user_id","=",$user->id)
										->where("type","=","hashtags")
										->get();
		return view('user.search-hashtags.index')->with(array(
			'collections'=>$collections,
		));
  }
	
	public function caption(){
		$user = Auth::user();
		if (!$user->is_confirmed) {
			return "Please Confirm Your Email";
		}

		$collections_hashtags = Template::where("user_id","=",$user->id)
										->where("type","=","hashtags")
										->get();
		
		$collections_captions = Template::where("user_id","=",$user->id)
										->where("type","=","templates")
										->get();
		return view('user.caption.index')->with(array(
			'collections_captions'=>$collections_captions,
			'collections_hashtags'=>$collections_hashtags,
		));
  }
	
	public function saved_images(){
		$user = Auth::user();
		if (!$user->is_confirmed) {
			return "Please Confirm Your Email";
		}
		
		$imageM = ImageModel::where("user_id","=",$user->id)
							->orderBy('id', 'desc')
							->get();
		return view('user.image.index')
		->with(array(
			'user'=>$user,
			'imageM'=>$imageM,
		));
  }
}
