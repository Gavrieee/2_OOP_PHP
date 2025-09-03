<?php

require_once __DIR__ . '/Database.php';

class Attendance extends Database
{
    private string $table = "attendance";
    private string $primaryKey = "attendance_id";

    public function create(array $data)
    {
        // Check if student already has attendance for today
        $existing = $this->getAttendanceByStudentAndDate($data['student_id'], $data['date']);
        if ($existing) {
            throw new Exception("Attendance already recorded for today");
        }

        // Determine if student is late (after 8:00 AM)
        $timeIn = strtotime($data['time_in']);
        $lateThreshold = strtotime('08:00:00');

        $attendanceData = $data;
        if ($timeIn > $lateThreshold) {
            $attendanceData['is_late'] = 1;
            $attendanceData['status'] = 'late';
        } else {
            $attendanceData['is_late'] = 0;
            $attendanceData['status'] = 'present';
        }

        return $this->insert($this->table, $attendanceData);
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

    public function getAttendanceByStudentAndDate($studentId, $date)
    {
        $sql = "SELECT * FROM {$this->table} WHERE student_id = :student_id AND date = :date LIMIT 1";
        return $this->runQuery($sql, ['student_id' => $studentId, 'date' => $date])->fetch();
    }

    public function getStudentAttendanceHistory($studentId)
    {
        $sql = "SELECT a.*, s.first_name, s.last_name, c.course_name, c.year_level 
                FROM {$this->table} a 
                JOIN students s ON a.student_id = s.student_id 
                JOIN courses c ON s.course_id = c.course_id 
                WHERE a.student_id = :student_id 
                ORDER BY a.date DESC, a.time_in DESC";
        return $this->runQuery($sql, ['student_id' => $studentId])->fetchAll();
    }

    public function getAttendanceByCourseAndDate($courseId, $date)
    {
        $sql = "SELECT a.*, s.first_name, s.last_name, c.course_name, c.year_level, c.section 
                FROM {$this->table} a 
                JOIN students s ON a.student_id = s.student_id 
                JOIN courses c ON s.course_id = c.course_id 
                WHERE c.course_id = :course_id AND a.date = :date 
                ORDER BY s.last_name, s.first_name";
        return $this->runQuery($sql, ['course_id' => $courseId, 'date' => $date])->fetchAll();
    }

    public function getAttendanceByCourseAndYearLevel($courseName, $yearLevel, $date = null)
    {
        $sql = "SELECT a.*, s.first_name, s.last_name, c.course_name, c.year_level, c.section 
                FROM {$this->table} a 
                JOIN students s ON a.student_id = s.student_id 
                JOIN courses c ON s.course_id = c.course_id 
                WHERE c.course_name = :course_name AND c.year_level = :year_level";

        $params = ['course_name' => $courseName, 'year_level' => $yearLevel];

        if ($date) {
            $sql .= " AND a.date = :date";
            $params['date'] = $date;
        }

        $sql .= " ORDER BY s.last_name, s.first_name";
        return $this->runQuery($sql, $params)->fetchAll();
    }

    public function getAttendanceByCourseAndYearLevelAndSection($courseName, $yearLevel, $section, $date = null)
    {
        $sql = "SELECT a.*, s.first_name, s.last_name, c.course_name, c.year_level, c.section 
                FROM {$this->table} a 
                JOIN students s ON a.student_id = s.student_id 
                JOIN courses c ON s.course_id = c.course_id 
                WHERE c.course_name = :course_name AND c.year_level = :year_level AND c.section = :section";

        $params = ['course_name' => $courseName, 'year_level' => $yearLevel, 'section' => $section];

        if ($date) {
            $sql .= " AND a.date = :date";
            $params['date'] = $date;
        }

        $sql .= " ORDER BY s.last_name, s.first_name";
        return $this->runQuery($sql, $params)->fetchAll();
    }

    public function getAttendanceSummary($courseId = null, $date = null)
    {
        $sql = "SELECT 
                    c.course_name, 
                    c.year_level, 
                    c.section,
                    COUNT(DISTINCT s.student_id) as total_students,
                    COUNT(a.attendance_id) as present_count,
                    SUM(a.is_late) as late_count,
                    COUNT(DISTINCT s.student_id) - COUNT(a.attendance_id) as absent_count
                FROM courses c 
                LEFT JOIN students s ON c.course_id = s.course_id 
                LEFT JOIN attendance a ON s.student_id = a.student_id";

        $where = [];
        $params = [];

        if ($courseId) {
            $where[] = "c.course_id = :course_id";
            $params['course_id'] = $courseId;
        }

        if ($date) {
            $where[] = "a.date = :date";
            $params['date'] = $date;
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " GROUP BY c.course_id ORDER BY c.course_name, c.year_level";
        return $this->runQuery($sql, $params)->fetchAll();
    }
}