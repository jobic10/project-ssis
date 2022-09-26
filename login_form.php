<?php
if (!isset($file_access))
die("Direct Access To File Not Allowed");
?>
		<form id="" class="form-signin" method="post">
			<h3 class="form-signin-heading"><i class="icon-lock"></i> Sign in</h3>
			<select name="category" id ='user' onchange="cat()" onLeave="cat()" required class="input-block-level span12">
				<option value="" selected>Choose category</option>
				<option value="1">Supervisor</option>
				<option value="0">Student</option>
			</select>
			<input type="text" class="input-block-level" id="username" name="username" placeholder="Matric No." required>
			<input type="password" class="input-block-level" id="password" name="password" placeholder="Password" required>
			<input type="number" min="0" max="10"  class="input-block-level" id="captcha" name="captcha" placeholder="<?php echo $_SESSION['captcha']; ?>" required>
			<button data-placement="right" title="Click Here to Sign In" id="signin" name="authenticate" class="btn btn-info" type="submit"><i class="icon-signin icon-large"></i> Sign in</button>

		</form>

		<div id="button_form" class="form-signin">
			<strong>SIGN UP</strong>
			<hr>
			<h3 class="form-signin-heading"><i class="icon-edit"></i> Sign up</h3>
			<button data-placement="top" title="Sign In as Student" id="signin_student" onclick="window.location='signup_student.php'"  name="login" class="btn btn-info" type="submit">I`m a Student</button>
			<div class="pull-right">
				<button data-placement="top" title="Sign In as Supervisor" id="signin_supervisor" onclick="window.location='signup_supervisor.php'" name="login" class="btn btn-info" type="submit">I`m a Supervisor</button>
			</div>
		</div>