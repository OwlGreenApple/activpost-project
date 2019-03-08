<?php

namespace Celebpost\Http\Controllers\User;

use Illuminate\Http\Request as req;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Carbon;

use Celebpost\Models\Schedule;
use Celebpost\Models\ScheduleAccount;
use Celebpost\Models\Account;
use Celebpost\Models\Template;
use Celebpost\Models\Image as ImageModel;
use Celebpost\Models\Proxies;
use Celebpost\Models\Users;
use Illuminate\Support\Facades\File;

use Illuminate\Pagination\LengthAwarePaginator;

use Celebpost\Jobs\SendInstagram;
use Image,Request,DB;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;

use \InstagramAPI\Instagram;

use Celebpost\Http\Controllers\User\ResearchController;

class ScheduleController extends Controller
{
	public function __construct()
	{
			$this->waktu = Carbon::now(''.env('IG_TIMEZONE').'');
	}

	public function index()
	{
		$user = Auth::user();
		if (!$user->is_confirmed) {
			return "Please Confirm Your Email";
		}
    if ($user->active_time  > 0){
      $dt = Carbon::now();
      $user->running_time = $dt->toDateTimeString();
      $user->is_started = 1;
      $user->save();
      
      $accounts = Account::where("user_id",$user->id)
                  ->where("is_active",1)
                  ->get();
      foreach ($accounts as $account) {
        $account->is_started = 1;
        $account->save();
      }
    }
    
		$from = new Carbon("first day of this month");
		$to = new Carbon("last day of this month");
		
		$pivot = Carbon::now()->subDays(7);
		$data = Schedule::orderBy("publish_at")
									->where("user_id","=",$user->id)
									->where('schedules.status','<',2)
									->whereDate("publish_at",">",$pivot->format('Y-m-d'))
									->count();
		$totalMainSchedulePage = floor($data / 10) +1;
		
		return view('schedule.index',compact('user','from','to','totalMainSchedulePage'));
	}
	
	public function load_main_schedule()
  {
		$user = Auth::user();
		$pivot = Carbon::now()->subDays(7);

		$schedules = Schedule::orderBy("publish_at")
									->where("user_id","=",$user->id)
									->where('schedules.status','<',2)
									->whereDate("publish_at",">",$pivot->format('Y-m-d'))
									->paginate(10);
									
		$arr["content"] = (string) view('schedule.main-content')->with(
                array(
                  'schedules'=>$schedules,
                ));
		// $arr["schedule"] = "";
		return $arr;
	}
	
	public function add($sid =0)
	{
		$user = Auth::user();
		if (!$user->is_confirmed) {
			return "Please Confirm Your Email";
		}
		
		$arr_repost = null;
		
		//check schedule page users, check schedule publish or not 
		if ($sid<>0) {
			//check schedule page users 
			$check = Schedule::where("user_id","=",$user->id)
								->where("schedules.id","=",$sid)
								->first();
			if (is_null($check)) {
				return "Not authorize";
			}
			
			//check schedule publish or not , klo status 2 = success, 3 = deleted maka schedule ga bole diedit
			$check = Schedule::where("user_id","=",$user->id)
								->where("id","=",$sid)
								->where('schedules.status','>=',2)
								->first();
			if (!is_null($check)) {
				return "error 404";
			}
		}

		
		$accounts = Account::where("user_id","=",$user->id)
								->where("is_active","=",1)
								->get();
		
		$hashtags_collections = Template::where("user_id","=",$user->id)
										->where("type","=","hashtags")
										->get();
		
		$collections_captions = Template::where("user_id","=",$user->id)
										->where("type","=","templates")
										->get();
		
		$imageM = ImageModel::where("user_id","=",$user->id)->orderBy('images.id', 'desc')->get();
		
		$max_date = Carbon::now()->addSeconds($user->active_time)->format('Y-m-d H:i');
		
		return view('schedule.add',compact('sid','accounts','collections_captions','hashtags_collections','imageM','user','arr_repost','max_date'));
	}

	public function repost($imageid = null)
	{
		$user = Auth::user();
		if (!$user->is_confirmed) {
			return "Please Confirm Your Email";
		}
		
		$sid =0;
		//fill in for repost 
		$arr_repost = null;
		if(!is_null($imageid)) {
			$repost = ImageModel::where("user_id","=",$user->id)
								->where("id","=",$imageid)
								// ->where("is_use_caption","=",1)
								->first();
			if(is_null($repost)) {
				return "page not found";
			}
			$arr_repost["caption"] = $repost->caption;
			$arr_repost["owner"] = $repost->owner_post;
			// $arr_repost["url"] = url('/images/users/'.$user->username.'-'.$user->id.'/'.$repost->file);
      if ($repost->is_s3) {
        $arr_repost["url"] = Storage::disk('s3')->url($repost->file);
      }
      else {
        $arr_repost["url"] = url('/../vp/users/'.$user->username.'-'.$user->id.'/'.$repost->file);
      }
		}
		
		//check schedule page users, check schedule publish or not 
		if ($sid<>0) {
			//check schedule page users 
			$check = Schedule::where("user_id","=",$user->id)
								->where("schedules.id","=",$sid)
								->first();
			if (is_null($check)) {
				return "Not authorize";
			}
			
			//check schedule publish or not , klo status 2 = success, 3 = deleted maka schedule ga bole diedit
			$check = Schedule::where("user_id","=",$user->id)
								->where("id","=",$sid)
								->where('schedules.status','>=',2)
								->first();
			if (!is_null($check)) {
				return "error 404";
			}
		}

		
		$accounts = Account::where("user_id","=",$user->id)
								->where("is_active","=",1)
								->get();
		
		$hashtags_collections = Template::where("user_id","=",$user->id)
										->where("type","=","hashtags")
										->get();
		
		$collections_captions = Template::where("user_id","=",$user->id)
										->where("type","=","templates")
										->get();
		
		$imageM = ImageModel::where("user_id","=",$user->id)->orderBy('images.id', 'desc')->get();
		
		$max_date = Carbon::now()->addSeconds($user->active_time)->format('Y-m-d H:i');
		
		return view('schedule.add',compact('sid','accounts','collections_captions','hashtags_collections','imageM','user','arr_repost','max_date'));
	}

