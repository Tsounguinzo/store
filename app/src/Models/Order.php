<?php

namespace Models;

use PDOException;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Order
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
        $this->logger = new Logger('OrderModel');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/app.log', Logger::DEBUG));
    }

    public function placeOrder($name, $number, $email, $method, $address)
    {
        if (!isset($this->user_id)) {
            header('Location: login.php');
            exit();
        }

        try {
            $cartTotal = 0;
            $cartProducts = [];
            $cartQuery = $this->conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
            $cartQuery->execute([$this->user_id]);

            if ($cartQuery->rowCount() === 0) {
                $this->logger->warning('Your cart is empty');
                return 'Your cart is empty';
            }

            while ($cartItem = $cartQuery->fetch(PDO::FETCH_ASSOC)) {
                $cartTotalPrice = ($cartItem['price'] * $cartItem['quantity']);
                $cartTotal += $cartTotalPrice;
                $cartProducts[] = $cartItem['name'] . ' (' . $cartItem['quantity'] . ')';
            }

            $totalProducts = implode(', ', $cartProducts);

            if ($this->isOrderExists($name, $number, $email, $method, $address, $totalProducts, $cartTotal)) {
                $this->logger->warning('Order already placed!');
                return 'Order already placed!';
            }

            $placedOn = date('d-M-Y');
            if ($this->insertOrder($name, $number, $email, $method, $address, $totalProducts, $cartTotal, $placedOn)) {
                $this->deleteCartItems();
                $this->logger->info('Order placed successfully!');
                return 'Order placed successfully!';
            } else {
                $this->logger->error('Failed to place order!');
                return 'Failed to place order!';
            }
        } catch (PDOException $e) {
            $this->logger->error('Failed to place order: ' . $e->getMessage());
            return 'Failed to place order!';
        }
    }

    private function isOrderExists($name, $number, $email, $method, $address, $totalProducts, $totalPrice)
    {
        $query = "SELECT COUNT(*) FROM `orders` WHERE name = :name AND number = :number AND email = :email AND method = :method AND address = :address AND total_products = :total_products AND total_price = :total_price";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':name' => $this->sanitizeInput($name),
            ':number' => $this->sanitizeInput($number),
            ':email' => $this->sanitizeInput($email),
            ':method' => $this->sanitizeInput($method),
            ':address' => $this->sanitizeInput($address),
            ':total_products' => $this->sanitizeInput($totalProducts),
            ':total_price' => $this->sanitizeInput($totalPrice),
        ]);

        $rowCount = $stmt->fetchColumn();
        return $rowCount > 0;
    }

    private function insertOrder($name, $number, $email, $method, $address, $totalProducts, $totalPrice, $placedOn)
    {
        $query = "INSERT INTO `orders` (user_id, name, number, email, method, address, total_products, total_price, placed_on) VALUES (:user_id, :name, :number, :email, :method, :address, :total_products, :total_price, :placed_on)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':user_id' => $this->user_id,
            ':name' => $this->sanitizeInput($name),
            ':number' => $this->sanitizeInput($number),
            ':email' => $this->sanitizeInput($email),
            ':method' => $this->sanitizeInput($method),
            ':address' => $this->sanitizeInput($address),
            ':total_products' => $this->sanitizeInput($totalProducts),
            ':total_price' => $this->sanitizeInput($totalPrice),
            ':placed_on' => $placedOn,
        ]);

        return $stmt->rowCount() > 0;
    }

    private function deleteCartItems()
    {
        $query = "DELETE FROM `cart` WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$this->user_id]);
    }

    private function sanitizeInput($input)
    {
        return htmlspecialchars($input);
    }
}
