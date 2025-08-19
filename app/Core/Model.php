<?php

namespace App\Core;

use PDO;
use PDOException;

class Model
{
    protected static ?PDO $pdo = null;
    protected static $table = '';
    protected static $primaryKey = 'id';

    protected static function db(): PDO
    {
        if (!self::$pdo) {
            try {
                $dsn = "mysql:host=" . env('DB_HOST') . ";dbname=" . env('DB_NAME') . ";charset=utf8mb4";
                self::$pdo = new PDO($dsn, env('DB_USER'), env('DB_PASS'), [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            } catch (PDOException $e) {
                throw new PDOException("Database connection failed: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }

    public static function all(): array
    {
        try {
            $stmt = self::db()->query("SELECT * FROM " . static::$table);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public static function find($id)
    {
        try {
            $stmt = self::db()->prepare("SELECT * FROM " . static::$table . " WHERE " . static::$primaryKey . " = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return null;
        }
    }

    public static function where($column, $value)
    {
        try {
            $stmt = self::db()->prepare("SELECT * FROM " . static::$table . " WHERE $column = ?");
            $stmt->execute([$value]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public static function firstWhere($column, $value)
    {
        $results = self::where($column, $value);
        return $results[0] ?? null;
    }

    public static function create(array $data): int|false
    {
        try {
            $keys = implode(', ', array_keys($data));
            $placeholders = implode(', ', array_fill(0, count($data), '?'));
            $stmt = self::db()->prepare("INSERT INTO " . static::$table . " ($keys) VALUES ($placeholders)");
            $stmt->execute(array_values($data));
            return self::db()->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function update($id, array $data): bool
    {
        try {
            $sets = implode(', ', array_map(fn($k) => "$k = ?", array_keys($data)));
            $stmt = self::db()->prepare("UPDATE " . static::$table . " SET $sets WHERE " . static::$primaryKey . " = ?");
            return $stmt->execute([...array_values($data), $id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function delete($id): bool
    {
        try {
            $stmt = self::db()->prepare("DELETE FROM " . static::$table . " WHERE " . static::$primaryKey . " = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