	/*
	* Schedule / Publish
	*/
	public function publish(req $request)
	{
		// $binarydata = pack('H_', '0a');
		$user = Auth::user();

		
		//check before publish
		if (!$request->has('accounts')) {
			$arr["type"]="error";
			$arr["message"]="Silahkan pilih account yang akan di post";
			return $arr;
		}
		
		//error klo ga ada image 
		if (Request::input("imguri")=="") {
			$arr["type"]="error";
			$arr["message"]="Silahkan Input file yang akan diupload";
			return $arr;
		}
		
		if ( ($request->hidden_method=="schedule") && ($request->publish_at == "") ) {
			$arr["type"]="error";
			$arr["message"]="Silahkan Input Waktu Publish File";
			return $arr;
		}
		
		if ( ($request->checkbox_delete) && ($request->delete_at == "") ) {
			$arr["type"]="error";
			$arr["message"]="Silahkan Input Waktu Delete File";
			return $arr;
		}

		if (count(explode("#",$request->description)) - 1 > 30 ) {
			$arr["type"] = "error";
			$arr["message"] = "hashtags tidak boleh lebih dari 30";
			return $arr;
		}
		
		if ( strlen($request->description) > 1700 ) {
			$arr["type"] = "error";
			$arr["message"] = "Character tidak boleh lebih dari 1700";
			return $arr;
		}
		
		
		//check klo delete at < publish at
		if ($request->hidden_method=="schedule")  {
			if ( ($request->checkbox_delete) && (strtotime($request->delete_at) <= strtotime($request->publish_at)) ) {
				$arr["type"]="error";
				$arr["message"]="Delete at harus lebih besar dari publish at";
				return $arr;
			}
      $max_date = Carbon::now()->addSeconds($user->active_time);
			$dtpublish = Carbon::createFromFormat('Y-m-d H:i', $request->publish_at);
			if ( $max_date->lt($dtpublish) ) {
				$arr["type"]="error";
				$arr["message"]="Waktu schedule publish tidak boleh diluar dari waktu akun";
				return $arr;
      }
		}

		
		//post tidak boleh lebih dari 3 dalam 1 jam untuk tiap accountnya
		/*if ($request->hidden_method=="schedule")  {
			$dt1 = Carbon::createFromFormat('Y-m-d H:i', $request->publish_at)->subMinutes(30);
			$dt2 = Carbon::createFromFormat('Y-m-d H:i', $request->publish_at)->addMinutes(30);
		}
		if ($request->hidden_method=="now")  {
			$dt1 = Carbon::now()->subMinutes(30);
			$dt2 = Carbon::now()->addMinutes(30);
		}*/
			foreach ($request->accounts as $account){
        //check 1 hari cuman bole 9 post per akun 
        $sa_count = ScheduleAccount::
                    join("schedules","schedules.id","=","schedule_account.schedule_id")
                    ->where("account_id",$account)
                    ->whereDate('schedule_account.publish_at', '=', date('Y-m-d'))
                    ->where('slug', 'not LIKE', '%StoryFile%')
                    ->count();
        if ($sa_count>9){
          $arr["type"] = "error";
          $arr["message"] = "Untuk tiap akun maksimal 1 hari posting 9 post";
          return $arr;
        }

				/*$check = Account::find($account);
				if (!is_null($check)) {
					if (!$check->is_started){
						$arr["type"] = "pending";
						return $arr;
					}
				}
				$check = Schedule::join("schedule_account","schedule_account.schedule_id","=","schedules.id")
									->where("user_id","=",$user->id)
									->where("account_id","=",$account)
									// ->where("schedules.publish_at",">=",$dt1->toDateString()." ".$dt1->format('H').":00:00" )
									// ->where("schedules.publish_at","<=",$dt1->toDateString()." ".$dt1->format('H').":59:59" )
									->whereBetween(DB::raw('DATE(schedules.publish_at)'), array($dt1, $dt2))
									->count();
				if ($check>3) {
					$arr["type"]="error";
					$arr["message"] = "Schedule Post maksimum 3 Post tiap jamnya";
					return $arr;
				}*/
			}
		
		// cek klo belum distart OLD
		/*if (!$user->is_started) {
			$arr["type"] = "pending";
			return $arr;
		}*/

		//check ga boleh dalam 15 menit  dengan post lain(dalam 1 account)
		/*$dt1 = Carbon::createFromFormat('Y-m-d H:i', $request->publish_at);
		foreach ($request->accounts as $account){
			$check = Schedule::join("schedule_account","schedule_account.schedule_id","=","schedules.id")
								->where("schedules.status","=",1)
								->where("user_id","=",$user->id)
								->where("account_id","=",$account)
								->get();
			foreach($check as $data){
				$dt2 = Carbon::createFromFormat('Y-m-d H:i:s', $data->publish_at);
				if ($dt1->diffInMinutes($dt2) <= 15 ) {
					$arr["type"]="error";
					$arr["message"]="Process schedule tidak boleh selisih 15 menit kurang, dari proses schedule lain";
					return $arr;
				}
			}
		}*/
		
		//check klo publish_at lebih kecil dari now 
		$now = Carbon::now();
		if ($request->hidden_method=="schedule") {
			$dt1 = Carbon::createFromFormat('Y-m-d H:i', $request->publish_at);
			if ( $dt1->lt($now) ) {
				$arr["type"]="error";
				$arr["message"]="Input waktu publish tidak boleh lebih kecil dari waktu sekarang";
				return $arr;
			}
		}
		
		// $dir = public_path('images/uploads/'.$user->username.'-'.$user->id); 
		$dir = public_path('../vp/uploads/'.$user->username.'-'.$user->id); 
		if (!file_exists($dir)) {
			mkdir($dir,0741,true);
		}
    $dirs3 = 'vp/uploads/'.$user->username.'-'.$user->id; 

		if ($request->id == 0) {
			// new schedule
			//copy file jadi file publish
			//slug file name 
			$last_hit = Schedule::where("user_id","=",$user->id)
									->where("slug","like","PublishFile%")
									->orderBy('id', 'desc')->first();
			if (is_null($last_hit)) {
				$slug = "PublishFile-00000";
			} else {
				$temp_arr1 = explode(".", $last_hit->slug );
				$temp_arr2 = explode("-", $temp_arr1[0] );
				$ctr = intval($temp_arr2[1]); $ctr++;
				$slug = "PublishFile-".str_pad($ctr, 5, "0", STR_PAD_LEFT);
			}
			
			$filename = $slug;
			// Image::make(Request::input("imguri"))->save($dir."/".$filename.".jpg");
      $url = Storage::disk('s3')->put($dirs3."/".$filename.".jpg", file_get_contents(Request::input("imguri")), 'public');

			$schedule = new Schedule;
			// $schedule->image = url('/images/uploads/'.$user->username.'-'.$user->id.'/'.$filename.".jpg");
			// $schedule->image = url('/../vp/uploads/'.$user->username.'-'.$user->id.'/'.$filename.".jpg");
			// $schedule->image = $url;
			$schedule->image = $dirs3."/".$filename.".jpg";
			$schedule->slug = $filename;
		} else {
			// edit schedule
			$schedule = Schedule::findOrFail($request->id);
			// Image::make(Request::input("imguri"))->save($dir."/".$request->slug.".jpg");
      if (Storage::disk('s3')->exists($schedule->image) ) {
        // Storage::disk('s3')->delete($schedule->image);
      }
      $url = Storage::disk('s3')->put($dirs3."/".$request->slug.".jpg", file_get_contents(Request::input("imguri")), 'public');
			
      // $schedule->image = $url;
      $schedule->image = $dirs3."/".$request->slug.".jpg";
			$schedule->slug = $request->slug;
			
			$check_sa = ScheduleAccount::where("schedule_id","=",$schedule->id)
									->where("status","=",5)
									->get();
			foreach($check_sa as $data) {
				$update_sa = ScheduleAccount::find($data->id);
				$update_sa->status = 0;
				$update_sa->status_helper = 0;
				$update_sa->status_process = 0;
				$update_sa->save();
			}
			
		}

		
		
		$schedule->user_id = $user->id;
		// $clean_text = ResearchController::removeEmoji($request->description);
		// $schedule->description = $clean_text;
		$schedule->description = $request->description;
		$schedule->status = 1;
		if ($request->hidden_method=="schedule")  {
			$schedule->publish_at = strtotime($request->publish_at);
		}
		if ($request->hidden_method=="now")  {
			$schedule->publish_at = Carbon::now();
		}
		
		if ($request->checkbox_delete) {
			$schedule->delete_at = strtotime($request->delete_at);
			$schedule->is_deleted = 1;
		} else {
			$schedule->is_deleted = 0;
		}
		$schedule->media_type = "photo";
    $schedule->is_s3 = 1;
		$schedule->save();
		if ($request->has('accounts')) {
			//klo edit maka schedule account dihapus dulu, klo uda ada yang keposting maka akan ke schedule ulang
			if ($request->id <> 0) {
				$delete_sa = ScheduleAccount::where("schedule_id", "=", $request->id)
									->delete();
			}
			
			// Account
			// $account = array();
			// foreach($request->accounts as $data) {
				// $account[] = array(
											// "id"=>$data->id,
											// "publish_at"=>$schedule->publish_at,
										// );
			// }
			$schedule->PutAccount($request->accounts);
			// $schedule->PutAccount($accounts);
		}
		$schedule->save();
		$check_sa = ScheduleAccount::where("schedule_id","=",$schedule->id)
								->get();
		foreach($check_sa as $data) {
			$update_sa = ScheduleAccount::find($data->id);
			$update_sa->publish_at = $schedule->publish_at;
			$update_sa->save();
		}
		
		//kasi tanda image sudah dischedule
		$imageM = ImageModel::find(Request::input("image_id"));
		if (!is_null($imageM)) {
			$imageM->is_schedule = 1;
			$imageM->save(); 			
		}

		
		
		$arr["type"]="success";
		$arr["message"]="Process publish berhasil disimpan";
		return $arr;
	}
	
