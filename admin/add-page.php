<?php
require_once '../core/init.php';
admin_protect();
?>
<?php
    if (isset($_GET['id'])) {
        $url_id = $_GET['id'];
        $subject = DB::instance()->get_subject_by_id($_GET['id']);
        if ($subject == null) {
            echo '<h1>This subject does not exist or has been deleted</h1>';
            die();
        } else {
            $page_count = DB::instance()->get_all_subject_pages_count($subject->id);
            $count = $page_count + 1;
        }
    } else {
        Redirect::to(SITE_ROOT.'/admin/staff.php');
    }
?>
<?php include_once '../includes/overall/header.php'; ?>
<h1>Add Page</h1>
<br/>
<?php
    if (isset($_POST['add_page'])) {
        if (trim($_POST['page_name']) === "" || $_POST['position'] === "" || $_POST['content'] === "") {
            $errors[] = '<i class="error">Please fill in all fields</i>';
            echo output_errors($errors);
        } else {
            if (!isset($_POST['visible'])) {
                $errors[] = '<i class="error">Please fill in all fields</i>';
                echo output_errors($errors);
            } else {
                $page_name = escape(ucwords(trim($_POST['page_name'])));
                $position = escape($_POST['position']);
                $visible = escape($_POST['visible']);
                $content = nl2br(escape($_POST['content']));

                if ((int)$position === $count) {
                    if (DB::instance()->insert('pages', array('subject_id', 'title', 'content', 'position', 'visible'), array($subject->id, $page_name, $content, $position, $visible))) {
                        $_SESSION['page_insert'] = 'success';
                        Redirect::to(SITE_ROOT.'/admin/edit.php');
                    } else {
                        die('<h1>An internal error occurred. Please try again later</h1>');
                    }
                } else {
                    $pages_consider = DB::instance()->select_by_sql("SELECT * FROM `pages` WHERE `position` >= ? AND `subject_id` = ?", array($position, $subject->id));

                    if (DB::instance()->insert('pages', array('subject_id', 'title', 'content', 'position', 'visible'), array($subject->id, $page_name, $content, $position, $visible))) {
                        foreach ($pages_consider as $pager) {
                            if (!DB::instance()->update('pages', array('position'), array($pager->position + 1), 'id', $pager->id)) {
                                die('An internal error occurred. Please try again later');
                            }
                        }
                        $_SESSION['page_insert'] = 'success';
                        Redirect::to(SITE_ROOT.'/admin/edit.php');
                    } else {
                        die('<h1>An internal error occurred. Please try again later</h1>');
                    }
                }
            }
        }
    } 
?> 
<form action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $url_id; ?>" method="POST">
    <label for="page">Page Title: </label><input type="text" name="page_name" id="page" autocomplete="off" value="<?php echo (isset($_POST['page_name'])) ? $_POST['page_name'] : ''; ?>">
        <br/><br/>
    <label for="position">Position: </label>
    <select name="position" id="position">
        <?php for ($i=1; $i <= $count; $i++) : ?>
            <option value="<?php echo $i; ?>" <?php if ($i === $count) { echo 'selected'; } ?>><?php echo $i; ?></option>
        <?php endfor; ?>
    </select>
        <br/><br/>
    <label for="">Visible: </label>
    <input type="radio" name="visible" id="no" value="0" <?php echo (isset($_POST['visible']) && $_POST['visible'] === '0') ? 'checked' : ''; ?>><label for="no" class="option">No</label>
    &nbsp;&nbsp;&nbsp;&nbsp;
    <input type="radio" name="visible" id="yes" value="1" <?php echo (isset($_POST['visible']) && $_POST['visible'] === '1') ? 'checked' : ''; ?>><label for="yes" class="option">Yes</label>
        <br/><br/>
    <label for="content">Content</label>
        <br/>
    <textarea name="content" id="content" cols="60"rows="15"><?php echo (isset($_POST['content'])) ? $_POST['content'] : ''; ?></textarea>
        <br/><br/>
    <button type="submit" name="add_page">Add Page</button>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <br/><br/><br>
    <button type="reset">Reset</button>
</form>

<?php include_once '../includes/overall/footer.php'; ?>