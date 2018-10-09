<?php

namespace Celebpost\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

use Celebpost\Models\Schedule;
use Celebpost\Models\ScheduleAccount;
use Celebpost\Models\Users;

use \InstagramAPI\Instagram;
use Exception,Mail,Image;

class UpdatePublishSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:publishschedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a thumbnail for Published Schedule Post';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
			parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
			$schedules = Schedule::where(function ($query) {
											$query->where("status","=",2);
											$query->orWhere("status","=",3);
										})
										->whereNull("thumbnail")
										->where("user_id","=",5)
										->get();
			foreach($schedules as $schedule){
				$user = Users::find($schedule->user_id);
				// $dir = public_path('images/uploads/'.$user->username.'-'.$user->id); 
				$dir = public_path('../vp/uploads/'.$user->username.'-'.$user->id); 
				if (!file_exists($dir)) {
					mkdir($dir,0741,true);
				}
				Image::make($dir."/".$schedule->slug.".jpg")
								->resize(50, null, function ($constraint) {
										$constraint->aspectRatio();
								})								
								->save($dir."/".$schedule->slug."-thumb.jpg");
				// Image::make($schedule->image)->resize(50,50);

				$update_schedule = Schedule::find($schedule->id);
				// $update_schedule->thumbnail = url('/images/uploads/'.$user->username.'-'.$user->id.'/'.$schedule->slug.".jpg");
				$update_schedule->thumbnail = url('/../vp/uploads/'.$user->username.'-'.$user->id.'/'.$schedule->slug.".jpg");
				$update_schedule->save();
				
				// $dir = basename($schedule->image);
				// $directory = public_path() . '/images/uploads/' .$dir;
				// File::delete($directory);
				
				// sleep(15);
			}
    }
}
