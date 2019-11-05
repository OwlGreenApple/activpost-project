<?php

namespace Celebpost\Console\Commands;

use Illuminate\Console\Command;
use InstagramAPI\Instagram;
use Celebpost\caches as Cache;
use Celebpost\Http\Controllers\User\SearchController;

class UpdateSearchIg extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:sig';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To update cache with actual data from instagram API';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->storage = "mysql";
        $this->dbhost = "localhost";
        $this->dbname = "instasearch";
        $this->dbusername = "root";
        $this->dbpassword = "";
        $this->login = "bungariaanastasya";
        $this->password = "qweasdzxc123";
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
         $get_user_id = Cache::where('type',1)->get();

         if($get_user_id->count() > 0)
         {
            foreach($get_user_id as $row)
            {
                $this->updateIgData($row->keyword,$row->id,$row->nextmaxid);
            }
         }
         else
         {
            echo 'Insight data not available';
         }
    }

    public function updateIgData($userId,$cacheId,$nextid)
    {

        /*
        $getdata = file_get_contents(storage_path('jsondata').'/'.$userId.'.json');
        $db = json_decode($getdata,true);

        dd($db);
        die('');
        */

         try {
            $error_message="";
            $i = new Instagram(false,false,[
                "storage" => $this->storage,
                "dbhost" => $this->dbhost,
                "dbname"=> $this->dbname,
                "dbusername"=> $this->dbusername,
                "dbpassword"=> $this->dbpassword,
            ]); 
            
            $i->login($this->login, $this->password, 300);

            $totalpost = 0;
            $totalpost = $i->people->getInfoById($userId)->getUser()->getMediaCount();
            $follower = $i->people->getInfoById($userId)->getUser()->getFollowerCount();

            $maxId = $nextid;
            $today = Date('d-m-Y');
            $timeline = $i->timeline->getUserFeed($userId,$maxId);
            $nextMaxId = $timeline->getNextMaxId();
            $countTimeline = count($timeline->getItems());
            $maxid[0] = $maxId;
            $maxid[] = $nextMaxId;
            $viewVideo = $hashtagposts = $totalhours = $totalweek = $hashtag_popularity = $average = $dataPoints['Image'] = $dataPoints['Album'] = $dataPoints['Video'] = array();


            /*#get max id for pagination
            if($nextMaxId <> null)
            {
                for($x=0;$x<=$totalpost;$x++)
                {
                    $timeline = $i->timeline->getUserFeed($userId,$nextMaxId);
                    $nextMaxId = $timeline->getNextMaxId();

                    if($nextMaxId <> null)
                    {
                        $maxid[] = $nextMaxId;
                    }
                    else
                    {
                        break;
                    }
                }
            }
            */

            if($countTimeline > 0)
            {
                #foreach($maxid as $idmax)
                #{
                    #foreach insight

                    #$timeline = $i->timeline->getUserFeed($userId,$idmax);

                    $sc = new SearchController;
                    foreach ($timeline->getItems() as $item) 
                    {   

                        $taken = Date('d-m-Y',$item->getTakenAt());
                        $converting_date = Date('Y-m-d',strtotime($taken)); // convert to new format so that not causing 'invalid date' on javascript

                        $time =  $sc->datediff('ww',$taken,$today,false).'w';

                        if($time > 52)
                        {
                            $time =  $sc->datediff('yyyy',$taken,$today,false).'y';
                        }


                        if(is_null($item->getImageVersions2()))
                        {
                            $img = $item->getCarouselMedia()[0]->getImageVersions2()->getCandidates()[1]->getUrl();
                        }
                        else
                        {
                            $img = $item->getImageVersions2()->getCandidates()[1]->getUrl();
                        }

                        if(is_null($item->getCaption()))
                        {
                            $caption = null;
                        }
                        else
                        {
                            $caption = $item->getCaption()->getText();
                        }

                        if($caption !== null)
                        {
                            preg_match_all("/(#\w+)/", $caption, $hashtagpost);
                            $hashtagposts[] = $hashtagpost[0];
                        }
                        
                        $posts[] = array(
                            'profile'=> $item->getUser()->getProfilePicUrl(),
                            'username' =>$item->getUser()->getUsername(),
                            'fullname' =>$item->getUser()->getFullName(),
                            'code' => 'https://www.instagram.com/p/'.$item->getCode().'/',
                            'comments' =>$item->getCommentCount(),
                            'likes' =>$item->getLikeCount(),
                            'img' => $img,
                            'time'=> $time,
                            'caption'=>$caption
                        );

                        #media type
                        /*
                            1 = image
                            2 = video / igtv
                            8 = album
                        */
                        $mediatype = $item->getMediaType();

                        if($mediatype == 8)
                        {
                            $typemedia = 'Album';
                        }
                        else if($mediatype == 2)
                        {
                            $typemedia = 'Video';
                            #data video view
                            $viewVideo[] = array(
                                'views'=> $item->getViewCount(),
                                'date_posting'=>$converting_date,
                                'link'=>'https://www.instagram.com/p/'.$item->getCode().'/'
                            );
                        }
                        else
                        {
                            $typemedia = 'Image';
                        }

                        #data total view for graph
                        $engagement = $item->getCommentCount()+$item->getLikeCount();

                        //print('<pre>'.print_r($engagement,true).' '.print_r($taken,true).'</pre>');
                        $dataPoints[$typemedia][] = array(
                            "x" => $converting_date,  //date when posting created
                            "y" => $engagement, // engagement rate
                            "z" => $engagement,  // size of bubble
                            "type"=> $typemedia,
                            "image" => $img, //image of post code
                            "link" => 'https://www.instagram.com/p/'.$item->getCode().'/', //go to post link when user click on bubble
                            "like" => $item->getLikeCount(),
                            "comments" => $item->getCommentCount(),
                        );


                        #average post by week
                        $totalweek[] = Date('D',$item->getTakenAt());
                        $totalhours[] = Date('H:00',$item->getTakenAt());

                    }

                    #end foreach insight

                 # end foreach maxid  
                #}
            } 
            else
            {
                $posts = array();
            }

            #average post by day
            $totalclock = array_count_values($totalhours);
            
            #average post by day
            $totalday = array_count_values($totalweek);

            if(!isset($totalday['Mon'])){$totalday['Mon'] = 0;}
            if(!isset($totalday['Tue'])){$totalday['Tue'] = 0;}
            if(!isset($totalday['Wed'])){$totalday['Wed'] = 0;}
            if(!isset($totalday['Thu'])){$totalday['Thu'] = 0;}
            if(!isset($totalday['Fri'])){$totalday['Fri'] = 0;}
            if(!isset($totalday['Sat'])){$totalday['Sat'] = 0;}
            if(!isset($totalday['Sun'])){$totalday['Sun'] = 0;}

            #graph data "Most Engaging Content Type"
            $datagraph = $dataPoints;

            #piegraphdata
            $piedata['image'] = count($datagraph['Image']);
            $piedata['album'] = count($datagraph['Album']);
            $piedata['video'] = count($datagraph['Video']);

            #bar column graph data

            #image like and comment
            $totalimagelike = 0;
            $totalimagecomments = 0;
            foreach($datagraph['Image'] as $rows)
            {
                $totalimagelike += $rows['like'];
                $totalimagecomments += $rows['comments'];
            }

            $average['imagelike'] = $sc->divisionLikeComments($totalimagelike,$piedata['image']);
            $average['imagecomments'] = $sc->divisionLikeComments($totalimagecomments,$piedata['image']);

            #album like and comment
            $totalalbumlike = 0;
            $totalalbumcomments = 0;
            foreach($datagraph['Album'] as $rows)
            {
                $totalalbumlike += $rows['like'];
                $totalalbumcomments += $rows['comments'];
            }

            $average['albumlike'] = $sc->divisionLikeComments($totalalbumlike,$piedata['album']);
            $average['albumcomments'] = $sc->divisionLikeComments($totalalbumcomments,$piedata['album']);

            #video like and comment
            $totalvideolike = $totalvideocomments = $totalvideoviews = 0;
            foreach($datagraph['Album'] as $rows)
            {
                $totalvideolike += $rows['like'];
                $totalvideocomments += $rows['comments'];
            }

            $average['videolike'] = $sc->divisionLikeComments($totalalbumlike,$piedata['video']);
            $average['videocomments'] = $sc->divisionLikeComments($totalalbumcomments,$piedata['video']);

            #hashtag post
            $hashtags_temp = array();
            $count = count($hashtagposts);
            if($count > 0)
            {
                foreach($hashtagposts as $arr)
                {
                    foreach ($arr as $value) {
                        $hashtags_temp[] = $value;
                    }
                }
                $hashtag_name = array_count_values($hashtags_temp);
            }
            else
            {
                $hashtag_name =  array();
            }

            #hashtag column
            $hashtag_per_post = $hashtag_popularity = [];
            if(count($hashtag_name) > 0)
            {
                foreach($hashtag_name as $hashtag=>$totalhashtag)
                {
                    $hashtagkey = str_replace('#','',$hashtag);
                    $hashtagpopularity = $i->hashtag->getInfo($hashtagkey)->getMediaCount();
                    $percenthashtag = ($totalhashtag/$totalpost) * 100;
                    
                    $hashtags[] = array(
                        'hashtagname'=>$hashtag,
                        'hashtagpopularity'=>$hashtagpopularity,
                        'hashtaginpost'=>$totalhashtag,
                        'hashtagpercent'=> round($percenthashtag)
                    );
                    #for graph 'Number of Hashtags per Post'
                    $hashtag_per_post[] = $totalhashtag;
                    #hashtag by popularity
                    $hashtag_popularity[] = $hashtagpopularity;
                }
            }
            else
            {
                $hashtags = array();
            }
            
            #hashtag by popularity
            $arr = $hashtag_popularity;     
            $hash = $hash['specific'] = $hash['medium'] = $hash['popular'] = $hash['very_popular'] = $hash['x_popular'] = array();

            $hash['specific'] = array_filter($arr, function($value) {
                if($value < pow(10,5))
                {
                    return $value;
                }
            });

            $hash['medium'] = array_filter($arr, function($value) {
                if($value >= pow(10,5) && $value < pow(10,6))
                {
                    return $value;
                }
            });

            $hash['popular'] = array_filter($arr, function($value) {
                if($value >= pow(10,6) && $value < pow(10,7))
                {
                    return $value;
                }
            });

            $hash['very_popular'] = array_filter($arr, function($value) {
                if($value >= pow(10,7) && $value < pow(10,8))
                {
                    return $value;
                }
            });

            $hash['x_popular'] = array_filter($arr, function($value) {
                if($value > pow(10,8))
                {
                    return $value;
                }
            });
        
            $hash['specific'] = count($hash['specific']);
            $hash['medium'] = count($hash['medium']);
            $hash['popular'] = count($hash['popular']);
            $hash['very_popular'] = count($hash['very_popular']);
            $hash['x_popular'] = count($hash['x_popular']);

            #for graph 'Number of Hashtags per Post'
            $totalhashtaginpost = array_count_values($hashtag_per_post);

            //print('<pre>'.print_r(round($percenthashtag),true).'</pre>');

            $data = array(
                'maxid'=>$maxId,
                'post'=>$posts,
                'hashtags'=>$hashtags,
                'graph'=>$datagraph,
                'piedata'=>$piedata, 
                'avgdata'=>$average, 
                'totalhashtaginpost'=>$totalhashtaginpost,
                'hashtagspopularity'=>$hash, 
                'totaldaypost'=>$totalday, 
                'totalclock'=>$totalclock,
                'totalvideoview'=>$viewVideo
            );

            #get old data
            $getdata = file_get_contents(storage_path('jsondata').'/'.$userId.'.json');
            $db = json_decode($getdata,true);

            #merging old data with new data
            $merge = array(
                'maxid'=>$maxId,
                'post'=>array_merge($db['post'],$data['post']),
                'hashtags'=>array_merge($db['hashtags'],$data['hashtags']),
                'graph'=>array_merge($db['graph'],$data['graph']),
                'piedata'=>array_merge($db['piedata'],$data['piedata']),
                'avgdata'=>array_merge($db['avgdata'],$data['avgdata']),
                'totalhashtaginpost'=>array_merge($db['totalhashtaginpost'],$data['totalhashtaginpost']),
                'hashtagspopularity'=>array_merge($db['hashtagspopularity'],$data['hashtagspopularity']),
                'totaldaypost'=>array_merge($db['totaldaypost'],$data['totaldaypost']),
                'totalclock'=>array_merge($db['totalclock'],$data['totalclock']),
                'totalvideoview'=>array_merge($db['totalvideoview'],$data['totalvideoview']),
            );
            #print('<pre>'.print_r($merge,true).'</pre>');
            $json = json_encode($merge,true);
            Cache::where('id',$cacheId)->update(['nextmaxid'=>$nextMaxId]);
            file_put_contents(storage_path('jsondata').'/'.$userId.'.json', $json);
        }   
            catch (\InstagramAPI\Exception\IncorrectPasswordException $e) {
                //klo error password
                $error_message = $e->getMessage();
            }
            catch (\InstagramAPI\Exception\AccountDisabledException $e) {
                //klo error password
                $error_message = $e->getMessage();
            }
            catch (\InstagramAPI\Exception\CheckpointRequiredException $e) {
                //klo error email / phone verification 
                $error_message = $e->getMessage();
            }
                    catch (\InstagramAPI\Exception\InstagramException $e) {
                        $is_error = true;
                        // if ($e->hasResponse() && $e->getResponse()->isTwoFactorRequired()) {
                            // echo "2 Factor perlu dioffkan";
                        // } 
                        // else {
                                // all other login errors would get caught here...
                            echo $e->getMessage();
                        // }
                    }   
            catch (NotFoundException $e) {
                // echo $e->getMessage();
                echo "asd";
            }                   
            catch (Exception $e) {
                $error_message = "fin ".$e->getMessage();
                if ($error_message == "InstagramAPI\Response\LoginResponse: The password you entered is incorrect. Please try again.") {
                    $error_message = "fin ".$e->getMessage();
                } 
                if ( ($error_message == "InstagramAPI\Response\LoginResponse: Challenge required.") || ( substr($error_message, 0, 18) == "challenge_required") || ($error_message == "InstagramAPI\Response\TimelineFeedResponse: Challenge required.") || ($error_message == "InstagramAPI\Response\LoginResponse: Sorry, there was a problem with your request.") ){
                    $error_message = "fin ".$e->getMessage();
                }
            }
            echo $error_message;
    }

/* end class */
}
