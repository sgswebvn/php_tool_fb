<?php

namespace App\Models;

use App\Core\Model;

class Message extends Model
{
    protected static $table = 'messages';
    protected static $primaryKey = 'id';

    public static function getByConversationId($convId): array
    {
        return self::where('conversation_id', $convId);
    }
}
