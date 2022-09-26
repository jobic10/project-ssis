<?php
if (!isset($file_access))
die("Direct Access To File Not Allowed");
?>
<div class="row-fluid">
       <a href="supervisors.php" class="btn btn-info"><i class="icon-plus-sign icon-large"></i> Add Supervisor</a>
                        <!-- block -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">Edit Supervisor</div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
								<form method="post">
				
									<?php
									$row = getDetailsById(base64_decode($_GET['id']),"staff");
									?>
										
										  <div class="control-group">
                                        </div>
										
										<div class="control-group">
                                          <div class="controls">
                                          <select class="input focused" name="title" id="title" required>
						<?php  echo listTitles($row['title_id']);?>
					</select>
                                            
                                          </div>
										</div>
										
										<div class="control-group">
                                          <div class="controls">
                                            <input class="input focused" value="<?php echo $row['firstname']; ?>" name="firstname" id="focusedInput" type="text" placeholder = "Firstname">
                                          </div>
                                        </div>
										
                                        <div class="control-group">
                                          <div class="controls">
                                            <input class="input focused" value="<?php echo $row['lastname']; ?>"  name="lastname" id="focusedInput" type="text" placeholder = "Lastname">
                                          </div>
                                        </div>

                                        	
										<div class="control-group">
                                          <div class="controls">
                                            <input class="input focused" value="<?php echo $row['max']; ?>"  name="max" id="focusedInput" type="text" placeholder = "Maximum Number of Students">
                                          </div>
                                        </div>
										
										<div class="control-group">Leave this blank if you do not wish to change supervisor's password
                                          <div class="controls">
                                            <input class="input focused"   name="password" id="focusedInput" type="text" placeholder = "Enter New Password ">
                                          </div>
                                        </div>
										
										
									
											<div class="control-group">
                                          <div class="controls">
												<button name="update" class="btn btn-success"><i class="icon-save icon-large"></i></button>

                                          </div>
                                        </div>
                                </form>
								</div>
                            </div>
                        </div>
                        <!-- /block -->
                    </div>
					
					
				   <?php
                            if (isset($_POST['update'])) {
                       
                                $firstname = $_POST['firstname'];
                                $lastname = $_POST['lastname'];
                                $title = $_POST['title'];
                                $password = $_POST['password'];
                                $max =  $_POST['max'];
                                $id = base64_decode($_GET['id']);
              $update = updateRecordById($id,$firstname,$lastname,$password,$title,$max,"supervisor");
              echo $update;
                             } ?>
						 
						 