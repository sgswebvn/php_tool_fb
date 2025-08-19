<?php

namespace App\Models;

use App\Core\Model;

class Page extends Model
{
    protected static $table = 'pages';
    protected static $primaryKey = 'id';

    public static function getByUserId($userId): array
    {
        return self::where('user_id', $userId);
    }

    public static function delete($id): bool
    {
        return parent::delete($id);
    }
}
