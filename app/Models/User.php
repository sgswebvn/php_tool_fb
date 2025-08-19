<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected static $table = 'users';
    protected static $primaryKey = 'id';

    public static function create(array $data): int|false
    {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        return parent::create($data);
    }

    public static function isAdmin($id): bool
    {
        $user = self::find($id);
        return $user['role'] === 'admin';
    }
}
