<?php

use Models\User;

session_start();
require_once  '../../../vendor/autoload.php';
require_once '../../../config/config.php';
require_once '../Models/User.php';

$user = new User($conn);

if (isset($_POST['submit'])) {

    $emailOrName = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['pass']);

    $result = $user->authenticate($emailOrName, $password);

    if ($result) {
        if ($result['user_type'] == 'admin') {
            $_SESSION['admin_id'] = $result['id'];
            header('location:admin_page.php');
            exit;
        } elseif ($result['user_type'] == 'user') {
            $_SESSION['user_id'] = $result['id'];
            header('location:home.php');
            exit;
        }
    } else {
        $message[] = 'Incorrect email or password!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>login</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/components.css">

</head>
<body>

<?php

if(isset($message)){
   foreach($message as $message){
      echo "
      <div class='message'>
         <span>'.$message.'</span>
         <i class='fas fa-times' onclick='this.parentElement.remove();'></i>
      </div>
      ";
   }
}

?>
   
<section class="form-container">

   <form action="" method="POST">
      <h3>CONNEXION</h3>
      <input type="email" name="email" class="box" placeholder="e-mail ou nom d'utilisateur" required">
      <input type="password" name="pass" class="box" placeholder="mot de passe" required>
      <input type="submit" value="se connecter" class="btn" name="submit">
      <p>vous n'avez pas de compte ? <a href="register.php">inscrivez-vous</a></p>
   </form>

</section>


</body>
</html>