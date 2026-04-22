<?php
class Semester {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Create a new semester record.
     */
    public function create($label, $academicYear) {
        $sql = "INSERT INTO semesters (label, academic_year) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$label, $academicYear]);
    }

    /**
     * Update an existing semester record.
     */
    public function update($id, $label, $academicYear) {
        $sql = "UPDATE semesters SET label = ?, academic_year = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$label, $academicYear, $id]);
    }

    /**
     * Logic: Sets all semesters to inactive (0) before activating a specific one.
     * This ensures only one semester is active at a time[cite: 385, 810].
     */
    public function setAllInactive() {
        $sql = "UPDATE semesters SET is_active = 0";
        return $this->db->query($sql);
    }

    /**
     * Set a specific semester as the active one[cite: 388].
     */
    public function setActive($id) {
        $sql = "UPDATE semesters SET is_active = 1 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Get the currently active semester for the student/professor dashboards[cite: 773].
     */
    public function getActive() {
        $sql = "SELECT * FROM semesters WHERE is_active = 1 LIMIT 1";
        $stmt = $this->db->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Delete a semester record[cite: 402].
     * Note: Check for linked courses in the Controller before calling this[cite: 397].
     */
    public function delete($id) {
        $sql = "DELETE FROM semesters WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Retrieve all semesters for the Admin management list[cite: 49].
     */
    public function getAll() {
        $sql = "SELECT * FROM semesters ORDER BY created_at DESC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find a single semester by ID.
     */
    public function find($id) {
        $sql = "SELECT * FROM semesters WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}