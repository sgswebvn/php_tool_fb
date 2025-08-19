<?php

namespace App\Models;

use App\Core\Model;

class Subscription extends Model
{
    protected static $table = 'subscriptions';
    protected static $primaryKey = 'id';

    public static function getActiveByUserId($userId)
    {
        return self::firstWhere('user_id', $userId);
    }
}
