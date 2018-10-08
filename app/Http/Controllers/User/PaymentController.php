<?php

namespace Celebpost\Http\Controllers\User;

/*Models*/

use Celebgramme\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


use View, Input, Mail, Request, App, Hash, Validator, Carbon, Crypt, Redirect;

class PaymentController extends Controller
{
  
	public function index()
	{
		$user = Auth::user();
		return view('account.index',compact('accounts','user'));
	}
	
	public function add()
	{
	}
	
}
