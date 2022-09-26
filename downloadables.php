<?php
if (isset($_GET['zip']) && in_array($_GET['zip'], range(-1, 1))) {
    $file_access = 1;
    require_once 'functions.php';
    require_once 'session.php';
    $mat = $_SESSION['id'];
    $student_id = getIdFromSession($mat);
    $type = $_GET['zip'];
    echo zipFilesByStudentId($student_id, $type);
}
include 'header_dashboard.php';?>
<?php include 'session.php';

?>
    <body>
		<?php include 'navbar_student.php';?>
        <div class="container-fluid">
            <div class="row-fluid">
				<?php include 'student_sidebar.php';

?>
                <div class="span9" id="content">
                     <div class="row-fluid">
					  <!-- breadcrumb -->
					  <div id="zip" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

					  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
		<h3 id="myModalLabel">Download Uploaded Materials</h3>
	</div>
		<div class="modal-body">
								<div class="control-group">

			<a href='<?php echo $_SERVER['PHP_SELF'] . "?zip=1"; ?>'><button class="btn btn-success" ><i class="icon-check icon-large"></i> Approved Materials</button></a>
			<hr/><a href='<?php echo $_SERVER['PHP_SELF'] . "?zip=0"; ?>'><button class="btn btn-info" ><i class="icon-question-sign icon-large"></i> Materials Awaiting Response</button></a><hr/>
			<a href='<?php echo $_SERVER['PHP_SELF'] . "?zip=-1"; ?>'><button class="btn btn-danger" ><i class="icon-remove icon-large"></i> Rejected Materials</button></a>
								</div>

		</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true"><i class="icon-remove icon-large"></i> Close</button>
		<button class="btn btn-info" name="change"><i class="icon-save icon-large"></i> Save</button>
	</div>
</div>


					     <ul class="breadcrumb">
							<?php /*<li><a href="#"><?php echo $class_row['class_name']; ?></a> <span class="divider">/</span></li>*/?>
							<?php /*<li><a href="#"><?php echo $class_row['subject_code']; ?></a> <span class="divider">/</span></li>*/?>
							<li><a href="#"><b>Downloadables</b></a></li>
						</ul>
						 <!-- end breadcrumb -->

                        <!-- block -->
                        <div id="block_bg" class="block">
                            <div class="navbar navbar-inner block-header">
                                <div id="" class="muted pull-left">Downloadables</div>
                                <div id="" class="pull-right"><a href="#zip" data-toggle="modal" class="badge-info badge">Download Uploaded Materials</a></div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">

								<?php
// echo $id;
$query = getMyFiles($id);
// echo $row;
?>
									<table class="table table-hover" id='example'>
									<caption><h4><?php echo $query->num_rows . " data returned"; ?></h4></caption>
<thead>
	<tr>
		<th>SN</th>
		<th>Message</th>
		<th>Action</th>
		<th>Properties</th>
		<th>Date Saved</th>
	</tr>
</thead>

<tbody>
<?php $sn = 0;
while ($row = $query->fetch_assoc()) {
    $loc = "uploads/" . $row['attachment'];
    ?>
<tr>
	<td><?php echo ++$sn; ?></td>
	<td><?php echo $row['msg']; ?></td>
	<td><a href="<?php echo $loc ?>"><button class='btn btn-info'>Download</button></a></td>
	<td><?php
$kb = ceil((filesize($loc)) / 1024);
    echo substr(($kb / 1024), 0, 5) . " MB";
    ?></td>
	<td><?php echo $row['entry_date']; ?></td>
</tr>
<?php }?>
</tbody>

									</table>
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