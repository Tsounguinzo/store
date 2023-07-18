<?php

require_once '../../../vendor/autoload.php';

use Models\User;

require_once '../../../config/config.php';

$user = new User($conn);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message[] = $user->register();
    if ($message[0] == 'Registration successful') {
        header("location:login.php");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>register</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/components.css">

</head>
<body>

<?php

if(isset($message)){
   foreach($message as $msg){
      echo "
      <div class='message'>
         <span>$msg</span>
         <i class='fas fa-times' onclick='this.parentElement.remove();'></i>
      </div>
      ";
   }
}

?>
   
<section class="form-container">
   <form action="" enctype="multipart/form-data" method="POST" onsubmit="validateForm()">
      <h3>Inscription</h3>
      <input type="text" name="name" class="box" placeholder="nom d'utilisateur" required>
      <input type="email" name="email" class="box" placeholder="Email" required>
      <input type="tel" id="tel" name="tel" class="box" placeholder="+243 ## ## ## ###" required>
      <input type="password" id="password" name="pass" class="box" placeholder="mot de passe" required>
       <input type="password" id="password" name="cpass" class="box" placeholder="confirmer mot de passe" required>
       <input type="hidden" id="error_message" name="error_message" value="">
       <input type="submit" value="s'inscrire" class="btn" name="submit">
      <p>Vous avez déjà un compte? <a href="login.php">se connecter</a></p>
   </form>
</section>

<script src="../../../public/js/passwordValidation.js"></script>
</body>
</html>