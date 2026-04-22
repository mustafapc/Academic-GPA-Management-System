<?php
class Course {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Create a new course linked to a semester.
     * Logic check: Ensure credits > 0 in your Controller before calling this.
     */
    public function create($semesterId, $name, $credits) {
        $sql = "INSERT INTO courses (semester_id, name, credits) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$semesterId, $name, $credits]);
    }

    /**
     * Update course details.
     */
    public function update($id, $semesterId, $name, $credits) {
        $sql = "UPDATE courses SET semester_id = ?, name = ?, credits = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$semesterId, $name, $credits, $id]);
    }

    /**
     * Delete a course.
     * Note: In your Admin logic, check if any assignments exist for this course 
     * before deleting to maintain referential integrity.
     */
    public function delete($id) {
        $sql = "DELETE FROM courses WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Get all courses, joined with their semester labels for the Admin list view.
     */
    public function getAll() {
        $sql = "SELECT c.*, s.label as semester_label 
                FROM courses c 
                JOIN semesters s ON c.semester_id = s.id 
                ORDER BY s.created_at DESC, c.name ASC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find a specific course by ID.
     */
    public function find($id) {
        $sql = "SELECT * FROM courses WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get courses belonging to a specific semester.
     * Used for filtering and assignment logic.
     */
    public function getBySemester($semesterId) {
        $sql = "SELECT * FROM courses WHERE semester_id = ? ORDER BY name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$semesterId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Helper to check if a semester has courses.
     * Used by Semester logic to prevent deleting a semester that isn't empty.
     */
    public function countBySemester($semesterId) {
        $sql = "SELECT COUNT(*) FROM courses WHERE semester_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$semesterId]);
        return $stmt->fetchColumn();
    }
}
