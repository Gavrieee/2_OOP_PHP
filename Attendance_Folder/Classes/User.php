<?php

require_once __DIR__ . '/Database.php';

class User extends Database
{
    private string $table = "users";
    private string $primaryKey = "user_id";

    public function create(array $data)
    {
        // Hash the password before storing
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        return $this->insert($this->table, $data);
    }

    public function authenticate($username, $password)
    {
        $sql = "SELECT * FROM {$this->table} WHERE username = :username LIMIT 1";
        $user = $this->runQuery($sql, ['username' => $username])->fetch();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    public function findByUsername($username)
    {
        $sql = "SELECT * FROM {$this->table} WHERE username = :username LIMIT 1";
        return $this->runQuery($sql, ['username' => $username])->fetch();
    }

    public function getUserById($id)
    {
        return parent::findById($this->table, $id, $this->primaryKey);
    }

    public function update($id, array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        return $this->updateByID($this->table, $id, $data, $this->primaryKey);
    }

    public function delete($id)
    {
        return $this->deleteById($this->table, $id, $this->primaryKey);
    }

    public function getAllUsers()
    {
        return $this->findAll($this->table);
    }

    public function getUsersByRole($role)
    {
        return $this->findAll($this->table, ['role' => $role]);
    }
}
