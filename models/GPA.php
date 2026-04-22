<?php
class GPA {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * The core math logic: sum(grade * credits) / sum(credits)
     */
    public function calculate($studentId, $semesterId) {
        // We need a Grade model instance to fetch the raw data
        require_once 'Grade.php';
        $gradeModel = new Grade($this->db);
        $records = $gradeModel->getStudentGradesBySemester($studentId, $semesterId);

        if (empty($records)) {
            return 0;
        }

        $totalPoints = 0;
        $totalCredits = 0;

        foreach ($records as $row) {
            $totalPoints += ($row['score'] * $row['credits']);
            $totalCredits += $row['credits'];
        }

        return ($totalCredits > 0) ? ($totalPoints / $totalCredits) : 0;
    }

    /**
     * Saves or updates the calculated GPA in the gpa_records table.
     * This should be called every time a professor saves a grade.
     */
    public function saveRecord($studentId, $semesterId) {
        $gpaValue = $this->calculate($studentId, $semesterId);

        $sql = "INSERT INTO gpa_records (student_id, semester_id, gpa_value) 
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE gpa_value = VALUES(gpa_value)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$studentId, $semesterId, $gpaValue]);
    }

    /**
     * Get the GPA for the current active semester.
     */
    public function getCurrentGPA($studentId, $semesterId) {
        $sql = "SELECT gpa_value FROM gpa_records 
                WHERE student_id = ? AND semester_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$studentId, $semesterId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['gpa_value'] : 0;
    }

    /**
     * Get all historical GPA records for the student.
     * Used for the "History" page and the CSV export.
     */
    public function getHistory($studentId) {
        $sql = "SELECT gr.gpa_value, s.label, s.academic_year 
                FROM gpa_records gr
                JOIN semesters s ON gr.semester_id = s.id
                WHERE gr.student_id = ?
                ORDER BY s.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$studentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cleanup: Used when a student is deleted from the system.
     */
    public function deleteByStudent($studentId) {
        $sql = "DELETE FROM gpa_records WHERE student_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$studentId]);
    }
}
