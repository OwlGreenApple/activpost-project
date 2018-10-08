<?php

namespace Celebpost\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Auth;
use Carbon;

class ScheduleAccount extends Model
{
	protected $table = 'schedule_account';
	protected $dates = ['created_at','updated_at','published_time','deleted_time'];
	// public $timestamps = false;
}
