<?php

namespace App\Models;

use App\Core\Model;

class UserSocialAccount extends Model
{
    protected static $table = 'user_social_accounts';
    protected static $primaryKey = 'id';

    public static function getByUserId($userId): array
    {
        return self::where('user_id', $userId);
    }

    public static function updateToken($id, $accessToken, $expiresAt = null): bool
    {
        $data = ['access_token' => $accessToken];
        if ($expiresAt) $data['token_expires'] = $expiresAt;
        return self::update($id, $data);
    }
}
