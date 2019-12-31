<?php
function Autoloader($class_name) {
    if (strpos($_SERVER['PHP_SELF'], 'admin') > 3) {
        require_once '../classes/'.$class_name.'.php';
    } else {
        require_once 'classes/'.$class_name.'.php';
    }
}

function escape($string) {
    return htmlentities($string, ENT_QUOTES, 'UTF-8');
}

function output_errors($errors) {
    return implode('<br/>', $errors);
}

function admin_protect() {
    if (!isset($_SESSION['id'])) {
        Redirect::to(SITE_ROOT.'/index.php');
    }
}

function login_redirect($page) {
    if (isset($_SESSION['id'])) {
        Redirect::to($page);
    }
}