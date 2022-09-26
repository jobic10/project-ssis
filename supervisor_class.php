<?php
if (!isset($file_access))
die("Direct Access To File Not Allowed");
?>
<?php

										if (isset($_POST['save'])){
											$fields = @$_POST['selector'];
											$no = @$_POST['no'];
											$number = array_values(array_filter($no,"cleanArray"));
											/*Function cleanArray is inside functions.php
											I used it to filter out zeroes
											*/
											echo saveFields($fields,$number);

										}
						if (isset($_GET['field'])){
							if (!validateIfFieldHasBeenChosen()){
								echo "<h4 class='text-info'>The maximum number of students you could get is ".getMyMax($id).", if you'd like to have more, contact the system administrator.</h4>";
							?>
							<form action="" method="post">
							<table cellpadding="0" cellspacing="0" border="0" class="table" id="example">
								<tr>
									<th>&nbsp;</th>
									<th>Field Of Interests</th>
									<th>N<u>o</u> of students</th>
								</tr>
							<?php echo getFieldsToUsers();?>
							</table>
							
							<button type="submit" onclick="return confirm('You will not be allowed to modify your selections afterwards\nProceed?')"  name="save" class="btn btn-info" type="submit">Save</button>
							</form>

										
							<?php }else{
								echo '<div class="alert alert-danger"><h1>You have already done this!</h1></div>';
							}
						}	else{	if (validateIfFieldHasBeenChosen()){
							$query = listAllGroups();
			
			
										
							$no_of_fields = $query->num_rows;
							echo '<ul	 id="da-thumbs" class="da-thumbs">';	
			
										while($row = $query->fetch_assoc()){
										$id = $row['id'];
											$status = $row['full']." out of ".$row['no']." ";
											if ($row['full'] == $row['no'])$status.= " <br/>(Full)";else $status.="<br/>(Not full)";
										?>
		<li id="del<?php echo $id; ?>">
			<a href="my_students.php<?php echo '?id='.$id; ?>">
				<center>	<?php genImageFromText($row['name']) ?></center>
				<div>
				<span><p><?php echo $row['name']; ?></p></span>
				</div>
			</a>
			<p class="class"><?php echo ucwords(strtolower($row['name'])); ?></p>
		<p class="subject" align='center'>Group Status: <?php echo $status; ?> </p> <hr/>
	<div align="center">	<a href="group_message.php?groupID=<?php echo $id; ?>" data-toggle="modal"><button class='btn btn-success'><i class="icon-comments"></i> Open Group</button></a>	
			<a href="#edit<?php echo $id; ?>" data-toggle="modal"><button class='btn btn-warning'><i class="icon-pencil"></i> Edit</button></a>	
			<a class='text-error' onclick="return confirm('Are you sure you would like to delete this field?')" href="<?php echo $_SERVER['PHP_SELF']; ?>?delID=<?php echo $id; ?>"><button class='btn btn-danger'><i class="icon-trash"></i> Delete</button></a>	</div>	
	</li>	
		
			
										<?php include("edit_group.php"); ?>
									<?php } }else{ ?>
									<div class="alert alert-danger"><i class="icon-info-sign"></i> <strong>Dear <?php echo getUserFullName($_SESSION['id'],"staff"); ?>, our system detects that you are yet to select field of research(s) you are interested in.</strong>
								</div>
								<button data-placement="top" id="signin_student" onclick="window.location='<?php echo $_SERVER['PHP_SELF'] ?>?field=yes'"  name="login" class="btn btn-info" type="submit">Select Field Of Interests</button>

									<?php  }} ?>
									</ul>