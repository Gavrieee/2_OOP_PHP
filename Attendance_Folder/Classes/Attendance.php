<?php

require_once __DIR__ . '/Database.php';

class Attendance extends Database
{
    private string $table = "attendance";
    private string $primaryKey;

    public function __construct()
    {
        $this->primaryKey = $this->table . "_id";
    }

    public function create(array $data)
    {
        return $this->insert($this->table, $data);
    }

    public function read()
    {
        return $this->findAll($this->table);
    }

    public function update(int $id, array $data)
    {
        return $this->updateByID($this->table, $id, $data, $this->primaryKey);
    }

    public function delete(int $id)
    {
        return $this->deleteById($this->table, $id, $this->primaryKey);
    }
}