<?php

namespace App\Models;

use App\Core\Model;

class Note extends Model
{
    protected static $table = 'notes';
    protected static $primaryKey = 'id';

    public static function getByCustomerId($customerId): array
    {
        return self::where('customer_id', $customerId);
    }
}
