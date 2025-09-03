<?php

require_once __DIR__ . '/Database.php';

class Student extends Database
{
    private string $table = "students";
    private string $primaryKey = "student_id";

    public function create(array $data)
    {
        return $this->insert($this->table, $data);
    }

    public function read()
    {
        return $this->findAll($this->table);
    }

    public function update($id, array $data)
    {
        return $this->updateByID($this->table, $id, $data, $this->primaryKey);
    }

    public function delete($id)
    {
        return $this->deleteById($this->table, $id, $this->primaryKey);
    }

    public function getStudentById($id)
    {
        return $this->findById($this->table, $id, $this->primaryKey);
    }

    public function getStudentByUserId($userId)
    {
        return $this->findAll($this->table, ['user_id' => $userId]);
    }

    public function getStudentWithCourse($studentId)
    {
        $sql = "SELECT s.*, c.course_name, c.year_level, c.section, u.username 
                FROM {$this->table} s 
                JOIN courses c ON s.course_id = c.course_id 
                JOIN users u ON s.user_id = u.user_id 
                WHERE s.student_id = :student_id";
        return $this->runQuery($sql, ['student_id' => $studentId])->fetch();
    }

    public function getAllStudentsWithCourses()
    {
        $sql = "SELECT s.*, c.course_name, c.year_level, c.section, u.username 
                FROM {$this->table} s 
                JOIN courses c ON s.course_id = c.course_id 
                JOIN users u ON s.user_id = u.user_id 
                ORDER BY c.course_name, c.year_level, s.last_name";
        return $this->runQuery($sql)->fetchAll();
    }

    public function getStudentsByCourse($courseId)
    {
        $sql = "SELECT s.*, c.course_name, c.year_level, c.section, u.username 
                FROM {$this->table} s 
                JOIN courses c ON s.course_id = c.course_id 
                JOIN users u ON s.user_id = u.user_id 
                WHERE s.course_id = :course_id 
                ORDER BY s.last_name";
        return $this->runQuery($sql, ['course_id' => $courseId])->fetchAll();
    }
}
