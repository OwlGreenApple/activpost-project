<?php namespace Celebpost\Models;

use Illuminate\Database\Eloquent\Model;


class Setting extends Model {

	protected $table = 'settings';
	protected $connection = 'mysql_celebgramme';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ ];
}
