<?php
header('Content-Type: application/json');
require_once '../config.php';
require_once '../models/Grade.php';
require_once '../models/GPA.php';
require_once '../models/Semester.php';

// 1. Security: Only Students allowed
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

$db = getDatabaseConnection();
$studentId = $_SESSION['user']['id']; // Use session ID for security

$gradeModel = new Grade($db);
$gpaModel = new GPA($db);
$semesterModel = new Semester($db);

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'current':
        // Get the active semester to show current performance
        $activeSemester = $semesterModel->getActive();
        if (!$activeSemester) {
            echo json_encode(['error' => 'No active semester found']);
            exit;
        }

        $grades = $gradeModel->getStudentGradesBySemester($studentId, $activeSemester['id']);
        $currentGPA = $gpaModel->getCurrentGPA($studentId, $activeSemester['id']);

        echo json_encode([
            'semester_label' => $activeSemester['label'],
            'grades' => $grades,
            'gpa' => number_format($currentGPA, 2)
        ]);
        break;

    case 'history':
        // Get all past semester GPA records
        $history = $gpaModel->getHistory($studentId);
        echo json_encode($history);
        break;

    case 'export':
        // CSV Export Logic
        $history = $gpaModel->getHistory($studentId);
        
        // Change header for file download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="gpa_history.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Column Headers
        fputcsv($output, ['Semester', 'Academic Year', 'GPA']);
        
        // Data Rows
        foreach ($history as $row) {
            fputcsv($output, [
                $row['label'],
                $row['academic_year'],
                number_format($row['gpa_value'], 2)
            ]);
        }
        
        fclose($output);
        exit; // Stop execution so no JSON is appended

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        break;
}
