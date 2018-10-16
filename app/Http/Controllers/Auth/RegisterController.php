<?php

namespace Celebpost\Http\Controllers\Auth;

use Celebpost\User;
use Celebpost\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;

use Illuminate\Http\Request as req;
use Input, Hash, Mail, Crypt, Carbon,Redirect,Validator,Request,GeneralHelper;

use Celebpost\Models\Coupon;
use Celebpost\Models\Package;
use Celebpost\Models\Order;
use Celebpost\Models\OrderMeta;
use Celebpost\Models\Users;

class RegisterController extends Controller
{
	/*
	|--------------------------------------------------------------------------
	| Register Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles the registration of new users as well as their
	| validation and creation. By default this controller uses a trait to
	| provide this functionality without requiring any additional code.
	|
	*/

	use RegistersUsers;

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
		$this->middleware('guest');
	}

	/**
	 * Get a validator for an incoming registration request.
	 *
	 * @param  array  $data
	 * @return \Illuminate\Contracts\Validation\Validator
	 */
	protected function validator(array $data)
	{
		return Validator::make($data, [
			'email' => 'required|email|max:255|unique:users',
			'name' => 'required|max:255',
			'password' => 'required|min:6|confirmed',
		]);
	}

	/**
	 * Create a new user instance after a valid registration.
	 *
	 * @param  array  $data
	 * @return User
	 */
	protected function create(array $data)
	{
		return User::create([
			'name' => $data['name'],
			'email' => $data['email'],
			'username' => $data['email'],
			// 'timezone' => $data['timezone'],
			'password' => Hash::make($data['password']),
			'is_confirmed' => 0,
		]);
	}

	public function showRegistrationForm()
	{
		return view('auth.register');
			// return redirect('/');
	}
	
	public function register(req $req)
	{
		$data = $req->input("data");
		$validator = $this->validator($data);
		// dd($validator);
		if (!$validator->fails()){
		// dd($data);
			$user = $this->create($data);
			
			$register_time = Carbon::now()->toDateTimeString();
			$verificationcode = Hash::make($user->email.$register_time);
			$secret_data = [
				'email' => $user->email,
				'register_time' => $register_time,
				'verification_code' => $verificationcode,
			];
			$user->verificationcode = $verificationcode;
			$user->max_account = 0;
			$user->is_admin = 0;
			$user->is_started = 0;
			$user->active_time = 0;
			$user->save();
			
			
			$data = [
				"url" => url("verifyemail")."/".Crypt::encrypt(json_encode($secret_data)),
				"email" => $data["email"],
				"password" => $data["password"],
			];
			Mail::send('emails.register.confirm-email', $data, function ($message) use ($data) {
				$message->from('no-reply@activpost.net', 'Activpost');
				$message->to($data['email']);
				$message->subject('[Activpost] Verify Email');
			});
			
			
			if ( $req->session()->has('checkout_data') ) {
				$checkout_data = $req->session()->get('checkout_data');
				$data = array (
					"month"=> $checkout_data["month"],
					"coupon_code" => $checkout_data["coupon_code"],
					"base_price" => $checkout_data["base_price"],
					"discount" => $checkout_data["discount"],
					"total" => $checkout_data["total"],
					"sub_price" => $checkout_data["sub_price"],
					"max_account" => $checkout_data["max_account"],
					"days" => $checkout_data["days"],
				);
        
				
		$today = date("Y-m-d H:i:s");
		$coupon_id = 0; 
		$coupon = Coupon::where('coupon_code', '=', $data['coupon_code'])
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
				$orderadd->user_id        = $user->id;
				$orderadd->order_type     = 'TS';
				$orderadd->order_status   = 'Pending';
				$orderadd->base_price     = (int)$data['base_price'];
				$orderadd->affiliate      = '0';
				$orderadd->package_id     = '0';
				$orderadd->total          = (int)$data['total'];
				$orderadd->added_account  = 0;
				
					// $orderadd->coupon_id    = $data['coupon_code'];
					// $orderadd->discount     = $data['discount'];
					$orderadd->coupon_id    = $coupon_id;
					$orderadd->discount     = $orderadd->base_price - $orderadd->total;
				
				
				$sisahari = Users::where("id", "=", $user->id)->first();
				$days = intval(intval($sisahari->active_time) / (3600*24));
				$tothari = $data['days'];//???? baru ditambah rr terakir, blm dicek
				
				$orderadd->month          = 0;
				$orderadd->sub_price      = $data['sub_price'];
				$orderadd->save();


				$torder = Users::join("orders","users.id", '=', "orders.user_id")  
											->where('orders.no_order', '=', $order_number)
											->first();

				$metamaxaccount = OrderMeta::createMeta('max_account',$data['max_account'],$torder->id);
				$conpay = OrderMeta::createMeta('total_hari',$tothari,$torder->id);
				
				
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
				
				
				
        // return redirect('/home');
        return Redirect::to("https://activpost.net/konfirmasi/");
			}
	
		} else {
			echo $validator->errors()->first();
		}
		// return "";
		return redirect('/');
	}
	
	public function showPrices()
	{
		$packages = Package::all();
		return view('auth.prices')->with(array(
			"packages" => $packages,
		));
	}

	public function process_checkout(req $request) {
		$total = 0;
		$package = Package::find(Request::input("select-package"));
		if (!is_null($package)) {
			$total = $package->price;
		}
		$dt = Carbon::now();
		$coupon = Coupon::where("coupon_code","=",Request::input('couponcode'))
					->where("valid_until",">=",$dt->toDateTimeString())->first();
		if (!is_null($coupon)) {
			$total -= $coupon->coupon_value;
			if ($total<0) { $total =0; }
		}

		$arr = array (
			"package_id"=>Request::input("select-package"),
			"coupon_code"=>Request::input("coupon-code"),
			"payment_method"=>Request::input("payment-method"),
			"total"=>$total,
		);
		
		$request->session()->put('checkout_data', $arr);
		return redirect("register");
		// return view('auth.register');
		// return "aa";
	}
	
	public function showCheckout()
	{
		return view('auth.checkout');
	}


	public function ordersg()
	{
		//$user = User::where("email","=",$data->email)->first();
		$user = '';
		return view('auth.orders', compact('user'));
	}


	public function prologinorder(req $request)
	{
		$arr = array (
			"month"=> $request->ordermonth,
			"coupon_code" => $request->coupon_code,
			"base_price" => $request->base_price,
			"discount" => $request->discount,
			"total" => $request->total,
			"sub_price" => $request->sub_price,
			"max_account" => $request->max_account,
			"days" => $request->days,
		);
		
		$request->session()->put('checkout_data', $arr);

		return view('auth.register');
	}
	

	
}
