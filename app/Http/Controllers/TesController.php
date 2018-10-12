<?php

namespace Celebpost\Http\Controllers;

use Illuminate\Http\Request;
use Celebpost\Models\Account;
use Celebpost\Models\Proxies;

use Illuminate\Support\Facades\Crypt;
use \InstagramAPI\Instagram;
use Config;

class TesController extends Controller
{
    public function tesvideo(){
      $account = Account::find(5);
      $proxy = Proxies::find($account->proxy_id);

      $i = new Instagram(false,false,[
                    "storage"       => "mysql",
                    "dbhost"       => Config::get('database.connections.mysql_celebgramme.host'),
                    "dbname"   => Config::get('database.connections.mysql_celebgramme.database'),
                    "dbusername"   => Config::get('database.connections.mysql_celebgramme.username'),
                    "dbpassword"   => Config::get('database.connections.mysql_celebgramme.password'),
                  ]);

      if ($account->proxy_id <> 0){
        // Check Login
        if (!is_null($proxy)) {
          if($proxy->cred==""){
            $i->setProxy("http://".$proxy->proxy.":".$proxy->port);
          } else {
            $i->setProxy("http://".$proxy->cred."@".$proxy->proxy.":".$proxy->port);
          }
        }

        $decrypted_string = Crypt::decrypt($account->password);
        $pieces = explode(" ~space~ ", $decrypted_string);
        $pass = $pieces[0];
        $password = $pass;

        $i->login($account->username, $password);
      }
      
      $dir = base_path('images/uploads/puspitanurhidayati@gmail.com-6'); 
      //$photo = $dir."/PublishFile-00000.jpg";
      $videoname = $dir."/videoplayback.mp4";
      $video = new \InstagramAPI\Media\Video\InstagramVideo($videoname);
      $caption = "tes caption";

      //$instagram = $i->timeline->uploadPhoto($photo, ['caption' => $caption]);
      $instagram = $i->timeline->uploadVideo($video->getFile(), ['caption' => $caption]);
    }
}
