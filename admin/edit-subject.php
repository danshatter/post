<?php
require_once '../core/init.php';
admin_protect();

    if (isset($_GET['id'])) {
        $url_id = $_GET['id'];
        $subject = DB::instance()->get_subject_by_id($url_id);
        if ($subject == null) {
            die('<h1>This Subject Does Not Exist or Has Been Deleted</h1>');
        } else {
            $subject_count = DB::instance()->get_all_subjects_count();
            $pages = DB::instance()->get_all_subject_pages($subject->id);
        }   
    } else {
        Redirect::to(SITE_ROOT.'/admin/staff.php');
    }

include_once '../includes/overall/header.php';
?>
<h1>Edit Subject: <?php echo $subject->subject; ?></h1>
<br/>
<?php
    if (isset($_POST['update'])) {
        if (trim($_POST['subject_name']) === "" || $_POST['position'] === "" || $_POST['visible'] === "") {
            $errors[] = '<i class="error">Please fill in all fields</i>';
            echo output_errors($errors);
        } else {
            $subject_name = escape(ucwords(trim($_POST['subject_name'])));
            $position = escape($_POST['position']);
            $visible = escape($_POST['visible']);

            if ((int)$position === $subject->position) {
                if (DB::instance()->update('subjects', array('subject', 'position', 'visible'), array($subject_name, $position, $visible), 'id', $_GET['id'])) {
                    $_SESSION['update'] = 'success';
                    Redirect::to(SITE_ROOT.'/admin/edit.php');
                }
            } else {
                if ($subject->position < $position) {
                    $small = $subject->position;
                    $large = $position;
                } elseif ($subject->position > $position) {
                    $small = $position;
                    $large = $subject->position;
                }

                $subjects_consider = DB::instance()->select_by_sql("SELECT * FROM `subjects` WHERE `position` BETWEEN ? AND ? ORDER BY `position` ASC", array($small, $large));

                if ($small === $subject->position) {
                    $main_subject = array_shift($subjects_consider);
                    if (DB::instance()->update('subjects', array('subject', 'position', 'visible'), array($subject_name, $position, $visible), 'id', $main_subject->id)) {
                        foreach ($subjects_consider as $sub) {
                           if (!DB::instance()->update('subjects', array('position'), array($sub->position - 1), 'id', $sub->id)) {
                            die('An internal error occurred. Please try again later');
                           }
                        }
                        $_SESSION['update'] = 'success';
                        Redirect::to(SITE_ROOT.'/admin/edit.php');
                    } else {
                        die('An error occurred. Please try again later');
                    }
                } elseif ($small === $position) {
                    $main_subject = array_pop($subjects_consider);
                    if (DB::instance()->update('subjects', array('subject', 'position', 'visible'), array($subject_name, $position, $visible), 'id', $main_subject->id)) {
                        foreach ($subjects_consider as $sub) {
                            if (!DB::instance()->update('subjects', array('position'), array($sub->position + 1), 'id', $sub->id)) {
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
<form action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $url_id; ?>" method="POST">
    <label for="subject">Subject Name: </label><input type="text" name="subject_name" id="subject" autocomplete="off" value="<?php echo $subject->subject; ?>">
        <br/><br/>
    <label for="position">Position: </label>
    <select name="position" id="position">
        <?php for ($i=1; $i <= $subject_count; $i++) : ?>
            <option value="<?php echo $i; ?>"  <?php if ($subject->position == $i) { echo 'selected'; } ?>><?php echo $i; ?></option>
        <?php endfor; ?>
    </select>
        <br/><br/>
    <label for="">Visible: </label>
    <input type="radio" name="visible" id="no" value="0" <?php if ($subject->visible == 0) { echo 'checked'; } ?>><label for="no" class="option">No</label>
    &nbsp; &nbsp; &nbsp; &nbsp;
    <input type="radio" name="visible" id="yes" value="1" <?php if ($subject->visible == 1) { echo 'checked'; } ?>><label for="yes" class="option">Yes</label>
        <br/><br/>
    <button type="submit" name="update">Update</button>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <button type="reset">Reset</button>
</form>
<br/><br/><br>
<form action="<?php echo SITE_ROOT; ?>/admin/delete-subject.php" method="POST">
    <input type="hidden" name="id" value="<?php echo $subject->id; ?>"/>
    <button type="submit" name="delete">Delete Subject</button>
</form>
<br/><br/>
<hr/>
    <div class="clear"></div>
<h2>Pages in this Subject</h2>
<br/>

    <?php
        if ($pages == null) {
            echo '<p>No pages added</p>';
        } else {
            echo '<ul>';
            foreach ($pages as $page) {
                echo '<li><a href="'.SITE_ROOT.'/content.php?id='.urlencode($page->id).'" class="edit-link">'.$page->title.'</a></li>';
            }
            echo '</ul>';
        }
    
    ?>
<br/><br/>
<label for="">+ </label><a href="<?php echo SITE_ROOT; ?>/admin/add-page.php?id=<?php echo $subject->id; ?>" class="edit-link">Add a new page to this subject</a>

<?php include_once '../includes/overall/footer.php'; ?>