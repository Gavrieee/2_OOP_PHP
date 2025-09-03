<?php

require_once __DIR__ . '/Database.php';

class Course extends Database
{
    private string $table = "courses";
    private string $primaryKey = "course_id";

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

    public function getCourseById($id)
    {
        return $this->findById($this->table, $id, $this->primaryKey);
    }

    public function getCoursesByYearLevel($yearLevel)
    {
        return $this->findAll($this->table, ['year_level' => $yearLevel]);
    }

    public function getCoursesByProgram($program)
    {
        return $this->findAll($this->table, ['course_name' => $program]);
    }

    public function getCoursesBySection($section)
    {
        return $this->findAll($this->table, ['section' => $section]);
    }

    public function getAllCoursesWithDetails()
    {
        $sql = "SELECT c.*, COUNT(s.student_id) as student_count 
                FROM {$this->table} c 
                LEFT JOIN students s ON c.course_id = s.course_id 
                GROUP BY c.course_id 
                ORDER BY c.course_name, c.year_level";
        return $this->runQuery($sql)->fetchAll();
    }
}
