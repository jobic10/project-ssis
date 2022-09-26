<?php include 'header_dashboard.php';?>
<?php include 'supervisor_session.php';?>
    <body>
		<?php include 'navbar_supervisor.php';
if (!isset($_GET['groupID'])) {
    session_destroy();
    die(script("You do not have access to view this page", "index.php"));
}
if (canThisIdAccess($_GET['groupID']) < 0) {
    session_destroy();
    die(script("You are not authorized to view group", "index.php"));

}
?>
        <div class="container-fluid">
            <div class="row-fluid">
			<?php include 'supervisor_sidebar.php';?>
                <div class="span6" id="content">
                     <div class="row-fluid">
					    <!-- breadcrumb -->
					     <ul class="breadcrumb">
						 <li><a href="#">School Year: <?php echo date('Y'); ?></a><span class="divider">/</span></li>
								<li><a href="#">Group Message</a><span class="divider">/</span></li>
								<li><a href="#"><b><?php echo getFieldNameFromCpuID($_GET['groupID']) ?></b></a><span class="divider"></span></li>
						</ul>
						 <!-- end breadcrumb -->

                        <!-- block -->
                        <div id="block_bg" class="block">
                            <div class="navbar navbar-inner block-header">
                                <div id="" class="muted pull-left"></div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">

										<ul class="nav nav-pills">
										<li class="active">
										<a href="#"><i class="icon-envelope-alt"></i>Group Messages For <?php echo getFieldNameFromCpuID($_GET['groupID']) ?></a>
										</li>
										<?php /*li class="">
<a href="sent_message.php"><i class="icon-envelope-alt"></i>Group messages</a>*/?>
										</li>
										</ul>

									<?php
$id = getIdFromSession($_SESSION['id'], "supervisor");
$query = getMessageForGroupByID($_GET['groupID'], $id, "supervisor");
$stud = $query[0];
$sup = $query[1];

$count_my_message = $sup->num_rows;
if ($count_my_message != '0') {
    while ($row = ($sup->fetch_assoc())) {
        $sender_name = "Me";
        ?>
											<div class="post"  id="del<?php echo $id; ?>">

											<div class="message_content">
											<?php echo $row['msg']; ?>
											</div>

													<hr>
											Sent by: <strong><?php echo $sender_name ?></strong>
											<i class="icon-calendar"></i> <?php echo $row['entry_date']; ?>
												<?php if (strlen($row['attachment']) > 5) {?>	<div class="pull-right">
														<a class="btn btn-link"  href="uploads/<?php echo $row['attachment']; ?>" ><i class="icon-download"></i> Download </a>
													</div>
												<?php }?>
											</div>

								<?php }
} else {?>
								<div class="alert alert-info"><i class="icon-info-sign"></i> You are Yet To Send A Group Message To <?php echo ucwords(strtolower(getFieldNameFromCpuID($_GET['groupID']))) ?></div>
								<?php }

//Start of students
$countStudentMsg = $stud->num_rows;
if ($countStudentMsg != '0') {
    while ($rows = ($stud->fetch_assoc())) {
        $sender_name = getStudentDetailsById($rows['student_id']);
        ?>
											<div class="post"  id="del<?php echo $id; ?>">

											<div class="message_content">
											<?php echo $rows['msg']; ?>
											</div>

													<hr>
													Sent by: <strong><span class="text-info"><?php echo $sender_name ?></span></strong>
											<i class="icon-calendar"></i> <?php echo $rows['entry_date']; ?>
												<?php if (strlen($rows['attachment']) > 5) {?>	<div class="pull-right">
														<a class="btn btn-link"  href="uploads/<?php echo $rows['attachment']; ?>" ><i class="icon-download"></i> Download </a>
													</div>
												<?php }?>
											</div>

								<?php }
} else {?>
								<div class="alert alert-info"><i class="icon-info-sign"></i> No Group Message From Any Student To <?php echo ucwords(strtolower(getFieldNameFromCpuID($_GET['groupID']))) ?></div>
								<?php }

?>
                                </div>
                            </div>
                        </div>
                        <!-- /block -->
                    </div>

                </div>
				<?php include 'create_message.php'?>
            </div>
		<?php include 'footer.php';?>
        </div>
		<?php include 'script.php';?>
    </body>
</html>