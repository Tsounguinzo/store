<?php

@include 'config.php';

session_start();

if(!isset($_SESSION['user_id'])){
   header('location:login.php');
}

$user_id = $_SESSION['user_id'];

if(isset($_POST['add_to_cart'])){

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

if(isset($_GET['delete'])){

   $delete_id = $_GET['delete'];
   $delete_waitlist_item = $conn->prepare("DELETE FROM `waitlist` WHERE id = ?");
   $delete_waitlist_item->execute([$delete_id]);
   header('location:waitlist.php');

}

if(isset($_GET['delete_all'])){

   $delete_waitlist_item = $conn->prepare("DELETE FROM `waitlist` WHERE user_id = ?");
   $delete_waitlist_item->execute([$user_id]);
   header('location:waitlist.php');

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>waitlist</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<section class="waitlist">

   <h1 class="title">products added</h1>

   <div class="box-container">

   <?php
      $grand_total = 0;
      $select_waitlist = $conn->prepare("SELECT * FROM `waitlist` INNER JOIN `products` ON waitlist.pid = products.id WHERE user_id = ?");
      $select_waitlist->execute([$user_id]);
      if($select_waitlist->rowCount() > 0){
         while($fetch_waitlist = $select_waitlist->fetch(PDO::FETCH_ASSOC)){ 
   ?>
   <form action="" method="POST" class="box">
      <a href="waitlist.php?delete=<?= $fetch_waitlist['id']; ?>" class="fas fa-times" onclick="return confirm('delete this from waitlist?');"></a>
      <a href="view_page.php?pid=<?= $fetch_waitlist['pid']; ?>" class="fas fa-eye"></a>
      <img src="uploaded_img/<?= $fetch_waitlist['image']; ?>" alt="">
      <div class="name"><?= $fetch_waitlist['name']; ?></div>
      <div class="price">$<?= $fetch_waitlist['price']; ?></div>

       <?php if(($fetch_waitlist['quantity'] > 0)): ?>
           <input type="number" min="1" max="<?= $fetch_waitlist['quantity']; ?>" value="1" name="p_qty" class="qty">
       <?php endif; ?>
      <input type="hidden" name="pid" value="<?= $fetch_waitlist['pid']; ?>">
      <input type="hidden" name="p_name" value="<?= $fetch_waitlist['name']; ?>">
      <input type="hidden" name="p_price" value="<?= $fetch_waitlist['price']; ?>">
      <input type="hidden" name="p_image" value="<?= $fetch_waitlist['image']; ?>">

       <input type="text"
              value="<?= ($fetch_waitlist['quantity'] > 0)? $fetch_waitlist['quantity']." left" : "Out Of Stock" ; ?>"
              class="btn" name="qty_left" readonly
       >
       <?php if(($fetch_waitlist['quantity'] > 0)): ?>
       <input type="submit" value="add to cart" name="add_to_cart" class="btn">
       <?php endif; ?>
   </form>
   <?php
      $grand_total += $fetch_waitlist['price'];
      }
   }else{
      echo '<p class="empty">your waitlist is empty</p>';
   }
   ?>
   </div>

   <div class="waitlist-total">
      <p>grand total : <span>$<?= $grand_total; ?>/-</span></p>
      <a href="shop.php" class="option-btn">continue shopping</a>
      <a href="waitlist.php?delete_all" class="delete-btn <?= ($grand_total > 1)?'':'disabled'; ?>">delete all</a>
   </div>

</section>








<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>