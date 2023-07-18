<?php

namespace Models;

use PDO;
use PDOException;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class User
{
    private $conn;
    private $logger;
    private $user_id;

    public function __construct($db) {
        $this->conn = $db;
        if(isset($_SESSION['user_id'])){
            $this->user_id = $_SESSION['user_id'];
        }

        // create a log channel
        $this->logger = new Logger('UserModel');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/app.log', Logger::DEBUG));
    }

    public function authenticate($email, $password) {
        $row = $this->user_exist($this->sanitizeInput($email));

        if ($row) {
            if (password_verify($this->sanitizeInput($password), $row['password'])) {
                return $row;
            }
        }
        return false;
    }

    public function register(): string
    {
        if ($_POST['pass'] !== $_POST['cpass']){
            $this->logger->warning("Confirm password not matched!");
            return "confirm password not matched!";
        }
        if($this->user_exist($_POST['email'])) {
            $this->logger->warning("User email already exist!");
            return 'user email already exist!';
        }

        if(!empty($_POST['error_message'])) {
            return $this->sanitizeInput($_POST['error_message']);
        }

        try {
            $query = "INSERT INTO `users` (`name`, `email`, `number`, `password`) VALUES (:name, :email, :number, :password)";
            $stmt = $this->conn->prepare($query);

            $password_hash = password_hash($this->sanitizeInput($_POST['pass']), PASSWORD_DEFAULT);
            $stmt->execute([
                ':name' => $this->sanitizeInput($_POST['name']),
                ':email' => $this->sanitizeInput($_POST['email']),
                ':number' => $this->sanitizeInput($_POST['tel']),
                ':password' => $password_hash
            ]);
        } catch (PDOException $e) {
            $this->logger->error('Registration failed: ' . $e->getMessage());
            return 'Registration failed';
        }
        $this->logger->info('Registration successful for email: ' . $_POST['email']);
        return 'Registration successful';
    }

    public function login($email, $password): bool
    {
        $user = $this->authenticate();
        if ($user) {
            session_start();
            $_SESSION['user_id'] = $user['id']; // Assuming user row contains 'id'
            return true;
        } else {
            return false;
        }
    }

    public function logout(){
        session_start();
        session_unset();
        session_destroy();
    }

    private function user_exist($email){
        $query = "SELECT * FROM `users` WHERE email=:email";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([":email" => $this->sanitizeInput($email)]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return empty($row) ? false : $row;
    }

    public function redirectIfNotLoggedIn() {
        if(!isset($this->user_id)) {
            header('location:login.php');
        }
    }

    public function sanitizeInput($input) {
        return htmlspecialchars($input);
    }

    public function updateProfile()
    {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['old_pass'];
        $user = $this->getUserInfo();

        if (!empty($name)) {
            $user['name'] = $this->sanitizeInput($name);
        }

        if (!empty($email)) {
            if ($email != $user["email"] && $this->user_exist($email)) {
                $this->logger->warning("User email already exists!");
                return 'User email already exists!';
            }
            $user['email'] = $this->sanitizeInput($email);
        }

        if (!empty($password)) {
            if (!password_verify(htmlspecialchars($password), $user['password'])) {
                $this->logger->warning("Incorrect old password!");
                return 'Incorrect old password!';
            }
            $newPassword = $this->sanitizeInput($_POST['new_pass']);
            $confirmPassword = $this->sanitizeInput($_POST['confirm_pass']);
            if ($newPassword !== $confirmPassword) {
                $this->logger->warning("Confirm password not matched!");
                return 'Confirm password not matched!';
            }
            $user['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        try {
            $query = "UPDATE `users` SET name = :name, email = :email, password = :password WHERE id = :user_id";
            $stmt = $this->conn->prepare($query);

            $stmt->execute([
                ':name' => $user['name'],
                ':email' => $user['email'],
                ':password' => $user['password'],
                ':user_id' => $this->user_id,
            ]);

            $this->logger->info('Profile updated successfully!');
            return 'Profile updated successfully!';
        } catch (PDOException $e) {
            $this->logger->error('Failed to update profile: ' . $e->getMessage());
            return 'Failed to update profile!';
        }
    }

    private function getUserInfo()
    {
        $query = "SELECT * FROM `users` WHERE id=:user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([":user_id" => $this->sanitizeInput($this->user_id)]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


}