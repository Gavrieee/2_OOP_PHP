<?php

class Database
{
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "oop_php";

    private $pdo = null;

    private $primaryKeys = [
        "attendance" => "attendance_id",
    ];

    protected function getPDO()
    {
        if ($this->pdo === null) {
            try {
                $this->pdo = new PDO(
                    "mysql:host={$this->host};dbname={$this->database};charset=utf8mb4",
                    $this->username,
                    $this->password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_PERSISTENT => true // Ensures a Persistent connection
                    ]
                );
            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }
        return $this->pdo;
    }

    public function runQuery($sql, $params = [])
    {
        $stmt = $this->getPDO()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function insert($table, $data)
    {
        $keys = array_keys($data);
        $columns = implode(", ", $keys);
        $placeholders = ":" . implode(", :", $keys);

        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $this->runQuery($sql, $data);
    }

    public function updateByID($table, $id, $data, $idColumn = 'id')
    {
        $setPart = implode(", ", array_map(fn($key) => "$key = :$key", array_keys($data)));
        $sql = "UPDATE $table SET $setPart WHERE $idColumn = :id";

        $data["id"] = $id;
        return $this->runQuery($sql, $data)->rowCount();
    }

    public function deleteById($table, $id, $idColumn = "id")
    {
        $sql = "DELETE FROM $table WHERE $idColumn = :id";
        return $this->runQuery($sql, ["id" => $id])->rowCount();
    }

    public function findAll($table, $where = [])
    {
        $sql = "SELECT * FROM $table";

        if (!empty($where)) {
            $conditions = implode(" AND ", array_map(fn($key) => "$key = :$key", array_keys($where)));
            $sql .= " WHERE $conditions";
        }

        // fallback: if no mapping, default to "id"
        $pk = $this->primaryKeys[$table] ?? "id";
        $sql .= " ORDER BY {$pk} DESC";

        return $this->runQuery($sql, $where)->fetchAll();
    }

    public function findById($table, $id, $idColumn = "id")
    {
        $sql = "SELECT * FROM $table WHERE $idColumn = :id LIMIT 1";
        return $this->runQuery($sql, ["id" => $id])->fetch();
    }

}