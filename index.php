<?php include('header.php'); ?>
<script>
function cat(){
	let category = document.getElementById("user").value;
	var username = document.getElementById("username");

	if (category == 1){
		username.placeholder = "Enter Access Code";
	}else{
		username.placeholder = "Matric Number";
	}
		
	
}
</script>
<body id="login">
<?php
		$var1 = rand(1, 5);
		$var2 = rand(1, 5);
		if (isset($_POST['authenticate'])) {
			login($_POST['username'], $_POST['password'], $_POST['captcha'], $_POST['category']);
		}
		$_SESSION['real_captcha'] = $var1 + $var2;
		$_SESSION['captcha'] = "$var1 + $var2";
		?>
    <div class="container">
		<div class="row-fluid">
			<div class="span6"><div class="title_index"><?php include('title_index.php'); ?></div></div>
			<div class="span6"><div class="pull-right"><?php include('login_form.php'); ?></div></div>
		</div>
		<?php /*<div class="row-fluid">
			<div class="span12"><div class="index-footer"><?php include('link.php'); ?></div></div>
		</div>*/?>
			<?php include('footer.php'); ?>
    </div>
<?php include('script.php'); ?>
</body>
</html>