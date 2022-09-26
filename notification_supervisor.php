<?php include 'header_dashboard.php';?>
<?php include 'supervisor_session.php';?>
    <body>
		<?php include 'navbar_supervisor.php';?>
        <div class="container-fluid">
            <div class="row-fluid">
			<?php include 'supervisor_sidebar.php';

if (isset($_GET['status'], $_GET['type'], $_GET['id']) && ($_GET['status'] == 1 || $_GET['status'] == -1) && ($_GET['type'] == 'special' || $_GET['type'] == 'normal')) {
    $type = $_GET['type'];
    $status = $_GET['status'];
    $id = $_GET['id'];
    echo assignReply($id, $status, $type);
    exit;
}

?>
                <div class="span9" id="content">
                     <div class="row-fluid">
					    <!-- breadcrumb -->
					     <ul class="breadcrumb">

							<li><a href="#"><b>My Class</b></a><span class="divider">/</span></li>
							<li><a href="#">School Year: <?php echo date('Y'); ?></a><span class="divider">/</span></li>
							<li><a href="#"><b>Notification</b></a></li>
						</ul>
						 <!-- end breadcrumb -->
                        <!-- block -->
                        <div id="block_bg" class="block">
                            <div class="navbar navbar-inner block-header">
								<div id="" class="muted pull-left"></div>
								<h5>Your Notifications</h5>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">



					<?php
if ($total == 0) {
    echo "You do not have any request from the admin.";
}
if ($getcount->num_rows) {echo "<h4 class='alert alert-info'>From Administrator, <hr/>Dear Supervisor, you have request" . (($getcount->num_rows == 1) ? "" : "s"), " to supervise " . (($getcount->num_rows == 1) ? "one student, approving this request" : "some students, approving this requests") . " means that you are accepting to supervise the student(s) in question.<br/> Kindly reply this request. Thanks</h4>";

    ?>
					<table class='table table-hover' id='example'>
						<thead><tr><th>SN</th><th>Student Details</th><th>	Field of interest</th><th>Action</th></tr></thead>
	<?php
$id = 0;
    while ($query = $getcount->fetch_assoc()) {
        ?>
						<tr>
							<td><?php echo ++$id; ?></td>
							<td><?php echo getStudentDetailsById($query['stdid']); ?></td>
		<td><?php echo getFieldNameFromCpuID($query['cpu_id']); ?></td>
		<td><a onclick="return confirm('Are you sure you want to approve this request? \nIf the number of students you wanted is full, there will be an incrementation.. \nThere is no going back once this action is performed\nProceed Still ?')" href='<?php echo $_SERVER['PHP_SELF'] . "?type=normal&status=1&id=" . $query['id']; ?>'><button class='btn btn-success'><i class='icon-check'></i> Approve  </button></a> ||| <a onClick='return confirm("You sure you want to reject this request?")' href='<?php echo $_SERVER['PHP_SELF'] . "?type=normal&status=-1&id=" . $query['id']; ?>'><button class='btn btn-danger'><i class='icon-ban-circle'></i> Reject  </button></a></td>

	</tr>

		<?php }?>
</table>

					<?php }

if ($getcount2->num_rows) {

    ?>
					<table class='table table-hover' id='example'>
						<thead><tr><th>SN</th><th>Student Details</th><th>	Field of interest</th><th>Action</th></tr></thead>
	<?php
$id = 0;
    while ($query = $getcount2->fetch_assoc()) {
        ?>
						<tr>
							<td><?php echo ++$id; ?></td>
							<td><?php echo getStudentDetailsById($query['stdid']); ?></td>
		<td><?php $field = getFieldById($query['field_id']);
        echo $field['name'];
        ?></td>
		<td><a onclick="return confirm('Are you sure you want to approve this request? \nIf the number of students you wanted is full, there will be an incrementation.. \nThere is no going back once this action is performed\nProceed Still ?')" href='<?php echo $_SERVER['PHP_SELF'] . "?type=special&status=1&id=" . $query['id']; ?>'><button class='btn btn-success'><i class='icon-check'></i> Approve  </button></a> ||| <a onClick='return confirm("You sure you want to reject this request?")' href='<?php echo $_SERVER['PHP_SELF'] . "?type=special&status=-1&id=" . $query['id']; ?>'><button class='btn btn-danger'><i class='icon-ban-circle'></i> Reject  </button></a></td>

	</tr>

		<?php }?>
</table>

					<?php }?>

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
</html>