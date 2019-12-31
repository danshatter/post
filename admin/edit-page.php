<?php
require_once '../core/init.php';
admin_protect();

    if (isset($_GET['id'])) {
        $url_id = $_GET['id'];
        $page = DB::instance()->get_page_by_id($url_id);
        if ($page == null) {
            echo '<h1>This Page Does Not Exist or Has Been Deleted</h1>';
            die();
        } else {
            $subject_pages_count = DB::instance()->get_all_subject_pages_count($page->subject_id);
        }
    } else {
        Redirect::to(SITE_ROOT.'/admin/staff.php');
    }

include_once '../includes/overall/header.php';
?>
<h1>Edit Page: <?php echo $page->title; ?></h1>
<br/>
<?php
    if (isset($_POST['update'])) {
        if (trim($_POST['page_name']) === "" || $_POST['position'] === "" || $_POST['visible'] === "" || $_POST['content'] === "") {
            $errors[] = '<i class="error">Please fill in all fields</i>';
            echo output_errors($errors);
        } else {
            $page_name = escape(ucwords(trim($_POST['page_name'])));
            $position = escape($_POST['position']);
            $visible = escape($_POST['visible']);
            $content = nl2br(escape($_POST['content']));

            if ((int)$position === $page->position) {
                if (DB::instance()->update('pages', array('title', 'content', 'position', 'visible'), array($page_name, $content, $position, $visible), 'id', $page->id)) {
                    $_SESSION['update'] = 'success';
                    Redirect::to(SITE_ROOT.'/admin/edit.php');
                }
            } else {
                if ($page->position < $position) {
                    $small = $page->position;
                    $large = $position;
                } elseif ($page->position > $position) {
                    $small = $position;
                    $large = $page->position;
                }

                $pages_consider = DB::instance()->select_by_sql("SELECT * FROM `pages` WHERE `position` BETWEEN ? AND ? AND `subject_id`= ? ORDER BY `position` ASC", array($small, $large, $page->subject_id));

                if ($small === $page->position) {
                    $main_page = array_shift($pages_consider);
                    if (DB::instance()->update('pages', array('title', 'content', 'position', 'visible'), array($page_name, $content, $position, $visible), 'id', $main_page->id)) {
                        foreach ($pages_consider as $pager) {
                           if (!DB::instance()->update('pages', array('position'), array($pager->position - 1), 'id', $pager->id)) {
                            die('An internal error occurred. Please try again later');
                           }
                        }
                        $_SESSION['update'] = 'success';
                        Redirect::to(SITE_ROOT.'/admin/edit.php');
                    } else {
                        die('An error occurred. Please try again later');
                    }
                } elseif ($small === $position) {
                    $main_page = array_pop($pages_consider);
                    if (DB::instance()->update('pages', array('title', 'content', 'position', 'visible'), array($page_name, $content, $position, $visible), 'id', $main_page->id)) {
                        foreach ($pages_consider as $pager) {
                            if (!DB::instance()->update('pages', array('position'), array($pager->position + 1), 'id', $pager->id)) {
                                die('An internal error occurred. Please try again later');
                            }
                        }
                        $_SESSION['update'] = 'success';
                        Redirect::to(SITE_ROOT.'/admin/edit.php');
                    } else {
                        die('An error occurred. Please try again later');
                    } 
                }
                $_SESSION['update'] = 'success';
                Redirect::to(SITE_ROOT.'/admin/edit.php');

            }

        }
    }
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $url_id; ?>" method="post">
    <label for="page">Page Title: </label><input type="text" name="page_name" id="page" value="<?php echo $page->title; ?>" autocomplete="off">
        <br/><br/>
    <label for="position">Position: </label>
    <select name="position" id="position">
        <?php for ($i=1; $i <= $subject_pages_count; $i++) : ?>
            <option value="<?php echo $i; ?>"  <?php if ($page->position == $i) { echo 'selected'; } ?>><?php echo $i; ?></option>
        <?php endfor; ?>
    </select>
        <br/><br/>
    <label for="">Visible: </label>
    <input type="radio" name="visible" id="no" value="0" <?php if ($page->visible == 0) { echo 'checked'; } ?>><label for="no" class="option">No</label>
    &nbsp; &nbsp; &nbsp; &nbsp;
    <input type="radio" name="visible" id="yes" value="1" <?php if ($page->visible == 1) { echo 'checked'; } ?>><label for="yes" class="option">Yes</label>
        <br/><br/>
    <label for="content">Content</label>
        <br/>
    <textarea name="content" id="content" cols="60"rows="15"><?php echo strip_tags($page->content); ?></textarea>
        <br/><br/>
    <button type="submit" name="update">Update</button>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <button type="reset">Reset</button>
</form>
<form action="<?php echo SITE_ROOT; ?>/admin/delete-page.php" method="POST">
    <input type="hidden" name="id" value="<?php echo $page->id; ?>">
    <br/><br/><br>
    <button type="submit" name="delete">Delete Page</button>
</form>
<?php include_once '../includes/overall/footer.php'; ?>