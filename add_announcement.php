<?php include 'header_dashboard.php';?>

<?php include 'supervisor_session.php';?>

<body id="class_div">
	<?php include 'navbar_supervisor.php';?>
	<div class="container-fluid">
		<div class="row-fluid">
			<?php include 'supervisor_sidebar.php';?>
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
							<div id="count_class" class="muted pull-right">

							</div>
						</div>
						<div class="block-content collapse in">
						<form class="" method="post" enctype="multipart/form-data">

							<div class="span4">
								<?php
if (isset($_POST['content'], $_POST['upload'])) {

    $content = $_POST['content'];
    $cpu = @$_POST['group'];
    if (count($cpu) < 1 || count($cpu) > listAllGroups()->num_rows) {
        echo script("Kindly check the group(s) you would like to send this announcement", 1);
    } else {
        $id = getIdFromSession($_SESSION['id'], "supervisor");
        echo addAnnouncement($id, $cpu, $content, 'file');
    }
}
?>

<div class="control-group">

<div class="controls">
Announcement Message:

<textarea name="content" required minlength="10" maxlength="1000" class="my_message" placeholder="Enter Announcement" rows="10" ></textarea>
</div>
</div>
<div class="control-group">
<div class="controls">
Attachment: (Optional)
<input name="file" type='file' class="input-file uniform_on" />
</div>
</div>
</div>
<div class="span8">
<div class="alert alert-info">Check the group(s) to receive this.</div>

<table cellpadding="0" cellspacing="0" border="0" class="table" id="">
<thead>
<tr><th></th><th>ID</th><th>Group Name</th></tr>
</thead>
<tbody>
<?php $query = listAllGroups();
$count = ($query->num_rows);
$sn = 0;
while ($row = ($query->fetch_assoc())) {
$id = $row['id'];
?>
<tr>
<td width="30">
<input class="uniform_on" name="group[]" type="checkbox" value="<?php echo $id; ?>">

</td>
<td><?php echo ++$sn; ?></td>
<td><?php echo $row['name']; ?></td>
</tr>

<?php }?>



</tbody>
</table>

</div>
<div class="span10">
<hr>
<center>
<div class="control-group">
<div class="controls">
<input name="upload" onclick="return confirm('Are you sure you checked the group(s) ?')" type="submit" value="Upload" class="btn btn-success" value='Post'>
</div>
</div>
</center>

</form>
							</div>
						</div>
					</div>
					<!-- /block -->
				</div>


			</div>
			<?php /*  include('supervisor_right_sidebar.php')  */?>

		</div>
		<?php include 'footer.php';?>
	</div>
	<?php include 'script.php';?>
</body>

</html>