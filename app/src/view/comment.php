<?php
require_once '../../../vendor/autoload.php';

use Models\Message;

require_once '../../../config/config.php';
require_once '../../../scripts/populate_php_file.php';

session_start();

if(isset($_SESSION['user_id'])){
    $user_id = $_SESSION['user_id'];
}

if( $_SERVER['REQUEST_METHOD'] == 'POST'){
    if(!isset($user_id)){
        header('location:login.php');
    }

    $comment = new Message($conn);

    $message = $comment->sendMessage();

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
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php require_once 'header.php'; ?>

<section class="contact">

   <h1 class="title">ENTRER EN CONTACT</h1>

   <form action="" method="POST">
      <input type="text" name="name" class="box" required placeholder="nom">
      <input type="email" name="email" class="box" required placeholder="email">
      <input type="number" name="number" min="0" class="box" required placeholder="numéro de téléphone">
      <textarea name="msg" class="box" required placeholder="entrer le message" cols="30" rows="10"></textarea>
      <input type="submit" value="envoyer" class="btn" name="send">
   </form>

</section>

<?php require_once 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>