	public function call_action_start_schedule_akun(req $request)
	{
		$user = Auth::user();
		$now = Carbon::now();
		foreach ($request->accounts as $account_id){
			//pengecekan server side
			$account = Account::find($account_id);
			if (!is_null($account)) {
				if ( ($user->id<>$account->user_id) || ($account->is_active<>1) ) {
					continue;
				}
				//kasi proxy ke account2 yang proxynya 0
				$account->is_started = 1;
				$account->running_time = $now;

				//kasi proxy ke account yang proxynya 0
				// klo ga ada sebelumnya maka proxy id dicari dari celebgramme
				//get proxy 
				if(!File::exists(storage_path('ig-cookies/'.$account->username))) {
					File::makeDirectory(storage_path('ig-cookies/'.$account->username), 0755, true);
				}

				$cookiefile = base_path('storage/ig-cookies/'.$account->username.'/').'cookies-celebpost-temp.txt';


				if ($user->is_member_rico==0) {
					$url = "https://activfans.com/dashboard/get-proxy-id/".$account->username;
				}
				else{
					$url = "https://activfans.com/amelia/get-proxy-id/".$account->username;
				}
				$c = curl_init();

				curl_setopt($c, CURLOPT_URL, $url);
				curl_setopt($c, CURLOPT_REFERER, $url);
				curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($c, CURLOPT_COOKIEFILE, $cookiefile);
				curl_setopt($c, CURLOPT_COOKIEJAR, $cookiefile);
				$page = curl_exec($c);
				curl_close($c);

				$arr_res = json_decode($page,true);

				$proxy_id = $arr_res["proxy_id"]; 
				$is_on_celebgramme = $arr_res["is_on_celebgramme"]; 

				$account->proxy_id = $proxy_id;
				$account->is_on_celebgramme = $is_on_celebgramme;
				
				$account->save();
			}
		}

		$arr["type"]="success";
		return $arr;
	}

	/* mau dihapus
	public function saveimage(req $request)
	{
			// Create Image
			foreach (array_filter($request->file('file')) as $value) {
					$myimg = $value;
			}
			
			$gambar = $myimg;
			$extension = $gambar->getClientOriginalExtension();
			$file_name = $gambar->getClientOriginalName();
			$image = Image::make($gambar);
			$image->crop(
					intval($request->width),
					intval($request->height),
					intval($request->x),
					intval($request->y)
			);
			$name = sha1(time() . $file_name);
			// $destination = public_path() . '/images/uploads/' . $name . '.' . $extension;
			$destination = public_path() . '../vp/uploads/' . $name . '.' . $extension;
			$image->save($destination);
			$uptogd = $name . '.' . $extension;
			// $uri = asset('images/uploads/'.$uptogd);
			// $uri = asset('../vp/uploads/'.$uptogd);
			$uri = url('../vp/uploads/'.$uptogd);
			// Update
			$schedule = Schedule::findOrFail($request->id);
			$schedule->image = $uri;
			$schedule->save();
			return response()->json(['status' => 200, 'uri' => $uri]);
			
	}*/
	
	public function delete($id)
	{
		$schedule = Schedule::findOrFail($id);
		$dir = basename($schedule->image);
		// $directory = public_path() . '/images/uploads/' .$dir;
		$directory = public_path() . '/../vp/uploads/' .$dir;
    if ($schedule->is_s3) {
      if (Storage::disk('s3')->exists($schedule->image) ) {
        Storage::disk('s3')->delete($schedule->image);
      }
    }
    else {
      File::delete($directory);
    }
		Schedule::destroy($id);
		ScheduleAccount::where("schedule_id",$id)->delete();
		return back()->with('status', 'Schedule Deleted!');
	}

