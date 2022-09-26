<?php include('header.php'); ?>
<?php include('session.php'); ?>
    <body>
		<?php include('navbar.php'); ?>
        <div class="container-fluid">
            <div class="row-fluid">
				<?php include('sidebar_dashboard.php'); ?>
		
						<div class="span9" id="content">
		                    <div class="row-fluid">
							
		                        <!-- block -->
		                        <div class="block">
		                            <div class="navbar navbar-inner block-header">
		                                <div class="muted pull-left">Add New Field of Interest</div>
		                            </div>
		                            <div class="block-content collapse in">
									<a href="fields.php"><i class="icon-arrow-left"></i> Back</a>
									<?php

									if (!isset($_GET['bulk'])){?>
									<form class="form-horizontal" method="post">
										<div class="control-group">
											<label class="control-label" for="foi">Field of Interest</label>
											<div class="controls">
											<input type="text"  class="span8" name="name" id="foi" placeholder="Field of Interest" required>

											
											</div>
										</div>
										
																		
											
										
										<button name="save" type="submit" class="btn btn-info"><i class="icon-save icon-large"></i> Add</button></form>
									<?php } else{
										?>
<form class="form-horizontal" method="post" enctype="multipart/form-data">
										<div class="control-group">
											<label class="control-label" for="foi">Select CSV file </label>
											<div class="controls">
											<input type="file"  class="span8" name="file" id="foi" placeholder="Subject Title" required>
											</div>
										</div>
										<button name="upload" type="submit" class="btn btn-info"><i class="icon-save icon-large"></i> Upload</button></form>
										<?php
									}
										if (isset($_POST['upload'])){
										$file = "file";
										$saveField = saveFieldInBulk($file);
										echo $saveField;exit;
										// if ($saveField == 1){
										// 	?>
										// 	<script>
										// 	alert("Field Added");
										// 	window.location = 'subjects.php';
										// 	</script>
										// 	<?php
										// }else{
										// 	?>
										// 	<script>
										// 	alert("Fill Form Properly\nUpload Only CSV Files");
										// 	</script>
										// 	<?php	
										// }
										}
										if (isset($_POST['save'])){
											$save = saveField($_POST['name']);
												if ($save == 1){
											?>
											<script>
											alert("Field Added");
											window.location = 'fields.php';
											</script>
											<?php
										}elseif ($save == -2){
											?>
											<script>
											alert("Field Of Interest Exists");
											window.location = 'fields.php';
											</script>
											<?php
										}else{
											?>
											<script>
											alert("Fill Form Properly");
											</script>
											<?php	
										}
										}
										?>
									
								
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