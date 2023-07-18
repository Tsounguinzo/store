<?php
$errorCode = "404";
$errorTitle = "Not Found";
$errorMessage = "We're sorry, but the page you were looking for doesn't exist.";

http_response_code(404);
error_log("404 Error: A user tried to access a page that doesn't exist: " . $_SERVER['REQUEST_URI']);

require_once 'error.php';
