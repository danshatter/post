<div class="aside">
<?php
if (isset($_SESSION['id'])) {
  if (basename($_SERVER['PHP_SELF']) === 'staff.php') {
      echo '';
    } elseif (strpos($_SERVER['PHP_SELF'], 'admin') == null) {
      echo DB::instance()->main_navigation();
      echo '<br/>';
      echo '<a href="'.SITE_ROOT.'/admin/staff.php" class="page-link">Go to admin Page</a>';
    } else {
      echo DB::instance()->admin_navigation();
    }
} else {
    if (strpos($_SERVER['PHP_SELF'], 'admin') > 3) {
      echo '<a href="'.SITE_ROOT.'/index.php" class="admin-link">Return to Public site</a>';
    } else {
      echo DB::instance()->main_navigation();
    }
}
?>
</div>
<div class="main">
        