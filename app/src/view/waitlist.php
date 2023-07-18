<?php

require_once '../../../vendor/autoload.php';

use Models\Cart;
use Models\Waitlist;

require_once '../../../config/config.php';
require_once '../../../scripts/populate_php_file.php';

$dataFilePath = '../Helpers/JSONData/fr/waitlist.json';
$waitlist_info = 'waitlist-info';
populatePage($dataFilePath, $waitlist_info);

session_start();

if(!isset($_SESSION['user_id'])){
    header('location:login.php');
}

$user_id = $_SESSION['user_id'];

$cart = new Cart($conn, $user_id);
$waitlist = new Waitlist($conn, $user_id);

if (isset($_POST['add_to_cart'])) {
    $cart->addToCart($_POST);
}

if (isset($_GET['delete'])) {
    $waitlist->deleteWaitlistItem($_GET['delete']);
}

if (isset($_GET['delete_all'])) {
    $waitlist->deleteAllWaitlistItems();
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
   
<?php require_once 'header.php'; ?>

<section class="waitlist">
    <h1 class="title"><?=$waitlist_info['title']?></h1>
    <div class="box-container">
        <?php
        $grand_total = 0;
        $select_waitlist = $conn->prepare("SELECT waitlist.*, products.quantity AS quantity FROM `waitlist` INNER JOIN `products` ON waitlist.pid = products.id WHERE user_id = ?");
        $select_waitlist->execute([$user_id]);
        $waitlist_items = $select_waitlist->fetchAll(PDO::FETCH_ASSOC);

        foreach ($waitlist_items as $waitlist_item) {
            $sub_total = ($waitlist_item['quantity'] > 0) ? ($waitlist_item['price'] * $waitlist_item['quantity']) : 0;
            $grand_total += $sub_total;
        }

        if (empty($waitlist_items)) {
            $empty = $waitlist_info['empty-text'];
           echo "<p class='empty'>$empty</p>";
        } else {
            foreach ($waitlist_items as $waitlist_item) {
                $sub_total = ($waitlist_item['quantity'] > 0) ? ($waitlist_item['price'] * $waitlist_item['quantity']) : 0;
                ?>
                <form action="" method="POST" class="box">
                    <a href="waitlist.php?delete=<?= $waitlist_item['id']; ?>" class="fas fa-times" onclick="return confirm('delete this from waitlist?');"></a>
                    <a href="view_page.php?pid=<?= $waitlist_item['pid']; ?>" class="fas fa-eye"></a>
                    <img src="uploaded_img/<?= $waitlist_item['image']; ?>" alt="">
                    <div class="name"><?= $waitlist_item['name']; ?></div>
                    <div class="price">$<?= $waitlist_item['price']; ?></div>

                    <?php if(($waitlist_item['quantity'] > 0)): ?>
                        <input type="number" min="1" max="<?= $waitlist_item['quantity']; ?>" value="1" name="p_qty" class="qty">
                    <?php endif; ?>
                    <input type="hidden" name="pid" value="<?= $waitlist_item['pid']; ?>">
                    <input type="hidden" name="p_name" value="<?= $waitlist_item['name']; ?>">
                    <input type="hidden" name="p_price" value="<?= $waitlist_item['price']; ?>">
                    <input type="hidden" name="p_image" value="<?= $waitlist_item['image']; ?>">

                    <input type="text"
                           value="<?= ($waitlist_item['quantity'] > 0)? $waitlist_item['quantity']. " " . $waitlist_info['left-txt'] : $waitlist_info['finished-txt'] ; ?>"
                           class="btn" name="qty_left" readonly
                    >
                    <?php if(($waitlist_item['quantity'] > 0)): ?>
                        <input type="submit" value="<?=$waitlist_info['add-to-cart']?>" name="add_to_cart" class="btn">
                    <?php endif; ?>
                </form>
                <?php
            }
        }
        ?>
    </div>

    <div class="waitlist-total">
        <p><?=$waitlist_info['total-cost']?> : <span>$<?= $grand_total; ?></span></p>
        <a href="shop.php" class="option-btn"><?=$waitlist_info['continue-btn']?></a>
        <a href="waitlist.php?delete_all" class="delete-btn <?= ($grand_total > 1)?'':'disabled'; ?>"><?=$waitlist_info['delete-all-btn']?></a>
    </div>

</section>

<?php require_once 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>