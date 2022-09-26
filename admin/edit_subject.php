<?php include('header.php'); ?>
<?php include('session.php'); ?>
<?php 
if (!isset($_GET['id'])) die("Access denied");
$get_id = base64_decode($_GET['id']); ?>
    <body>
		<?php include('navbar.php'); ?>
        <div class="container-fluid">
            <div class="row-fluid">
				<?php include('sidebar_dashboard.php'); ?>
		
						<div class="span9" id="content">
		                    <div class="row-fluid">
									 <a href="add_fields.php" class="btn btn-info"><i class="icon-plus-sign icon-large"></i> Add Field</a>
		                        <!-- block -->
		                        <div id="" class="block">
		                            <div class="navbar navbar-inner block-header">
		                                <div class="muted pull-left">Edit Field</div>
		                            </div>
		                            <div class="block-content collapse in">
									<a href="fields.php"><i class="icon-arrow-left"></i> Back</a>
									
									<?php
									$row = getFieldById($get_id);
									if (($row) == NULL){ echo "<script>alert('Access Denied');</script>";
									die("<h1>Access Denied</h1>");
									}
									?>
									
									    <form class="form-horizontal" method="post">
										<div class="control-group">
											<label class="control-label" for="inputPassword">Field of Interest</label>
											<div class="controls">
											<input type="text" value="<?php echo $row['name']; ?>" class="span8" name="name" id="inputPassword" placeholder="Field of Interest" required>

											<input type="hidden" value="<?php echo $row['id']; ?>" name="id"  required>
											</div>
										</div>
										
																		
											
										
										<button name="update" type="submit" class="btn btn-info"><i class="icon-save icon-large"></i> Update</button></form>
										</div>
										
										
										<?php
										if (isset($_POST['update'])){
										$id = $_POST['id'];
										$name = $_POST['name'];
																		
											$update = updateFieldById($id,$name);
											if ($update == 1){
										?>
										<script>
											alert("Field Updated");
										window.location = "fields.php";
										</script>
										<?php
										}
										else{
											?>
												<script>
													alert("Field Not Updated");
										</script>
											<?php
										}
									}
										
										?>
									
								
		                            </div>
		                        </div>
		                        <!-- /block -->
		                
            </div>
		<?php include('footer.php'); ?>
        </div>
		<?php include('script.php'); ?>
    </body>

</html>