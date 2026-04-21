
<?php
require_once 'config.php';

//  timeout
checkSessionTimeout();

//  page
$page = $_GET['page'] ?? 'login';

//  Controllers
require_once 'controllers/AuthController.php';
require_once 'controllers/AdminController.php';
require_once 'controllers/ProfessorController.php';
require_once 'controllers/StudentController.php';

//  Routing
switch (true) {

    case ($page === 'login' || $page === 'logout'):
        $controller = new AuthController();
        break;

    case (str_starts_with($page, 'admin.')):
        requireRole('admin');
        $controller = new AdminController();
        break;

    case (str_starts_with($page, 'professor.')):
        requireRole('professor');
        $controller = new ProfessorController();
        break;

    case (str_starts_with($page, 'student.')):
        requireRole('student');
        $controller = new StudentController();
        break;

    default:
        redirect('login');
}

//  action
$parts = explode('.', $page);
$action = $parts[1] ?? 'login';

//  execute
if (!method_exists($controller, $action)) {
    die("❌ Method not found");
}

$controller->$action();
