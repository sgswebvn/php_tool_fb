<?php

namespace App\Models;

use App\Core\Model;

class Customer extends Model
{
    protected static $table = 'customers';
    protected static $primaryKey = 'id';

    public static function getByPageId($pageId): array
    {
        return self::where('page_id', $pageId);
    }

    public static function getByPsid($pageId, $psid): array
    {
        $stmt = self::db()->prepare("SELECT * FROM " . static::$table . " WHERE page_id = ? AND psid = ?");
        $stmt->execute([$pageId, $psid]);
        return $stmt->fetchAll();
    }
}
