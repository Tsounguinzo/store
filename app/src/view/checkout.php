<?php

require_once '../../../config/config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
};

if(isset($_POST['order'])){

   $name = $_POST['name'];
   $name = htmlspecialchars($name);
   $number = $_POST['number'];
   $number = htmlspecialchars($number);
   $email = $_POST['email'];
   $email = htmlspecialchars($email);
   $method = $_POST['method'];
   $method = htmlspecialchars($method);
   $address = 'flat no. '. $_POST['flat'] .' '. $_POST['street'] .' '. $_POST['city'] .' '. $_POST['state'] .' '. $_POST['country'] .' - '. $_POST['pin_code'];
   $address = htmlspecialchars($address);
   $placed_on = date('d-M-Y');

   $cart_total = 0;
   $cart_products[] = '';

   $cart_query = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
   $cart_query->execute([$user_id]);
   if($cart_query->rowCount() > 0){
      while($cart_item = $cart_query->fetch(PDO::FETCH_ASSOC)){
         $cart_products[] = $cart_item['name'].' ( '.$cart_item['quantity'].' )';
         $sub_total = ($cart_item['price'] * $cart_item['quantity']);
         $cart_total += $sub_total;
      };
   };

   $total_products = implode(', ', $cart_products);

   $order_query = $conn->prepare("SELECT * FROM `orders` WHERE name = ? AND number = ? AND email = ? AND method = ? AND address = ? AND total_products = ? AND total_price = ?");
   $order_query->execute([$name, $number, $email, $method, $address, $total_products, $cart_total]);

   if($cart_total == 0){
      $message[] = 'your cart is empty';
   }elseif($order_query->rowCount() > 0){
      $message[] = 'order placed already!';
   }else{
      $insert_order = $conn->prepare("INSERT INTO `orders`(user_id, name, number, email, method, address, total_products, total_price, placed_on) VALUES(?,?,?,?,?,?,?,?,?)");
      $insert_order->execute([$user_id, $name, $number, $email, $method, $address, $total_products, $cart_total, $placed_on]);
       $products = $conn->prepare("SELECT pid, quantity FROM `cart` WHERE user_id = ?");
       $products->execute([$user_id]);
       $products = $products->fetchAll(PDO::FETCH_ASSOC);

       foreach ($products as $product){
           $previous_qty = $conn->prepare("SELECT quantity FROM `products` WHERE id=:id");
           $previous_qty->execute([':id' => $product['pid']]);
           $previous_qty = $previous_qty->fetch(PDO::FETCH_ASSOC);

            $update_product_qty = $conn->prepare("UPDATE `products` SET quantity = :qty WHERE id=:id");
           $update_product_qty->execute([
                   ':qty'=> $previous_qty['quantity'] - $product['quantity'],
                   ':id'=>$product['pid']]);
       }


      $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
      $delete_cart->execute([$user_id]);

      $message[] = 'order placed successfully!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>checkout</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php require_once 'header.php'; ?>

<section class="display-orders">

   <?php
      $cart_grand_total = 0;
      $select_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
      $select_cart_items->execute([$user_id]);
      $fetch_cart_items = $select_cart_items->fetchAll(PDO::FETCH_ASSOC);

      $user = $conn->prepare("SELECT * FROM `users` WHERE id=?");
      $user->execute([$user_id]);
      $user = $user->fetch(PDO::FETCH_ASSOC);

      if(empty($fetch_cart_items)) {
          echo '<p class="empty">your cart is empty!</p>';
      } else {
          foreach ($fetch_cart_items as $fetch_cart_item) {
            $cart_total_price = ($fetch_cart_item['price'] * $fetch_cart_item['quantity']);
            $cart_grand_total += $cart_total_price;
   ?>
   <p> <?= $fetch_cart_item['name']; ?> <span>(<?= '$'.$fetch_cart_item['price'].' x '. $fetch_cart_item['quantity']; ?>)</span> </p>
   <?php
    }
      }
   ?>
   <div class="grand-total">grand total : <span>$<?= $cart_grand_total; ?></span></div>
</section>

<section class="checkout-orders">

   <form action="" method="POST">

      <h3>place your order</h3>

      <div class="flex">
         <div class="inputBox">
            <span>name :</span>
            <input type="text" value="<?=$user['name']?>" name="name" class="box" required>
         </div>
         <div class="inputBox">
            <span>number :</span>
            <input type="tel" value="<?=$user['number']?>" name="number"  class="box" required>
         </div>
         <div class="inputBox">
            <span>email :</span>
            <input type="email" name="email" value="<?=$user['email']?>" class="box" required>
         </div>
         <div class="inputBox">
            <span>mode de paiement :</span>
            <select name="method" class="box" required>
               <option value="cash on delivery" selected>espèce</option>
               <option value="mobile money">M-PESA</option>
               <option value="paypal">Orange Money</option>
            </select>
         </div>
         <div class="inputBox">
            <span>address line 01 :</span>
            <input type="text" name="flat" placeholder="e.g. flat number" class="box" required>
         </div>
         <div class="inputBox">
            <span>address line 02 :</span>
            <input type="text" name="street" placeholder="e.g. street name" class="box" required>
         </div>
         <div class="inputBox">
            <span>city :</span>
            <input type="text" name="city" placeholder="e.g. braza" class="box" required>
         </div>
         <div class="inputBox">
            <span>state :</span>
            <input type="text" name="state" placeholder="e.g. kin" class="box" required>
         </div>
         <div class="inputBox">
            <span>country :</span>
            <input type="text" name="country" placeholder="e.g. RDC" class="box" required>
         </div>
         <div class="inputBox">
            <span>pin code :</span>
            <input type="number" min="0" name="pin_code" placeholder="e.g. 123456" class="box" required>
         </div>
      </div>

      <input type="submit" name="order" class="btn <?= ($cart_grand_total > 1)?'':'disabled'; ?>" value="place order">

   </form>

</section>

<?php require_once 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>