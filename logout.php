<?php
require_once 'config/config.php';

// Destroy session and redirect to login page
session_destroy();
header("Location: login.php");
exit();
?>
