<?php

namespace Celebpost\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Auth;
use Carbon;

class Schedule extends Model
{
    protected $dates = ['created_at','updated_at','publish_at','delete_at'];
    public function PutAccount($account)
    {
        if (!is_array($account)) {
            $account = compact('account');
        }
        $this->accounts()->sync($account, false);
    }
    public function isSchedule($schedule)
    {
        return $this->accounts->contains($schedule);
    }
    public function accounts()
    {
    	return $this->belongsToMany('Celebpost\Models\Account', 'schedule_account', 'schedule_id', 'account_id')->withPivot('status','msg','publish_at')->withTimestamps();
    }
    public function failed()
    {
        return $this->belongsToMany('Celebpost\Models\Account', 'schedule_account', 'schedule_id', 'account_id')->wherePivot('status',1);
    }
    public function success()
    {
        return $this->belongsToMany('Celebpost\Models\Account', 'schedule_account', 'schedule_id', 'account_id')->wherePivot('status',2);
    }
    public function proccess()
    {
        return $this->belongsToMany('Celebpost\Models\Account', 'schedule_account', 'schedule_id', 'account_id')->wherePivot('status',0);
    }
		// protected function asDateTime($value)
    // {
			// if($value instanceof Carbon) {
				// return $value;
			// } elseif($value instanceof \DateTime) {
				// $value = $value->format('Y-m-d H:i:s');
			// }

			// $value = new Carbon($value);

			// if(Auth::user()) {
				// $value->setTimezone(Auth::user()->timezone);
			// }

			// return $value;
    // }
}
