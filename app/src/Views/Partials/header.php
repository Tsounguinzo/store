<?php
if (isset($message)) {
    foreach ($message as $msg) {
        echo '
      <div class="message">
         <span>' . htmlspecialchars($msg) . '</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
    }
}
?>

<header class="header">
    <div class="flex">
        <a href="store/public/index.php" class="logo">Ferme Bio <span>KmerPlus</span></a>

        <nav class="navbar">
            <a href="/public/index.php">Accueil</a>
            <a href="shop.php">Boutique</a>
            <a href="orders.php">Mes commandes</a>
            <a href="../about.php">À propos</a>
            <a href="../contact.php">Commentaire</a>
        </nav>

        <div class="icons">
            <div id="menu-btn" class="fas fa-bars"></div>
            <div id="user-btn" class="fas fa-user"></div>
            <a href="search_page.php" class="fas fa-search"></a>

            <?php
            if (isset($user_id)) {
                $count_cart_items = $conn->prepare("SELECT COUNT(*) FROM `cart` WHERE user_id = ?");
                $count_cart_items->execute([$user_id]);
                $cart_item_count = $count_cart_items->fetchColumn();

                $count_waitlist_items = $conn->prepare("SELECT COUNT(*) FROM `waitlist` WHERE user_id = ?");
                $count_waitlist_items->execute([$user_id]);
                $waitlist_item_count = $count_waitlist_items->fetchColumn();
            }
            ?>

            <a href="waitlist.php"><i class="fas fa-clock"></i><span><?php if (isset($user_id)) echo '(' . $waitlist_item_count . ')'; ?></span></a>
            <a href="cart.php"><i class="fas fa-shopping-cart"></i><span><?php if (isset($user_id)) echo '(' . $cart_item_count . ')'; ?></span></a>

            <div id="language-btn" class="fas">
                <select name="language" id="language" style="font-size: inherit">
                    <option value="fr" selected>Fr</option>
                    <option value="en">En</option>
                    <option value="ln">Ln</option>
                </select>
            </div>
        </div>

        <div class="profile">
            <?php
                if (isset($user_id)){

                $select_profile = $conn->prepare("SELECT image, name FROM `users` WHERE id = :id");
                $select_profile->execute([':id' => $user_id]);
                $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);

                if ($fetch_profile) {
                $image = htmlspecialchars($fetch_profile['image']);
                $name = htmlspecialchars($fetch_profile['name']);
                ?>

                <img src="uploaded_img/<?= $image; ?>" alt="">
                <p><?= $name; ?></p>
                <a href="user_profile_update.php" class="btn">Mettre à jour le profil</a>
                <a href="../logout.php" class="delete-btn">Déconnexion</a>

            <?php
                    }
                }
            ?>

            <div class="flex-btn">
                <a href="login.php" class="option-btn">Connexion</a>
                <a href="register.php" class="option-btn">S'inscrire</a>
            </div>
        </div>
    </div>
</header>
