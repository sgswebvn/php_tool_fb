<?php

namespace App\Models;

use App\Core\Model;

class Conversation extends Model
{
    protected static $table = 'conversations';
    protected static $primaryKey = 'id';

    public static function getByPageId($pageId): array
    {
        return self::where('page_id', $pageId);
    }

    public static function getUnreadCountByPageId($pageId): int
    {
        $stmt = self::db()->prepare("SELECT SUM(unread_count) as total FROM " . static::$table . " WHERE page_id = ?");
        $stmt->execute([$pageId]);
        $result = $stmt->fetch();
        return (int)($result['total'] ?? 0);
    }
}
