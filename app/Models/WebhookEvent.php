<?php

namespace App\Models;

use App\Core\Model;

class WebhookEvent extends Model
{
    protected static $table = 'webhook_events';
    protected static $primaryKey = 'id';

    public static function markProcessed($id): bool
    {
        return self::update($id, ['processed' => 1]);
    }

    public static function getUnprocessed(): array
    {
        return self::where('processed', 0);
    }
}
