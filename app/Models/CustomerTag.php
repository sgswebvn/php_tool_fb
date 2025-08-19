<?php

namespace App\Models;

use App\Core\Model;

class CustomerTag extends Model
{
    protected static $table = 'customer_tags';
    protected static $primaryKey = '';

    public static function assign($customerId, $tagId): bool
    {
        return self::create(['customer_id' => $customerId, 'tag_id' => $tagId]);
    }

    public static function getTagsByCustomer($customerId): array
    {
        $stmt = self::db()->prepare("SELECT t.* FROM tags t JOIN customer_tags ct ON t.id = ct.tag_id WHERE ct.customer_id = ?");
        $stmt->execute([$customerId]);
        return $stmt->fetchAll();
    }
}
