<?php
require_once '../core/init.php';
admin_protect();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['delete']) && isset($_POST['id'])) {
            $subject = DB::instance()->get_subject_by_id($_POST['id']);
            if ($subject == null) {
                die('This Subject Does not exist or has already been deleted');
            } else {
                $subjects_consider = DB::instance()->select_by_sql("SELECT * FROM `subjects` WHERE `position` > ? ORDER BY `position` ASC", array($subject->position));
                if (count($subjects_consider) === 0) {
                    if (DB::instance()->delete('pages', 'subject_id', $subject->id) && DB::instance()->delete('subjects', 'id', $subject->id)) {
                    $_SESSION['delete'] = 'success';
                    Redirect::to(SITE_ROOT.'/admin/edit.php');
                    } else {
                        die('An unknown error occurred while to delete this subject. Please try again later');
                    }
                } else {
                    if (DB::instance()->delete('pages', 'subject_id', $subject->id) && DB::instance()->delete('subjects', 'id', $subject->id)) {
                        foreach ($subjects_consider as $sub) {
                            if (!DB::instance()->update('subjects', array('position'), array($sub->position - 1), 'id', $sub->id)) {
                                die('An internal error occurred. Please contact your administrator');
                            }
                        }
                        $_SESSION['delete'] = 'success';
                        Redirect::to(SITE_ROOT.'/admin/edit.php');
                    } else {
                        die('An unknown error occurred while to delete this subject. Please try again later');
                    }
                }
            }
        }
    }