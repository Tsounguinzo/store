<?php

@include 'config.php';

session_start();

if(!isset($_SESSION['user_id'])){
   header('location:login.php');
}

$user_id = $_SESSION['user_id'];

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE id = ?");
   $delete_cart_item->execute([$delete_id]);
   header('location:cart.php');
}

if(isset($_GET['delete_all'])){
   $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
   $delete_cart_item->execute([$user_id]);
   header('location:cart.php');
}

if(isset($_POST['update_qty'])){
   $cart_id = $_POST['cart_id'];
   $p_qty = $_POST['p_qty'];
   $p_qty = htmlspecialchars($p_qty);
   $update_qty = $conn->prepare("UPDATE `cart` SET quantity = ? WHERE id = ?");
   $update_qty->execute([$p_qty, $cart_id]);
   $message[] = 'cart quantity updated';
}

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
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<section class="shopping-cart">

   <h1 class="title">products added</h1>

   <div class="box-container">

   <?php
      $grand_total = 0;
      $select_cart = $conn->prepare("SELECT cart.* , products.quantity AS qty FROM `cart` INNER JOIN `products` ON cart.pid = products.id WHERE user_id = ?");
      $select_cart->execute([$user_id]);
      if($select_cart->rowCount() > 0){
         while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){ 
   ?>
   <form action="" method="POST" class="box">
      <a href="cart.php?delete=<?= $fetch_cart['id']; ?>" class="fas fa-times" onclick="return confirm('delete this from cart?');"></a>
      <a href="view_page.php?pid=<?= $fetch_cart['pid']; ?>" class="fas fa-eye"></a>
      <img src="uploaded_img/<?= $fetch_cart['image']; ?>" alt="">
      <div class="name"><?= $fetch_cart['name']; ?></div>
      <div class="price">$<?= $fetch_cart['price']; ?></div>
      <input type="hidden" name="cart_id" value="<?= $fetch_cart['id']; ?>">

          <?php if(($fetch_cart['qty'] > 0)): ?>
      <div class="flex-btn">
              <input type="number" min="1" max="<?= $fetch_cart['qty']; ?>" value="<?= $fetch_cart['quantity']; ?>" name="p_qty" class="qty">
              <input type="submit" value="update" name="update_qty" class="option-btn">
      </div>
      <div><input type="text" value="<?=$fetch_cart['qty']." left"?>" class="btn" name="qty_left" readonly></div>
             <?php else: ?>
      <div><input type="text" value="Out Of Stock" class="btn" name="qty_left" readonly></div>
             <?php endif; ?>

      <div class="sub-total"> sub total :
          <span>$<?= $sub_total = ($fetch_cart['quantity'] <= $fetch_cart['qty'])? ($fetch_cart['price'] * $fetch_cart['quantity']) : 0;?></span> </div>
   </form>
   <?php
      $grand_total += $sub_total;
      }
   }else{
      echo '<p class="empty">your cart is empty</p>';
   }
   ?>
   </div>

   <div class="cart-total">
      <p>grand total : <span>$<?= $grand_total; ?></span></p>
      <a href="shop.php" class="option-btn">continue shopping</a>
      <a href="cart.php?delete_all" class="delete-btn <?= ($grand_total > 1)?'':'disabled'; ?>">delete all</a>
      <a href="checkout.php" class="btn <?= ($grand_total > 1)?'':'disabled'; ?>">proceed to checkout</a>
   </div>

</section>








<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>