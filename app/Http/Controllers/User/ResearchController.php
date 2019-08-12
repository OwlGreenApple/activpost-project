<?php

namespace Celebpost\Http\Controllers\User;

use Illuminate\Http\Request as req;
use Illuminate\Support\Facades\Crypt;

use Celebpost\Models\Account;
use Celebpost\Models\Image as ImageModel;
use Celebpost\Models\Template;
use Celebpost\Models\Proxies;

use File,Request,Auth;

use \InstagramAPI\Instagram;
use Exception, Image, App, Config;

class ResearchController extends Controller
{
	public function search_hashtags()
	{
		$user = Auth::user();
		$arr_proxys[] = [
			"proxy"=>"51.15.5.159",
			// "cred"=>"michaelsugih:TUhmQPS2erGtEe2",
			"port"=>"2016",
			"no"=>"1",
		];
		$arr_proxys[] = [
			"proxy"=>"51.15.5.159",
			// "cred"=>"michaelsugih:TUhmQPS2erGtEe2",
			"port"=>"2017",
			"no"=>"2",
		];
		$arr_proxys[] = [
			"proxy"=>"51.15.5.159",
			// "cred"=>"michaelsugih:TUhmQPS2erGtEe2",
			"port"=>"2018",
			"no"=>"3",
		];
		
		$arr_proxys[] = [
			"proxy"=>"51.15.5.159",
			// "cred"=>"michaelsugih:TUhmQPS2erGtEe2",
			"port"=>"2019",
			"no"=>"4",
		];
		$arr_proxys[] = [
			"proxy"=>"51.15.5.159",
			// "cred"=>"michaelsugih:TUhmQPS2erGtEe2",
			"port"=>"2020",
			"no"=>"5",
		];
		$arr_proxys[] = [
			"proxy"=>"51.15.5.159",
			// "cred"=>"michaelsugih:TUhmQPS2erGtEe2",
			"port"=>"2021",
			"no"=>"6",
		];
		
		$arr_proxys[] = [
			"proxy"=>"51.15.5.159",
			// "cred"=>"michaelsugih:TUhmQPS2erGtEe2",
			"port"=>"2022",
			"no"=>"7",
		];
		$arr_proxys[] = [
			"proxy"=>"51.15.5.159",
			// "cred"=>"michaelsugih:TUhmQPS2erGtEe2",
			"port"=>"2023",
			"no"=>"8",
		];
		$arr_proxys[] = [
			"proxy"=>"51.15.5.159",
			// "cred"=>"michaelsugih:TUhmQPS2erGtEe2",
			"port"=>"2024",
			"no"=>"9",
		];
		
		$arr_proxys[] = [
			"proxy"=>"51.15.5.159",
			// "cred"=>"michaelsugih:TUhmQPS2erGtEe2",
			"port"=>"2025",
			"no"=>"10",
		];
		
		$arr_proxy = $arr_proxys[array_rand($arr_proxys)];


		$searchString = Request::input("searchString");
		if (substr($searchString,0,1)=="#") {
			$searchString = str_replace("#", "", $searchString);
		}
		
		// $cookiefile = 'E:\cookies-grab.txt'; //??
		if(!File::exists(storage_path('ig-cookies/'.$user->id))) {
				File::makeDirectory(storage_path('ig-cookies/'.$user->id), 0755, true);
		}
		
		$cookiefile = base_path('storage/ig-cookies/'.$user->id.'/').'cookies-grab-hashtags.txt';
		
		$url = "https://www.instagram.com/web/search/topsearch/?context=blended&query=%23".$searchString;
		$c = curl_init();


		curl_setopt($c, CURLOPT_PROXY, $arr_proxy["proxy"]);
		curl_setopt($c, CURLOPT_PROXYPORT, $arr_proxy["port"]);
		// curl_setopt($c, CURLOPT_PROXYUSERPWD, $arr_proxy["cred"]);
		curl_setopt($c, CURLOPT_PROXYTYPE, 'HTTP');
		curl_setopt($c, CURLOPT_URL, $url);
		curl_setopt($c, CURLOPT_REFERER, $url);
		curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($c, CURLOPT_COOKIEFILE, $cookiefile);
		curl_setopt($c, CURLOPT_COOKIEJAR, $cookiefile);
		$page = curl_exec($c);
		curl_close($c);
		
		$arr = json_decode($page,true);
		if (count($arr)>0) {
			// var_dump(json_decode($page,true));exit;

			$arr_temp = $arr["hashtags"];
			
			if (Request::input("sortBy")==2) {
				$sortArray = array(); 

				foreach($arr_temp as $person){ 
						foreach($person["hashtag"] as $key=>$value){ 
								if(!isset($sortArray[$key])){ 
										$sortArray[$key] = array(); 
								} 
								$sortArray[$key][] = $value; 
						} 
				} 		
						
				$orderby = "media_count"; //change this to whatever key you want from the array 

				array_multisort($sortArray[$orderby],SORT_DESC,$arr_temp); 
			}
		
		
			$result = ""; $counter=1;
			foreach ($arr_temp as $data) {
				$result .= "<tr>";
				$hashtag = $data["hashtag"]["name"];
				$result .= '<td><button value="Add" class="btn btn-home button-add" data-hashtag="'.$hashtag.'"><span class="glyphicon glyphicon-plus"></span></button> &nbsp #'.$hashtag."</td>";
				$result .= "<td align=''>".number_format($data["hashtag"]["media_count"],0,'','.')."</td>";
				$result .= "<td><a href='".url("show-photo-hashtags")."/".$hashtag."' class='link-home'><input type='button' class='form-control btn-home' value='View'></td>";
				$result .= "</tr>";
				if ($counter == 20) { break; }
				$counter += 1 ;
			}
			if (count($arr["hashtags"])==0) {
				$result = '<tr><td colspan="3"> Tidak ada data </td></tr>';
			}

			
			unlink($cookiefile);
		} else {
			//error proxy
			File::delete(storage_path('ig-cookies/'.$user->id));
			$arr["type"]= "error";
			$arr["message"]= "Silahkan coba tekan button search lagi";
			return $arr;
		}
			
		
		$arr["type"]="success";
		$arr["message"]="";
		$arr["result"]=$result;
		return $arr;
	}

