<?php
if (!isset($file_access))
die("Direct Access To File Not Allowed");
?>
   <div class="row-fluid">
                        <!-- block -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">Add Supervisor</div>
                                
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
                                <?php if (!isset($_GET['bulk'])){?>

								<form method="post">
										
                <div class="control-group">
											<label>Enter File No:</label>
                                          <div class="controls">
                                          
                                          </div>
                                        </div>
										
										<div class="control-group">
                                          <div class="controls">
<input class="input focused" maxlength="5" minlength="5" name="fileno" id="fileno" type="text" required  placeholder = "File Number"> 
                                          </div>
                                        </div>

                                        <div class="control-group">
											<label>Enter Max No of Students To Be Assigned:</label>
                                          <div class="controls">
                                          
                                          </div>
                                        </div>
										
										<div class="control-group">
                                          <div class="controls">
<input class="input focused" maxlength="2" minlength="1" name="max" id="max" type="number" min='1' max='40' required  placeholder = "Maximum Number Of Students"> 
                                          </div>
                                        </div>
										
									<div class="control-group">
                                          <div class="controls">
												<button name="save" class="btn btn-info"><i class="icon-plus-sign icon-large"></i> Save Supervisor</button>

                                          </div>
                                        </div>
                                </form>
                                <a href="<?php echo $_SERVER['PHP_SELF']."?bulk";?>"><button name="save" class="btn btn-info"><i class="icon-upload icon-large"></i> Upload List of Supervisor</button></a> <?php } else{ ?> 
                                    <form method="post" enctype="multipart/form-data">
										
										  <div class="control-group">
											<label>Upload CSV File:</label>
                                          <div class="controls">
                                          
                                          </div>
                                        </div>
										
										<div class="control-group">
                                          <div class="controls">
<input class="input focused" maxlength="5" minlength="5" name="file" type="file" required  placeholder = "Upload File" accept="*.csv"> 
                                          </div>
                                        </div>
										
									<div class="control-group">
                                          <div class="controls">
												<button name="uploadfile" class="btn btn-info"><i class="icon-plus-sign icon-large"></i> Process CSV File</button>

                                          </div>
                                        </div>
                                </form>
                                <a href="<?php echo $_SERVER['PHP_SELF'];?>"><button name="save" class="btn btn-info"><i class="icon-upload icon-large"></i> Add Supervisor (Single)</button></a>
                                    <?php }		?>		
								</div>
                            </div>
                        </div>
                        <!-- /block -->
                    </div>
  

	
					
					
						 