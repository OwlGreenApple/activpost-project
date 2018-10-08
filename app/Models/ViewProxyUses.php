<?php namespace Celebpost\Models;

use Illuminate\Database\Eloquent\Model;


class ViewProxyUses extends Model {
	protected $table = 'proxy_uses_total';
	protected $connection = 'mysql_celebgramme';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ ];
}
