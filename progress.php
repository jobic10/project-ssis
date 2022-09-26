<?php include 'header_dashboard.php';?>
<?php include 'session.php';?>

<body>
	<?php include 'navbar_student.php';
if (isset($_GET['action'], $_GET['uploadId']) && ($_GET['action'] == 'download' || $_GET['action'] == 'delete') && (in_array($_GET['uploadId'], (range(0, 10))))) {
    $id = getIdFromSession($_SESSION['id'], 'student');
    echo actionOnProgress($id, $_GET['uploadId'], $_GET['action']);
}

if (isset($_POST['upload'], $_POST['id'], $_FILES['file'])) {
    $file = 'file';
    $chapter = $_POST['id'];
    echo uploadProgress($chapter, $file);
}
?>
	<div class="container-fluid">
		<div class="row-fluid">
			<?php include 'student_sidebar.php';?>
			<div class="span9" id="content">
				<div class="row-fluid">
					<!-- breadcrumb -->



					<ul class="breadcrumb">
						<?php /*<li><a href="#"><?php echo $class_row['class_name']; ?></a> <span
class="divider">/</span></li>*/?>
						<?php /*<li><a href="#"><?php echo $class_row['subject_code']; ?></a> <span
class="divider">/</span></li>*/?>
						<li><a href="#">School Year: <?php echo date('Y'); ?></a> <span class="divider">/</span></li>
						<li><a href="#"><b>Progress</b></a></li>
					</ul>
					<!-- end breadcrumb -->

					<!-- block -->
					<div id="block_bg" class="block">
						<div class="navbar navbar-inner block-header">
							<div id="" class="muted pull-left">
								<h4> My Project Progress</h4>
							</div>
						</div>
						<div class="block-content collapse in">
							<div class="span12">
								<table cellpadding="0" cellspacing="0" border="0" class="table" id="">

									<thead>
										<tr>
											<th>SN</th>
											<th>Project</th>
											<th>Status</th>
											<th>Action</th>

									</thead>
									<tbody>
										<tr>
											<td>1</td>
											<td>Project Field</td>
											<?php $isAssign = 0;
$a = isStudentAssigned($_SESSION['id']);
if ($a[0] != 1) {

    ?><td>
													<span class="badge badge-important">You do not have any field of
														interest</span>
												</td>
												<td><a href="dashboard_student.php?field=yes"><button class='btn btn-primary'><i class="icon icon-pencil"></i>
															Choose</button></a></td>
											<?php
} else {
    $isAssign = 1;
    echo '<td>
					<span class="badge badge-info">Field of interest approved!</span>
					</td><td><i class="icon icon-check"></i></td>';
}
?>
										</tr>
										<?php
$id = getIdFromSession($_SESSION['id']);
if (file_exists($id . ".png")) {
    @unlink($id . ".png"); //Barcode might exist, to clean it away from the server, I had to unlink
}
$avatar = getStudentAvatarById($id);
$sn = 2;
$set = 0;
$countCleared = 0;
for ($i = 0; $i < 7; $i++) {
    echo "<tr>
			<td>" . ($sn++) . "</td>";
    echo '<td>';
    $clearance = "";
    if ($i == 0) {
        echo $name = "Project Proposal";
    } elseif ($i < 6) {
        echo $name = "Chapter " . ($i);
    } else {
        echo $name = "Project Clearance";
        $clearance = "Upload Your Whole Project - From Cover page to appendix";
    }
    echo "</td>";
    echo '';

    $row = checkProgressByStudentId($id, $i);
    if ($row[0] < 1) {
        ++$set;

        $response = $row[1];
        ?>
												<td><span class="badge badge-important"><?php echo substr($response, 0, 12) ?></span>
												</td>
												<td>
													<?php if (($set == 1) && $isAssign) {
            echo '<a  href="#upload' . $i . '" data-toggle="modal"><button class="btn btn-info"><i class="icon icon-upload"></i> Upload</button></a> || <a  href="#view' . $i . '" data-toggle="modal"><button class="btn btn-success"><i class="icon icon-download"></i> View</button></a>

<div id="upload' . $i . '" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
		<h3 id="myModalLabel">Upload Your ' . $name . '</h3>
		<h4>' . $clearance . '</h4>
	</div>
		<div class="modal-body">
	<form method="POST" enctype="multipart/form-data">

		<div>
		<!-- <p><span class="badge badge-info">Upload Valid Extensions (.docx, .doc, .pdf, .pptx, .ppt)</span></p> -->
	<input type="file"  required name="file" class="input-block-level input-file uniform_on">
	<input type="hidden"  name="id" value ="' . $i . '"><hr/>
	<button onClick="return confirm(\'Are you sure you wish to upload this?\nIf you have already uploaded for ' . $name . ', this will overwrite it\n\nProceed?\')" class="btn btn-success" type="submit" name="upload"><i class="icon-check icon-large"></i> Upload</button>
		</div>
	</form>
		</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true"><i class="icon-remove icon-large"></i> Close</button>

	</div>
</div>

<div id="view' . $i . '" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
		<h3 id="myModalLabel">Viewing Your ' . $name . ' Status</h3>

	</div>
		<div class="modal-body">
	<h4>' . $response . '</h4>
	' . ((strlen($row[2]) < 10) ? "" : '<a href="' . $_SERVER['PHP_SELF'] . '?uploadId=' . $i . '&action=delete"><button onClick="return confirm(\'Deleting This Has No Undo\n\nProceed?\')" class="btn btn-danger" type="submit" name="editNo"><i class="icon-trash icon-large"></i> Delete Previous Upload</button></a> || <a target="_blank" href="' . $_SERVER['PHP_SELF'] . '?uploadId=' . $i . '&action=download"><button class="btn btn-success" type="submit" name="editNo"><i class="icon-download icon-large"></i> Download Previous Upload</button></a>') . '
		</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true"><i class="icon-remove icon-large"></i> Close</button>

	</div>
</div>
';
        } else {
            echo '<span class="badge badge-inverse">' . $response . '</span>';
        }?>

												</td>
											<?php
} else {
        $countCleared++;
        echo '<td><span class="badge badge-success">Cleared On ' . $row[1] . '</span> </td>
					<td><a target="_blank"	 href="' . $_SERVER['PHP_SELF'] . '?uploadId=' . $i . '&action=download"><button class="btn btn-success" type="submit" name="editNo"><i class="icon-download "></i> Download</button></a></td>';
    }
    echo '</td>';
    echo '</tr>';
    ?>

										<?php
}?>
										<?php
if ($countCleared == 7) {
    $pass = getPasswordById($id);

    echo "<tr><th colspan='4' class=''><p class=''>Congratulations, you have completed your project. Use the link below to print your clearance and enter <span class='alert alert-info' style='text-transform:lowercase'><b><i>$pass</i></b></span> as your password</p><a href='print.php'><button class='btn-info btn-large'>Print Clearance</button></a></th></tr>";
}
?>




									</tbody>
								</table>

							</div>
						</div>
					</div>
					<!-- /block -->
				</div>


			</div>



			<div class="span5" id="content">
				<div class="row-fluid">
					<!-- breadcrumb -->



					<ul class="breadcrumb">

						<li><a href="#"><b>..</b></a></li>
					</ul>
					<!-- end breadcrumb -->


				</div>
			</div>
			<?php /* include('downloadable_sidebar.php') */?>
		</div>
		<?php include 'footer.php';?>
	</div>
	<?php include 'script.php';?>
</body>

</html>
