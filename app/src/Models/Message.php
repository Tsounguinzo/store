<?php

namespace Models;

use PDOException;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Message
{
    private $conn;
    private $logger;
    private $user_id;

    public function __construct($db)
    {
        $this->conn = $db;

        session_start();
        if (isset($_SESSION['user_id'])) {
            $this->user_id = $_SESSION['user_id'];
        }

        // Create a log channel
        $this->logger = new Logger('MessageModel');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/app.log', Logger::DEBUG));
    }

    public function sendMessage()
    {

        $name = $this->sanitizeInput($_POST['name']);
        $email = $this->sanitizeInput($_POST['email']);
        $number = $this->sanitizeInput($_POST['number']);
        $message = $this->sanitizeInput($_POST['msg']);

        if (!isset($this->user_id)) {
            header('Location: login.php');
            exit();
        }

        try {
            $messageExists = $this->doesMessageExists($name, $email, $number, $message);
            if ($messageExists) {
                $this->logger->warning('Message already sent!');
                return 'Message already sent!';
            }

            $inserted = $this->insertMessage($name, $email, $number, $message);
            if ($inserted) {
                $this->logger->info('Message sent successfully!');
                return 'Message sent successfully!';
            } else {
                $this->logger->error('Failed to send message!');
                return 'Failed to send message!';
            }
        } catch (PDOException $e) {
            $this->logger->error('Failed to send message: ' . $e->getMessage());
            return 'Failed to send message!';
        }
    }

    private function doesMessageExists($name, $email, $number, $message)
    {
        $query = "SELECT COUNT(*) FROM `message` WHERE name = :name AND email = :email AND number = :number AND message = :message";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':name' => $this->sanitizeInput($name),
            ':email' => $this->sanitizeInput($email),
            ':number' => $this->sanitizeInput($number),
            ':message' => $this->sanitizeInput($message),
        ]);

        $rowCount = $stmt->fetchColumn();
        return $rowCount > 0;
    }

    private function insertMessage($name, $email, $number, $message)
    {
        $query = "INSERT INTO `message` (user_id, name, email, number, message) VALUES (:user_id, :name, :email, :number, :message)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':user_id' => $this->user_id,
            ':name' => $this->sanitizeInput($name),
            ':email' => $this->sanitizeInput($email),
            ':number' => $this->sanitizeInput($number),
            ':message' => $this->sanitizeInput($message),
        ]);

        return $stmt->rowCount() > 0;
    }

    private function sanitizeInput($input)
    {
        return htmlspecialchars($input);
    }

}
