<?php

namespace App\Models;

use App\Core\Model;

class Tag extends Model
{
    protected static $table = 'tags';
    protected static $primaryKey = 'id';

    public static function getByUserId($userId): array
    {
        return self::where('user_id', $userId);
    }
}
