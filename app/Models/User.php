<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected string $table = 'users';
    protected array $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    public function findByEmail(string $email): ?array
    {
        return $this->findBy('email', $email);
    }

    public function getManagers(): array
    {
        $db = self::db();
        $stmt = $db->query("SELECT id, name, email, role, is_active FROM {$this->getTable()} WHERE role = 'manager' AND is_active = 1 ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    public function getAllUsers(): array
    {
        $db = self::db();
        $stmt = $db->query("SELECT id, name, email, role, is_active, created_at FROM {$this->getTable()} ORDER BY id DESC");
        return $stmt->fetchAll();
    }
}