	public function show_photo_hashtags($input_hashtags)
	{
		$user = Auth::user();
		if (!$user->is_confirmed) {
			return "Please Confirm Your Email";
		}
		
		$account = Account::where("user_id",$user->id)
								->where("is_active",1)
								->where("is_error",0)
								->first();
		if (is_null($account)) {
			return "Input account terlebih dahulu";
		}
		
    // Generate a random rank token.
    $rankToken = \InstagramAPI\Signatures::generateUUID();
		
		$media_count = 0;

		$proxy = Proxies::find($account->proxy_id);
		// Decrypt
		$decrypted_string = Crypt::decrypt($account->password);
		$pieces = explode(" ~space~ ", $decrypted_string);
		$password = $pieces[0];
		try {
			$i = new Instagram(false,false,[
				"storage"       => "mysql",
				"dbhost"       => Config::get('database.connections.mysql_celebgramme.host'),
				"dbname"   => Config::get('database.connections.mysql_celebgramme.database'),
				"dbusername"   => Config::get('database.connections.mysql_celebgramme.username'),
				"dbpassword"   => Config::get('database.connections.mysql_celebgramme.password'),
			]);
			
			// Check Login
			if (!is_null($proxy)) {
				if($proxy->cred==""){
					$i->setProxy("http://".$proxy->proxy.":".$proxy->port);
				}
				else {
					$i->setProxy("http://".$proxy->cred."@".$proxy->proxy.":".$proxy->port);
				}
			}
			// $i->setUser($account->username, $password);
			$i->login($account->username, $password, 300);
			$search_tag_response = $i->hashtag->search($input_hashtags);
			foreach ($search_tag_response->getResults() as $data) {
				if ($data->getName()==$input_hashtags) {
					$media_count = $data->getMediaCount();
				}
  		}
			$tag_feed_response = $i->hashtag->getFeed($input_hashtags,$rankToken);
		} catch (Exception $e) {
			return $e->getMessage();
		}



		$result = serialize($tag_feed_response->getItems()); 
		$end_cursor = $tag_feed_response->getNextMaxId();
		
		return view('user.search-hashtags.show-photo-hashtags')
		->with(array(
			'user'=>$user,
			'result'=>$result,
			'input_hashtags'=>$input_hashtags,
			'media_count'=>$media_count,
			'end_cursor'=>$end_cursor,
		));
	}
	
