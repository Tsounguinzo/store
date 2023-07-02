<?php

use Handlers\ProductHandler;

require_once 'vendor/autoload.php';

require_once 'config/config.php';

session_start();

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$productHandler = new ProductHandler($conn, $_SESSION);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    /*
    if (!isset($user_id)) {
        header('Location: login.php');
        exit();
    }
*/
    $productHandler->handleRequest($_POST, $message);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category</title>

    <!-- Font Awesome CDN link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- Custom CSS file link -->
    <link rel="stylesheet" href="/public/css/style.css">

</head>
<body>

<?php require_once 'Partials/header.php'; ?>


<section class="quick-view">

    <h1 class="title">quick view</h1>

    <?php
    $fetch_products = [];
    try {
    $pid = $_GET['pid'];
    $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = :product_id");
    $select_products->execute([':product_id' => $pid]);
    $fetch_products = $select_products->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log($e->getMessage());
    }

    if (empty($fetch_products)):
        echo '<p class="empty">No products added yet!</p>';
    else:
    foreach ($fetch_products as $fetch_product):
    $quantity = $fetch_products['quantity'];
    $isAvailable = $quantity > 0;
    $productID = htmlspecialchars($fetch_products['id']);
    $productName = htmlspecialchars($fetch_products['name']);
    $productPrice = htmlspecialchars($fetch_products['price']);
    $productDetails = htmlspecialchars($fetch_products['details']);
    $productImage = htmlspecialchars($fetch_products['image']);
    $quantityLeft = $isAvailable ? $quantity . ' left' : 'Out Of Stock';
    $message = $isAvailable ? 'add to cart' : 'add to waitlist';
    $messageType = $isAvailable ? 'btn' : 'option-btn';
    $formAction = $isAvailable ? 'add_to_cart' : 'add_to_waitlist';
    ?>
            <form action="" class="box" method="POST">
                <div class="price">$<span><?= $productPrice; ?></span></div>
                <img src="/public/uploaded_img/<?= $productImage; ?>" alt="">
                <div class="name"><?= $productName; ?></div>
                <div class="details"><?= $productDetails; ?></div>
                <input type="hidden" name="pid" value="<?= $productID; ?>">
                <input type="hidden" name="p_name" value="<?= $productName; ?>">
                <input type="hidden" name="p_price" value="<?= $productPrice; ?>">
                <input type="hidden" name="p_image" value="<?= $productImage; ?>">

                <?php if ($isAvailable): ?>
                    <input type="number" min="1" max="<?= $quantity; ?>" value="1" name="p_qty" class="qty">
                <?php endif; ?>

                <input type="text" value="<?= $quantityLeft; ?>" class="btn" name="qty_left" readonly>

                <input type="submit" value="<?= $message; ?>" name="<?= $formAction; ?>" class="<?= $messageType; ?>">
            </form>
        <?php
            endforeach;
        endif;
        ?>
</section>

<?php require_once 'Partials/footer.php'; ?>

<script src="/public/js/script.js"></script>

</body>
</html>
