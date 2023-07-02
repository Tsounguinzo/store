<?php

namespace Models;

class Product
{
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getProducts() {
        $select_products = $this->conn->prepare("SELECT * FROM `products`");
        $select_products->execute();
        return $select_products;
    }
}