<?php namespace Celebpost\Models;

use Illuminate\Database\Eloquent\Model;


class UserSetting extends Model {
	protected $connection = 'mysql_celebgramme';
	protected $table = 'user_sessions';
	public $timestamps = false;
}

