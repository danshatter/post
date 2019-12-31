<?php
require_once '../core/init.php';
admin_protect();
include_once '../includes/overall/header.php';
?>
<h1>&larr; Select a Subject or Page to Edit</h1>
<br/>
<?php
    if (isset($_SESSION['page_insert'])) {
        echo Session::instance()->flash('Page successfully added', 'success', 'page_insert');
    }
    if (isset($_SESSION['subject_insert'])) {
        echo Session::instance()->flash('Subject successfully added', 'success', 'subject_insert');
    }
    if (isset($_SESSION['update'])) {
        echo Session::instance()->flash('Update successful', 'success', 'update');
    }
    if (isset($_SESSION['delete'])) {
        echo Session::instance()->flash('Delete successful', 'success', 'delete');
    }

include_once '../includes/overall/footer.php';
?>