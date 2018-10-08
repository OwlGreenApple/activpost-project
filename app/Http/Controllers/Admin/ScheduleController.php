<?php

namespace Celebpost\Http\Controllers\Admin;

/*Models*/

use Illuminate\Http\Request as req;
use Celebpost\Http\Controllers\Controller;
use Celebpost\Models\Account;
use Celebpost\Models\Users;
use Celebpost\Models\UserLog;
use Celebpost\Models\Schedule;
use Celebpost\Models\ScheduleAccount;
use Celebpost\Models\Order;
use Illuminate\Support\Facades\Auth;


use File,View, Input, Mail, Request, App, Hash, Validator, Carbon, Crypt, Redirect;

class ScheduleController extends Controller
{
  
 
 public function index()
  {
     $user = Auth::user();

     if ($user->is_admin == 1) {

        $listschedule = Schedule::paginate(15);
        return view('admin.schedule.viewschedule', compact('user','listschedule'));
     }else{

        return Redirect::to('https://activpost.net/not-authorized/');
     }
     
  }


  public function schedulaccount(req $request)
  {
      $user = Auth::user();
      //$id   = $request->get('id');

      if ($user->is_admin == 1) {

        $id = Input::get('id');

      $accountschedule = ScheduleAccount::join('accounts', 'schedule_account.account_id', '=', 'accounts.id')
                                          ->join('schedules', 'schedule_account.schedule_id', '=', 'schedules.id')
                                          ->select('schedule_account.*', 'schedules.*', 'accounts.*')
                                          ->where('schedule_account.schedule_id', '=', $id)
                                          ->paginate(15);
      $pagination = $accountschedule->appends (array('id' => Input::get('id')));
     

      return view('admin.schedule.scheduleaccount', compact('user','accountschedule'))->renderSections()['content'];



      }else{

        return Redirect::to('https://activpost.net/not-authorized/');
      }

      
  } 

 public function searcschedul(req $request)
 {
    $user = Auth::user();
   // $query = $request->get('q');
    if ($user->is_admin == 1) {

    $q = Input::get ( 'q' );
    $listschedule = Users::join('accounts', 'users.id', '=', 'accounts.user_id')
                 ->join('schedules', 'users.id', '=', 'schedules.user_id')
                 ->where('users.username', 'LIKE', '%'.$q.'%')
                 ->orWhere('accounts.username', 'LIKE', '%'.$q.'%')
                 ->paginate(15);
                
    $pagination = $listschedule->appends ( array (
        'q' => Input::get ( 'q' ) 
    ) );

      return view('admin.schedule.shedule-search', compact('listschedule','user'));
    
    }else{

      return Redirect::to('https://activpost.net/not-authorized/');
    }

 }

  
}
