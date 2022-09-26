<?php
if (!isset($file_access))
die("Direct Access To File Not Allowed");
?>
   <div class="row-fluid">
                        <!-- block -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">Add Student</div>
                                
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
                                <?php if (!isset($_GET['bulk'])){?>

								<form method="post">
										
										  <div class="control-group">
											<label>Enter Matric No:</label>
                                          <div class="controls">
                                          
                                          </div>
                                        </div>
										
										<div class="control-group">
                                          <div class="controls">
<input class="input focused" maxlength="10" minlength="10" name="regno" id="regno"  pattern="[0-9]{2}/[0-9]{2}[A-Za-z]{2}[0-9]{3}" type="text" required  placeholder = "Matric Number"> 
                                          </div>
                                        </div>
										
									<div class="control-group">
                                          <div class="controls">
												<button name="save" class="btn btn-info"><i class="icon-plus-sign icon-large"></i> Save Student</button>

                                          </div>
                                        </div>
                                </form>
                                <a href="<?php echo $_SERVER['PHP_SELF']."?bulk";?>"><button name="save" class="btn btn-info"><i class="icon-upload icon-large"></i> Upload List of Student</button></a> <?php } else{ ?> 
                                    <form method="post" enctype="multipart/form-data">
										
										  <div class="control-group">
											<label>Upload CSV File:</label>
                                          <div class="controls">
                                          
                                          </div>
                                        </div>
										
										<div class="control-group">
                                          <div class="controls">
<input class="input focused" name="file" type="file" required  placeholder = "Upload File" accept="*.csv"> 
                                          </div>
                                        </div>
										
									<div class="control-group">
                                          <div class="controls">
												<button name="uploadfile" class="btn btn-info"><i class="icon-plus-sign icon-large"></i> Process CSV File</button>

                                          </div>
                                        </div>
                                </form>
                                <a href="<?php echo $_SERVER['PHP_SELF'];?>"><button name="save" class="btn btn-info"><i class="icon-upload icon-large"></i> Add Student (Single)</button></a>
                                    <?php }		?>		
								</div>
                            </div>
                        </div>
                        <!-- /block -->
                    </div>
  

	
					
					
						 