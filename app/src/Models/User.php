<?php

namespace Models;

use PDOException;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class User
{
    private $conn;
    private $logger;
    public $user_id;

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
        $row = $this->user_email_exist($email);
        if ($row) {
            if (password_verify(htmlspecialchars($password), $row['password'])) {
                return $row;
            }
        }

        return false;
    }

    public function register($post, $file){
        if ($post['pass'] !== $post['cpass']){
            $this->logger->warning("Confirm password not matched!");
            return "confirm password not matched!";
        }
        if($this->user_email_exist($post['email'])) {
            $this->logger->warning("User email already exist!");
            return 'user email already exist!';
        }
        $image = null;
        if (isset($file['image']) && $file['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $image = $file['image']['name'];
            $image = $this->sanitizeInput($image);
            $image_size = $file['image']['size'];
            $image_tmp_name = $file['image']['tmp_name'];
            $image_folder = __DIR__.'/uploaded_img/'.$image;
            if($image_size > 2000000){
                $this->logger->warning("Image size is too large!");
                return 'image size is too large!';
            }
            move_uploaded_file($image_tmp_name, $image_folder);
        } else {
            $this->logger->info("No image was submitted.");
        }
        try {
            $query = "INSERT INTO `users` (`name`, `email`, `password`, `image`) VALUES (:name, :email, :password, :image)";
            $stmt = $this->conn->prepare($query);

            $password_hash = password_hash($post['pass'], PASSWORD_DEFAULT);
            $stmt->execute([
                ':name' => $this->sanitizeInput($post['name']),
                ':email' => $this->sanitizeInput($post['email']),
                ':password' => $password_hash,
                ':image' => $image, // Here we save the image name, not the path
            ]);
        } catch (PDOException $e) {
            $this->logger->error('Registration failed: ' . $e->getMessage());
            return 'Registration failed';
        }
        $this->logger->info('Registration successful for email: ' . $post['email']);
        return 'Registration successful';
    }

    public function login($email, $password){
        $user = $this->authenticate($email, $password);
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

    private function user_email_exist($email){
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
        $image = $_FILES['image'];

        $user = $this->getUserById($this->user_id);
        if (!$user) {
            $this->logger->warning("User not found!");
            return 'User not found!';
        }

        if (!empty($name)) {
            $user['name'] = $this->sanitizeInput($name);
        }

        if (!empty($email)) {
            if ($this->userEmailExists($email)) {
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

        if (isset($image['name']) && $image['error'] !== UPLOAD_ERR_NO_FILE) {
            $image_name = $image['name'];
            $image_name = $this->sanitizeInput($image_name);
            $image_size = $image['size'];
            $image_tmp_name = $image['tmp_name'];
            $image_folder = __DIR__ . '/uploaded_img/' . $image_name;

            if ($image_size > 2000000) {
                $this->logger->warning("Image size is too large!");
                return 'Image size is too large!';
            }

            if (move_uploaded_file($image_tmp_name, $image_folder)) {
                if ($user['image'] !== null) {
                    unlink(__DIR__ . '/uploaded_img/' . $user['image']);
                }
                $user['image'] = $image_name;
            } else {
                $this->logger->warning("Failed to upload image!");
                return 'Failed to upload image!';
            }
        }

        try {
            $query = "UPDATE `users` SET name = :name, email = :email, password = :password, image = :image WHERE id = :user_id";
            $stmt = $this->conn->prepare($query);

            $stmt->execute([
                ':name' => $user['name'],
                ':email' => $user['email'],
                ':password' => $user['password'],
                ':image' => $user['image'],
                ':user_id' => $this->user_id,
            ]);

            $this->logger->info('Profile updated successfully!');
            return 'Profile updated successfully!';
        } catch (PDOException $e) {
            $this->logger->error('Failed to update profile: ' . $e->getMessage());
            return 'Failed to update profile!';
        }
    }

}