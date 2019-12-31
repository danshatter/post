<?php
    if (strpos($_SERVER['PHP_SELF'], 'admin') > 3) {
        include_once '../includes/footer.php';
    } else {
        include_once 'includes/footer.php';
    }
