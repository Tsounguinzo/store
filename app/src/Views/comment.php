<?php

use Models\Message;

@include 'config.php';

session_start();

if(isset($_SESSION['user_id'])){
    $user_id = $_SESSION['user_id'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment = new Message($conn);
    $comment->sendMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>contact</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="../../../public/css/style.css">

</head>
<body>

<?php include 'Partials/header.php'; ?>
<section class="contact">
    <h1 class="title">get in touch</h1>
    <form action="" method="POST">
        <input type="text" name="name" class="box" required placeholder="enter your name">
        <input type="email" name="email" class="box" required placeholder="enter your email">
        <input type="number" name="number" min="0" class="box" required placeholder="enter your number">
        <textarea name="msg" class="box" required placeholder="enter your message" cols="30" rows="10"></textarea>
        <input type="submit" value="send message" class="btn" name="send">
    </form>
</section>
<?php include 'Partials/footer.php'; ?>

<script src="../../../public/js/script.js"></script>
</body>
</html>