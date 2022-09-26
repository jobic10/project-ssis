<?php include('header.php'); ?>
<?php include('session.php'); 
if (!isset($_GET['type']) || (isset($_GET['type']) && !in_array($_GET['type'],array("admin","student","supervisor")))){
logDown(0,"System log link was manipulated by the admin ",3,3);
@session_destroy();
echo script("You are not allowed to view this",1);
exit;
}
$type = $_GET['type'];
if (isset($_GET['action'])){
echo clearLogsByType($type);
}
?>
<body>
<?php include('navbar.php'); ?>
<div class="container-fluid">
<div class="row-fluid">
<?php include('sidebar_dashboard.php'); ?>

<div class="span9" id="">
	<div class="row-fluid">
	<!-- block -->
<?php
	$getAssign = getLogsByType($type);
	?>
<div id="block_bg" class="block">
		<div class="navbar navbar-inner block-header">
			<div class="muted pull-left">System Logs For <?php echo ucwords($type); ?></div>
			<div id="" class="muted pull-right"><a onClick="return confirm('Are you sure you would like to clear this logs?\nThere is no undo. \n\n Proceed Still?')" href="<?php echo $_SERVER['PHP_SELF'];?>?type=<?php echo $type?>&action=del"><button class="badge badge-important">Clear</button></a></div>
		</div>
		<div class="block-content collapse in">
			<div class="span12">
			
			
			<table cellpadding="0" cellspacing="0" border="0" class="table" id="example"><thead>
				
			<tr>
								<th><?php echo ucwords($type); ?>'s name</th>
								<th>Message</th>
								<th>Entry Date</th>
								<th>Severity</th>
					</tr>
					</thead>
					<tbody>
					
				<?php while($row = $getAssign->fetch_assoc()){?>			
					<tr>
					
		<td><?php 
		if ($type == 'admin')echo 'System Admin.';
		elseif ($type == 'student') echo getStudentDetailsById($row['student_id']);
		else echo (getSupervisorNameById($row['supervisor_id'],1));
		?></td>
		<td>
						<?php echo ($row['action']);  ?>
						</td>
		<td><?php echo (($row['entry_date'])); ?></td>
						<td><?php
						
						$severity = $row['level'];
						echo "<strong><em>".(($severity == 1)? ("<font color='blue'>$severity - Normal</font>"): (($severity == 2)? "<font color='#B24'>$severity - Medium</font>": "<font color='red'>$severity - High</font>"))."</em></strong>";
					
						?></td>
				
					</tr>
				<?php } ?>
		
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<!-- /block -->
</div>


</div>
</div>
<?php include('footer.php'); ?>
</div>
<?php include('script.php'); ?>
</body>

</html>