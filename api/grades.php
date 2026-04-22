<?php
header('Content-Type: application/json');
require_once '../config.php';
require_once '../models/Grade.php';
require_once '../models/GPA.php';
require_once '../models/Assignment.php';
require_once '../models/Enrollment.php';

// 1. Security Check: Only Professors allowed
// Note: Ensure your session_start() is in config.php or here
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'professor') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

$db = getDatabaseConnection();
$gradeModel = new Grade($db);
$assignmentModel = new Assignment($db);
$enrollmentModel = new Enrollment($db);
$gpaModel = new GPA($db);

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'fetch_students':
        $assignmentId = $_GET['assignment_id'] ?? 0;
        
        // Security: Does this assignment belong to this professor?
        if (!$assignmentId || !$assignmentModel->isAssignedTo($assignmentId, $_SESSION['user']['id'])) {
            http_response_code(403);
            echo json_encode(['error' => 'Assignment not found or access denied']);
            exit;
        }

        // Fetch assignment details to get the semester_id
        $sql = "SELECT semester_id FROM assignments WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$assignmentId]);
        $assignment = $stmt->fetch(PDO::FETCH_ASSOC);

        // Get students enrolled in that semester
        $students = $enrollmentModel->getStudentsInSemester($assignment['semester_id']);
        
        // Get existing grades for this assignment
        $existingGrades = $gradeModel->getByAssignment($assignmentId);
        $gradeMap = [];
        foreach ($existingGrades as $g) {
            $gradeMap[$g['student_id']] = $g['score'];
        }

        // Merge: attach scores to the student list
        foreach ($students as &$student) {
            $student['score'] = $gradeMap[$student['id']] ?? '';
        }

        echo json_encode($students);
        break;

    case 'save_grades':
        $assignmentId = $_POST['assignment_id'] ?? 0;
        $grades = $_POST['grades'] ?? []; // Expecting array: [student_id => score]

        if (!$assignmentModel->isAssignedTo($assignmentId, $_SESSION['user']['id'])) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        // Get semester_id for GPA update
        $sql = "SELECT semester_id FROM assignments WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$assignmentId]);
        $semesterId = $stmt->fetchColumn();

        $successCount = 0;
        foreach ($grades as $studentId => $score) {
            // Validation: score must be 0-20 (or your local scale)
            if (is_numeric($score) && $score >= 0 && $score <= 20) {
                if ($gradeModel->save($studentId, $assignmentId, $score)) {
                    // CRITICAL: Recompute GPA for this student after every grade save
                    $gpaModel->saveRecord($studentId, $semesterId);
                    $successCount++;
                }
            }
        }

        echo json_encode(['success' => true, 'message' => "Updated $successCount grades"]);
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        break;
}
