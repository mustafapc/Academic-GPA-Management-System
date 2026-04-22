<?php
class Assignment {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Link a professor to a course in a specific semester.
     * Logic: Uses INSERT IGNORE to prevent duplicate assignments.
     */
    public function create($professorId, $courseId, $semesterId) {
        $sql = "INSERT IGNORE INTO assignments (professor_id, course_id, semester_id) 
                VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$professorId, $courseId, $semesterId]);
    }

    /**
     * Get all assignments for the Admin list view.
     * Includes professor name, course name, and semester label.
     */
    public function getAll() {
        $sql = "SELECT a.id, u.full_name as professor_name, c.name as course_name, s.label as semester_label
                FROM assignments a
                JOIN users u ON a.professor_id = u.id
                JOIN courses c ON a.course_id = c.id
                JOIN semesters s ON a.semester_id = s.id
                ORDER BY s.created_at DESC, u.full_name ASC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get assignments specifically for a logged-in professor.
     * Used by ProfessorController to populate the "Select Course" dropdown.
     */
    public function getByProfessor($professorId) {
        $sql = "SELECT a.id, c.name as course_name, s.label as semester_label, a.semester_id, a.course_id
                FROM assignments a
                JOIN courses c ON a.course_id = c.id
                JOIN semesters s ON a.semester_id = s.id
                WHERE a.professor_id = ?
                ORDER BY s.is_active DESC, s.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$professorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Remove an assignment.
     * Note: In your logic, check if grades have already been entered 
     * for this assignment before allowing deletion.
     */
    public function delete($id) {
        $sql = "DELETE FROM assignments WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Verify if a specific assignment belongs to a professor.
     * Critical for security in api/grades.php.
     */
    public function isAssignedTo($assignmentId, $professorId) {
        $sql = "SELECT COUNT(*) FROM assignments WHERE id = ? AND professor_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$assignmentId, $professorId]);
        return $stmt->fetchColumn() > 0;
    }
}
