<?php include('header.php'); ?>

<body id="login">
	<div class="container">
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
	$login = verify_login($_POST['username'],$_POST['password'],$_POST['captcha'],$_POST['access']);
}
$var1 = rand(1, 5);
$var2 = rand(1, 5);
$_SESSION['real_captcha'] = $var1 + $var2;
$_SESSION['captcha'] = "$var1 + $var2";
?>
		<form id="login_form" class="form-signin" method="post">
			<h3 class="form-signin-heading"><i class="icon-lock"></i> Please Login</h3>
			<input type="text" class="input-block-level" id="usernmae" name="username" placeholder="Username" required>
			<input type="password" class="input-block-level" id="password" name="password" placeholder="Password" required>
			<input type="text" class="input-block-level" id="captcha" name="captcha" placeholder="<?php echo $_SESSION['captcha']." = ?"; ?>" required>
			<input type="password" class="input-block-level" id="acc_code" name="access" placeholder="Access Code" required>
			<input name="login" class="btn btn-info" type="submit" value="Login">


	</div> <!-- /container -->
	<?php include('script.php'); ?>
</body>

</html>