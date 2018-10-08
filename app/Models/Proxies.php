<?php namespace Celebpost\Models;

use Illuminate\Database\Eloquent\Model;


class Proxies extends Model {

	protected $table = 'proxies';
	protected $connection = 'mysql_celebgramme';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ ];
}
