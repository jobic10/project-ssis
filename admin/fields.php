<?php include('header.php'); ?>
<?php include('session.php'); ?>
<?php
if (isset($_POST['delete_subject'])){
	$id=@$_POST['selector'];
	if ($id == NULL){
		?>
	<script>alert("Select Before You Delete");
	window.location = 'subjects.php';
	</script>
		<?php exit;
	}
	$response = delField($id);
	if ($response == 1){
	?>
	<script>alert("Deleted Successfully");</script>
	<?php }elseif($response == 2){
		?>
	<script>alert("In your last action, one or more fields could not be deleted because supervisor already chose them. Try Editing It Instead");</script>
	
		<?php
	}else{
		echo '<script>alert("Unknown Error Occured");</script>';
	}
	}
?>
    <body>
		<?php include('navbar.php'); ?>
        <div class="container-fluid">
            <div class="row-fluid">
				<?php include('sidebar_dashboard.php'); ?>
		
                <div class="span9" id="content">
                     <div class="row-fluid">
					 <a href="add_fields.php" class="btn btn-info"><i class="icon-plus-sign icon-large"></i> Add Field Of Interest (Single)</a>
					 <a href="add_fields.php?bulk=true" class="btn btn-info"><i class="icon-upload icon-large"></i> Add Field Of Interests (Upload CSV)</a>
                        <!-- block -->
                        <div id="block_bg" class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">Subject List</div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
									<form action="" method="post">
  									<table cellpadding="0" cellspacing="0" border="0" class="table" id="example">
									<a data-toggle="modal" href="#delete_field" id="delete"  class="btn btn-danger" name=""><i class="icon-trash icon-large"></i></a>
									<?php include('modal_delete.php'); ?>
										<thead>
										  <tr>
											    <th></th>
												<th>ID</th>
												<th>Field of Interest</th>
												<th></th>
										   </tr>
										</thead>
										<tbody>
											
										<?php echo getFields(); ?>
											
                              
										</tbody>
									</table>
									</form>
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