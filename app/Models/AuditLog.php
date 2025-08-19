<?php

namespace App\Models;

use App\Core\Model;

class AuditLog extends Model
{
    protected static $table = 'audit_logs';
    protected static $primaryKey = 'id';

    public static function log($data): int|false
    {
        return self::create($data);
    }
}
