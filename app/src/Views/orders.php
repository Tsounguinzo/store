<?php

require_once '../../../config/config.php';

session_start();

if(!isset($_SESSION['user_id'])){
    header('location:login.php');
    exit; // It's important to call exit() here so the script doesn't continue to run
}

$order = new Order($conn);
$orders = $order->getOrdersByUserId($_SESSION['user_id']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>orders</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="../../../public/css/style.css">
</head>
<body>

<?php include 'Partials/header.php'; ?>

<section class="placed-orders">
    <h1 class="title">placed orders</h1>
    <div class="box-container">
        <?php

        if (empty($orders)):
            echo '<p class="empty">no orders placed yet!</p>';
        else:
            foreach ($orders as $order):
                ?>
                <div class="box">
                    <p> placed on : <span><?= $order['placed_on']; ?></span> </p>
                    <p> name : <span><?= $order['name']; ?></span> </p>
                    <p> number : <span><?= $order['number']; ?></span> </p>
                    <p> email : <span><?= $order['email']; ?></span> </p>
                    <p> address : <span><?= $order['address']; ?></span> </p>
                    <p> payment method : <span><?= $order['method']; ?></span> </p>
                    <p> your orders : <span><?= $order['total_products']; ?></span> </p>
                    <p> total price : <span>$<?= $order['total_price']; ?>/-</span> </p>
                    <p> payment status : <span style="color:<?php if($order['payment_status'] == 'pending'){ echo 'red'; }else{ echo 'green'; }; ?>"><?= $order['payment_status']; ?></span> </p>
                </div>
        <?php
                endforeach;
            endif;
        ?>
    </div>
</section>

<?php include 'Partials/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>