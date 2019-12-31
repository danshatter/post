<?php
require_once 'core/init.php';

if (isset($_GET['id'])) {
    $data = DB::instance()->get_subject_by_id($_GET['id']);
    if ($data === false) {
        die('<h1>This subject does not exist or has been deleted</h1>');
    } else {
        if ($data->visible === 0) {
            if (isset($_SESSION['id'])) {
                die('<h1>This subject was made invisible by an administrator</h1>');
            }
            die('<h1>This page is not visible at the moment. Please endeavour to check later</h1>');
        }
    }
} else {
    Redirect::to(SITE_ROOT.'/index.php');
}

include_once 'includes/overall/header.php';

    if (isset($data)) {  
        $datas = DB::instance()->get_all_visible_subject_pages($_GET['id']);
        if (count($datas) === 0) {
            echo '<h1>There are no pages currently on this subject</h1>';
        } else {
            $first_page = array_shift($datas);
            echo '<h1 class="content">'.$first_page->title.'</h1>';
            echo '<p class="content">'.strip_tags(ucfirst($first_page->content), '<br/><br>').'</p>';
        }
    }

include_once 'includes/overall/footer.php';