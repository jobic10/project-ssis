<?php
if (!isset($file_access))
die("Direct Access To File Not Allowed");
?>
<form id="" class="form-signin" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
					<h3 class="form-signin-heading"><i class="icon-user"></i> Student Sign Up
				
				<?php 
				$var1 = rand(1, 5);
				$var2 = rand(1, 5);
		
				
					if (isset($_POST['register'])){
						$firstname = $_POST['firstname'];
						$lastname = $_POST['lastname'];
						$matric = $_POST['matric'];
						$password = $_POST['password'];
						$cpassword = $_POST['cpassword'];
						$captcha = $_POST['captcha'];
						$email = $_POST['email'];
						$phone = $_POST['phone']; 
						$signup = signupstudent($firstname,$lastname,$matric,$password,$cpassword,$captcha,$email,$phone,"student");
						echo "<script>alert('$signup[0]');</script>";
						echo "<h3 class='form-signin-heading ".$signup[1]."'>".$signup[0]."</h3>";
						if ($signup[1] == 'alert-success'){
							
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
	<input type="text" class="input-block-level" id="matric" name="matric" placeholder="Matric Number" pattern="[0-9]{2}/[0-9]{2}[A-Za-z]{2}[0-9]{3}" required>
	<input type="text" class="input-block-level" name="firstname" placeholder="Firstname" required>
	<input type="text" class="input-block-level" name="lastname" placeholder="Lastname" required>
	<input type="email" class="input-block-level" name="email" placeholder="Email" required>
	<input type="text" minlength="11" maxlength="11" class="input-block-level" name="phone" placeholder="Phone Number" required>
	<input type="password" class="input-block-level" id="password" name="password" placeholder="Password" required>
	<input type="password" class="input-block-level" id="cpassword" name="cpassword" placeholder="Re-type Password" required>
	<input type="text" class="input-block-level" id="captcha" name="captcha" placeholder="<?php echo $_SESSION['captcha']; ?> = ?" required>
	<button id="signin" name="register" class="btn btn-info" type="submit"><i class="icon-check icon-large"></i> Sign Up</button>
</form>
<a onclick="window.location='index.php'" id="btn_login" name="login" class="btn" type="submit"><i class="icon-signin icon-large"></i> Click here to Login</a>
