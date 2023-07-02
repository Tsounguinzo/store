<?php

use Models\User;

require_once '../../../config/config.php';
require_once '../Models/User.php';

session_start();

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if (isset($_POST['send'])) {

    if ($user_id == null) {
        $message[] = 'You must be logged in to perform this action.';
        header("location:javascript:history.back()");
    }

    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $number = htmlspecialchars($_POST['number']);
    $msg = htmlspecialchars($_POST['msg']);

    $select_message = $conn->prepare("SELECT * FROM `message` WHERE name = ? AND email = ? AND number = ? AND message = ?");
    $select_message->execute([$name, $email, $number, $msg]);

    if($select_message->rowCount() > 0){
        $message[] = 'already sent message!';
    }else{
        $insert_message = $conn->prepare("INSERT INTO `message`(user_id, name, email, number, message) VALUES(?,?,?,?,?)");
        $insert_message->execute([$user_id, $name, $email, $number, $msg]);

        $message[] = 'sent message successfully!';
    }
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

<?php require_once 'Partials/header.php'; ?>
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
<?php require_once 'Partials/footer.php'; ?>

<script src="/public/js/script.js"></script>

</body>
</html>

