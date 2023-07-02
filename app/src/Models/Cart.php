<?php

namespace Models;

class Cart
{

    private $conn;
    private $user_id;

    public function __construct($db, $user_id) {
        $this->conn = $db;
        $this->user_id = $user_id;
    }

    public function deleteCartItem($delete_id) {
        $stmt = $this->conn->prepare("DELETE FROM `cart` WHERE id = ?");
        return $stmt->execute([$delete_id]);
    }

    public function deleteAllCartItem() {
        $stmt = $this->conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
        return $stmt->execute([$this->user_id]);
    }

    public function updateQuantity($cart_id, $quantity) {
        $stmt = $this->conn->prepare("UPDATE `cart` SET quantity = ? WHERE id = ?");
        return $stmt->execute([$quantity, $cart_id]);
    }

    public function getAll() {
        $stmt = $this->conn->prepare("SELECT cart.* , products.quantity AS qty FROM `cart` INNER JOIN `products` ON cart.pid = products.id WHERE user_id = ?");
        $stmt->execute([$this->user_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function isInCart($product_name) {
        $check_cart_numbers = $this->conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
        $check_cart_numbers->execute([$product_name, $this->user_id]);
        return $check_cart_numbers->rowCount() > 0;
    }

    public function addToCart($post) {

        $pid = $this->sanitize($post['pid']);
        $p_name = $this->sanitize($post['p_name']);
        $p_price = $this->sanitize($post['p_price']);
        $p_image = $this->sanitize($post['p_image']);
        $p_qty = $this->sanitize($post['p_qty']);


        $cartItem = $this->getCartItem($p_name);
        if ($cartItem !== null) {
            $message[] = 'Item is already in the cart.';
        } else {
            $waitlistItem = $this->getWaitlistItem($p_name);
            if ($waitlistItem !== null) {
                $this->deleteWaitlistItem($p_name);
            }

            $this->insertCartItem($pid, $p_name, $p_price, $p_qty, $p_image);
            $message[] = 'Item added to the cart successfully.';
        }
    }

    private function sanitize($input){
        return htmlspecialchars($input);
    }

    private function getCartItem($p_name)
    {
        $query = $this->conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
        $query->execute([$p_name, $this->user_id]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    private function getWaitlistItem($p_name)
    {
        $query = $this->conn->prepare("SELECT * FROM `waitlist` WHERE name = ? AND user_id = ?");
        $query->execute([$p_name, $this->user_id]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    private function deleteWaitlistItem($p_name)
    {
        $query = $this->conn->prepare("DELETE FROM `waitlist` WHERE name = ? AND user_id = ?");
        $query->execute([$p_name, $this->user_id]);
    }

    private function insertCartItem($pid, $p_name, $p_price, $p_qty, $p_image)
    {
        $query = $this->conn->prepare("INSERT INTO `cart`(user_id, pid, name, price, quantity, image) VALUES(?,?,?,?,?,?)");
        $query->execute([$this->user_id, $pid, $p_name, $p_price, $p_qty, $p_image]);
    }
}