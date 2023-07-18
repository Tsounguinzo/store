<?php
$errorCode = "500";
$errorTitle = "Internal Server Error";
$errorMessage = "Oops! Something went wrong. We're working on getting this fixed.";

http_response_code(500);
error_log("500 Error: There was an internal server error: " . $_SERVER['REQUEST_URI']);

require_once 'error.php';
