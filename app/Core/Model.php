<?php

namespace App\Core;

use PDO;

abstract class Model
{
    protected string $table = '';
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected bool $useSoftDelete = false;

    public static function db(): PDO
    {
        return Database::connect();
    }

    public static function query(): static
    {
        return new static();
    }

    public function getTable(): string
    {
        if (empty($this->table)) {
            $class = (new \ReflectionClass($this))->getShortName();
            return strtolower($class) . 's';
        }
        return $this->table;
    }

    public function find(int $id): ?array
    {
        $db = self::db();
        $soft = $this->useSoftDelete ? ' AND deleted_at IS NULL' : '';
        $stmt = $db->prepare("SELECT * FROM {$this->getTable()} WHERE {$this->primaryKey} = :id {$soft} LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function all(string $orderBy = 'id DESC'): array
    {
        $db = self::db();
        $where = $this->useSoftDelete ? ' WHERE deleted_at IS NULL' : '';
        $stmt = $db->query("SELECT * FROM {$this->getTable()} {$where} ORDER BY {$orderBy}");
        return $stmt->fetchAll();
    }

    public function where(string $column, mixed $value, string $operator = '='): array
    {
        $db = self::db();
        $soft = $this->useSoftDelete ? ' AND deleted_at IS NULL' : '';
        $stmt = $db->prepare("SELECT * FROM {$this->getTable()} WHERE {$column} {$operator} :val {$soft}");
        $stmt->execute(['val' => $value]);
        return $stmt->fetchAll();
    }

    public function findBy(string $column, mixed $value): ?array
    {
        $db = self::db();
        $soft = $this->useSoftDelete ? ' AND deleted_at IS NULL' : '';
        $stmt = $db->prepare("SELECT * FROM {$this->getTable()} WHERE {$column} = :val {$soft} LIMIT 1");
        $stmt->execute(['val' => $value]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function count(string $where = '', array $params = []): int
    {
        $db = self::db();
        $conditions = [];
        if ($this->useSoftDelete) {
            $conditions[] = 'deleted_at IS NULL';
        }
        if (!empty($where)) {
            $conditions[] = "({$where})";
        }

        $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
        $stmt = $db->prepare("SELECT COUNT(*) FROM {$this->getTable()} {$whereClause}");
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function create(array $data): int
    {
        $filtered = $this->filterFillable($data);
        if (empty($filtered)) {
            throw new \InvalidArgumentException("Kiritiladigan maydonlar boʻsh yoki ruxsat etilmagan.");
        }

        $db = self::db();
        $columns = array_keys($filtered);
        $fields = implode(', ', array_map(fn($c) => "`{$c}`", $columns));
        $placeholders = implode(', ', array_map(fn($c) => ":{$c}", $columns));

        $sql = "INSERT INTO {$this->getTable()} ({$fields}) VALUES ({$placeholders})";
        $stmt = $db->prepare($sql);
        $stmt->execute($filtered);

        return (int)$db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $filtered = $this->filterFillable($data);
        if (empty($filtered)) {
            return false;
        }

        $db = self::db();
        $sets = [];
        foreach ($filtered as $column => $val) {
            $sets[] = "`{$column}` = :{$column}";
        }
        $setString = implode(', ', $sets);

        $sql = "UPDATE {$this->getTable()} SET {$setString} WHERE {$this->primaryKey} = :_primary_id";
        $filtered['_primary_id'] = $id;

        $stmt = $db->prepare($sql);
        return $stmt->execute($filtered);
    }

    public function delete(int $id, bool $force = false): bool
    {
        $db = self::db();
        if ($this->useSoftDelete && !$force) {
            $stmt = $db->prepare("UPDATE {$this->getTable()} SET deleted_at = NOW() WHERE {$this->primaryKey} = :id");
            return $stmt->execute(['id' => $id]);
        }

        $stmt = $db->prepare("DELETE FROM {$this->getTable()} WHERE {$this->primaryKey} = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function paginate(
        int $page = 1,
        int $perPage = 15,
        string $where = '',
        array $params = [],
        string $orderBy = 'id DESC'
    ): array {
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $offset = ($page - 1) * $perPage;

        $db = self::db();
        $conditions = [];
        if ($this->useSoftDelete) {
            $conditions[] = "deleted_at IS NULL";
        }
        if (!empty($where)) {
            $conditions[] = "({$where})";
        }
        $whereSql = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

        // Total count
        $countStmt = $db->prepare("SELECT COUNT(*) FROM {$this->getTable()} {$whereSql}");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        // Items
        $sql = "SELECT * FROM {$this->getTable()} {$whereSql} ORDER BY {$orderBy} LIMIT :limit OFFSET :offset";
        $stmt = $db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll();

        $totalPages = (int)ceil($total / $perPage);

        return [
            'data' => $items,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => max(1, $totalPages),
            'has_prev' => $page > 1,
            'has_next' => $page < $totalPages,
        ];
    }

    protected function filterFillable(array $data): array
    {
        if (empty($this->fillable)) {
            return $data;
        }

        $result = [];
        foreach ($this->fillable as $field) {
            if (array_key_exists($field, $data)) {
                $result[$field] = $data[$field];
            }
        }
        return $result;
    }
}
