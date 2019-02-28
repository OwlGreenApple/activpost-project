<?php
namespace Celebpost\Http\Controllers\User;
/*Models*/
use Celebpost\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request as req;
use Celebpost\Http\Controllers\User\ResearchController;
use Illuminate\Support\Facades\Storage;
use Celebpost\Models\Image as ImageModel;
use View, Input, Mail, Request, App, Hash, Validator, Carbon, Crypt, Redirect, Image;
class ImageController extends Controller
{
  
	
	public function save_image()
	{
		$user = Auth::user();
		//check jumlah image
		$count = ImageModel::where("user_id","=",$user->id)->count();
		if ($count>50) {
			$arr["type"] = "error";
			$arr["message"] = "File Images tidak boleh lebih dari 50";
			return $arr;
		}
		
		/*if (Request::input("decryptData") == "1") {
			$file = $decode_data;
		} else {
			$file = Request::input("imgData");
		}
		$rules = array('file' => 'required|max:2048'); 
		$validator = Validator::make(array('file'=> $file), $rules);
		if(!$validator->passes()){
			$arr["type"] = "error";
			$arr["message"] = "Ukuran file Maksimum 2MB";
			return $arr;
		}*/
		if (Request::input("decryptData") == "1") {
		} else {		
			$arr_size = getimagesize(Request::input("imgData"));
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
		}
		
		//slug file name 
		$last_hit = ImageModel::where("user_id","=",$user->id)
								->where("file","like","slug%")
								->orderBy('id', 'desc')->first();
		if (is_null($last_hit)) {
			$slug = "slug-00000";
		} else {
			$temp_arr1 = explode(".", $last_hit->file );
			$temp_arr2 = explode("-", $temp_arr1[0] );
			$ctr = intval($temp_arr2[1]); $ctr++;
			$slug = "slug-".str_pad($ctr, 5, "0", STR_PAD_LEFT);
		}
		
		$filename = $slug;
		// $dir = public_path('images/users/'.$user->username.'-'.$user->id); 
		/*$dir = public_path('../vp/users/'.$user->username.'-'.$user->id); 
		if (!file_exists($dir)) {
			mkdir($dir,0741,true);
		}*/
    $dir = 'vp/users/'.$user->username.'-'.$user->id; 
		//check imgData url or encode image data
		// if ( (Request::input("captionData")=="") && (Request::input("ownerData")=="") ) {
		if (Request::input("decryptData") == "1") {
      // *ga boleh di potong urlnya yang ? harus dipake
			// *$pieces = explode("?", Crypt::decrypt(Request::input("imgData")));
			// *$decode_data = $pieces[0];
			
			// Image::make($decode_data)->save($dir."/".$filename.".jpg");
			// file_put_contents($dir."/".$filename.".jpg",$decode_data);
			// *$url = $decode_data;
			$url = Crypt::decrypt(Request::input("imgData"));
			$img = $dir."/".$filename.".jpg";
			// file_put_contents($img, file_get_contents($url));			
      // $urls3 = Storage::disk('s3')->putFile($dir, file_get_contents($url),'public');
      $urls3 = Storage::disk('s3')->put($dir, file_get_contents($url),'public');
		} else if (Request::input("decryptData") == "0"){
			// Image::make(Request::input("imgData"))->save($dir."/".$filename.".jpg");
      //decode base64 string
      list($baseType, $image) = explode(';', Request::input("imgData"));
      list(, $image) = explode(',', $image);
      $image = base64_decode($image);
      // $urls3 = Storage::disk('s3')->putFile($dir, $image,'public');
      $urls3 = Storage::disk('s3')->put($dir."/".$filename.".jpg", $image,'public');
		}
		$imageM = new ImageModel;
		$imageM->is_schedule = 0;
		$imageM->user_id = $user->id;
		// $imageM->file = $filename.".jpg";
		// $imageM->file = $urls3;
    $imageM->file = $dir."/".$filename.".jpg";
    $imageM->is_s3 = 1;
		if ( (Request::input("captionData")=="") && (Request::input("ownerData")=="") ) {
			$imageM->is_use_caption = 0;
			$imageM->caption = "";
			$imageM->owner_post = "";
			$imageM->save();
		} else {
			$imageM->is_use_caption = 1;
			$val = mb_check_encoding( Request::input("captionData"), 'UTF-8') ?  Request::input("captionData") : utf8_encode( Request::input("captionData"));
			$val = ResearchController::removeEmoji($val);
			$imageM->caption =$val;
			$imageM->owner_post = Request::input("ownerData");
			$imageM->save();
			$arr["imageId"] = $imageM->id;
		}
		
		$arr["type"] = "success";
		return $arr;
	}
	
