<?php
require_once '../../../../scripts/populate_php_file.php';

if(isset($message)){
    foreach($message as $message){
        echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
    }
}


$dataFilePath = '../../Helpers/JSONData/fr/admin_header.json';
$navbar = 'navbar';
$icons = 'icons';
$profile = 'profile';
populatePage($dataFilePath, $navbar, $icons, $profile);
?>

<header class="header">

    <div class="flex">

        <a href="../Admin/admin_page.php" class="logo">Admin<span>Panel</span></a>

        <nav class="navbar">
            <?php foreach ($navbar['links'] as $links):?>
            <a href="<?=$links['url']?>"><?=$links['text']?></a>
            <?php endforeach;?>
        </nav>

        <div class="icons">
            <?php foreach ($icons as $icon):?>
                <div id="<?=$icon['name']?>" class="<?=$icon['icon']?>"></div>
            <?php endforeach;?>
            <div id="language-btn" class="fas">
                <select name="language" id="language" style="font-size: inherit">
                    <option value="fr" selected>Fr</option>
                    <option value="en" disabled>En</option>
                    <option value="ln" disabled>Ln</option>
                </select>
            </div>
        </div>

        <div class="profile">
            <?php
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
            $select_profile->execute([$admin_id]);
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
            ?>
            <img src="<?=$profile['img-root'] . $fetch_profile['image']; ?>" alt="">
            <p><?= $fetch_profile['name']; ?></p>

            <?php foreach ($profile['buttons'] as $btn):?>
                <a href="<?=$btn['url']?>" class="<?=$btn['class']?>"><?=$btn['text']?></a>
            <?php endforeach;?>

            <div class="flex-btn">

                <?php foreach ($profile['flex-buttons'] as $btn):?>
                    <a href="<?=$btn['url']?>" class="<?=$btn['class']?>"><?=$btn['text']?></a>
                <?php endforeach;?>

            </div>
        </div>

    </div>

</header>