<?php

namespace Celebpost\Http\Controllers\Auth;

use Celebpost\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
    * Get the login username to be used by the controller.
    *
    * @return string
    */
    public function username()
    {
        return 'username';
    }
		
		
		
	/**
	* 
	*
	* @return view
	*/

  protected function authenticated()
  {
    if(env('APP_PROJECT')=='Celebgramme'){
      if (auth()->user()->is_member_rico==1 && auth()->user()->is_admin==0) {
        auth()->logout();

        if(env('APP_ENV')=='local'){
          return redirect('/login')->with(array("error"=>"Anda terdaftar sebagai user Amelia. Silahkan masuk melalui Login User Amelia"));
        } else {
          return redirect('https://activpost.net/amelia/login')->with(array("error"=>"Anda terdaftar sebagai user Amelia. Silahkan masuk melalui Login User Amelia"));
        }
        
      } 
    } else {
      if (auth()->user()->is_member_rico==0 && auth()->user()->is_admin==0) {
        auth()->logout();
        return redirect('/login')->with(array("error"=>"Anda tidak terdaftar sebagai member amelia"));
      } 
    }
  }

	public function test()
	{
		$url = '../vp/uploads/asd.jpg';
		// if ($request->session()->has('url')) {
			// $url = $request->session()->get('url');
		// }
		return view('user.search-hashtags.image-editor')->with(array(
			'url'=>$url,
		));		
	}
	
  public function showLoginForm()
  {
    if(env('APP_PROJECT')=='Celebgramme'){
      return view('auth.login');
    } else {
      return view('amelia.login');
    }
  }


}
