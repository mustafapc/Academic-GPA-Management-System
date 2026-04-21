
<?php
require_once 'config.php';

class User {

    public static function findByEmail($email) {
        $pdo = getPDO();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public static function create($name, $email, $password, $role) {
        $pdo = getPDO();
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare(
            "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)"
        );

        return $stmt->execute([$name, $email, $hash, $role]);
    }

    public static function updatePassword($id, $password) {
        $pdo = getPDO();
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare(
            "UPDATE users SET password = ? WHERE id = ?"
        );

        return $stmt->execute([$hash, $id]);
    }
}
