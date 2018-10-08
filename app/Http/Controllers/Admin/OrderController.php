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
use Celebpost\Models\OrderMeta;
use Illuminate\Support\Facades\Auth;


use File,View, Input, Mail, Request, App, Hash, Validator, Carbon, Crypt, Redirect;

class OrderController extends Controller
{
  
 
  public function index()
  {
    $user = Auth::user();

    if ($user->is_admin == 1) {
      # code...
      $listorder = Users::join("orders","users.id", '=', "orders.user_id")
                      ->orderBy('orders.created_at', 'desc')
                   // ->where('Orders.no_order', '=', $query)
                    //->orWhere('Users.username', '=', $query)
                    //->orWhere('Users.email', '=', $query)
                    //->select('Users.*','Orders.*')
                    ->paginate(15);
       

      return view('admin.order.listorders', compact('user', 'listorder'));
    
    }else{

      return Redirect::to('https://activpost.net/not-authorized/');
    }

    

  }

  public function searchorder(req $request)
  {
    $user = Auth::user();
    if ($user->is_admin == 1) {
      $q2 = Input::get ( 'q2' );
      if ($q2) {
        if ($q2 == "Confirmed" || $q2 == "Not Confirmed") {
          $listorder = Users::join("orders","users.id", '=', "orders.user_id")
                    ->where('orders.order_status', '=', $q2)
                    ->paginate(15);
					if ($q2== "Confirmed") {
						$pes = "Confirmed";
					}elseif($q2 == "Not Confirmed"){
						$pes = "Not Confirmed";
					}

          $pagination = $listorder->appends ( array (
						'q2'=> Input::get ( 'q2') 
          ) ); 
        }else{
          $listorder = Users::join("orders","users.id", '=', "orders.user_id")
                    ->orderBy('orders.created_at', 'desc')
                    ->paginate(15);
					$pes = "All";
          $pagination = $listorder->appends ( array (
						'q2'=> "all"
          ) ); 

        }

      }

        
			$q = Input::get ( 'q' );
			if ($q){
				$listorder = Users::join("orders","users.id", '=', "orders.user_id")
									->where('orders.no_order', '=', $q)
									->orWhere('users.username', 'LIKE', "%".$q."%")
									->orWhere('users.email', 'LIKE', "%".$q."%")
									->paginate(15);

				$pes = "other"; 
				$pagination = $listorder->appends ( array (
					'q' => Input::get ( 'q' ) 
				));
			}
			return view('admin.order.searchorders', compact('user', 'listorder','pes'));
		}else{
      return Redirect::to('https://activpost.net/not-authorized/');
    }
   
        
  }

  public function deleteorder(req $request)
  {
    $user = Auth::user();

    if ($user->is_admin == 1) {
      
      $id = $request->get('id');

      $delorder = Order::where('id','=',$id)->first();

    //return $delorder->id;

      return view('admin.order.showdelorder', compact('user','delorder'))->renderSections()['content'];

    }else{

        return Redirect::to('https://activpost.net/not-authorized/');

    }
    
     

  }


  public function prosesdelorder(req $request)
  {
    $user = Auth::user();

      if ($user->is_admin == 1) {
        
        $delorders = Order::findOrFail($request->id)->delete();

      return back()->with('message', 'Order Succes Delete');

      }else{

        return Redirect::to('https://activpost.net/not-authorized/');
      }
      

  }


  public function confirorders(req $request)
  {
    $user = Auth::user();

    if ($user->is_admin == 1) {

      $id = $request->get('id');

    $ordercon = Order::where('id', '=', $id)->first();

    //return $ordercon->id;

    return view('admin.order.confirorder', compact('user','ordercon'))->renderSections()['content'];
      
    }else{

      return Redirect::to('https://activpost.net/not-authorized/');
    }
    

  }

  public function prosesconfir(req $request)
  {
    $user = Auth::user();
    if ($user->is_admin) {
			$montorder = Users::join("orders","users.id", '=', "orders.user_id")
											->where("orders.id", "=", $request->id )
											->first();

			$conorder = Order::findOrFail($request->id);
			$conorder->order_status = "Confirmed";
			$conorder->save();

			if ($conorder->added_account == 0) {
				$maxaccount = OrderMeta::where([
																['meta_name', '=', 'max_account'],
																['order_id','=', $montorder->id]
																])->first();
				
				$total_hari = OrderMeta::getMeta($montorder->id,"total_hari"); //
				
				$userorder = Users::findOrFail($montorder->user_id);
				$userorder->active_time += $total_hari * 86400; //
				if ($userorder->max_account < (int)$maxaccount->meta_value ) {
					$userorder->max_account = $maxaccount->meta_value;
				}
				$userorder->save();
			}
			else {
				$userorder = Users::findOrFail($montorder->user_id);
				$userorder->max_account += $conorder->added_account;
				$userorder->save();
			}
			return back()->with('message', 'Confirmed Succes');
		}else{
			return Redirect::to('https://activpost.net/not-authorized/');
		}
  
  
  }

  
}
