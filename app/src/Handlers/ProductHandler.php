<?php

namespace Handlers;
use Exception;

class ProductHandler
{
    private $conn;
    private $user_id;

    public function __construct($conn, $session) {
        $this->conn = $conn;
        $this->user_id = isset($session['user_id']) ? $session['user_id'] : null;
    }

    public function handleRequest($postData, &$message) {
        try {
            if ($this->user_id == null) {
                throw new Exception('User is not logged in.');
            }

            $pid = htmlspecialchars(isset($postData['pid']) ? $postData['pid'] : '');
            $p_name = htmlspecialchars(isset($postData['p_name']) ? $postData['p_name'] : '');
            $p_price = htmlspecialchars(isset($postData['p_price']) ? $postData['p_price'] : '');
            $p_image = htmlspecialchars(isset($postData['p_image']) ? $postData['p_image'] : '');

            if (empty($pid) || empty($p_name) || empty($p_price) || empty($p_image)) {
                throw new Exception('Missing required data.');
            }

            if (isset($postData['add_to_waitlist'])) {
                $stmt = $this->conn->prepare("SELECT COUNT(*) FROM `waitlist` WHERE name = :name AND user_id = :user_id");
                $stmt->execute([':name' => $p_name, ':user_id' => $this->user_id]);
                $exists = $stmt->fetchColumn();

                if ($exists) {
                    $message[] = 'Already added to waitlist!';
                } else {
                    $stmt = $this->conn->prepare("INSERT INTO `waitlist`(user_id, pid, name, price, image) VALUES(:user_id, :pid, :name, :price, :image)");
                    $stmt->execute([':user_id' => $this->user_id, ':pid' => $pid, ':name' => $p_name, ':price' => $p_price, ':image' => $p_image]);
                    $message[] = 'Added to waitlist!';
                }
            }

            if (isset($postData['add_to_cart'])) {
                $p_qty = htmlspecialchars(isset($postData['p_qty']) ? $postData['p_qty'] : '');

                if (empty($p_qty)) {
                    throw new Exception('Missing quantity.');
                }

                $stmt = $this->conn->prepare("SELECT COUNT(*) FROM `cart` WHERE name = :name AND user_id = :user_id");
                $stmt->execute([':name' => $p_name, ':user_id' => $this->user_id]);
                $exists = $stmt->fetchColumn();

                if ($exists) {
                    $message[] = 'Already added to cart!';
                } else {
                    $stmt = $this->conn->prepare("DELETE FROM `waitlist` WHERE name = :name AND user_id = :user_id");
                    $stmt->execute([':name' => $p_name, ':user_id' => $this->user_id]);

                    $stmt = $this->conn->prepare("INSERT INTO `cart`(user_id, pid, name, price, quantity, image) VALUES(:user_id, :pid, :name, :price, :quantity, :image)");
                    $stmt->execute([':user_id' => $this->user_id, ':pid' => $pid, ':name' => $p_name, ':price' => $p_price, ':quantity' => $p_qty, ':image' => $p_image]);
                    $message[] = 'Added to cart!';
                }
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            // Set a user-friendly error message based on the exception
            $message[] = $this->getFriendlyErrorMessage($e->getMessage());
        }

    }

    public function getFriendlyErrorMessage($exceptionMessage) {
        switch ($exceptionMessage) {
            case 'User is not logged in.':
                return 'You must be logged in to perform this action.';
            case 'Missing required data.':
                return 'Some required data is missing. Please check your inputs.';
            case 'Missing quantity.':
                return 'Please specify a quantity.';
            default:
                return 'There was an unexpected error processing your request. Please try again later.';

        }
    }
}