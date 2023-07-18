<?php

require_once '../../../config/config.php';

session_start();

if(isset($_SESSION['user_id'])){
    $user_id = $_SESSION['user_id'];
}

if(isset($_POST['add_to_waitlist'])){

    if(!isset($user_id)){
        header('location:login.php');
    }

   $pid = $_POST['pid'];
   $pid = htmlspecialchars($pid);
   $p_name = $_POST['p_name'];
   $p_name = htmlspecialchars($p_name);
   $p_price = $_POST['p_price'];
   $p_price = htmlspecialchars($p_price);
   $p_image = $_POST['p_image'];
   $p_image = htmlspecialchars($p_image);

   $check_waitlist_numbers = $conn->prepare("SELECT * FROM `waitlist` WHERE name = ? AND user_id = ?");
   $check_waitlist_numbers->execute([$p_name, $user_id]);

   $check_cart_numbers = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
   $check_cart_numbers->execute([$p_name, $user_id]);

   if($check_waitlist_numbers->rowCount() > 0){
      $message[] = 'already added to waitlist!';
   }elseif($check_cart_numbers->rowCount() > 0){
      $message[] = 'already added to cart!';
   }else{
      $insert_waitlist = $conn->prepare("INSERT INTO `waitlist`(user_id, pid, name, price, image) VALUES(?,?,?,?,?)");
      $insert_waitlist->execute([$user_id, $pid, $p_name, $p_price, $p_image]);
      $message[] = 'added to waitlist!';
   }

}

if(isset($_POST['add_to_cart'])){

    if(!isset($user_id)){
        header('location:login.php');
    }

   $pid = $_POST['pid'];
   $pid = htmlspecialchars($pid);
   $p_name = $_POST['p_name'];
   $p_name = htmlspecialchars($p_name);
   $p_price = $_POST['p_price'];
   $p_price = htmlspecialchars($p_price);
   $p_image = $_POST['p_image'];
   $p_image = htmlspecialchars($p_image);
   $p_qty = $_POST['p_qty'];
   $p_qty = htmlspecialchars($p_qty);

   $check_cart_numbers = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
   $check_cart_numbers->execute([$p_name, $user_id]);

   if($check_cart_numbers->rowCount() > 0){
      $message[] = 'already added to cart!';
   }else{

      $check_waitlist_numbers = $conn->prepare("SELECT * FROM `waitlist` WHERE name = ? AND user_id = ?");
      $check_waitlist_numbers->execute([$p_name, $user_id]);

      if($check_waitlist_numbers->rowCount() > 0){
         $delete_waitlist = $conn->prepare("DELETE FROM `waitlist` WHERE name = ? AND user_id = ?");
         $delete_waitlist->execute([$p_name, $user_id]);
      }

      $insert_cart = $conn->prepare("INSERT INTO `cart`(user_id, pid, name, price, quantity, image) VALUES(?,?,?,?,?,?)");
      $insert_cart->execute([$user_id, $pid, $p_name, $p_price, $p_qty, $p_image]);
      $message[] = 'added to cart!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>search page</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<section class="search-form">

   <form action="" method="POST">
      <input type="text" class="box" name="search_box" placeholder="Recherche de produits...">
      <input type="submit" name="search_btn" value="search" class="btn">
   </form>

</section>
<section class="products" style="padding-top: 0; min-height:100vh;">

   <div class="box-container">

   <?php
      if(isset($_POST['search_btn'])){
      $search_box = $_POST['search_box'];
      $search_box = htmlspecialchars($search_box);
      $select_products = $conn->prepare("SELECT * FROM `products` WHERE name LIKE '%{$search_box}%' OR category LIKE '%{$search_box}%'");
      $select_products->execute();
      if($select_products->rowCount() > 0){
         while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){ 
   ?>
   <form action="" class="box" method="POST">
      <div class="price">$<span><?= $fetch_products['price']; ?></span></div>
      <a href="view_page.php?pid=<?= $fetch_products['id']; ?>" class="fas fa-eye"></a>
      <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="">
      <div class="name"><?= $fetch_products['name']; ?></div>
      <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
      <input type="hidden" name="p_name" value="<?= $fetch_products['name']; ?>">
      <input type="hidden" name="p_price" value="<?= $fetch_products['price']; ?>">
      <input type="hidden" name="p_image" value="<?= $fetch_products['image']; ?>">

       <?php if(($fetch_products['quantity'] > 0)): ?>
           <input type="number" min="1" max="<?= $fetch_products['quantity']; ?>" value="1" name="p_qty" class="qty">
       <?php endif; ?>

       <input type="text"
              value="<?= ($fetch_products['quantity'] > 0)? $fetch_products['quantity']." restant(s)" : "rupture de stock" ; ?>"
              class="btn" name="qty_left" readonly
       >

       <?php if(($fetch_products['quantity'] <= 0)): ?>
           <input type="submit" value="être Notifié" class="option-btn" name="add_to_waitlist">
       <?php else: ?>
           <input type="submit" value="Ajouter au panier" name="add_to_cart" class="btn">
       <?php endif; ?>
   </form>
   <?php
         }
      }else{
         echo '<p class="empty">Aucun produit de ce nom</p>';
      }
      
   }
   ?>

   </div>

</section>






<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>