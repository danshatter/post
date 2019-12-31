<?php
session_start();
ob_start();
$errors = array();
define('SITE_ROOT', '/post');
if (strpos($_SERVER['PHP_SELF'], 'admin') > 3) {
    include_once '../functions/func.php';
    include_once '../credentials/secure.php';
} else {
    include_once 'functions/func.php';
    include_once 'credentials/secure.php';
}
spl_autoload_register('Autoloader');