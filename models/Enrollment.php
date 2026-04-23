<?php
class Enrollment {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Get all semesters a specific student is currently enrolled in.
     */
    public function getStudentEnrollments($studentId) {
        $sql = "SELECT semester_id FROM enrollments WHERE student_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$studentId]);
        // Return as a flat array of IDs for easier checkbox comparison
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Simple enrollment of a student into a semester.
     */
    public function enroll($studentId, $semesterId) {
        $sql = "INSERT IGNORE INTO enrollments (student_id, semester_id) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$studentId, $semesterId]);
    }

    /**
     * Remove a student from a semester.
     * Note: Controller must check for existing grades before calling this!
     */
    public function unenroll($studentId, $semesterId) {
        $sql = "DELETE FROM enrollments WHERE student_id = ? AND semester_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$studentId, $semesterId]);
    }

    /**
     * Get details of all students enrolled in a specific semester.
     * Useful for the Professor's "Enter Grades" view.
     */
    public function getStudentsInSemester($semesterId) {
        $sql = "SELECT u.id, u.full_name, u.email 
                FROM users u
                JOIN enrollments e ON u.id = e.student_id
                WHERE e.semester_id = ? AND u.role = 'student'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$semesterId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Total cleanup for when a student account is deleted.
     */
    public function deleteByStudent($studentId) {
        $sql = "DELETE FROM enrollments WHERE student_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$studentId]);
    }
}
