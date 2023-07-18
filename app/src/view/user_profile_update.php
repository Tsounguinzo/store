<?php
require_once '../../../vendor/autoload.php';

use Models\User;

require_once '../../../config/config.php';
require_once '../../../scripts/populate_php_file.php';

$dataFilePath = '../Helpers/JSONData/fr/user_profile.json';
$user_info = 'user_info';
populatePage($dataFilePath, $user_info);

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
    header('location:login.php');
};

if(isset($_POST['update_profile'])) {

    $user = new User($conn);
    $message[] = $user->updateProfile();

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>update user profile</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/components.css">

</head>
<body>
<?php require_once 'header.php'; ?>
<section class="update-profile">

   <h1 class="title"><?=$user_info['title']?></h1>

   <form action="" method="POST">
      <div class="flex">
         <div class="inputBox">
            <span><?=$user_info['name']['prompt']?> :</span>
            <input type="text" name="name" value="<?= $fetch_profile['name']; ?>" placeholder="<?=$user_info['name']['place-holder']?>" required class="box">
            <span><?=$user_info['email']['prompt']?> :</span>
            <input type="email" name="email" value="<?= $fetch_profile['email']; ?>" placeholder="<?=$user_info['email']['prompt']?>" required class="box">
         </div>
         <div class="inputBox">
            <input type="hidden" name="old_pass">
            <span><?=$user_info['old-pwd']['prompt']?> :</span>
            <input type="password" name="update_pass" placeholder="<?=$user_info['old-pwd']['place-holder']?>" class="box">
            <span><?=$user_info['new-pwd']['prompt']?> :</span>
            <input type="password" name="new_pass" placeholder="<?=$user_info['new-pwd']['place-holder']?>" class="box">
            <span><?=$user_info['cnew-pwd']['prompt']?> :</span>
            <input type="password" name="confirm_pass" placeholder="<?=$user_info['cnew-pwd']['place-holder']?>" class="box">
         </div>
      </div>
      <div class="flex-btn">
         <input type="submit" class="btn" value="<?=$user_info['update-btn']?>" name="update_profile">
         <a href="javascript:history.back()" class="option-btn"><?=$user_info['back-btn']?></a>
      </div>
   </form>

</section>

<?php require_once 'footer.php'; ?>
<script src="js/script.js"></script>

</body>
</html>