<?php
if (!isset($file_access))
die("Direct Access To File Not Allowed");
?>
<?php
if (isset($_POST['my_message'],$_POST['to']) && ($_POST['to']== 0 || $_POST['to'] == 1 || $_POST['to'] == 2)){
	$msg = $_POST['my_message'];
	$to = $_POST['to'];
	$file = "attachment";
	$id = getIdFromSession($_SESSION['id']);
	echo sendMessageFromStudent($id,$to,$msg,$file);
}
										?>
<div class="span3" id="">
	<div class="row-fluid">

				      <!-- block -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div id="" class="muted pull-left"><h4><i class="icon-pencil"></i> Create Message</h4></div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
								
								    <ul class="nav nav-tabs">
										<li class="active">
											<a href="student_message.php">Send New Message</a>
										</li>
										
									</ul>
								
								
	

								<form method="post" enctype="multipart/form-data">
										<div class="control-group">
											<label>To:</label>
                                          <div class="controls">
                                            <select name="to" class="chzn-select" required>
                                              	<option value="">Choose Recipient</option>

<option value="0">Supervisor</option>
<option value="1">Group</option>
<option value="2">Both</option>
												
										
											
                                            </select>
                                          </div>
                                        </div>
								
							
										<div class="control-group">
											<label>Content:</label>
                                          <div class="controls">
											<textarea name="my_message" class="my_message" required></textarea>
                                          </div>
										</div>
										
										<div class="control-group">
											<label>Attachment (If any):</label>
                                          <div class="controls">
											<input type="file" name="attachment" class="input-file uniform_on"  >
                                          </div>
                                        </div>
										<div class="control-group">
                                          <div class="controls">
					<button  class="btn btn-success"><i class="icon-envelope-alt" onclick="return confirm('You will not be allowed to edit/delete this afterwards\nProceed?')"></i> Send </button>

                                          </div>
                                        </div>
                                </form>

					
								
								
							
								
								
										
								
								</div>
                            </div>
                        </div>
                        <!-- /block -->
						

	</div>
</div>