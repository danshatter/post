<?php
require_once 'core/init.php';

if (isset($_GET['id'])) {
    $data = DB::instance()->get_page_by_id($_GET['id']);
    if ($data === false) {
        die('<h1>This page does not exist or has been deleted</h1>');
    } else {
        if ($data->visible === 0) {
            if (isset($_SESSION['id'])) {
                die('<h1>This page was made invisible by an administrator</h1>');
            }
            die('<h1>This page is not visible at the moment. Please endeavour to check later</h1>');
        }
    }
} else {
    Redirect::to(SITE_ROOT.'/index.php');
}
include_once 'includes/overall/header.php';

if (isset($data)) {
    echo '<h1 class="content">'.$data->title.'</h1>';
    echo '<p class="content">'.strip_tags(ucfirst($data->content), '<br/><br><br />').'</p>';
    if (isset($_SESSION['id'])) {
        echo '<br/>';
        echo '<a href="'.SITE_ROOT.'/admin/edit-page.php?id='.urlencode($_GET['id']).'" class="edit-link-home">Edit Page</a>';
    }
}

include_once 'includes/overall/footer.php';
