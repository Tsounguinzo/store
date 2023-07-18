<?php

require_once '../../../vendor/autoload.php';

use Handlers\ProductHandler;

require_once '../../../config/config.php';
require_once '../../../scripts/populate_php_file.php';

$dataFilePath = '../Helpers/JSONData/fr/view.json';
$view_info = 'view_info';
populatePage($dataFilePath, $view_info);

session_start();

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$productHandler = new ProductHandler($conn, $_SESSION);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($user_id)) {
        header('Location: login.php');
        exit();
    }
    $productHandler->handleRequest($_POST, $message);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>quick view</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
<?php require_once 'header.php'; ?>

<section class="quick-view">
   <h1 class="title"><?=$view_info['title']?></h1>
   <?php
      $pid = $_GET['pid'];
      $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
      $select_products->execute([$pid]);
      $fetch_products = $select_products->fetchAll(PDO::FETCH_ASSOC);

   if (empty($fetch_products)) {
       $empty = $view_info['empty-text'];
       echo "<p class='empty'>$empty</p>";
   } else {
   foreach ($fetch_products as $fetch_product) {
   ?>
   <form action="" class="box" method="POST">
      <div class="price">$<span><?= $fetch_product['price']; ?></span></div>
      <img src="uploaded_img/<?= $fetch_product['image']; ?>" alt="">
      <div class="name"><?= $fetch_product['name']; ?></div>
      <div class="details"><?= $fetch_product['details']; ?></div>
      <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
      <input type="hidden" name="p_name" value="<?= $fetch_product['name']; ?>">
      <input type="hidden" name="p_price" value="<?= $fetch_product['price']; ?>">
      <input type="hidden" name="p_image" value="<?= $fetch_product['image']; ?>">

       <?php if(($fetch_product['quantity'] > 0)): ?>
           <input type="number" min="1" max="<?= $fetch_product['quantity']; ?>" value="1" name="p_qty" class="qty">
       <?php endif; ?>

       <input type="text"
              value="<?= ($fetch_product['quantity'] > 0)? $fetch_product['quantity']. " " . $view_info['left-txt'] : $view_info['finished-txt'] ; ?>"
              class="btn" name="qty_left" readonly
       >

       <?php if(($fetch_product['quantity'] <= 0)): ?>
           <input type="submit" value="<?=$view_info['add-to-waitlist']?>" class="option-btn" name="add_to_waitlist">
       <?php else: ?>
           <input type="submit" value="<?=$view_info['add-to-cart']?>" name="add_to_cart" class="btn">
       <?php endif; ?>
   </form>
   <?php
         }
      }
   ?>

</section>
<?php require_once 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>