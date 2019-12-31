<?php
require_once '../core/init.php';
if (isset($_SESSION['id'])) {
    session_unset();
    session_destroy();
    Redirect::to(SITE_ROOT.'/admin/login.php');
} else {
    Redirect::to(SITE_ROOT.'/index.php');
}