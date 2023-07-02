<?php

use Models\Cart;

session_start();

require_once '../../../config/config.php';
require_once '../Models/Cart.php';

if (!isset($_SESSION['user_id'])) {
    header('location:login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$cart = new Cart($conn, $user_id);

if (isset($_GET['delete'])) {
    if ($cart->deleteCartItem($_GET['delete'])) {
        header('location:cart.php');
        exit;
    }
}

if (isset($_GET['delete_all'])) {
    if ($cart->deleteAllCartItem()) {
        header('location:cart.php');
        exit;
    }
}

if (isset($_POST['update_qty'])) {
    $cart_id = $_POST['cart_id'];
    $p_qty = htmlspecialchars($_POST['p_qty']);

    if ($cart->updateQuantity($cart_id, $p_qty)) {
        $message[] = 'Cart quantity updated';
    }
}

$items = $cart->getAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>shopping cart</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="/../../public/css/style.css">

</head>
<body>

<?php include 'Partials/header.php'; ?>

<section class="shopping-cart">

    <h1 class="title">products added</h1>

    <div class="box-container">

        <?php
        // Logic and DB operations
        $grand_total = 0;
        $select_cart = $conn->prepare("SELECT cart.*, products.quantity AS qty FROM `cart` INNER JOIN `products` ON cart.pid = products.id WHERE user_id = ?");
        $select_cart->execute([$user_id]);

        // Initialize the items as empty array if no rows found
        $items = $select_cart->fetchAll(PDO::FETCH_ASSOC);

        foreach ($items as $item) {
            $sub_total = ($item['quantity'] <= $item['qty']) ? ($item['price'] * $item['quantity']) : 0;
            $grand_total += $sub_total;
        }
        ?>
        <!-- HTML -->
        <?php if (!empty($items)): ?>
            <?php foreach ($items as $item):
                $sub_total = ($item['quantity'] <= $item['qty']) ? ($item['price'] * $item['quantity']) : 0;
            ?>
                <form action="" method="POST" class="box">
                    <a href="cart.php?delete=<?= $item['id']; ?>" class="fas fa-times" onclick="return confirm('delete this from cart?');"></a>
                    <a href="view_page.php?pid=<?= $item['pid']; ?>" class="fas fa-eye"></a>
                    <img src="uploaded_img/<?= $item['image']; ?>" alt="">
                    <div class="name"><?= $item['name']; ?></div>
                    <div class="price">$<?= $item['price']; ?></div>
                    <input type="hidden" name="cart_id" value="<?= $item['id']; ?>">
                    <?php if ($item['qty'] > 0): ?>
                        <div class="flex-btn">
                            <input type="number" min="1" max="<?= $item['qty']; ?>" value="<?= $item['quantity']; ?>" name="p_qty" class="qty">
                            <input type="submit" value="update" name="update_qty" class="option-btn">
                        </div>
                        <div><input type="text" value="<?= $item['qty'] . " left" ?>" class="btn" name="qty_left" readonly></div>
                    <?php else: ?>
                        <div><input type="text" value="Out Of Stock" class="btn" name="qty_left" readonly></div>
                    <?php endif; ?>
                    <div class="sub-total"> sub total :
                        <span>$<?= $sub_total ?></span>
                    </div>
                </form>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="empty">your cart is empty</p>
        <?php endif; ?>


        <div class="cart-total">
        <p>grand total : <span>$<?= $grand_total; ?></span></p>
        <a href="shop.php" class="option-btn">continue shopping</a>
        <a href="cart.php?delete_all" class="delete-btn <?= ($grand_total > 1)?'':'disabled'; ?>">delete all</a>
        <a href="checkout.php" class="btn <?= ($grand_total > 1)?'':'disabled'; ?>">proceed to checkout</a>
    </div>

</section>

<?php include 'Partials/footer.php'; ?>

<script src="/../../public/js/script.js"></script>

</body>
</html>
