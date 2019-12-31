<?php
require_once '../core/init.php';
login_redirect(SITE_ROOT.'/admin/staff.php'); 
include_once '../includes/overall/header.php';
?>
<h1>Login</h1>
<br/>
<?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    	if (isset($_POST['login'])) {
        	User::instance()->login_validate(escape($_POST['username']), escape($_POST['password']));
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
            <td><button type="submit" name="login">Login</button></td>
        </tr>
    </table>
</form>
<?php include_once '../includes/overall/footer.php'; ?>