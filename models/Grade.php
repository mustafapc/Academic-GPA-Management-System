/*
In your api/grades.php, after you successfully call $grade->save(...), you should immediately call:

$gpa = new GPA($db);
$gpa->saveRecord($studentId, $semesterId);

This keeps the data synchronized in real-time.
*/
<?php
class Grade {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Logic: Update or Insert a grade.
     * Uses the UNIQUE constraint (student_id, assignment_id) in the database.
     */
    public function save($studentId, $assignmentId, $score) {
        $sql = "INSERT INTO grades (student_id, assignment_id, score) 
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE score = VALUES(score)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$studentId, $assignmentId, $score]);
    }

    /**
     * Fetch all grades for a specific professor's assignment.
     * Used by api/grades.php to populate the grading table.
     */
    public function getByAssignment($assignmentId) {
        $sql = "SELECT g.*, u.full_name as student_name 
                FROM grades g
                JOIN users u ON g.student_id = u.id
                WHERE g.assignment_id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$assignmentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch a student's grades for a specific semester with course credits.
     * This is critical for the GPA calculation engine.
     */
    public function getStudentGradesBySemester($studentId, $semesterId) {
        $sql = "SELECT g.score, c.credits, c.name as course_name
                FROM grades g
                JOIN assignments a ON g.assignment_id = a.id
                JOIN courses c ON a.course_id = c.id
                WHERE g.student_id = ? AND a.semester_id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$studentId, $semesterId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Delete grades for a student.
     * Used during the "Student management" deletion flow.
     */
    public function deleteByStudent($studentId) {
        $sql = "DELETE FROM grades WHERE student_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$studentId]);
    }

    /**
     * Check if a grade exists for a student in a specific semester.
     * Used by the Enrollment logic to prevent deleting enrollments with existing data.
     */
    public function hasGradesInSemester($studentId, $semesterId) {
        $sql = "SELECT COUNT(*) FROM grades g
                JOIN assignments a ON g.assignment_id = a.id
                WHERE g.student_id = ? AND a.semester_id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$studentId, $semesterId]);
        return $stmt->fetchColumn() > 0;
    }
}