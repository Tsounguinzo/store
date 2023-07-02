<?php

namespace Models;

class Waitlist
{
    private $conn;
    private $user_id;

    public function __construct($db, $user_id) {
        $this->conn = $db;
        $this->user_id = $user_id;
    }

    public function deleteWaitlistItem($delete_id)
    {
        $deleteWaitlistItem = $this->conn->prepare("DELETE FROM `waitlist` WHERE id = ?");
        $deleteWaitlistItem->execute([$delete_id]);
        header('location:waitlist.php');
    }

    public function deleteAllWaitlistItems()
    {
        $deleteWaitlistItem = $this->conn->prepare("DELETE FROM `waitlist` WHERE user_id = ?");
        $deleteWaitlistItem->execute([$this->user_id]);
        header('location:waitlist.php');
    }

    public function isInWaitlist($product_name) {
        $check_waitlist_numbers = $this->conn->prepare("SELECT * FROM `waitlist` WHERE name = ? AND user_id = ?");
        $check_waitlist_numbers->execute([$product_name, $this->user_id]);
        return $check_waitlist_numbers->rowCount() > 0;
    }

    public function addToWaitlist($pid, $p_name, $p_price, $p_image) {
        $insert_waitlist = $this->conn->prepare("INSERT INTO `waitlist`(user_id, pid, name, price, image) VALUES(?,?,?,?,?)");
        $insert_waitlist->execute([$this->user_id, $pid, $p_name, $p_price, $p_image]);
    }
}