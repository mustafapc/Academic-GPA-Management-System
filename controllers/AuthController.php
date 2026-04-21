<?php
require_once 'models/User.php';

class AuthController {

    //  عرض login
    public function login() {
        require 'views/login.php';
    }

    //  معالجة login
    public function doLogin() {

        $email = sanitize($_POST['email']);
        $password = $_POST['password'];

        $user = User::findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {

            //  session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['last_activity'] = time();

            //  redirect حسب role
            if ($user['role'] === 'admin') {
                redirect('admin.dashboard');
            } elseif ($user['role'] === 'professor') {
                redirect('professor.grades');
            } else {
                redirect('student.dashboard');
            }

        } else {
            flash('error', 'Email ou mot de passe incorrect');
            redirect('login');
        }
    }

    //  logout
    public function logout() {
        session_destroy();
        redirect('login');
    }
}
