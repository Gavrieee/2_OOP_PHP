<?php

require_once __DIR__ . '/Database.php';

class ExcuseLetter extends Database
{
    private string $table = 'excuse_letters';
    private string $primaryKey = 'letter_id';

    public function create(array $data)
    {
        return $this->insert($this->table, $data);
    }

    public function updateStatus(int $letterId, string $status, ?string $adminComment = null)
    {
        $status = in_array($status, ['pending', 'approved', 'rejected'], true) ? $status : 'pending';
        $data = [
            'status' => $status,
            'admin_comment' => $adminComment,
            'reviewed_at' => date('Y-m-d H:i:s')
        ];
        return $this->updateByID($this->table, $letterId, $data, $this->primaryKey);
    }

    public function getByStudent(int $studentId)
    {
        $sql = "SELECT el.*, c.course_name, c.year_level, c.section
                FROM {$this->table} el
                JOIN courses c ON el.course_id = c.course_id
                WHERE el.student_id = :student_id
                ORDER BY el.created_at DESC";
        return $this->runQuery($sql, ['student_id' => $studentId])->fetchAll();
    }

    public function getByFilters(?string $courseName = null, ?string $yearLevel = null, ?string $section = null, ?string $status = null)
    {
        $sql = "SELECT el.*, s.first_name, s.last_name, c.course_name, c.year_level, c.section
                FROM {$this->table} el
                JOIN students s ON el.student_id = s.student_id
                JOIN courses c ON el.course_id = c.course_id";
        $where = [];
        $params = [];
        if (!empty($courseName)) {
            $where[] = 'c.course_name = :course_name';
            $params['course_name'] = $courseName;
        }
        if (!empty($yearLevel)) {
            $where[] = 'c.year_level = :year_level';
            $params['year_level'] = $yearLevel;
        }
        if (!empty($section)) {
            $where[] = 'c.section = :section';
            $params['section'] = $section;
        }
        if (!empty($status)) {
            $where[] = 'el.status = :status';
            $params['status'] = $status;
        }
        if (!empty($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY el.created_at DESC';
        return $this->runQuery($sql, $params)->fetchAll();
    }

    public function getById(int $id)
    {
        return $this->findById($this->table, $id, $this->primaryKey);
    }
}