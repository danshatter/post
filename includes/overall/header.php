<?php
    if (strpos($_SERVER['PHP_SELF'], 'admin') > 3) {
        include_once '../includes/head.php';
        include_once '../includes/aside.php';
    } else {
        include_once 'includes/head.php';
        include_once 'includes/aside.php';
    }
?>