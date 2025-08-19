<?php

namespace App\Models;

use App\Core\Model;

class Payment extends Model
{
    protected static $table = 'payments';
    protected static $primaryKey = 'id';

    public static function getByUserId($userId): array
    {
        return self::where('user_id', $userId);
    }

    public static function updateStatus($id, $status, $transactionId = null): bool
    {
        $data = ['status' => $status];
        if ($transactionId) $data['payos_transaction_id'] = $transactionId;
        return self::update($id, $data);
    }
}
