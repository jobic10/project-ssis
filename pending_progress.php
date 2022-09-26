<?php include('header_dashboard.php'); ?>
<?php include('supervisor_session.php'); ?>
<body>
<?php include('navbar_supervisor.php'); 
if (isset($_GET['action'],$_GET['uploadId']) && ($_GET['action'] == 'download' || $_GET['action'] == 'delete') && (in_array($_GET['uploadId'],(range(0,10))))){
	$id = getIdFromSession($_SESSION['id'],'student');
	echo actionOnProgress($id,$_GET['uploadId'],$_GET['action']);
	
}

if (isset( $_POST['action'],$_POST['id'])){
$id = $_POST['id'];
$action = $_POST['action'];
if ($action == 'yes'){
	//No need for MSG
echo	updateStudentProgress($id,$action);

}else{
	//
	if (!isset($_POST['msg'])) echo script("Fill the response field");
echo	updateStudentProgress($id,$action,$_POST['msg']);
}
}
?>
<div class="container-fluid">
<div class="row-fluid">
<?php include('supervisor_sidebar.php'); ?>
<div class="span9" id="content">
<div class="row-fluid">
<!-- breadcrumb -->

	

<ul class="breadcrumb">
<?php /*<li><a href="#"><?php echo $class_row['class_name']; ?></a> <span class="divider">/</span></li>*/ ?>
<?php /*<li><a href="#"><?php echo $class_row['subject_code']; ?></a> <span class="divider">/</span></li>*/ ?>
<li><a href="#">School Year: <?php echo date('Y');  ?></a> <span class="divider">/</span></li>
<li><a href="#"><b>Pending Progress</b></a></li>
</ul>
<!-- end breadcrumb -->

<!-- block -->
<div id="block_bg" class="block">
<div class="navbar navbar-inner block-header">
<div id="" class="muted pull-left"><h4> Student Project Progress - Pending</h4></div>
</div>
<div class="block-content collapse in">
<div class="span12">
		<table cellpadding="0" cellspacing="0" border="0" class="table" id="example">

		<thead>
				<tr>
				<th>SN</th>
				<th>Student</th>
				<th>Chapter</th>
				<th>Action</th>
				</tr>
		</thead>
		<tbody>
		<?php
		// $id = getIdFromSession($_SESSION['id'],'supervisor');
		$query = getProgress(1);
		/*SELECT progress.status, progress.link, progress.chapter FROM `students`  
		*/
		$sn = 0;
		while($row = $query->fetch_assoc()){
			$link = "";
			$file_link = "uploads/".$row['link'];
			$thisId = $row['id'];
			
$chapter = $row['chapter'];
if ($chapter == 0){
	$chapter = "Project Proposal";

}elseif ($chapter == 6){
	$chapter = "Project Clearance";
}else{
	$chapter = "Chapter $chapter";
}
$yes = "Are you sure you wish to approve this ? There is no going back once your confirmation is saved <br/><button type='submit' onClick='return confirm(\"Proceed Still?\")' class='btn btn-success'>Approve</button>";
$no = "Are you sure you wish to reject this? The student will have to upload again<br/><input type='submit' onClick='return confirm(\"Proceed Still?\")' class='btn btn-danger' value='Reject' />";
?>
<tr>
	<td><?php echo ++$sn; ?></td>
	<td><?php echo $name = getStudentDetailsById($row['std_id']) ?></td>
	<td><?php echo $chapter; ?></td>
	<td><?php echo '	
	<a onClick="return confirm(\'File Will Be Downloaded. Proceed?\')" href="' . $file_link . '" ><button class="btn btn-info"> View</button></a> ||  <a  href="#accept' . $thisId . '" data-toggle="modal"><button class="btn btn-success"> Accept</button></a> || <a  href="#reject' . $thisId . '" data-toggle="modal"><button class="btn btn-danger"> Reject</button> </a>	
	
	<!-- modal starts -->
    <div id="accept'.$thisId.'" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
      <h4 id="myModalLabel">Confirm Your Action On   '.ucwords(strtolower($name)).' Progress Status (Approve)</h4>
    </div>
	  <div class="modal-body">
	  <form method="POST">
	  <input type="hidden" name="id" value="'.$thisId.'" />
	  <input type="hidden" name="action" value="yes"/>
	  
	<h4>'.$yes.'</h4>

	</form>
          </div>
    <div class="modal-footer">
      <button class="btn" data-dismiss="modal" aria-hidden="true"><i class="icon-remove icon-large"></i> Close</button>
      
    </div>
  </div>
	<!-- Modal ends -->
	
	
	
	
	
	<!-- modal starts -->
    <div id="reject'.$thisId.'" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
      <h4 id="myModalLabel">Confirm Your Action On  '.ucwords(strtolower($name)).' Progress Status (Reject)</h4>
    </div>
	  <div class="modal-body">
	  <form method="POST">
	  <input type="hidden" name="action" value="no"/>
	  <input type="hidden" name="id" value="'.$thisId.'" />
	  <p>Why are you rejecting this? Let the student know his shortcomings</p>
	  <textarea class="" required name="msg" minlength ="10" maxlength = "999" placeholder="Enter message to send to this student"></textarea>
	<h4>'.$no.'</h4>

	</form>
          </div>
    <div class="modal-footer">
      <button class="btn" data-dismiss="modal" aria-hidden="true"><i class="icon-remove icon-large"></i> Close</button>
      
    </div>
  </div>
	<!-- Modal ends -->'; 
	

	
	
	?></td>
</tr>
			<?php
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


</div></div>				
<?php /* include('downloadable_sidebar.php') */ ?>
</div>
<?php include('footer.php'); ?>
</div>
<?php include('script.php'); ?>
</body>
</html>