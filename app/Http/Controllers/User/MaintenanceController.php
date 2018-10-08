<?php

namespace Celebpost\Http\Controllers\User;

/*Models*/

use Celebpost\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use Celebpost\Models\Schedule;
use File,Artisan;


use View, Input, Mail, Request, App, Hash, Validator, Carbon, Crypt, Redirect;

class MaintenanceController extends Controller
{
  
	public function test(){
  }

	public function maintenance(){
		$user = Auth::user();

		if ($user->is_admin == 1) {
			return view('maintenance');
		}else{
			return Redirect::to('https://activpost.net/not-authorized/');
		}
		
  }
	
	public function clearcache(){
		Artisan::call('cache:clear');
		return back()->with('status','Success Clear Cache!');
  }
	
	public function clearview(){
		Artisan::call('view:clear');
		return back()->with('status','Success Clear View!');
  }
	
	public function clearroute(){
		Artisan::call('route:clear');
		return back()->with('status','Success Clear Route!');
  }
	
	public function clearconfig(){
		Artisan::call('route:clear');
		return back()->with('status','Success Clear Route!');
  }
	
	public function optimize(){
		Artisan::call('route:clear');
		return back()->with('status','Success Clear Route!');
  }
	
	// Delete all schedule
	public function delsche()
	{
		$scs = Schedule::all();
		foreach ($scs as $sc) {
			$dir = basename($sc->image);
			// $directory = public_path() . '/images/uploads/' .$dir;
			$directory = public_path() . '/../vp/uploads/' .$dir;
			File::delete($directory);
			Schedule::destroy($sc->id);
		}
		return back()->with('status','Success Clear Schedule!');
	}
}