	public function more_photo()
	{
		$user = Auth::user();


		$account = Account::where("user_id",$user->id)
								->where("is_active",1)
								->where("is_error",0)
								->first();
		if (is_null($account)) {
			return "Input account terlebih dahulu";
		}
		
    // Generate a random rank token.
    $rankToken = \InstagramAPI\Signatures::generateUUID();
		$media_count = 0;

		$proxy = Proxies::find($account->proxy_id);
		// Decrypt
		$decrypted_string = Crypt::decrypt($account->password);
		$pieces = explode(" ~space~ ", $decrypted_string);
		$password = $pieces[0];
		try {
			$i = new Instagram(false,false,[
				"storage"       => "mysql",
				"dbhost"       => Config::get('database.connections.mysql_celebgramme.host'),
				"dbname"   => Config::get('database.connections.mysql_celebgramme.database'),
				"dbusername"   => Config::get('database.connections.mysql_celebgramme.username'),
				"dbpassword"   => Config::get('database.connections.mysql_celebgramme.password'),
			]);
			
			// Check Login
			if (!is_null($proxy)) {
				if($proxy->cred==""){
					$i->setProxy("http://".$proxy->proxy.":".$proxy->port);
				}
				else {
					$i->setProxy("http://".$proxy->cred."@".$proxy->proxy.":".$proxy->port);
				}
			}
			// $i->setUser($account->username, $password);
			$i->login($account->username, $password, 300);
			$tag_feed_response = $i->hashtag->getFeed(Request::input("inputHashtags"),$rankToken,Request::input("endCursor"));
		} catch (Exception $e) {
			return $e->getMessage();
		}



		
		// var_dump(json_decode($page,true));exit;
		$view = "";
		foreach ($tag_feed_response->getItems() as $res) {
			$username = "";
			if (!is_null($res->getUser())) {
				$username = $res->getUser()->getUsername();
			}
			
			$url = "";
			if (!is_null($res->getImageVersions2())) {
				$url =$res->getImageVersions2()->getCandidates()[0]->getUrl();
			}
			
			$caption = "";
			if (!is_null($res->getCaption())){
				$caption = $res->getCaption()->getText();
			}			
			
			$crypt_url = Crypt::encrypt($url);
			$view.='						
			<div class="col-md-4 col-xs-12 container-fluid same-height container-content" style="margin-bottom:28px;" data-container="image">
					<div style="background-image:url('.$url.');" class="same-height image-div">
						<div class="description-bar hide">
							<a href="https://instagram.com/'.$username.'" target="_blank" class="col-md-12 col-xs-12 link-home link-action-right"> @ : '.$username.'</a>
							<label class="col-md-12 col-xs-12 link-home link-action-right" style="margin-top:5px;"><span class="glyphicon glyphicon-heart"></span> : '.$res->getLikeCount().'</label>
							<label class="col-md-12 col-xs-12 link-home link-action-right"><span class="glyphicon glyphicon glyphicon-comment"></span> : '.$res->getCommentCount().'</label>
						</div>
						<div class="action-image hide" data-url="'.$crypt_url.'" data-owner="'.$username.'" data-caption="'.$caption.'" >
							<a href="" class="col-md-4 col-xs-4 link-home link-action-left link-edit"><span class="glyphicon glyphicon-pencil"></span> Edit</a>
							<a href="" class="col-md-4 col-xs-4 link-home link-action-center link-download" data-toggle="modal" data-target="#confirm-download"><span class="glyphicon glyphicon-download-alt"></span> Save</a>
							<a href="" class="col-md-4 col-xs-4 link-home link-action-right link-repost"><span class="glyphicon glyphicon-share-alt"></span> Repost</a>
						</div>
					</div>
					<div class="same-height caption-div" style="display:none;">
						<textarea style="width:100%;" class="same-height">'.$caption.'</textarea>
						<div class="action-image hide">
							<input type="text" class="fl input-name-template" placeholder="Name Templates"> 
							<a href="" class="col-md-5 col-xs-5 link-home link-action-center link-download-template" data-toggle="modal" data-target="#confirm-save-template"><span class="glyphicon glyphicon-download-alt"></span> Save Caption</a>
						</div>
					</div> 
					
			</div>
';
			
		}
		
		$end_cursor = $tag_feed_response->getNextMaxId();
		// echo $end_cursor;
		
		// unlink($cookiefile);
		
		$arr["type"]="success";
		$arr["view"]=$view;
		$arr["endCursor"]=$end_cursor;
		return $arr;
		
	}

