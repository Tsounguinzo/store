<?php

require_once '../../../config/config.php';

session_start();

if(isset($_SESSION['user_id'])){
    $user_id = $_SESSION['user_id'];
}

$jsonDataPath = '../Helpers/JSONData/fr/';

// Load data.json
$dataFilePath = $jsonDataPath . 'about.json';
if (file_exists($dataFilePath)) {
    $jsonData = file_get_contents($dataFilePath);
    if ($jsonData !== false) {
        $data = json_decode($jsonData, true);
        if ($data !== null) {
            $contactData = $data['contactData'];
            $productData = $data['productData'];
            $reviewsData = $data['reviewsData'];
            // Rest of your code goes here

        } else {
            echo "Error: Unable to decode the JSON file.";
        }
    } else {
        echo "Error: Unable to read the JSON file.";
    }
} else {
    echo "Error: JSON file not found.";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>about</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="../../../public/css/style.css">

</head>
<body>

<?php require_once 'Partials/header.php'; ?>

<section class="about">

    <div class="row">

        <div class="box">
            <img src="<?=$contactData['image']?>" alt="">
            <h3><?=$contactData['title']?></h3>
            <p><?=$contactData['description']?></p>
            <a href="<?=$contactData['link']?>" class="btn"><?=$contactData['link-name']?></a>
        </div>

        <div class="box">
            <img src="<?=$productData['image']?>" alt="">
            <h3><?=$productData['title']?></h3>
            <p><?=$productData['description']?></p>
            <a href="<?=$productData['link']?>" class="btn"><?=$productData['link-name']?></a>
        </div>

    </div>

</section>

<section class="reviews">

    <h1 class="title">avis clients</h1>

    <div class="box-container">

        <?php foreach ($reviewsData as $review): ?>
            <div class="box">
                <img src="<?=$review['image']?>" alt="">
                <p><?=$review['content']?></p>
                <div class="stars">
                    <?php
                    $rating = $review['rating'];
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= $rating) {
                            echo '<i class="fas fa-star"></i>';
                        } elseif ($i - $rating === 0.5) {
                            echo '<i class="fas fa-star-half-alt"></i>';
                        } else {
                            echo '<i class="far fa-star"></i>';
                        }
                    }
                    ?>
                </div>
                <h3><?=$review['name']?></h3>
            </div>
        <?php endforeach; ?>
</section>

<?php require_once 'Partials/footer.php'; ?>

<script src="../../../public/js/script.js"></script>

</body>
</html>