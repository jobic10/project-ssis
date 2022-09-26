<?php
if (!isset($file_access))
die("Direct Access To File Not Allowed");
?>
<?php
if (isset($_POST['my_message'],$_POST['to']) && ($_POST['to']== 0 || $_POST['to'] > -1 || $_POST['to'] == 2)){
	$msg = $_POST['my_message'];
	$to = $_POST['to'];
	$file = "attachment";
	$id = getIdFromSession($_SESSION['id'],'supervisor');
	echo sendMessageFromSupervisor($id,$to,$msg,$file);
}

if (isset($_POST['my_message_to_student'],$_POST['to']) && ($_POST['to']== 0 || $_POST['to'] > -1)){
	$msg = $_POST['my_message_to_student'];
	$to = $_POST['to'];
	$file = "attachment";
	$id = getIdFromSession($_SESSION['id'],'supervisor');
	echo sendMessageFromSupervisor($id,$to,$msg,$file,'student');
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
										<li class="<?php if (!isset($_GET['sendToGroup'])) echo 'active'; ?>">
											<a href="supervisor_message.php">Compose new message</a>
										</li>
										<li class="<?php if (isset($_GET['sendToGroup'])) echo 'active' ?>">
											<a href="supervisor_message.php?sendToGroup=true">Send To Group</a>
										</li>
									</ul>
								
								
	<?php if (isset($_GET['sendToGroup'])) {?>

								<form method="post" enctype="multipart/form-data">
										<div class="control-group">
											<label>To:</label>
                                          <div class="controls">
                                            <select name="to" class="chzn-select" required>
                                              	<option value="">Choose Recipient</option>

<option value="0">All Groups</option>
<?php 
$query = listAllGroups();
while ($row= $query->fetch_assoc()){
	$id = $row['id'];
	$name = $row['name'];
	?>
<option value="<?php echo $id ?>">Group -> <?php echo $name; ?></option>
	<?php
}
?>
												
										
											
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

<?php }else{
	// echo listAllStudents();
?>

<form method="post" enctype="multipart/form-data">
										<div class="control-group">
											<label>To:</label>
                                          <div class="controls">
                                            <select name="to" class="chzn-select" required>
                                              	<option value="">Choose Recipient (Students)</option>
<option value="0">All Students Under Me</option>

<?php 
$query = listAllStudents();
while ($row= $query->fetch_assoc()){
	$id = $row['id'];
	$name = ucwords(strtolower($row['std']));
	$name = substr($name,0,20);
	?>
<option value="<?php echo $id ?>"><?php echo $name; ?></option>
	<?php
}
?>
												
										
											
                                            </select>
                                          </div>
                                        </div>
								
							
										<div class="control-group">
											<label>Content:</label>
                                          <div class="controls">
											<textarea name="my_message_to_student" class="my_message" required></textarea>
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
					<button  class="btn btn-success"><i class="icon-envelope-alt" onclick="return confirm('You will not be allowed to edit/delete this afterwards\nProceed?')"></i> Send To Student</button>

                                          </div>
                                        </div>
                                </form>
<?php } ?>
								
								
							
								
								
										
								
								</div>
                            </div>
                        </div>
                        <!-- /block -->
						

	</div>
</div>