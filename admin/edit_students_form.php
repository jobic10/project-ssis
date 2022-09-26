<?php
if (!isset($file_access))
die("Direct Access To File Not Allowed");
?><div class="row-fluid">
       <a href="students.php" class="btn btn-info"><i class="icon-plus-sign icon-large"></i> Add Student</a>
                        <!-- block -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">Edit Student</div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
								<form method="post">
				
									<?php
									$row = getDetailsById(base64_decode($_GET['id']),"students");
									?>
										
										  <div class="control-group">
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
                                            <input class="input focused" value="<?php echo $row['phone']; ?>" name="phone" id="focusedInput" type="text" required placeholder = "Phone">
                                          </div>
                                        </div>


                                        <div class="control-group">
                                          <div class="controls">
                                            <input class="input focused" value="<?php echo $row['email']; ?>" name="email" id="focusedInput" type="email" required placeholder = "Email">
                                          </div>
                                        </div>


										<div class="control-group">Leave this blank if you do not wish to change student's password
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
                                $phone = $_POST['phone'];
                                $email = $_POST['email'];
                                $password = $_POST['password'];
                                $id = base64_decode($_GET['id']);
              $update = updateRecordById($id,$firstname,$lastname,$password,"-1","students",$email,$phone);
              echo $update;
                             } ?>
						 
						 