	public function delete_image()
	{
		$arr["type"] = "success";
		$arr["message"] = "Image berhasil dihapus";
		
		$user = Auth::user();
		if (Request::input("inputId")=="all") { 
      $dir = public_path('../vp/users/'.$user->username.'-'.$user->id); 
			$images = ImageModel::where("user_id","=",$user->id)
                ->where("is_s3",0)
								->get();
			foreach ($images as $image) {
				// $dir = public_path('images/users/'.$user->username.'-'.$user->id); 
				unlink($dir."/".$image->file);
				$image->delete();
			}
			$images = ImageModel::where("user_id","=",$user->id)
                ->where("is_s3",1)
								->get();
			foreach ($images as $image) {
        $image->delete();
        Storage::disk('s3')->delete($image->file);
      }
		} 
		else {
			$image = ImageModel::where("user_id","=",$user->id)
								->where("id","=",Request::input("inputId"))
								->first();
			if (!is_null($image)) {
        if (!$image->is_s3) {
          // $dir = public_path('images/users/'.$user->username.'-'.$user->id); 
          $dir = public_path('../vp/users/'.$user->username.'-'.$user->id); 
          unlink($dir."/".$image->file);
        } else {
          Storage::disk('s3')->delete($image->file);
        }
        $image->delete();
			} else {
				$arr["type"] = "error";
				$arr["message"] = "Image tidak berhasil dihapus";
			}
		}
		return $arr;
	}
	public function save_temp_image(req $request)
	{
		$user = Auth::user();
		// session(['key' => Request::input("url")]);
		
		$pieces = explode("?", Crypt::decrypt(Request::input("url")));
		$path = $pieces[0];
		$filename = "temp.jpg";
		// $dir = public_path('images/users/'.$user->username.'-'.$user->id); 
		/*$dir = public_path('../vp/users/'.$user->username.'-'.$user->id); 
		if (!file_exists($dir)) {
			mkdir($dir,0741,true);
		}*/
    $dir = 'vp/users/'.$user->username.'-'.$user->id; 
		// Image::make($path)->save($dir."/".$filename);
    $urls3 = Storage::disk('s3')->putFile($dir, $path,'public');
		$imageM = ImageModel::where("file","=",$filename)
							->where("user_id","=",$user->id)
							->first();
		if(is_null($imageM)) {
			$imageM = new ImageModel;
		}
		$imageM->user_id = $user->id;
		// $imageM->file = $filename;
		$imageM->file = $urls3;
		$imageM->is_use_caption = 0;
		$imageM->caption = "";
		$imageM->owner_post = "";
    $imageM->is_s3 = 1;
		$imageM->save();
		
		// $request->session()->put('url', 'images/users/'.$user->username.'-'.$user->id."/temp.jpg");
		$request->session()->put('url', '../vp/users/'.$user->username.'-'.$user->id."/temp.jpg");
		
		$arr["type"] = "success";
		$arr["message"] = "Data berhasil disimpan";
		// $arr["url"] = asset('images/users/'.$user->username.'-'.$user->id."/temp.jpg");
		$arr["url"] = asset('../vp/users/'.$user->username.'-'.$user->id."/temp.jpg");
		return $arr;
		
	}
	//ngga perlu disave ke s3, karena cuman single file
	public function save_image_schedule()
	{
		$user = Auth::user();
		
		$filename = "temp.jpg";
		// $dir = public_path('images/uploads/'.$user->username.'-'.$user->id); 
		$dir = public_path('../vp/uploads/'.$user->username.'-'.$user->id); 
		if (!file_exists($dir)) {
			mkdir($dir,0741,true);
		}
		$arr_size = getimagesize(Request::input("imgData"));
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
		
		Image::make(Request::input("imgData"))->save($dir."/".$filename);
		
		$arr["type"] = "success";
		$arr["message"] = "Data berhasil disimpan";
		// $arr["url"] = asset('images/uploads/'.$user->username.'-'.$user->id."/temp.jpg");
		$arr["url"] = asset('../vp/uploads/'.$user->username.'-'.$user->id."/temp.jpg");
		return $arr;
	}		
	public function multiple_upload()
	{
		$user = Auth::user();
    // getting all of the post data
    $files = Input::file('images');
    // Making counting of uploaded images
    $file_count = count($files);
		
		//check jumlah image
		$count = ImageModel::where("user_id","=",$user->id)->count();
		if ($count+$file_count>50) {
			return Redirect::to('saved-images')->with(array("error"=>"File Images tidak boleh lebih dari 50"));
		}
		// return $file_count;
		
    $uploadcount = 0;
		$error_message = "";
    foreach($files as $file) {
      // $rules = array('file' => 'required|mimes:png,gif,jpeg,jpg|dimensions:ratio>=0.8|dimensions:ratio<=1.9|dimensions:min_width=640,max_width=1080,min_height=640,max_height=1350'); 
			$is_error = false;
			$arr_size = getimagesize($file);
			// if ( ($arr_size[0]>1400) && ($arr_size[1]>1400) ) {
			if ( ( ($arr_size[0]>1090) && ($arr_size[1]>1090) ) || ( ($arr_size[0]>1300) && ($arr_size[1]>800) ) || ( ($arr_size[0]>800) && ($arr_size[1]>1300) ) ) {
				$is_error = true;
				$error_message .= $file->getClientOriginalName()." -> Ukuran maximum width = 1080px & height = 1080px;";
			}
			if ( ($arr_size[0]<640) || ($arr_size[1]<640) ) {
				$is_error = true;
				$error_message .= $file->getClientOriginalName()." -> Ukuran file Minimum 640px X 640px;";
			}
			if ( ($arr_size[0]>1080) || ($arr_size[1]>1350) ) {
				$is_error = true;
				$error_message .= $file->getClientOriginalName()." -> Ukuran maximum width = 1080px & height = 1350px;";
			}
			$ratio_img = $arr_size[0] / $arr_size[1];
			if ( ($ratio_img < 0.8) || ($ratio_img>1.91) ) {
				$is_error = true;
 			  $error_message .= $file->getClientOriginalName()." -> Ratio image (Width / Height) Harus berkisar antara 0.8 sampai 1.91. Ratio image anda ".$ratio_img.";";
			}
			
			
      /*$validator = Validator::make(array('file'=> $file), $rules);
      if($validator->passes()){*/
		  if (!$is_error) {
        // $destinationPath = 'uploads';
        // $filename = $file->getClientOriginalName();
        $uploadcount ++;
				
				//slug file name 
				$last_hit = ImageModel::where("user_id","=",$user->id)
										->where("file","like","slug%")
										->orderBy('id', 'desc')->first();
				if (is_null($last_hit)) {
					$slug = "slug-00000";
				} else {
					$temp_arr1 = explode(".", $last_hit->file );
					$temp_arr2 = explode("-", $temp_arr1[0] );
					$ctr = intval($temp_arr2[1]); $ctr++;
					$slug = "slug-".str_pad($ctr, 5, "0", STR_PAD_LEFT);
				}
				
				$filename = $slug;
				// $dir = public_path('images/users/'.$user->username.'-'.$user->id); 
				/*$dir = public_path('../vp/users/'.$user->username.'-'.$user->id); 
				if (!file_exists($dir)) {
					mkdir($dir,0741,true);
				}*/
        $dir = 'vp/users/'.$user->username.'-'.$user->id; 
				
				// Image::make(Request::input("imgData"))->save($dir."/".$filename.".jpg");
        // $upload_success = $file->move($dir, $filename.".".$file->getClientOriginalExtension());
        $urls3 = Storage::disk('s3')->putFile($dir, $file,'public');
				
				$imageM = new ImageModel;
				$imageM->user_id = $user->id;
				// $imageM->file = $filename.".".$file->getClientOriginalExtension();
				$imageM->file = $urls3;
				$imageM->is_use_caption = 0;
				$imageM->caption = "";
				$imageM->owner_post = "";
				$imageM->is_schedule = 0;
        $imageM->is_s3 = 1;
				$imageM->save();
				
				
      }
    }
    if($uploadcount == $file_count){
      // Session::flash('success', 'Upload successfully'); 
      return Redirect::to('saved-images');
    } 
    else {
			
			// $errors = $validator->errors();
			// if ($errors->any()) {
				// foreach($errors->all() as $error) {
						// $error_message .= $error."\n";
				// }
			// }
      // return Redirect::to('saved-images')->withInput()->withErrors($validator);
			return Redirect::to('saved-images')->with(array("error"=>$error_message));
    }
	}		
	public function load_images()
  {
    $user = Auth::user();
		
		$data = ImageModel::where("user_id","=",$user->id)
						->orderBy('id', 'desc')
						->paginate(25);
			
    return view('user.image.content')->with(
                array(
                  'user'=>$user,
                  'data'=>$data,
                  'page'=>Request::input('page'),
                ));
  }
	public function pagination_images()
  {
		$user = Auth::user();
		// if (Request::input('search')=="") {
			// $data = Proxies::paginate(15);
		// } else {
			$data = ImageModel::where("user_id","=",$user->id)
							->orderBy('id', 'desc')
							->paginate(25);
		// }
    return view('user.image.pagination')->with(
                array(
                  'user'=>$user,
                  'data'=>$data,
                  'page'=>Request::input('page'),
                ));
  }
	
	
}