<?php

namespace Celebpost\Models;

use Illuminate\Database\Eloquent\Model;

class OrderUserAffiliate extends Model
{
    protected $connection = 'mysql_affiliate';
    protected $table = 'order_users';
}
