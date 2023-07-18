<?php
$errorCode = "403";
$errorTitle = "Forbidden";
$errorMessage = "Sorry, you are not allowed to access this page.";

http_response_code(403);
error_log("403 Error: A user tried to access a restricted page: " . $_SERVER['REQUEST_URI']);

require_once 'error.php';
