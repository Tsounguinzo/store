<?php

require_once '../../../config/config.php';

session_start();
session_unset();
session_destroy();

header('location:javascript:history.back(2)');