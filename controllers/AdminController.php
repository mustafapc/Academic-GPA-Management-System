<?php
class AdminController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
        // Middleware: Strict Admin access only
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: index.php?page=login');
            exit;
        }
    }

    // --- SEMESTER LOGIC ---
    public function manageSemesters() {
        require_once 'models/Semester.php';
        $semesterModel = new Semester($this->db);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            
            if ($action === 'create') {
                $semesterModel->create($_POST['label'], $_POST['academic_year']);
            } elseif ($action === 'toggle_active') {
                $id = $_POST['id'];
                $semesterModel->setAllInactive();
                $semesterModel->setActive($id);
            } elseif ($action === 'delete') {
                require_once 'models/Course.php';
                $courseModel = new Course($this->db);
                // logic: prevent deletion if courses are linked
                if ($courseModel->countBySemester($_POST['id']) > 0) {
                    $_SESSION['error'] = "Cannot delete semester: It contains courses.";
                } else {
                    $semesterModel->delete($_POST['id']);
                }
            }
            header('Location: index.php?page=admin_semesters');
            exit;
        }

        $semesters = $semesterModel->getAll();
        require_once 'views/admin/semesters.php';
    }

    // --- STUDENT LOGIC (With specialized deletion) ---
    public function manageStudents() {
        require_once 'models/User.php'; // Assume a base User model for CRUD
        $userModel = new User($this->db);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            $id = $_POST['id'] ?? null;

            if ($action === 'save') {
                $email = $_POST['email'];
                // Check if email exists for a DIFFERENT user
                if ($userModel->emailExists($email, $id)) {
                    $_SESSION['error'] = "Email already in use.";
                } else {
                    $data = [
                        'full_name' => $_POST['full_name'],
                        'email' => $email,
                        'role' => 'student'
                    ];
                    if ($id) {
                        $userModel->update($id, $data);
                    } else {
                        $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
                        $userModel->create($data);
                    }
                }
            } elseif ($action === 'delete') {
                // LOGIC: Order of deletion matters for foreign keys
                require_once 'models/Grade.php';
                require_once 'models/GPA.php';
                require_once 'models/Enrollment.php';

                (new Grade($this->db))->deleteByStudent($id);
                (new GPA($this->db))->deleteByStudent($id);
                (new Enrollment($this->db))->deleteByStudent($id);
                $userModel->delete($id);
            }
            header('Location: index.php?page=admin_students');
            exit;
        }

        $students = $userModel->getByRole('student');
        require_once 'views/admin/students.php';
    }

    // --- ENROLLMENT LOGIC (Checkbox Sync) ---
    public function manageEnrollments() {
        require_once 'models/Enrollment.php';
        require_once 'models/Grade.php';
        require_once 'models/Semester.php';
        
        $enrollModel = new Enrollment($this->db);
        $gradeModel = new Grade($this->db);
        $studentId = $_GET['student_id'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $studentId = $_POST['student_id'];
            $newSemesters = $_POST['semesters'] ?? []; // Array of IDs from checkboxes
            $currentSemesters = $enrollModel->getStudentEnrollments($studentId);

            // 1. Add new ones
            foreach (array_diff($newSemesters, $currentSemesters) as $semId) {
                $enrollModel->enroll($studentId, $semId);
            }

            // 2. Remove unchecked ones (with safety check)
            foreach (array_diff($currentSemesters, $newSemesters) as $semId) {
                if ($gradeModel->hasGradesInSemester($studentId, $semId)) {
                    $_SESSION['warning'] = "Some enrollments weren't removed because grades exist.";
                } else {
                    $enrollModel->unenroll($studentId, $semId);
                }
            }
            header("Location: index.php?page=admin_enrollments&student_id=$studentId");
            exit;
        }

        $semesters = (new Semester($this->db))->getAll();
        $studentEnrollments = $studentId ? $enrollModel->getStudentEnrollments($studentId) : [];
        require_once 'views/admin/enrollments.php';
    }

    // --- COURSE & ASSIGNMENT LOGIC ---
    public function manageCourses() {
        require_once 'models/Course.php';
        $courseModel = new Course($this->db);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validation: credits > 0
            if ($_POST['credits'] <= 0) {
                $_SESSION['error'] = "Credits must be greater than zero.";
            } else {
                $courseModel->create($_POST['semester_id'], $_POST['name'], $_POST['credits']);
            }
            header('Location: index.php?page=admin_courses');
            exit;
        }
        $courses = $courseModel->getAll();
        require_once 'views/admin/courses.php';
    }
}
