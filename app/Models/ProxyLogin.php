<?php namespace Celebpost\Models;

use Illuminate\Database\Eloquent\Model;


class ProxyLogin extends Model {

	protected $table = 'proxy_logins';
	protected $connection = 'mysql_celebgramme';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ ];
}
