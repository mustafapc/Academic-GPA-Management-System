<?php
class StudentController {
    private $db;
    private $studentId;

    public function __construct($db) {
        $this->db = $db;
        // Logic: Ensure only students can access this controller
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
            header('Location: index.php?page=login');
            exit;
        }
        $this->studentId = $_SESSION['user']['id'];
    }

    /**
     * Loads the main student dashboard.
     * Shows current grades and overall GPA for the active semester.
     */
    public function dashboard() {
        require_once 'models/Semester.php';
        require_once 'models/Grade.php';
        require_once 'models/GPA.php';

        $semesterModel = new Semester($this->db);
        $gradeModel = new Grade($this->db);
        $gpaModel = new GPA($this->db);

        $activeSemester = $semesterModel->getActive();
        
        $currentGrades = [];
        $currentGPA = 0;

        if ($activeSemester) {
            $currentGrades = $gradeModel->getStudentGradesBySemester($this->studentId, $activeSemester['id']);
            $currentGPA = $gpaModel->getCurrentGPA($this->studentId, $activeSemester['id']);
        }

        // Pass variables to the view
        require_once 'views/student/dashboard.php';
    }

    /**
     * Loads the GPA history page.
     * Displays a table of all past semesters and their respective GPAs.
     */
    public function history() {
        require_once 'models/GPA.php';
        
        $gpaModel = new GPA($this->db);
        $gpaHistory = $gpaModel->getHistory($this->studentId);

        // Pass history to the view
        require_once 'views/student/history.php';
    }
}
