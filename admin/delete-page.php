<?php
require_once '../core/init.php';
admin_protect();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['delete']) && isset($_POST['id'])) {
            $page = DB::instance()->get_page_by_id($_POST['id']);
            if ($page == null) {
                die('This Page Does not exist or has already been deleted');
            } else {
                $pages_consider = DB::instance()->select_by_sql("SELECT * FROM `pages` WHERE `position` > ? AND `subject_id` = ? ORDER BY `position` ASC", array($page->position, $page->subject_id));

                if (count($pages_consider) === 0) {
                    if (DB::instance()->delete('pages', 'id', $page->id)) {
                        $_SESSION['delete'] = 'success';
                        Redirect::to(SITE_ROOT.'/admin/edit.php');
                    } else {
                        die('An unknown error occurred while to delete this page. Please try again later');
                    }
                } else {
                    if (DB::instance()->delete('pages', 'id', $page->id)) {
                        foreach ($pages_consider as $pager) {
                            if (!DB::instance()->update('pages', array('position'), array($pager->position - 1), 'id', $pager->id)) {
                                die('An internal error occurred. Please contact your administrator');
                            }
                        }
                        $_SESSION['delete'] = 'success';
                        Redirect::to(SITE_ROOT.'/admin/edit.php');
                    } else {
                        die('An unknown error occurred while to delete this page. Please try again later');
                    }
                }
            }
            
        }
    }