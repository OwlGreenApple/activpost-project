<?php

namespace Celebpost\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    public $timestamps = false;
    protected $hidden = ['password'];
    protected $dates = ['last_post'];

    // Schedule
    public function schedules()
    {
    	return $this->belongsToMany('Celebpost\Models\Schedule', 'schedule_account', 'account_id', 'schedule_id')->withPivot('status','msg');
    }
    public function failed()
    {
        return $this->belongsToMany('Celebpost\Models\Schedule', 'schedule_account', 'account_id', 'schedule_id')->wherePivot('status',1);
    }
    public function success()
    {
        return $this->belongsToMany('Celebpost\Models\Schedule', 'schedule_account', 'account_id', 'schedule_id')->wherePivot('status',2);
    }
    public function proccess()
    {
        return $this->belongsToMany('Celebpost\Models\Schedule', 'schedule_account', 'account_id', 'schedule_id')->wherePivot('status',0);
    }
}
