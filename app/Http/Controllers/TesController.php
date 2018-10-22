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

    public function tesStory(){
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
      
      try {
        $location = $i->location->search('40.7439862', '-73.998511')
                      ->getVenues()[0];
      } catch (\Exception $e) {
        echo 'Something went wrong: '.$e->getMessage()."\n";
      }

      $metadata = [
          // (optional) Captions can always be used, like this:
          'caption'  => '#test This is a great API!',

          // (optional) To add a hashtag, do this:
          'hashtags' => [
              // Note that you can add more than one hashtag in this array.
              [
                  'tag_name'         => 'test', // Hashtag WITHOUT the '#'! NOTE: This hashtag MUST appear in the caption.
                  'x'                => 0.5, // Range: 0.0 - 1.0. Note that x = 0.5 and y = 0.5 is center of screen.
                  'y'                => 0.5, // Also note that X/Y is setting the position of the CENTER of the clickable area.
                  'width'            => 0.24305555, // Clickable area size, as percentage of image size: 0.0 - 1.0
                  'height'           => 0.07347973, // ...
                  'rotation'         => 0.0,
                  'is_sticker'       => false, // Don't change this value.
                  'use_custom_title' => false, // Don't change this value.
              ],
              // ...
          ],

          // (optional) To add a location, do BOTH of these:
          /*'location_sticker' => [
              'width'         => 0.89333333333333331,
              'height'        => 0.071281859070464776,
              'x'             => 0.5,
              'y'             => 0.2,
              'rotation'      => 0.0,
              'is_sticker'    => true,
              'location_id'   => $location->getExternalId(),
          ],
          'location' => $location,*/

          // (optional) You can use story links ONLY if you have a business account with >= 10k followers.
          // 'link' => 'https://github.com/mgp25/Instagram-API',
      ];

      $dir = base_path('images/uploads/puspitanurhidayati@gmail.com-6'); 
      $photoFilename = $dir."/StoryFile-00000.mp4";
      //$videoname = $dir."/videoplayback.mp4";
      //$video = new \InstagramAPI\Media\Video\InstagramVideo($videoname);
      //$caption = "tes caption";

      //$instagram = $i->timeline->uploadPhoto($photo, ['caption' => $caption]);
      //$instagram = $i->timeline->uploadVideo($video->getFile(), ['caption' => $caption]);
      $caption = 'apayaaa';
      //$photo = new \InstagramAPI\Media\Photo\InstagramPhoto($photoFilename, ['targetFeed' => \InstagramAPI\Constants::FEED_STORY]);
      //$i->story->uploadPhoto($photoFilename, $metadata);
      $i->story->uploadVideo($photoFilename, $metadata);
    }
}