	public function load_schedule()
  {
    $user = Auth::user();
		
		if (Request::input("fromSort")==1) {
			$str_sort = "asc";
		} else if (Request::input("fromSort")==2) {
			$str_sort = "desc";
		}
		
		if (Request::input("sortBy")==1) {
			$collection1 = Schedule::where("user_id","=",$user->id)
										->orderBy('created_at', $str_sort)
										->get();
		} else if (Request::input("sortBy")==2) {
			$collection1 = Schedule::where("user_id","=",$user->id)
										->orderBy('publish_at', $str_sort)
										->get();
		}
		
		if (Request::input("showStatus")==1) {
			$collection2 = Schedule::where("user_id","=",$user->id)
										->get();
		} else if (Request::input("showStatus")==2) {
			//published
			$collection2 = Schedule::where("user_id","=",$user->id)
										->where("status","=",2)
										->get();
		} else if (Request::input("showStatus")==3) {
			//deleted
			$collection2 = Schedule::where("user_id","=",$user->id)
										->where("status","=",3)
										->get();
		} else if (Request::input("showStatus")==4) {
			//pending
			$collection2 = Schedule::where("user_id","=",$user->id)
										->where("status","=",1)
										->get();
		}
		
		$collection3 = Schedule::where("user_id","=",$user->id)
										// ->where("publish_at",">=",date("Y-m-d", intval(Request::input('from'))))
										// ->where("publish_at","<=",date("Y-m-d", intval(Request::input('to'))))
										->whereDate("publish_at",">=",date("Y-m-d H:i:s", intval(Request::input('from'))))
										->whereDate("publish_at","<=",date("Y-m-d H:i:s", intval(Request::input('to'))))
										->get();
		
		$data = $collection1->intersect($collection2);
		$data = $data->intersect($collection3);

    //$data = $data->forPage(Request::input('page'), 10); //Filter the page var

$page = Request::input('page'); // Get the current page or default to 1, this is what you miss!
$perPage = 10;
$offset = ($page * $perPage) - $perPage;

		$itemsForCurrentPage = array_slice($data->all(), $offset, $perPage, true);
		$pagination= new LengthAwarePaginator($itemsForCurrentPage,  count($data), // Total items
			$perPage, // Items per page
			$page, // Current page
			['path' => "", 'query' => ""]
		);
		
    /*return view('schedule.content')->with(
                array(
                  'user'=>$user,
                  'data'=>$data,
                  'page'=>Request::input('page'),
                ));*/
		$arr['content'] = (string) view('schedule.content')->with(
                array(
                  'user'=>$user,
                  'data'=>$data,
                  'page'=>Request::input('page'),
                ));
		$arr['pagination'] = (string) view('schedule.pagination',compact('data','user','pagination','page'));

		return $arr;
  }

	public function pagination_schedule()
  {
		$user = Auth::user();
		
		if (Request::input("fromSort")==1) {
			$str_sort = "asc";
		} else if (Request::input("fromSort")==2) {
			$str_sort = "desc";
		}
		
		if (Request::input("sortBy")==1) {
			$collection1 = Schedule::where("user_id","=",$user->id)
										->orderBy('created_at', $str_sort)
										->get();
		} else if (Request::input("sortBy")==2) {
			$collection1 = Schedule::where("user_id","=",$user->id)
										->orderBy('publish_at', $str_sort)
										->get();
		}
		
		if (Request::input("showStatus")==1) {
			$collection2 = Schedule::where("user_id","=",$user->id)
										->get();
		} else if (Request::input("showStatus")==2) {
			//published
			$collection2 = Schedule::where("user_id","=",$user->id)
										->where("status","=",2)
										->get();
		} else if (Request::input("showStatus")==3) {
			//deleted
			$collection2 = Schedule::where("user_id","=",$user->id)
										->where("status","=",3)
										->get();
		} else if (Request::input("showStatus")==4) {
			//pending
			$collection2 = Schedule::where("user_id","=",$user->id)
										->where("status","=",1)
										->get();
		}
		
			$collection3 = Schedule::where("user_id","=",$user->id)
										->where("publish_at",">=",date("Y-m-d", intval(Request::input('from'))))
										->where("publish_at","<=",date("Y-m-d", intval(Request::input('to'))))
										->get();
		
		$data = $collection1->intersect($collection2);
		$data = $data->intersect($collection3);

		
$page = Request::input('page'); // Get the current page or default to 1, this is what you miss!
$perPage = 10;
$offset = ($page * $perPage) - $perPage;

		$itemsForCurrentPage = array_slice($data->all(), $offset, $perPage, true);
		$pagination= new LengthAwarePaginator($itemsForCurrentPage,  count($data), // Total items
			$perPage, // Items per page
			$page, // Current page
			['path' => "", 'query' => ""]
		);
		
	
    // return view('schedule.pagination')->with(
                // array(
                  // 'user'=>$user,
                  // 'data'=>$data,
                // ));
		return view('schedule.pagination',compact('data','user','pagination','page'));
  }
	
