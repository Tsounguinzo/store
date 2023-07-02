<?php
require_once '../vendor/autoload.php';

use Handlers\ProductHandler;

require_once '../config/config.php';

session_start();

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

$productHandler = new ProductHandler($conn, $_SESSION);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productHandler->handleRequest($_POST, $message);
}
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

<?php require_once '../app/src/Views/Partials/header.php'; ?>

<div class="home-bg">
    <section class="home">
        <div class="content">
            <span>Don't panic, we are here for you</span>
            <h3>Achieve your health with quality meat</h3>
            <p>We provide locally raised and fed animal meat here in Kinshasa</p>
            <a href="../app/src/Views/about.php" class="btn">Our Philosophy</a>
        </div>
    </section>
</div>

<section class="home-category">
    <h1 class="title">Buy by Category</h1>
    <div class="box-container">
        <!-- Category boxes -->
        <div class="box">
            <img src="images/cat-1.png" alt="poulet">
            <h3>poulet de chair</h3>
            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Exercitationem, quaerat.</p>
            <a href="../app/src/Views/category.php?category=poulet" class="btn">poulet</a>
        </div>

        <div class="box">
            <img src="images/cat-2.png" alt="porc">
            <h3>porc</h3>
            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Exercitationem, quaerat.</p>
            <a href="../app/src/Views/category.php?category=porc" class="btn">porc</a>
        </div>

        <div class="box">
            <img src="images/cat-3.png" alt="canard">
            <h3>canard</h3>
            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Exercitationem, quaerat.</p>
            <a href="../app/src/Views/category.php?category=canard" class="btn">canard</a>
        </div>

        <div class="box">
            <img src="images/cat-4.png" alt="pigeon">
            <h3>pigeon</h3>
            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Exercitationem, quaerat.</p>
            <a href="../app/src/Views/category.php?category=pigeaon" class="btn">pigeon</a>
        </div>

        <div class="box">
            <img src="images/cat-5.png" alt="caille">
            <h3>caille</h3>
            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Exercitationem, quaerat.</p>
            <a href="../app/src/Views/category.php?category=caille" class="btn">caille</a>
        </div>
    </div>
</section>

<section class="products">
    <h1 class="title">Latest Products</h1>
    <div class="box-container">
        <?php
        try {
            $select_products = $conn->prepare("SELECT id, price, image, name, quantity FROM `products` LIMIT 6");
            $select_products->execute();
            $fetch_products = $select_products->fetchAll(PDO::FETCH_ASSOC);

            if (empty($fetch_products)) {
                echo '<p class="empty">No products added yet!</p>';
            } else {
                foreach ($fetch_products as $fetch_product) {
                    ?>
                    <form action="" class="box" method="POST">
                        <div class="price">$<span><?= $fetch_product['price']; ?></span></div>
                        <a href="../app/src/Views/view_page.php?pid=<?= $fetch_product['id']; ?>" class="fas fa-eye"></a>
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
                               value="<?= ($fetch_product['quantity'] > 0)? $fetch_product['quantity']." left" : "Out Of Stock" ; ?>"
                               class="btn" name="qty_left" readonly
                        >

                        <?php if(($fetch_product['quantity'] <= 0)): ?>
                            <input type="submit" value="add to waitlist" class="option-btn" name="add_to_waitlist">
                        <?php else: ?>
                            <input type="submit" value="add to cart" name="add_to_cart" class="btn">
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

<?php require_once '../app/src/view/Partials/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
