<?php namespace Celebpost\Models;

use Illuminate\Database\Eloquent\Model;


class SettingHelper extends Model {

	protected $table = 'setting_helpers';
	protected $connection = 'mysql_celebgramme';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ ];
}
