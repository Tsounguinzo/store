<?php
require_once '../../../vendor/autoload.php';

use Handlers\ProductHandler;

require_once '../../../config/config.php';
require_once '../../../scripts/populate_php_file.php';

session_start();

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

$productHandler = new ProductHandler($conn, $_SESSION);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productHandler->handleRequest($_POST, $message);
}

$dataFilePath = '../Helpers/JSONData/fr/index.json';
$heroData = 'heroData';
$categoriesTitle = 'categories-title';
$categories = 'categories';
$products = 'products';
populatePage($dataFilePath, $heroData, $categoriesTitle, $categories,$products);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>

    <!-- Font Awesome CDN link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- Custom CSS file link -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php require_once 'header.php'; ?>

<div class="home-bg">
    <section class="home">
        <div class="content">
            <span><?=$heroData['intro']?></span>
            <h3><?=$heroData['titre']?></h3>
            <p><?=$heroData['description']?></p>
            <a href="<?=$heroData['action']['lien']?>" class="btn"><?=$heroData['action']['text']?></a>
        </div>
    </section>
</div>

<section class="home-category">
    <h1 class="title"><?=$categoriesTitle?></h1>
    <div class="box-container">
        <!-- Category boxes -->
        <?php foreach ($categories as $category):?>
        <div class="box">
            <img src="<?=$category['image']['link']?>" alt="<?=$category['image']['alt']?>">
            <h3><?=$category['title']?></h3>
            <p><?=$category['description']?></p>
            <a href="<?=$category['category-btn']['link']?>" class="btn"><?=$category['category-btn']['text']?></a>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="products">
    <h1 class="title"><?=$products['title']?></h1>
    <div class="box-container">
        <?php
        try {
            $select_products = $conn->prepare("SELECT id, price, image, name, quantity FROM `products` LIMIT 6");
            $select_products->execute();
            $fetch_products = $select_products->fetchAll(PDO::FETCH_ASSOC);

            if (empty($fetch_products)) {
                $empty = $products['empty-txt'];
                echo "<p class='empty'>$empty</p>";
            } else {
                foreach ($fetch_products as $fetch_product) {
                    ?>
                    <form action="" class="box" method="POST">
                        <div class="price">$<span><?= $fetch_product['price']; ?></span></div>
                        <a href="view_page.php?pid=<?= $fetch_product['id']; ?>" class="fas fa-eye"></a>
                        <img src="uploaded_img/<?= $fetch_product['image']; ?>" alt="">
                        <div class="name"><?= $fetch_product['name']; ?></div>
                        <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
                        <input type="hidden" name="p_name" value="<?= $fetch_product['name']; ?>">
                        <input type="hidden" name="p_price" value="<?= $fetch_product['price']; ?>">
                        <input type="hidden" name="p_image" value="<?= $fetch_product['image']; ?>">

                        <?php if(($fetch_product['quantity'] > 0)): ?>
                            <input type="number" min="1" max="<?= $fetch_product['quantity']; ?>" value="1" name="p_qty" class="qty">
                        <?php endif; ?>

                        <input type="text"
                               value="<?= ($fetch_product['quantity'] > 0)? $fetch_product['quantity'] . " " . $products['left-txt'] : $products['finished-txt'] ; ?>"
                               class="btn" name="qty_left" readonly
                        >

                        <?php if(($fetch_product['quantity'] <= 0)): ?>
                            <input type="submit" value="<?=$products['add-to-waitlist']?>" class="option-btn" name="add_to_waitlist">
                        <?php else: ?>
                            <input type="submit" value="<?=$products['add-to-cart']?>" name="add_to_cart" class="btn">
                        <?php endif; ?>
                    </form>
                    <?php
                }
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
        ?>
    </div>
</section>

<?php require_once 'Partials/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
