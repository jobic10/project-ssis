<?php
if (!isset($file_access)) {
    die("Direct Access To File Not Allowed");
}

$reg_completed = validateIfFieldHasBeenChosen();
?>
<script>
function no(id){
	id.href="supervisor.php"
	alert('You are yet to have field of interest, kindly complete this.');
	window.location = 'supervisor.php';
	return false;
}
</script>
<?php
if ($reg_completed) {
    $enabled = '';
} else {
    $enabled = 'onClick="no(this.id); return false;"';
}

$currentPage = basename($_SERVER['PHP_SELF']);
?><div class="span3" id="sidebar">
	<img id="avatar" class="img-polaroid" src="uploads/<?php echo getPassport($_SESSION['id'], "staff"); ?>">
	<ul class="nav nav-list bs-docs-sidenav nav-collapse collapse">
		<li   <?php if ($currentPage == 'supervisor.php') {
    echo ' class="active"';
}
?> ><a id = 'home' href="supervisor.php"><i class="icon-chevron-right"></i><i class="icon-group"></i>&nbsp;<?php if ($reg_completed) {
    echo 'My Class';
} else {
    echo 'Choose field(s) of interest';
}
?></a></li>
		<li   <?php if ($currentPage == 'notification_supervisor.php') {
    echo ' class="active"';
}
?> ><a id = 'notification'  <?php echo $enabled ?> href="notification_supervisor.php"><i class="icon-chevron-right"></i><i class="icon-info-sign"></i>&nbsp;Notification
			<?php
$id = getIdFromSession($_SESSION['id'], "supervisor");
updateMax($id);
$getcount = getCountById($id);
$getcount2 = getSpecialCountById($id);
$total = $getcount->num_rows + $getcount2->num_rows;
if ($total == '0') {
} else {?>
					<span class="badge badge-important"><?php echo $total; ?></span>
				<?php }?>
		</a></li>
		<li   <?php if ($currentPage == 'supervisor_message.php' || $currentPage == 'group_message.php') {
    echo ' class="active"';
}
?> ><a id='message' <?php echo $enabled ?> href="supervisor_message.php"><i class="icon-chevron-right"></i><i class="icon-envelope-alt"></i>&nbsp;Message</a></li>
		<li   <?php if ($currentPage == 'change_field_request.php') {
    echo ' class="active"';
}
?> ><a id='request' <?php echo $enabled ?> href="change_field_request.php"><i class="icon-chevron-right"></i><i class="icon-suitcase"></i>&nbsp;Change Request <?php

echo countChangeFieldRequest($id);

?></a></li>
		<li   <?php if ($currentPage == 'view_by_field.php' || $currentPage == 'print_student.php' || $currentPage == 'my_students.php') {
    echo ' class="active"';
}
?> ><a id='view' <?php echo $enabled ?> href="view_by_field.php"><i class="icon-chevron-right"></i><i class="icon-table"></i>&nbsp;View Students By Field</a></li>
		<li   <?php if ($currentPage == 'add_announcement.php') {
    echo ' class="active"';
}
?> ><a id='announcement' <?php echo $enabled ?> href="add_announcement.php"><i class="icon-chevron-right"></i><i class="icon-comment"></i>&nbsp;Add Announcement</a></li>
		<li   <?php if ($currentPage == 'view_progress.php') {
    echo ' class="active"';
}
?> ><a id='view_progress' <?php echo $enabled ?> href="view_progress.php"><i class="icon-chevron-right"></i><i class="icon-magic"></i>&nbsp;View Progress</a></li>
	<li   <?php if ($currentPage == 'pending_progress.php') {
    echo ' class="active"';
}
?> ><a id='pending' <?php echo $enabled ?> href="pending_progress.php"><i class="icon-chevron-right"></i><i class="icon-random"></i>&nbsp; Pending Progress <?php

echo ((getProgress(1)->num_rows > 0) ? '<span class="badge badge-important">' . getProgress(1)->num_rows . '</span>' : '');

?></a></li>

	</ul>

</div>

