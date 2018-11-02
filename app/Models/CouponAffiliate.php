<?php

namespace Celebpost\Models;

use Illuminate\Database\Eloquent\Model;

class CouponAffiliate extends Model
{
    protected $connection = 'mysql_affiliate';
    protected $table = 'coupons';
}
