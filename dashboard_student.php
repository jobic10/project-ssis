<?php include 'header_dashboard.php';?>
<?php include 'session.php';?>

<body>

	<?php include 'navbar_student.php';?>
	<div class="container-fluid">
		<div class="row-fluid">
			<?php include 'student_sidebar.php';?>
			<div class="span9" id="content">
				<div class="row-fluid">
					<!-- breadcrumb -->


					<ul class="breadcrumb">

						<li><a href="#"><b>My Class</b></a><span class="divider">/</span></li>
						<li><a href="#">School Year: <?php echo date('Y'); ?></a></li>
					</ul>
					<!-- end breadcrumb -->



					<!-- block -->
					<div class="block">
						<div class="navbar navbar-inner block-header">
							<div id="" class="muted pull-right">

								<?php $assigned = isStudentAssigned($_SESSION['id']);
if ($assigned[0] == 0) {
    $yes = 0;
    echo '<span class="badge badge-important"><strong>You are yet to have a field of study</strong>';
} else {
    $yes = 1;
    echo '<span class="badge badge-info">Your field of study is ' . ucwords(strtolower($assigned[1]));
}
;?>
								</span>
							</div>
						</div>
						<div class="block-content collapse in">
							<div class="span12">
								<?php
if ($yes) {
    $id = getIdFromSession($_SESSION['id'], "student");
    echo recordFromChange($id);

    if (isset($_GET['change'])) {
        if (canThisIdRequestForChange($id)) {
            //Can this student change
            if (isset($_POST['reason'])) {
                //If Form is submitted
                echo sendChangeOfField($_POST['reason'], $id);
            }
            ?>
											<table class="table-bordered table">
												<form action="" method="post">
													<tr>
														<th width='45%'>
															<h4>Give Reason Why You'd Like To Reset Your Field</h4>
														</th>
														<td width="55%"><textarea placeholder="I'd like to request for change of field because ......................." required minlength="20" maxlength="999" name="reason" class="input-block-level" id="reason" rows="10"></textarea>
														</td>
													<tr>
														<td colspan="2"><button type="submit" onclick="return confirm('This will be sent to the supervisor in charge of your already-selected field to request for his permission for you to be re-assigned\n\nProceed?')" name="save" class="btn btn-info" type="submit"><i class='icon icon-save'></i> Send Request</button></td>
													</tr>
												</form>
											</table>

									<?php
} else {
            echo "<script>alert('You already used your chance. Contact The Admin');window.location='" . $_SERVER['PHP_SELF'] . "';</script>";
        }
    }
    ?>

									<?php
if (!isset($_GET['change']) && canThisIdRequestForChange($id)) {
        ?>
										<a href="<?php echo $_SERVER['PHP_SELF']; ?>?change=yes"><button type="submit" onclick="return confirm('You will be allowed to do this just once\n\nProceed?')" name="save" class="btn btn-info" type="submit"><i class='icon icon-random'></i> Request To Reset Change Of Field</button>
										</a>
								<?php }
    echo "<br/>";
}?>
								<ul id="da-thumbs" class="da-thumbs">
									<?php
if (!isset($_GET['field'])) {
    if ($yes) {
        $id = isIdAlreadyAllocated($_SESSION['id']);
        echo "<br/>" . getMatesById($id);
        ?>



										<?php } else {
        $pending = isAssignRequestById(getIdFromSession($_SESSION['id']));
        if ($pending) {
            $msg = "You curently do not have any selected field of interest and you will not be allowed to carry out the allocation by yourself because you have a pending request that awaits rejection/approval.";
        } else {
            $msg = "You curently do not have any selected field of interest!";
        }
        ?>
											<div class="alert alert-danger"><i class="icon-ban-circle"></i>
												<?php echo $msg; ?>
											</div>
											<button data-placement="top" id="signin_student" onclick="window.location='<?php echo $_SERVER['PHP_SELF'] ?>?field=yes'" name="login" class="btn btn-info" type="submit">Select Field Of Interests Now !</button>
										<?php }?>
								</ul>
							<?php } else {
    if (isset($_POST['fields'], $_POST['save'])) {
        echo allocator(@$_POST['fields'], @$_SESSION['id']);
    }
    ?>

								<div class="alert alert-info"><i class="icon-info-sign"></i> You are expected to select (based on preference) 3 fields of interests.
								</div>
								<form action="" method="post" onsubmit="return validate()">
									<p>
										<label for='field1'>Select Your Most Preferred Field</label>
										<select onchange="validateForm(this.id)" type="text" name="fields[]" id="field1" class="input-block-level span12" required>
											<option value="">Most Preferred</option>
											<?php echo getFieldsToStudents(); ?>
										</select>
									</p>
									<p>
										<label for='field2'>Select Your Preferred Field</label>
										<select type="text" onchange="validateForm(this.id)" name="fields[]" id="field2" class="input-block-level span12" required>
											<option value="">Preferred</option>
											<?php echo getFieldsToStudents(); ?>
										</select>
									</p>
									<p>
										<label for='field3'>Select Your Least Preferred Field</label>
										<select onchange="validateForm(this.id)" type="text" name="fields[]" id="field3" class="input-block-level span12" required>
											<option value="">Least Preferred</option>
											<?php echo getFieldsToStudents(); ?>
										</select>
									</p>
									<button type="submit" onclick="return confirm('You will not be allowed to modify your selections afterwards\nProceed?')" name="save" class="btn btn-info" type="submit">Proceed</button>
								</form>
							<?php
}?>
							</div>
						</div>
					</div>
					<!-- /block -->
				</div>


			</div>

		</div>
		<?php include 'footer.php';?>
	</div>
	<?php include 'script.php';?>
</body>
<script>
	let none = false;

	function validateForm(f) {
		let field = document.getElementById(f).value;
		if (none == false) {
			if (field == 0) {
				none = true;
				alert("You just selected \'None\'\nSystem will automatically allocate you to a random field of interest\nAre you sure about this?");
				return true;
			}
		} else {
			if (none == true) {

			} else
				none = false;
		}
		return true;
	}
</script>

</html>