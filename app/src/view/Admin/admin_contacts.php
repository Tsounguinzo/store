<?php

require_once '../../../../config/config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
    header('location:login.php');
};

if(isset($_GET['delete'])){

    $delete_id = $_GET['delete'];
    $delete_message = $conn->prepare("DELETE FROM `message` WHERE id = ?");
    $delete_message->execute([$delete_id]);
    header('location:admin_contacts.php');

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>messages</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="../../../../public/css/admin_style.css">

</head>
<body>
<?php require_once '../Partials/admin_header.php'; ?>
<section class="messages">
    <h1 class="title">messages</h1>
    <div class="box-container">
        <?php
        $select_message = $conn->prepare("SELECT * FROM `message`");
        $select_message->execute();
        $select_message = $select_message->fetchAll(PDO::FETCH_ASSOC);

        if(empty($select_message)) {
            echo '<p class="empty">you have no messages!</p>';
        } else {
            foreach ($select_message as $message){
                ?>
                <div class="box">
                    <p> user id : <span><?= $message['user_id']; ?></span> </p>
                    <p> name : <span><?= $message['name']; ?></span> </p>
                    <p> number : <span><?= $message['number']; ?></span> </p>
                    <p> email : <span><?= $message['email']; ?></span> </p>
                    <p> message : <span><?= $message['message']; ?></span> </p>
                    <a href="admin_contacts.php?delete=<?= $message['id']; ?>" onclick="return confirm('delete this message?');" class="delete-btn">delete</a>
                </div>
                <?php
            }
        }
        ?>
    </div>
</section>
<script src="../../../../public/js/script.js"></script>
</body>
</html>