<?php
require_once '../core/init.php';
login_redirect(SITE_ROOT.'/admin/staff.php'); 
include_once '../includes/overall/header.php';
?>
<h1>Register</h1>
<br/>
<?php
if (isset($_SESSION['success'])) {
    echo Session::instance()->flash('User successfully created', 'success', 'success');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_POST['create'])) {
    	if (User::instance()->create_user(trim(escape($_POST['username'])), escape($_POST['password']))) {
            $_SESSION['success'] = 'success';
            Redirect::to($_SERVER['PHP_SELF']);
    	}
	}
}
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
    <table>
        <tr>
            <td><label for="username">Username:</label></td>
            <td><input type="text" name="username" id="username" autocomplete="off" value="<?php echo (isset($_POST['username'])) ? $_POST['username'] : ''; ?>"></td>
        </tr>
        <tr>
            <td><label for="password">Password:</label></td>
            <td><input type="password" name="password" id="password"></td>
        </tr>
        <tr>
            <td><button type="submit" name="create">Register</button></td>
        </tr>
    </table>
    <p>Just signed up? <a href="<?php echo SITE_ROOT; ?>/admin/login.php" class="register-login">Login</a></p>
</form>

<?php include_once '../includes/overall/footer.php'; ?>