<?php
class ProfessorController {
    private $db;
    private $professorId;

    public function __construct($db) {
        $this->db = $db;
        // Logic: Middle-ware check for Professor role
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'professor') {
            header('Location: index.php?page=login');
            exit;
        }
        $this->professorId = $_SESSION['user']['id'];
    }

    /**
     * Loads the grading interface.
     * Fetches all courses assigned to this professor across all semesters.
     */
    public function grades() {
        require_once 'models/Assignment.php';
        require_once 'models/Semester.php';

        $assignmentModel = new Assignment($this->db);
        $semesterModel = new Semester($this->db);

        // Get all academic links for this professor
        $myAssignments = $assignmentModel->getByProfessor($this->professorId);
        
        // Get the active semester to help the UI highlight current work
        $activeSemester = $semesterModel->getActive();

        // Pass data to the view
        // The view (views/professor/grades.php) will use these to build the <select> dropdown
        require_once 'views/professor/grades.php';
    }
}
