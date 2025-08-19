<?php

namespace App\Models;

use App\Core\Model;

class Post extends Model
{
    protected static $table = 'posts';
    protected static $primaryKey = 'id';

    public static function getByPageId($pageId): array
    {
        return self::where('page_id', $pageId);
    }

    public static function delete($id): bool
    {
        return parent::delete($id);
    }
}
