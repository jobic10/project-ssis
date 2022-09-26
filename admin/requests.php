<?php include('header.php'); ?>
<?php include('session.php'); ?>
    <body>
		<?php include('navbar.php'); ?>
        <div class="container-fluid">
            <div class="row-fluid">
				<?php include('sidebar_dashboard.php'); ?>
				<div class="span3" id="adduser">
				<?php include('search.php'); ?>		   			
				</div>
                <div class="span6" id="">
                     <div class="row-fluid">
                        <!-- block -->
					   <?php 
					 if (isset($_GET['del'], $_GET['id'])){
						 $id  = base64_decode($_GET['id']);
						 echo delAssign($id);
					 }  
					 
					   if (isset($_POST['fetch'])){
							$matric = $_POST['matric'];
							$row = fetchStudentByMat($matric);
							if ($row != 0){
						   ?>
						<div id="block_bg" class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left"><?php echo $name = $row['ln'].", ".$row['fn'];?></div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
								  
								
								<table cellpadding="0" cellspacing="0" border="0" class="table" id="example"><thead>
								<tr><td colspan="2">
									<?php  if ($row['cpu_id']>0)echo '<a  href="'.$_SERVER['PHP_SELF'].'?action=clear&regno='.base64_encode($row['regno']).'" id="clear" onClick="return confirm(\'Are you sure about this\')"  class="btn btn-danger" name=""><i class="icon-trash icon-large"></i> Clear Student Field Of Study</a>';else echo '<a   href="'.$_SERVER['PHP_SELF'].'?action=assign&regno='.base64_encode($row['regno']).'" id="allocate" onClick="return confirm(\'Are you sure about this\')"  class="btn btn-success" name=""><i class="icon-signin icon-large"></i> Assign Student To Field</a>'; ?>
									</td>
								</tr>
									
										  <tr>
													<th>Student name</th>
													<th>Field Of Interest</th>
										   </tr>
										</thead>
										<tbody>
										
												
										<tr>
											<td>
											<?php echo $name;  ?>
											</td>
							<td><?php echo (getFieldNameFromCpuID($row['cpu_id'])); ?></td>
											
                                     
                               
										</tr>
                               
                               
										</tbody>
									</table>
                                </div>
                            </div>
						</div>
					   <?php }else{
						   echo "<h1 class='text-error'>Invalid Matriculation Number</h1>";
					   }
					
					}else
					 if (isset($_GET['action'],$_GET['regno']) && ($_GET['action'] == 'clear' || $_GET['action'] == 'assign')){
						 $matric = base64_decode($_GET['regno']);
						$row = fetchStudentByMat($matric);
						if ($row < 1)echo "<h1 class='text-error'>Denied</h1>";else{
							if ($_GET['action'] == 'clear'){
								$id = getIdFromSession($matric, "student");
								// echo $id;
								$result = resetStudentData($id);
								if ($result < 1){
									echo '<script>alert("This student do not have any allocation \n Kindly assign field of interest.");window.location="'.$_SERVER['PHP_SELF'].'";</script>';
								}else{
									echo '<script>alert("Done!");window.location="'.$_SERVER['PHP_SELF'].'";</script>';
								}
							}else{
								//If Assign
								if (isset($_POST['field'])){
									$assign = assigner($matric,$_POST['field']);
									if ($assign == -1){
										echo "<h1 class='text-error'>Form Denied</h1>";
									}else{
										echo $assign;
									}
								}
						 ?>
<div id="block_bg" class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left"><?php echo $name = $row['ln'].", ".$row['fn'];?></div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
				<form action="<?php echo $_SERVER['PHP_SELF'];?>?action=assign&regno=<?php echo base64_encode($matric)?>" method="post">
				<table cellpadding="0" cellspacing="0" border="0" class="table" id="example">
					<tr>
					<th>Student Matric</th><td><?php echo $matric?></td>
				</tr>
				<tr>
					<th>Select Field</th><td><?php $row =  getFieldsToAdmin(); 
					if ($row){
						?>
<select required name="field" id="">	
<option value="">Choose Field</option>
<?php while($result = $row->fetch_assoc()){
	$cpu_id = $result['cpu_id'];
	$display = ucwords(strtolower($result['name']." => ".$result['title_name']." ".$result['ln']." ".$result['fn']." - ".$result['fileno']));
echo "<option value='".$cpu_id."'>".$display."</option>";
}
?>
</select>

<?php
					}else{
						echo "<span class='text-error'>No Supervisors Yet.. Try again later</span>";
					}
					?></td>
				</tr>
				<tr>
					<td colspan="2">Kindly Note That Assigning Field Of Interest From Admin Does Not Guarantee That Allocation Was Successful. A Request Will Be Sent To Selected Supervisor And On Approval, Allocation Will Be Made!</td>
				</tr><tr><td colspan="2">
				<?php
				if ($row){
						echo '<input type="submit" value="Send Request">';
					}else{
						echo "<span class='text-error'>No Supervisors Yet.. Try again later</span>";
					}
					?>
				
					</td>
				</tr>
				</table>
				</form>
                                </div>
                            </div>
						</div>
						<?php 
						
					} //End of assign
				
				} //Student must be valid
					 }  //Two actions only (assign or clear)
					else{
						$getAssign = getAllStudentInAssign();
						?>
<div id="block_bg" class="block">
						 <div class="navbar navbar-inner block-header">
							 <div class="muted pull-left">All Requests Sent</div>
						 </div>
						 <div class="block-content collapse in">
							 <div class="span12">
							   
							 
							 <table cellpadding="0" cellspacing="0" border="0" class="table" id="example"><thead>
								 
							 <tr>
												 <th>Student name</th>
												 <th>Field Of Interest</th>
												 <th>Supervisor</th>
												 <th>Action</th>
										</tr>
									 </thead>
									 <tbody>
									 
								 <?php while($row = $getAssign->fetch_assoc()){?>			
									 <tr>
										 <td>
										 <?php echo getStudentDetailsById($row['student_id']);  ?>
										 </td>
						 <td><?php echo (getFieldNameFromCpuID($row['cpu_id'])); ?></td>
						 <td><?php echo (getSupervisorNameByCpuId($row['cpu_id'])); ?></td>
										 <td><?php
										 
										 if ($row['status'] == 0){
echo "<a onClick='return confirm(\"Are you sure you wanna delete this request?\")' href='".$_SERVER['PHP_SELF']."?del=yes&id=".base64_encode($row['student_id'])."'><button class='btn btn-danger'><i class='icon-trash'></i> Delete Request </button></a>";
										 }elseif ($row['status'] == 1){
											 echo "<a href='#'><button class='btn btn-success'><i class='icon-signin'></i> Approved  </button></a>";

										 }else{
											 echo "<a href='#'><button class='btn btn-warning'><i class='icon-ban-circle'></i> Not Approved  </button></a>";

										 }
										 ?></td>
								  
							
									 </tr>
								 <?php } ?>
							
									 </tbody>
								 </table>
							 </div>
						 </div>
					 </div>
						<?php
					}
					 ?>
                        <!-- /block -->
                    </div>


                </div>
            </div>
		<?php include('footer.php'); ?>
        </div>
		<?php include('script.php'); ?>
    </body>

</html>