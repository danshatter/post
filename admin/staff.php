<?php
require_once '../core/init.php';
admin_protect();
include_once '../includes/overall/header.php';
?>
<h1>Staff Page</h1>
<p>Welcome to the Staff area, <?php echo ucfirst(strtolower(User::instance()->user_data($_SESSION['id'])->username)); ?>.</p>
<ul class="staff">
    <li><a href="<?php echo SITE_ROOT; ?>/admin/edit.php">Manage Website Content</a></li>
    <li><a href="<?php echo SITE_ROOT; ?>/admin/logout.php">Logout</a></li>
</ul>

<?php include_once '../includes/overall/footer.php'; ?>
