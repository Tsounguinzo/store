<?php

use Models\Cart;
use Models\User;
use Models\Waitlist;

require_once '../../../config/config.php';

$user = new User($conn);

if($_SERVER['REQUEST_METHOD' == 'POST']){
    if($_POST['add_to_waitlist']){
        $user->redirectIfNotLoggedIn();
        $waitlist = new Waitlist($conn, $user->user_id);

    } else if($_POST['add_to_cart']) {
        $user->redirectIfNotLoggedIn();
        $cart = new Cart($conn, $user->user_id);
        $cart->isInCart($_POST['p_name']);
    } else {

    }
}