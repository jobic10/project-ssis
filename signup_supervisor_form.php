<?php
if (!isset($file_access))
	die("Direct Access To File Not Allowed");
?>
<form id="" class="form-signin" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" autocomplete="off">
	<h3 class="form-signin-heading"><i class="icon-group"></i> Supervisor Sign Up

		<?php
		$var1 = rand(1, 5);
		$var2 = rand(1, 5);


		if (isset($_POST['signup'])) {
			$firstname = $_POST['firstname'];
			$lastname = $_POST['lastname'];
			$fileno = $_POST['accesscode'];
			$password = $_POST['password'];
			$cpassword = $_POST['cpassword'];
			$title = $_POST['title'];
			$captcha = $_POST['captcha'];
			$email = $_POST['email'];
			$phone = $_POST['phone'];
			$signup = signup($firstname, $lastname, $fileno, $password, $cpassword, $captcha, $title,$email,$phone);
			echo "<script>alert('$signup[0]');</script>";
			echo "<h3 class='form-signin-heading " . $signup[1] . "'>" . $signup[0] . "</h3>";
			if ($signup[1] == 'alert-success') {

				?>
				<a onclick="window.location='index.php'" id="btn_login" name="login" class="btn" type="submit"><i class="icon-signin icon-large"></i> Click here to Login</a>
		<?php
				exit;
			}
		}
		$_SESSION['real_captcha'] = $var1 + $var2;
		$_SESSION['captcha'] = "$var1 + $var2";
		?>
	</h3>

	<select class="input-block-level span12" name="title" id="title" required>
		<?php echo listTitles(); ?>
	</select>
	<input type="text" class="input-block-level" name="firstname" placeholder="Firstname" required>
	<input type="text" class="input-block-level" name="lastname" placeholder="Lastname" required>
	<input type="text" class="input-block-level" id="accesscode" pattern="[S|s][0-9]{4}" name="accesscode" placeholder="Access Code" required>
	<input type="email" class="input-block-level" name="email" placeholder="Email" required>
	<input type="text" minlength="11" maxlength="11" class="input-block-level" name="phone" placeholder="Phone Number" required>
	<input type="password" class="input-block-level" id="password" name="password" placeholder="Password" required>
	<input type="password" class="input-block-level" id="cpassword" name="cpassword" placeholder="Re-type Password" required>
	<input type="text" class="input-block-level" id="captcha" name="captcha" placeholder="<?php echo $_SESSION['captcha']; ?> = ?" required>
	<button id="signin" name="signup" class="btn btn-info" type="submit"><i class="icon-check icon-large"></i> Complete Registration</button>
</form>
<a onclick="window.location='index.php'" id="btn_login" name="login" class="btn" type="submit"><i class="icon-signin icon-large"></i> Click here to Login</a>