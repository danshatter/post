<?php
require_once '../core/init.php';
admin_protect();
?>
<?php include_once '../includes/overall/header.php'; ?>
<h1>Add Subject</h1>
<br/>
<?php
    $subject_count = DB::instance()->get_all_subjects_count();
    $count = $subject_count + 1;
    if (isset($_POST['add_subject'])) {
        if (trim($_POST['subject_name']) === "" || $_POST['position'] === "") {
            $errors[] = '<i class="error">Please fill in all fields</i>';
            echo output_errors($errors);
        } else {
            if (!isset($_POST['visible'])) {
                $errors[] = '<i class="error">Please fill in all fields</i>';
                echo output_errors($errors);
            } else {
                $subject_name = escape(ucwords(trim($_POST['subject_name'])));
                $position = escape($_POST['position']);
                $visible = escape($_POST['visible']);

                if ((int)$position === $count) {
                    if (DB::instance()->insert('subjects', array('subject', 'position', 'visible'), array($subject_name, $position, $visible))) {
                        $_SESSION['subject_insert'] = 'success';
                        Redirect::to(SITE_ROOT.'/admin/edit.php');
                    } else {
                        die('An internal error occurred. Please try again later');
                    }
                } else {
                    $subjects_consider = DB::instance()->select_by_sql("SELECT * FROM `subjects` WHERE `position` >= ?", array($position));
                    if (DB::instance()->insert('subjects', array('subject', 'position', 'visible'), array($subject_name, $position, $visible))) {
                        foreach ($subjects_consider as $sub) {
                            if (!DB::instance()->update('subjects', array('position'), array($sub->position + 1), 'id', $sub->id)) {
                                die('An internal error occurred. Please try again later');
                            }
                        }
                        $_SESSION['subject_insert'] = 'success';
                        Redirect::to(SITE_ROOT.'/admin/edit.php');
                    }
                }
            }
        }
    }    
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
    <label for="subject">Subject Name: </label><input type="text" name="subject_name" id="subject" autocomplete="off" value="<?php echo (isset($_POST['subject_name'])) ? $_POST['subject_name'] : ''; ?>">
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
    <button type="submit" name="add_subject">Add Subject</button>
</form>

<?php include_once '../includes/overall/footer.php'; ?>