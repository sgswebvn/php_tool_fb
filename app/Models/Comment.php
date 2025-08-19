<?php

namespace App\Models;

use App\Core\Model;

class Comment extends Model
{
    protected static $table = 'comments';
    protected static $primaryKey = 'id';

    public static function getByPostId($postId): array
    {
        return self::where('post_id', $postId);
    }

    public static function getReplies($parentId): array
    {
        return self::where('parent_id', $parentId);
    }
}