	public function test_image()
  {
		$users = Users::all();
		foreach ($users as $user) {
			$accounts = Account::where("user_id","=",$user->id)->get();
			foreach($accounts as $account){
				$update_account = Account::find($account->id);
				$update_account->is_post_berurutan = $user->is_post_berurutan;
				$update_account->save();
			}
		}
		exit;

		
		
		
		
		$user = Users::find(5);
		// $dir = public_path('images/uploads/'.$user->username.'-'.$user->id); 
		$dir = public_path('../vp/uploads/'.$user->username.'-'.$user->id); 
		if (!file_exists($dir)) {
			mkdir($dir,0741,true);
		}
		$arr_size = getimagesize("https://activpost.net/vp/uploads/it.axiapro@gmail.com-6/PublishFile-00011.jpg");
		dd($arr_size);
		return Image::make("https://activpost.net/vp/uploads/it.axiapro@gmail.com-6/PublishFile-00011.jpg")
						->resize(50, null, function ($constraint) {
								$constraint->aspectRatio();
						})								
						->save($dir."/thumb.jpg");

	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	

/*
	Status:
	- 0 draft
*/
	public function index_video()
	{
		$schedules = Schedule::where('status','!=',0)->orderBy('created_at', 'desc')->paginate(10);
		return view('schedule-video.index',compact('schedules'));
	}
	public function add_video()
	{
		$chkdraft = Schedule::where('status',0)->first();
		if (!$chkdraft) {
			$create = new Schedule;
			$create->save();
			$sid = $create->id;
		} else {
			$sid = $chkdraft->id;
		}
		$schedule = Schedule::findOrFail($sid);
		$accounts = Account::all();
		return view('schedule-video.add',compact('schedule','accounts'));
	}
	public function publish_video(req $request)
	{
		ini_set('memory_limit', '-1');
		$user = Auth::user();
		
			foreach (array_filter($request->file('file')) as $value) {
					$myimg = $value;
			}
			
			$gambar = $myimg;
			$extension = $gambar->getClientOriginalExtension();
			$file_name = $gambar->getClientOriginalName();

			$name = sha1(time() . $file_name);
			$destination = public_path() . '/../vp/uploads/' . $name . '.' . $extension;
			
			// $request->file('image')->move(base_path() . '/images/catalog/', $imageName);
			// $file = Request::file('file');
			// Storage::disk('local')->put($destination,  File::get($myimg) );
			$gambar->move(public_path() . '/../vp/uploads/', $name . '.' . $extension);
			
			$uptogd = $name . '.' . $extension;
			$uri = asset('../vp/uploads/'.$uptogd);
			// Update
			$schedule = Schedule::findOrFail($request->id);
			$schedule->image = $uri;
			$schedule->save();
		
		
		
		
		
		
		$schedule = Schedule::findOrFail($request->id);
		if ($request->has('accounts')) {
			if (!empty($schedule->image)) {
				$schedule->description = $request->description;
				$schedule->status = 1;
				$schedule->publish_at = strtotime($request->publish_at);
				$schedule->delay = $request->delay;
				$schedule->media_type = "video";
				$schedule->save();
				if ($request->has('accounts')) {
					// Account
					$schedule->PutAccount($request->accounts);
				}
				return response()->json(['status' => 200]);
			} else {
				return response()->json(['status' => 403, 'msg' => 'Image is Required']);
			}
		} else {
			return response()->json(['status' => 403, 'msg' => 'Accounts is Required']);
		}
	}
	public function savevideo(req $request)
	{
			// Create Image
			foreach (array_filter($request->file('file')) as $value) {
					$myimg = $value;
			}
			
			$gambar = $myimg;
			$extension = $gambar->getClientOriginalExtension();
			$file_name = $gambar->getClientOriginalName();
			$image = Image::make($gambar);
			$image->crop(
					intval($request->width),
					intval($request->height),
					intval($request->x),
					intval($request->y)
			);
			$name = sha1(time() . $file_name);
			$destination = public_path() . '/../vp/uploads/' . $name . '.' . $extension;
			$image->save($destination);
			$uptogd = $name . '.' . $extension;
			// $uri = asset('public/images/uploads/'.$uptogd);
			// Update
			$schedule = Schedule::findOrFail($request->id);
			$schedule->image = $uri;
			$schedule->save();
			return response()->json(['status' => 200, 'uri' => $uri]);
			
	}
	public function delete_video($id)
	{
		$schedule = Schedule::findOrFail($id);
		$dir = basename($schedule->image);
		// $directory = public_path() . '/images/uploads/' .$dir;
		$directory = public_path() . '/../vp/uploads/' .$dir;
		File::delete($directory);
		Schedule::destroy($id);
		return back()->with('status', 'Schedule Deleted!');
	}

	public function makeimage()  
	{  
		$img = Image::make(public_path('images/uploads/asd.jpg'));  
		// $img->text('This is a example ', 120, 100);  
		$img->text('This is a example ', 120, 100, function($font) {  
				$font->file(public_path('fonts/bootstrap/Helvetica.ttf'));  
				$font->size(28);  
				$font->color('#e1e1e1');  
				// $font->align('center');  
				// $font->valign('bottom');  
				// $font->angle(90);  
		});  			 
		$img->save(public_path('../vp/uploads/asd-new.jpg'));  
	}  		

  public function schedule_video($sid=0){
    $user = Auth::user();
    if (!$user->is_confirmed) {
      return "Please Confirm Your Email";
    }
    
    $arr_repost = null;
    
    //check schedule page users, check schedule publish or not 
    if ($sid<>0) {
      //check schedule page users 
      $check = Schedule::where("user_id","=",$user->id)
                ->where("schedules.id","=",$sid)
                ->first();
      if (is_null($check)) {
        return "Not authorize";
      }
      
      //check schedule publish or not , klo status 2 = success, 3 = deleted maka schedule ga bole diedit
      $check = Schedule::where("user_id","=",$user->id)
                ->where("id","=",$sid)
                ->where('schedules.status','>=',2)
                ->first();
      if (!is_null($check)) {
        return "error 404";
      }
    }

    
    $accounts = Account::where("user_id","=",$user->id)
                ->where("is_active","=",1)
                ->get();
    
    $hashtags_collections = Template::where("user_id","=",$user->id)
                    ->where("type","=","hashtags")
                    ->get();
    
    $collections_captions = Template::where("user_id","=",$user->id)
                    ->where("type","=","templates")
                    ->get();
    
    $max_date = Carbon::now()->addSeconds($user->active_time)->format('Y-m-d H:i');
    
    return view('schedule-video2.add',compact('sid','accounts','collections_captions','hashtags_collections','user','arr_repost','max_date'));
  }

  public function save_video_schedule(req $request)
  {
    $user = Auth::user();
		
    if($request->duration_video>=61){
      $arr["type"] = "error";
      $arr["message"] = "Durasi video untuk upload post maksimal 1 menit";  
      return $arr;
    }
    
		/*if ( ( ($request->width>1090) && ($request->height>1090) ) || ( ($request->width>1300) && ($request->height>800) ) || ( ($request->width>800) && ($request->height>1300) ) ) {
			$arr["type"] = "error";
			$arr["message"] = "Ukuran maximum width = 1080px & height = 1080px";
			return $arr;
		}
		if ( ($request->width<640) || ($request->height<640) ) {
			$arr["type"] = "error";
			$arr["message"] = "Ukuran file Minimum 640px X 640px";
			return $arr;
		}
		if ( ($request->width>1080) || ($request->height>1350) ) {
			$arr["type"] = "error";
			$arr["message"] = "Ukuran maximum width = 1080px & height = 1350px";
			return $arr;
		}*/
		$ratio_img = $request->widthFile/$request->heightFile;
		if ( ($ratio_img < 0.8) || ($ratio_img>1.91) ) {
			$arr["type"] = "error";
			$arr["message"] = "Ratio video (Width / Height) Harus berkisar antara 0.8 sampai 1.91. Ratio image anda ".$ratio_img.' gunakan width 1080px x height 1080px';
			return $arr;
		}
		
    $arr["type"] = "success";
    $arr["message"] = "Data berhasil disimpan";
    //$arr["url"] = asset('../vp/uploads/'.$user->username.'-'.$user->id."/".$filename);
    //$arr["url"] = $dir.'.'.$filename;
    $arr["url"] = 'file uploaded';
    return $arr;
  } 

  public function publish_video_schedule(req $request)
  {
    $user = Auth::user();

    //check before publish
    if (!$request->has('accounts')) {
      $arr["type"]="error";
      $arr["message"]="Silahkan pilih account yang akan di post";
      return $arr;
    }
		
		//pengecekan cuman bole schedule 10 video(yang belum terpost) dalam user email
		$schedule_count = Schedule::where("media_type","video")
											->where("status","<",2)
                      ->where("user_id",$user->id)
											->count();
		if ($schedule_count> 10 ) {
      $arr["type"]="error";
      $arr["message"]="Schedule Video maksimal 10 video yang belum terposting(Post &Story)";
      return $arr;
		}
    
    //error klo ga ada image 
    if (Request::input("imguri")=="") {
      $arr["type"]="error";
      $arr["message"]="Silahkan Input file yang akan diupload";
      return $arr;
    }
    
    if ( ($request->hidden_method=="schedule") && ($request->publish_at == "") ) {
      $arr["type"]="error";
      $arr["message"]="Silahkan Input Waktu Publish File";
      return $arr;
    }
    
    if ( ($request->checkbox_delete) && ($request->delete_at == "") ) {
      $arr["type"]="error";
      $arr["message"]="Silahkan Input Waktu Delete File";
      return $arr;
    }

    if (count(explode("#",$request->description)) - 1 > 30 ) {
      $arr["type"] = "error";
      $arr["message"] = "hashtags tidak boleh lebih dari 30";
      return $arr;
    }
    
    if ( strlen($request->description) > 1700 ) {
      $arr["type"] = "error";
      $arr["message"] = "Character tidak boleh lebih dari 1700";
      return $arr;
    }    
    
    //check klo delete at < publish at
    if ($request->hidden_method=="schedule")  {
      if ( ($request->checkbox_delete) && (strtotime($request->delete_at) <= strtotime($request->publish_at)) ) {
        $arr["type"]="error";
        $arr["message"]="Delete at harus lebih besar dari publish at";
        return $arr;
      }
      $max_date = Carbon::now()->addSeconds($user->active_time);
			$dtpublish = Carbon::createFromFormat('Y-m-d H:i', $request->publish_at);
			if ( $max_date->lt($dtpublish) ) {
				$arr["type"]="error";
				$arr["message"]="Waktu schedule publish tidak boleh diluar dari waktu akun";
				return $arr;
      }
    }
    
    //post tidak boleh lebih dari 3 dalam 1 jam untuk tiap accountnya
    /*if ($request->hidden_method=="schedule")  {
      $dt1 = Carbon::createFromFormat('Y-m-d H:i', $request->publish_at)->subMinutes(30);
      $dt2 = Carbon::createFromFormat('Y-m-d H:i', $request->publish_at)->addMinutes(30);
    }
    if ($request->hidden_method=="now")  {
      $dt1 = Carbon::now()->subMinutes(30);
      $dt2 = Carbon::now()->addMinutes(30);
    }*/
      foreach ($request->accounts as $account){
        //check 1 hari cuman bole 9 post per akun 
        $sa_count = ScheduleAccount::
                    join("schedules","schedules.id","=","schedule_account.schedule_id")
                    ->where("account_id",$account)
                    ->whereDate('schedule_account.publish_at', '=', date('Y-m-d'))
                    ->where('slug', 'not LIKE', '%StoryFile%')
                    ->count();
        if ($sa_count>9){
          $arr["type"] = "error";
          $arr["message"] = "Untuk tiap akun maksimal 1 hari posting 9 post";
          return $arr;
        }

        /*$check = Account::find($account);
        if (!is_null($check)) {
          if (!$check->is_started){
            $arr["type"] = "pending";
            return $arr;
          }
        }
        $check = Schedule::join("schedule_account","schedule_account.schedule_id","=","schedules.id")
                  ->where("user_id","=",$user->id)
                  ->where("account_id","=",$account)
                  // ->where("schedules.publish_at",">=",$dt1->toDateString()." ".$dt1->format('H').":00:00" )
                  // ->where("schedules.publish_at","<=",$dt1->toDateString()." ".$dt1->format('H').":59:59" )
                  ->whereBetween(DB::raw('DATE(schedules.publish_at)'), array($dt1, $dt2))
                  ->count();
        if ($check>3) {
          $arr["type"]="error";
          $arr["message"] = "Schedule Post maksimum 3 Post tiap jamnya";
          return $arr;
        }*/
      }
  
    //check klo publish_at lebih kecil dari now 
    $now = Carbon::now();
    if ($request->hidden_method=="schedule") {
      $dt1 = Carbon::createFromFormat('Y-m-d H:i', $request->publish_at);
      if ( $dt1->lt($now) ) {
        $arr["type"]="error";
        $arr["message"]="Input waktu publish tidak boleh lebih kecil dari waktu sekarang";
        return $arr;
      }
    }
    
    /* $dir = public_path('../vp/uploads/'.$user->username.'-'.$user->id); 
    if (!file_exists($dir)) {
      mkdir($dir,0741,true);
    }*/
    $dir = 'vp/uploads/'.$user->username.'-'.$user->id; 

    if ($request->id == 0) {
      // new schedule
      //copy file jadi file publish
      //slug file name 
      $last_hit = Schedule::where("user_id","=",$user->id)
                  ->where("slug","like","VideoFile%")
                  ->orderBy('id', 'desc')->first();
      if (is_null($last_hit)) {
        $slug = "VideoFile-00000";
      } else {
        $temp_arr1 = explode(".", $last_hit->slug );
        $temp_arr2 = explode("-", $temp_arr1[0] );
        $ctr = intval($temp_arr2[1]); $ctr++;
        $slug = "VideoFile-".str_pad($ctr, 5, "0", STR_PAD_LEFT);
      }
      
      $uploadedFile = $request->file('imgData');   
      $filename = $slug.'.'.$uploadedFile->getClientOriginalExtension();
      // $uploadedFile->move($dir, $filename);   
      $url = Storage::disk('s3')->putFile($dir, $request->file('imgData'),'public');

      //Storage::move($request->imguri, $dir.'/'.$filename.'mp4');

      $schedule = new Schedule;
      // $schedule->image = url('/../vp/uploads/'.$user->username.'-'.$user->id.'/'.$filename);
      $schedule->image = $url;
      $schedule->slug = $filename;
    } else {
      // edit schedule
      $schedule = Schedule::findOrFail($request->id);
      //dd($request->all());
      if($request->hasFile('imgData')){
        $uploadedFile = $request->file('imgData');   

        $ext = explode('.', $request->slug);

        $filename = $ext[0].'.'.$uploadedFile->getClientOriginalExtension();

        if($ext[1]!=$uploadedFile->getClientOriginalExtension()){
          //delete file lama 
          File::delete($dir.'/'.$request->slug);
        }

        // $uploadedFile->move($dir, $filename);   
        if (Storage::disk('s3')->exists($schedule->image) ) {
          Storage::disk('s3')->delete($schedule->image);
        }
        $url = Storage::disk('s3')->putFile($dir, $request->file('imgData'),'public');

        //Storage::move($request->imguri, $dir.'/'.$request->slug.'mp4');
        
        $schedule->image = $url;
        $schedule->slug = $request->slug;
      }
      
      $check_sa = ScheduleAccount::where("schedule_id","=",$schedule->id)
                  ->where("status","=",5)
                  ->get();
      foreach($check_sa as $data) {
        $update_sa = ScheduleAccount::find($data->id);
        $update_sa->status = 0;
        $update_sa->status_helper = 0;
        $update_sa->status_process = 0;
        $update_sa->save();
      }
      
    }

    $schedule->thumbnail_video = $request->thumbnail;
    $schedule->user_id = $user->id;
    $schedule->description = $request->description;
    $schedule->status = 1;
    if ($request->hidden_method=="schedule")  {
      $schedule->publish_at = strtotime($request->publish_at);
    }
    if ($request->hidden_method=="now")  {
      $schedule->publish_at = Carbon::now();
    }
    
    if ($request->checkbox_delete) {
      $schedule->delete_at = strtotime($request->delete_at);
      $schedule->is_deleted = 1;
    } else {
      $schedule->is_deleted = 0;
    }
    $schedule->media_type = "video";
    $schedule->save();
    if ($request->has('accounts')) {
      //klo edit maka schedule account dihapus dulu, klo uda ada yang keposting maka akan ke schedule ulang
      if ($request->id <> 0) {
        $delete_sa = ScheduleAccount::where("schedule_id", "=", $request->id)
                  ->delete();
      }
      
      // Account
      // $account = array();
      // foreach($request->accounts as $data) {
        // $account[] = array(
                      // "id"=>$data->id,
                      // "publish_at"=>$schedule->publish_at,
                    // );
      // }
      $schedule->PutAccount($request->accounts);
      // $schedule->PutAccount($accounts);
    }
    $schedule->is_s3 = 1;
    $schedule->save();
    $check_sa = ScheduleAccount::where("schedule_id","=",$schedule->id)
                ->get();
    foreach($check_sa as $data) {
      $update_sa = ScheduleAccount::find($data->id);
      $update_sa->publish_at = $schedule->publish_at;
      $update_sa->save();
    }
    
    //kasi tanda image sudah dischedule
    $imageM = ImageModel::find(Request::input("image_id"));
    if (!is_null($imageM)) {
      $imageM->is_schedule = 1;
      $imageM->save();      
    }

    $arr["type"]="success";
    $arr["message"]="Process publish berhasil disimpan";
    return $arr;
  }

  public function schedule_story($sid=0){
    $user = Auth::user();
    if (!$user->is_confirmed) {
      return "Please Confirm Your Email";
    }
    
    $arr_repost = null;
    
    //check schedule page users, check schedule publish or not 
    if ($sid<>0) {
      //check schedule page users 
      $check = Schedule::where("user_id","=",$user->id)
                ->where("schedules.id","=",$sid)
                ->first();
      if (is_null($check)) {
        return "Not authorize";
      }
      
      //check schedule publish or not , klo status 2 = success, 3 = deleted maka schedule ga bole diedit
      $check = Schedule::where("user_id","=",$user->id)
                ->where("id","=",$sid)
                ->where('schedules.status','>=',2)
                ->first();
      if (!is_null($check)) {
        return "error 404";
      }
    }

    
    $accounts = Account::where("user_id","=",$user->id)
                ->where("is_active","=",1)
                ->get();
    
    $hashtags_collections = Template::where("user_id","=",$user->id)
                    ->where("type","=","hashtags")
                    ->get();
    
    $collections_captions = Template::where("user_id","=",$user->id)
                    ->where("type","=","templates")
                    ->get();
    
    $max_date = Carbon::now()->addSeconds($user->active_time)->format('Y-m-d H:i');
    
    return view('schedule-story.add',compact('sid','accounts','collections_captions','hashtags_collections','user','arr_repost','max_date'));
  }

  public function save_story_schedule(req $request)
  {
    $user = Auth::user();
    /*$uploadedFile = $request->file('imgData');   

    $filename = "temp.".$uploadedFile->getClientOriginalExtension();

    $dir = public_path('../vp/uploads/'.$user->username.'-'.$user->id); 
    if (!file_exists($dir)) {
      mkdir($dir,0741,true);
    }
    
    if($request->hasFile('imgData')){
      $upload_success = $uploadedFile->move($dir, $filename);   
    }*/
    
    $image =  array("jpg", "png", "gif", "bmp", "jpeg","tiff","JPG","PNG","GIF","BMP","JPEG","TIFF");

    if(in_array($request->extFile,$image) ) {
      $arr['jenisfile'] = 'image';

			$ratio = $request->widthFile/ $request->heightFile;
      if($ratio<0.56 || $ratio>0.67){
        $arr['type'] = 'error';
        $arr['message'] = 'Image harus memiliki aspect ratio 9:16 ratio file anda : '.$ratio.' gunakan width 1080px x height 1920px';
        return $arr;
      }
    } 
    else {
      $arr['jenisfile'] = 'video';

      /*$ffprobe = FFMpeg\FFProbe::create();
      $video_dimensions = $ffprobe
          ->streams( $uploadedFile )   // extracts streams informations
          ->videos()                      // filters video streams
          ->first()                       // returns the first video stream
          ->getDimensions();              // returns a FFMpeg\Coordinate\Dimension object
      $width = $video_dimensions->getWidth();
      $height = $video_dimensions->getHeight();
      $ratio = $width/$height;
      $duration = $ffprobe->format( $uploadedFile )->get('duration');*/

			$ratio = $request->widthFile/ $request->heightFile;
      if($ratio<0.56 || $ratio>0.67){
        $arr['type'] = 'error';
        $arr['message'] = 'Video harus memiliki aspect ratio 9:16 ratio file anda : '.$ratio.' gunakan width 1080px x height 1920px';
        return $arr;
      } else if ($request->duration_video>=16) {
        $arr['type'] = 'error';
        $arr['message'] = 'Durasi video untuk upload story maksimal 15 detik';
        return $arr;
      }
    }

    $arr["type"] = "success";
    $arr["message"] = "Data berhasil disimpan";
    //$arr["url"] = asset('../vp/uploads/'.$user->username.'-'.$user->id."/".$filename);
    //$arr["url"] = $dir.'.'.$filename;
    $arr["url"] = 'file uploaded';
    return $arr;
  } 

  public function publish_story_schedule(req $request)
  {
    $user = Auth::user();

    //check before publish
    if (!$request->has('accounts')) {
      $arr["type"]="error";
      $arr["message"]="Silahkan pilih account yang akan di post";
      return $arr;
    }
    
		//pengecekan cuman bole schedule 10 video(yang belum terpost) dalam user email
		$schedule_count = Schedule::where("media_type","video")
											->where("status","<",2)
											->where("user_id",$user->id)
											->count();
		if ($schedule_count> 10 ) {
      $arr["type"]="error";
      $arr["message"]="Schedule Video maksimal 10 video yang belum terposting(Post &Story)";
      return $arr;
		}
    
    //error klo ga ada image 
    if (Request::input("imguri")=="") {
      $arr["type"]="error";
      $arr["message"]="Silahkan Input file yang akan diupload";
      return $arr;
    }
    
    if ( ($request->hidden_method=="schedule") && ($request->publish_at == "") ) {
      $arr["type"]="error";
      $arr["message"]="Silahkan Input Waktu Publish File";
      return $arr;
    }
    
    if ( ($request->checkbox_delete) && ($request->delete_at == "") ) {
      $arr["type"]="error";
      $arr["message"]="Silahkan Input Waktu Delete File";
      return $arr;
    }

    /*if (count(explode("#",$request->description)) - 1 > 30 ) {
      $arr["type"] = "error";
      $arr["message"] = "hashtags tidak boleh lebih dari 30";
      return $arr;
    }
    
    if ( strlen($request->description) > 1700 ) {
      $arr["type"] = "error";
      $arr["message"] = "Character tidak boleh lebih dari 1700";
      return $arr;
    }*/    
    
    //check klo delete at < publish at
    if ($request->hidden_method=="schedule")  {
      if ( ($request->checkbox_delete) && (strtotime($request->delete_at) <= strtotime($request->publish_at)) ) {
        $arr["type"]="error";
        $arr["message"]="Delete at harus lebih besar dari publish at";
        return $arr;
      }
      $max_date = Carbon::now()->addSeconds($user->active_time);
			$dtpublish = Carbon::createFromFormat('Y-m-d H:i', $request->publish_at);
			if ( $max_date->lt($dtpublish) ) {
				$arr["type"]="error";
				$arr["message"]="Waktu schedule publish tidak boleh diluar dari waktu akun";
				return $arr;
      }
    }
    
    //post tidak boleh lebih dari 3 dalam 1 jam untuk tiap accountnya
    /*if ($request->hidden_method=="schedule")  {
      $dt1 = Carbon::createFromFormat('Y-m-d H:i', $request->publish_at)->subMinutes(30);
      $dt2 = Carbon::createFromFormat('Y-m-d H:i', $request->publish_at)->addMinutes(30);
    }
    if ($request->hidden_method=="now")  {
      $dt1 = Carbon::now()->subMinutes(30);
      $dt2 = Carbon::now()->addMinutes(30);
    }*/
      /*foreach ($request->accounts as $account){
        //check 1 hari cuman bole 9 post per akun 

        $check = Account::find($account);
        if (!is_null($check)) {
          if (!$check->is_started){
            $arr["type"] = "pending";
            return $arr;
          }
        }
        $check = Schedule::join("schedule_account","schedule_account.schedule_id","=","schedules.id")
                  ->where("user_id","=",$user->id)
                  ->where("account_id","=",$account)
                  // ->where("schedules.publish_at",">=",$dt1->toDateString()." ".$dt1->format('H').":00:00" )
                  // ->where("schedules.publish_at","<=",$dt1->toDateString()." ".$dt1->format('H').":59:59" )
                  ->whereBetween(DB::raw('DATE(schedules.publish_at)'), array($dt1, $dt2))
                  ->count();
        if ($check>3) {
          $arr["type"]="error";
          $arr["message"] = "Schedule Post maksimum 3 Post tiap jamnya";
          return $arr;
        }
      }*/
  
    //check klo publish_at lebih kecil dari now 
    $now = Carbon::now();
    if ($request->hidden_method=="schedule") {
      $dt1 = Carbon::createFromFormat('Y-m-d H:i', $request->publish_at);
      if ( $dt1->lt($now) ) {
        $arr["type"]="error";
        $arr["message"]="Input waktu publish tidak boleh lebih kecil dari waktu sekarang";
        return $arr;
      }
    }
    
    /* $dir = public_path('../vp/uploads/'.$user->username.'-'.$user->id); 
    if (!file_exists($dir)) {
      mkdir($dir,0741,true);
    }*/
    $dir = 'vp/uploads/'.$user->username.'-'.$user->id; 

    if ($request->id == 0) {
      // new schedule
      //copy file jadi file publish
      //slug file name 
      $last_hit = Schedule::where("user_id","=",$user->id)
                  ->where("slug","like","StoryFile%")
                  ->orderBy('id', 'desc')->first();
      if (is_null($last_hit)) {
        $slug = "StoryFile-00000";
      } else {
        $temp_arr1 = explode(".", $last_hit->slug );
        $temp_arr2 = explode("-", $temp_arr1[0] );
        $ctr = intval($temp_arr2[1]); $ctr++;
        $slug = "StoryFile-".str_pad($ctr, 5, "0", STR_PAD_LEFT);
      }
      
      $uploadedFile = $request->file('imgData');   
      $filename = $slug.'.'.$uploadedFile->getClientOriginalExtension();
      // $uploadedFile->move($dir, $filename);   
      $url = Storage::disk('s3')->putFile($dir, $request->file('imgData'),'public');

      //Storage::move($request->imguri, $dir.'/'.$filename.'mp4');

      $schedule = new Schedule;
      // $schedule->image = url('/../vp/uploads/'.$user->username.'-'.$user->id.'/'.$filename);
      $schedule->image = $url;
      $schedule->slug = $filename;
    } else {
      // edit schedule
      $schedule = Schedule::findOrFail($request->id);
      
      if($request->hasFile('imgData')){
        $uploadedFile = $request->file('imgData'); 

        $ext = explode('.', $request->slug);

        $filename = $ext[0].'.'.$uploadedFile->getClientOriginalExtension();

        if($ext[1]!=$uploadedFile->getClientOriginalExtension()){
          //delete file lama 
          File::delete($dir.'/'.$request->slug);
        }

        // $uploadedFile->move($dir, $filename);   
        if (Storage::disk('s3')->exists($schedule->image) ) {
          Storage::disk('s3')->delete($schedule->image);
        }
        $url = Storage::disk('s3')->putFile($dir, $request->file('imgData'),'public');

        //Storage::move($request->imguri, $dir.'/'.$request->slug.'mp4');
        
        // $schedule->image = url('/../vp/uploads/'.$user->username.'-'.$user->id.'/'.$filename);
        $schedule->image = $url;
        $schedule->slug = $filename;
      }
      
      $check_sa = ScheduleAccount::where("schedule_id","=",$schedule->id)
                  ->where("status","=",5)
                  ->get();
      foreach($check_sa as $data) {
        $update_sa = ScheduleAccount::find($data->id);
        $update_sa->status = 0;
        $update_sa->status_helper = 0;
        $update_sa->status_process = 0;
        $update_sa->save();
      }
      
    }

    $schedule->user_id = $user->id;
    //$schedule->description = $request->description;
    $schedule->description = '';
    $schedule->status = 1;
    if ($request->hidden_method=="schedule")  {
      $schedule->publish_at = strtotime($request->publish_at);
    }
    if ($request->hidden_method=="now")  {
      $schedule->publish_at = Carbon::now();
    }
    
		/* diremark karena ga ada delete di story
    if ($request->checkbox_delete) {
      $schedule->delete_at = strtotime($request->delete_at);
      $schedule->is_deleted = 1;
    } else {
      $schedule->is_deleted = 0;
    }
		*/

    if($request->type=='image'){
      $schedule->media_type = "photo";
    } else {
      $schedule->media_type = "video";  
    }
    
    $schedule->save();
    if ($request->has('accounts')) {
      //klo edit maka schedule account dihapus dulu, klo uda ada yang keposting maka akan ke schedule ulang
      if ($request->id <> 0) {
        $delete_sa = ScheduleAccount::where("schedule_id", "=", $request->id)
                  ->delete();
      }
      
      // Account
      // $account = array();
      // foreach($request->accounts as $data) {
        // $account[] = array(
                      // "id"=>$data->id,
                      // "publish_at"=>$schedule->publish_at,
                    // );
      // }
      $schedule->PutAccount($request->accounts);
      // $schedule->PutAccount($accounts);
    }
    $schedule->is_s3 = 1;
    $schedule->save();
    $check_sa = ScheduleAccount::where("schedule_id","=",$schedule->id)
                ->get();
    foreach($check_sa as $data) {
      $update_sa = ScheduleAccount::find($data->id);
      $update_sa->publish_at = $schedule->publish_at;
      $update_sa->save();
    }

    $arr["type"]="success";
    $arr["message"]="Process publish berhasil disimpan";
    return $arr;
  }
}