	public function show_photo_hashtags_old($input_hashtags)
	{
		$user = Auth::user();
		if (!$user->is_confirmed) {
			return "Please Confirm Your Email";
		}

		if(!File::exists(storage_path('ig-cookies/'.$user->id))) {
				File::makeDirectory(storage_path('ig-cookies/'.$user->id), 0755, true);
		}
		
		$cookiefile = base_path('storage/ig-cookies/'.$user->id.'/').'cookies-celebpost-temp.txt';

		if ($user->is_member_rico==0) {
			$url = "https://activfans.com/dashboard/get-photo-hashtags/".$input_hashtags;
		}
		else {
			$url = "https://activfans.com/amelia/get-photo-hashtags/".$input_hashtags;
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
	
		// var_dump(json_decode($page,true));exit;
		$result = $arr_res["result"]; 
		$media_count = $arr_res["media_count"];
		$end_cursor = $arr_res["end_cursor"];
		
		
		
		return view('user.search-hashtags.show-photo-hashtags')
		->with(array(
			'user'=>$user,
			'result'=>$result,
			'input_hashtags'=>$input_hashtags,
			'media_count'=>$media_count,
			'end_cursor'=>$end_cursor,
		));
	}
	
	public function more_photo_old()
	{
		$user = Auth::user();
		if(!File::exists(storage_path('ig-cookies/'.$user->id))) {
				File::makeDirectory(storage_path('ig-cookies/'.$user->id), 0755, true);
		}
		
		$cookiefile = base_path('storage/ig-cookies/'.$user->id.'/').'cookies-celebpost-temp.txt';

		if ($user->is_member_rico==0) {
			$url = "https://activfans.com/dashboard/get-photo-hashtags/".Request::input("inputHashtags").'/'.Request::input("endCursor");
		}
		else {
			$url = "https://activfans.com/amelia/get-photo-hashtags/".Request::input("inputHashtags").'/'.Request::input("endCursor");
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
	
		// var_dump(json_decode($page,true));exit;
		$view = "";
		foreach ($arr_res["result"] as $data) {
			$crypt_url = Crypt::encrypt($data["url"]);
			$data_owner = $data["owner"];
			$data_caption = $data["caption"];
			$view.='						
			<div class="col-md-4 col-xs-12 container-fluid same-height container-content" style="margin-bottom:28px;" data-container="image">
					<div style="background-image:url('.$data["url"].');" class="same-height image-div">
						<div class="description-bar hide">
							<a href="https://instagram.com/'.$data['owner'].'" target="_blank" class="col-md-12 col-xs-12 link-home link-action-right"> @ : '.$data['owner'].'</a>
							<label class="col-md-12 col-xs-12 link-home link-action-right" style="margin-top:5px;"><span class="glyphicon glyphicon-heart"></span> : '.$data['likes_count'].'</label>
							<label class="col-md-12 col-xs-12 link-home link-action-right"><span class="glyphicon glyphicon glyphicon-comment"></span> : '.$data['comments_count'].'</label>
						</div>
						<div class="action-image hide" data-url="'.$crypt_url.'" data-owner="'.$data_owner.'" data-caption="'.$data_caption.'" >
							<a href="" class="col-md-4 col-xs-4 link-home link-action-left link-edit"><span class="glyphicon glyphicon-pencil"></span> Edit</a>
							<a href="" class="col-md-4 col-xs-4 link-home link-action-center link-download" data-toggle="modal" data-target="#confirm-download"><span class="glyphicon glyphicon-download-alt"></span> Save</a>
							<a href="" class="col-md-4 col-xs-4 link-home link-action-right"><span class="glyphicon glyphicon-share-alt"></span> Repost</a>
						</div>
					</div>
					<div class="same-height caption-div" style="display:none;">
						<textarea style="width:100%;" class="same-height">'.$data["caption"].'</textarea>
						<div class="action-image hide">
							<input type="text" class="fl input-name-template" placeholder="Name Templates"> 
							<a href="" class="col-md-5 col-xs-5 link-home link-action-center link-download-template" data-toggle="modal" data-target="#confirm-save-template"><span class="glyphicon glyphicon-download-alt"></span> Save Caption</a>
						</div>
					</div> 
					
			</div>
';
			
		}
		$end_cursor = $arr_res["end_cursor"];
		// echo $end_cursor;
		
		// unlink($cookiefile);
		
		$arr["type"]="success";
		$arr["view"]=$view;
		$arr["endCursor"]=$end_cursor;
		return $arr;
		
	}

	public function image_editor_index(req $request)
	{
		$user = Auth::user();
		$url = "";
		if ($request->session()->has('url')) {
			$url = $request->session()->get('url');
		}
		return view('user.search-hashtags.image-editor')->with(array(
			'user'=>$user,
			'url'=>$url,
		));		
	}

	public function save_image_IG()
	{
		$user = Auth::user();
		//check jumlah image
		$count = ImageModel::where("user_id","=",$user->id)->count();
		if ($count>50) {
			$arr["type"] = "error";
			$arr["message"] = "File Images tidak boleh lebih dari 50";
			return $arr;
		}
		
		
		// *ga boleh di potong urlnya yang ? harus dipake
		//*$pieces = explode("?", Crypt::decrypt(Request::input("inputUrl")));
		//*$path = $pieces[0];
		$pieces = explode("?", Crypt::decrypt(Request::input("inputUrl")));
		$path = $pieces[0];//cmn buat dapat basename
		$filename = basename($path);
		$path = Crypt::decrypt(Request::input("inputUrl"));
		
		//check valid file
		$arr_size = getimagesize($path);
		// if ( ($arr_size[0]>1400) && ($arr_size[1]>1400) ) {
		if ( ( ($arr_size[0]>1090) && ($arr_size[1]>1090) ) || ( ($arr_size[0]>1300) && ($arr_size[1]>800) ) || ( ($arr_size[0]>800) && ($arr_size[1]>1300) ) ) {
			$arr["type"] = "error";
			$arr["message"] = "Ukuran maximum width = 1080px & height = 1080px";
			return $arr;
		}
		if ( ($arr_size[0]<640) || ($arr_size[1]<640) ) {
			$arr["type"] = "error";
			$arr["message"] = "Ukuran file Minimum 640px X 640px";
			return $arr;
		}
		if ( ($arr_size[0]>1080) || ($arr_size[1]>1350) ) {
			$arr["type"] = "error";
			$arr["message"] = "Ukuran maximum width = 1080px & height = 1350px";
			return $arr;
		}
		$ratio_img = $arr_size[0] / $arr_size[1];
		if ( ($ratio_img < 0.8) || ($ratio_img>1.91) ) {
			$arr["type"] = "error";
			$arr["message"] = "Ratio image (Width / Height) Harus berkisar antara 0.8 sampai 1.91. Ratio image anda ".$ratio_img;
			return $arr;
		}

		// $dir = public_path('images/users/'.$user->username.'-'.$user->id); 
		$dir = public_path('../vp/users/'.$user->username.'-'.$user->id); 
		if (!file_exists($dir)) {
			mkdir($dir,0741,true);
		}
		

		Image::make($path)->save($dir."/".$filename);
		$imageM = new ImageModel;
		$imageM->user_id = $user->id;
		$imageM->file = $filename;
		$imageM->is_use_caption = 1;
		
		// $clean_text = $this->removeEmoji(Request::input("inputCaption"));
		// $imageM->caption = $clean_text;
		$imageM->caption = Request::input("inputCaption");
		
		$imageM->owner_post = Request::input("inputOwner");
		$imageM->save();
		
		$arr["type"] = "success";
		$arr["message"] = "Data berhasil disimpan di folder saved image";
		return $arr;
	}

	
	/*
	*Hashtags Research
	*/
	public function submit_hashtags()
	{
		$arr["type"] = "success";
		$arr["message"] = "Data berhasil di edit";
		$arr["typeSubmit"] = "update";
		$user = Auth::user();
		
		if (count(explode("#",Request::input("hashtagsBasket"))) - 1 > 30 ) {
			$arr["type"] = "error";
			$arr["message"] = "hashtags tidak boleh lebih dari 30";
			return $arr;
		}
		
		if (Request::input("hashtagsFolder")==""){
			$arr["type"] = "error";
			$arr["message"] = "Nama Hashtags Collection tidak boleh kosong";
			return $arr;
		}
		
		$clean_text = $this->removeEmoji(Request::input("hashtagsBasket"));
		
		$collection = Template::where("user_id","=",$user->id)
										->where("type","=","hashtags")
										->where("name","=",Request::input("hashtagsFolder"))
										->first();
		if (is_null($collection)){
			$collection = new Template;
			$collection->name = Request::input("hashtagsFolder");
			$collection->type = "hashtags";
			$collection->user_id = $user->id;
			$arr["message"] = "Data berhasil di add";
			$arr["typeSubmit"] = "insert";
		}
		$collection->value = $clean_text;
		$collection->save();
										
		return $arr;
	}
	
	public function delete_hashtags()
	{
		$user = Auth::user();
		$arr["type"] = "success";
		$arr["message"] = "Data berhasil dihapus";
		
		$collection = Template::where("user_id","=",$user->id)
										->where("type","=","hashtags")
										->where("name","=",Request::input("hashtagsFolder"))
										->delete();
										
		return $arr;
	}
	
	
	public function show_photo_hashtags_backup($input_hashtags)
	{
    $user = Auth::user();

		$cookiefile = 'E:\cookiess.txt';



		$url = "https://www.instagram.com/explore/tags/".$input_hashtags."/?__a=1";
		$c = curl_init();

		// curl_setopt($c, CURLOPT_PROXY, $proxy);
		// curl_setopt($c, CURLOPT_PROXYPORT, $port);
		// curl_setopt($c, CURLOPT_PROXYUSERPWD, $cred);
		// curl_setopt($c, CURLOPT_PROXYTYPE, 'HTTP');
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
	
		var_dump(json_decode($page,true));exit;
		$result = array(); 
		foreach ($arr_res["tag"]["media"]["nodes"] as $data) {
/*			
			//scrape media
			echo "media id : ".$data["id"]."<br>"; 
			echo "user id : ".$data["owner"]["id"]."<br>"; 
			echo "code : ".$data["code"]."<br>"; 
			echo "URL : ".$data["display_src"]."<br>"; 
			// print_r($data); 
			echo "<br><br>";
*/			


			$result[] = [
				"url"=>$data["display_src"],
				"code"=>$data["code"],
				"media_id"=>$data["id"],
				"caption"=>$data["caption"],
				"owner"=>"",
			]; 
		}
		// dd($result);
		$media_count = number_format($arr_res["tag"]["media"]["count"],0,"",".");
		$end_cursor = $arr_res["tag"]["media"]["page_info"]["end_cursor"];
		
		// unlink($cookiefile);
		
		return view('user.search-hashtags.show-photo-hashtags')
		->with(array(
			'user'=>$user,
			'result'=>$result,
			'input_hashtags'=>$input_hashtags,
			'media_count'=>$media_count,
			'end_cursor'=>$end_cursor,
		));
	}
	
	
	public function more_photo_backup()
	{
		$cookiefile = 'E:\cookiess.txt';
		
		$cotext = @file_get_contents($cookiefile);
		
		preg_match('/(sessionid)\\s+(IGSC[^\\s]+)/', $cotext, $id);
		if (count($id)>0){
			$session_id = $id[2];
		}		
		preg_match('/(mid)\s(\S*)/', $cotext, $id);
		if (count($id)>0){
			$mid = $id[2];
		}		
		preg_match('/(csrftoken)\s(\S*)/', $cotext, $id);
		if (count($id)>0){
			$csrftoken = $id[2];
		}		
		$array_cookie = array(
			// 'cookie:mid='.$mid.'; fbm_124024574287414=base_domain=.instagram.com; sessionid='.$session_id.'; s_network=; ds_user_id='.$ds_user_id,
			'cookie:mid='.$mid.'; fbm_124024574287414=base_domain=.instagram.com; sessionid='.$session_id.'; s_network=; ',
			'origin:https://www.instagram.com',
			'user-agent:Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.89 Safari/537.36',
			'x-csrftoken:'.$csrftoken,
			'x-instagram-ajax:1',
			'x-requested-with:XMLHttpRequest',
		);
		
		
		
		$fields = array(
					'format' => 'json',
					'q' => "ig_hashtag(".Request::input("inputHashtags").") { media.after(".Request::input("endCursor").", 12) {
										count,
										nodes {
											caption,
											code,
											comments {
												count
											},
											comments_disabled,
											date,
											dimensions {
												height,
												width
											},
											display_src,
											id,
											is_video,
											likes {
												count
											},
											owner {
												id
											},
											thumbnail_src,
											video_views
										},
										page_info{
											end_cursor
										}
									}
								}",
					'ref' => 'tags::show',
		);
		// url-ify the data for the POST
		$field_string = urldecode(http_build_query($fields));
		$len = strlen($field_string);

		
		$array_cookie[] = 'content-type:application/x-www-form-urlencoded; charset=UTF-8';
		$array_cookie[] = 'content-length:'.strlen($field_string);
		
		
		$url = "https://www.instagram.com/query/";
		$c = curl_init();

		curl_setopt($c,CURLOPT_HTTPHEADER, $array_cookie );

		// curl_setopt($c, CURLOPT_PROXY, $proxy);
		// curl_setopt($c, CURLOPT_PROXYPORT, $port);
		// curl_setopt($c, CURLOPT_PROXYUSERPWD, $cred);
		// curl_setopt($c, CURLOPT_PROXYTYPE, 'HTTP');
		curl_setopt($c, CURLOPT_URL, $url);
		curl_setopt($c, CURLOPT_REFERER, $url);
		curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($c, CURLOPT_POSTFIELDS, $field_string);
		curl_setopt($c, CURLOPT_COOKIEFILE, $cookiefile);
		curl_setopt($c, CURLOPT_COOKIEJAR, $cookiefile);
		curl_setopt($c, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($c, CURLOPT_POST, true);
		$page = curl_exec($c);
		curl_close($c);

		$arr_res = json_decode($page,true);
	
		// var_dump(json_decode($page,true));exit;
		$arr_res = json_decode($page,true);
		$view = "";
		foreach ($arr_res["media"]["nodes"] as $data) {
			$crypt_url = Crypt::encrypt($data["display_src"]);
			$view.='						
			<div class="col-md-4 col-xs-12 container-fluid same-height container-content" style="margin-bottom:28px;" data-container="image">
					<div style="background-image:url('.$data["display_src"].');" class="same-height image-div">
						<div class="action-image hide">
							<a href="" data-url="'.$crypt_url.'" class="col-md-4 col-xs-4 link-home link-action-left link-edit"><span class="glyphicon glyphicon-pencil"></span> Edit</a>
							<a href="" data-url="'.$crypt_url.'" class="col-md-4 col-xs-4 link-home link-action-center link-download" data-toggle="modal" data-target="#confirm-download"><span class="glyphicon glyphicon-download-alt"></span> Save</a>
							<a href="" class="col-md-4 col-xs-4 link-home link-action-right"><span class="glyphicon glyphicon-share-alt"></span> Repost</a>
						</div>
					</div>
					<div class="same-height caption-div" style="display:none;">
						<textarea style="width:100%;" class="same-height">'.$data["caption"].'</textarea>
						<div class="action-image hide">
							<input type="text" class="fl input-name-template" placeholder="Name Templates"> 
							<a href="" class="col-md-5 col-xs-5 link-home link-action-center link-download-template" data-toggle="modal" data-target="#confirm-save-template"><span class="glyphicon glyphicon-download-alt"></span> Save Caption</a>
						</div>
					</div> 
					
			</div>
';
			
		}
		$end_cursor = $arr_res["media"]["page_info"]["end_cursor"];
		// echo $end_cursor;
		
		// unlink($cookiefile);
		
		$arr["type"]="success";
		$arr["view"]=$view;
		$arr["endCursor"]=$end_cursor;
		return $arr;
		
	}

	public static function removeEmoji($text) {

    $clean_text = $text;

/*    // Match Emoticons
    $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
    $clean_text = preg_replace($regexEmoticons, '', $text);

    // Match Miscellaneous Symbols and Pictographs
    $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
    $clean_text = preg_replace($regexSymbols, '', $clean_text);

    // Match Transport And Map Symbols
    $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
    $clean_text = preg_replace($regexTransport, '', $clean_text);

    // Match Miscellaneous Symbols
    $regexMisc = '/[\x{2600}-\x{26FF}]/u';
    $clean_text = preg_replace($regexMisc, '', $clean_text);

    // Match Dingbats
    $regexDingbats = '/[\x{2700}-\x{27BF}]/u';
    $clean_text = preg_replace($regexDingbats, '', $clean_text);
    
		// Match IM TRYING
    $regexDingbats = '/[\x{0000}-\x{FFFF}]/u';
    $clean_text = preg_replace($regexDingbats, '', $clean_text);*/
		
		// $clean_text = preg_replace("/[^A-Za-z0-9 # \n]/", '', $text);		
		// $clean_text = preg_replace("/[^A-Za-z0-9\d\w\D\W \n]/", '', $text);		
		// $clean_text = preg_replace("/[A-Za-z0-9_~\-!@#\$%\^&\*\(\)]/", '', $text);		
		$clean_text =  preg_replace('/([0-9#][\x{20E3}])|[\x{00ae}\x{00a9}\x{203C}\x{2047}\x{2048}\x{2049}\x{3030}\x{303D}\x{2139}\x{2122}\x{3297}\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/u', '', $text);

		
    return $clean_text;
	}
}
