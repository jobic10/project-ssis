<?php include 'header.php';?>
<?php include 'session.php';?>
    <body>
		<?php include 'navbar.php';?>
        <div class="container-fluid">
            <div class="row-fluid">
				<?php include 'sidebar_dashboard.php';?>

                <div class="span9" id="">
                     <div class="row-fluid">
                        <!-- block -->
					   <?php
if (isset($_GET['del'], $_GET['id'])) {
	$id = base64_decode($_GET['id']);
	$response = $_GET['del'];
	if ($response != 'no' && $response != 'yes'){
		echo script("Not allowed",1);
	}else
    echo ChangeRequest($id,$response);
}

if (isset($_GET['dell'], $_GET['idd'])) {
	
	$id = base64_decode($_GET['idd']);
	$response = $_GET['dell'];
	if ($response != 'no' && $response != 'yes'){
		echo script("Not allowed",1);
	}else
    echo ChangeRequest($id,$response,'admin2');
}


if (isset($_GET['action'], $_GET['regno']) && ($_GET['action'] == 'clear' || $_GET['action'] == 'assign')) {
    $matric = base64_decode($_GET['regno']);
    $row = fetchStudentByMat($matric);
    if ($row < 1) {
        echo "<h1 class='text-error'>Denied</h1>";
    } else {
        if ($_GET['action'] == 'clear') {
            $id = getIdFromSession($matric, "student");
            // echo $id;
            $result = resetStudentData($id);
            if ($result < 1) {
                echo '<script>alert("This student do not have any allocation \n Kindly assign field of interest.");window.location="' . $_SERVER['PHP_SELF'] . '";</script>';
            } else {
                echo '<script>alert("Done!");window.location="' . $_SERVER['PHP_SELF'] . '";</script>';
            }
        } else {
            //If Assign
            if (isset($_POST['field'])) {
                $assign = assigner($matric, $_POST['field']);
                if ($assign == -1) {
                    echo "<h1 class='text-error'>Form Denied</h1>";
                } else {
                    echo $assign;
                }
            }
            ?>
<div id="block_bg" class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left"><?php echo $name = $row['ln'] . ", " . $row['fn']; ?></div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>?action=assign&regno=<?php echo base64_encode($matric) ?>" method="post">
				<table cellpadding="0" cellspacing="0" border="0" class="table" id="example">
					<tr>
					<th>Student Matric</th><td><?php echo $matric ?></td>
				</tr>
				<tr>
					<th>Select Field</th><td><?php $row = getFieldsToAdmin();
            if ($row) {
                ?>
<select required name="field" id="">
<option value="">Choose Field</option>
<?php while ($result = $row->fetch_assoc()) {
                    $cpu_id = $result['cpu_id'];
                    $display = ucwords(strtolower($result['name'] . " => " . $result['title_name'] . " " . $result['ln'] . " " . $result['fn'] . " - " . $result['fileno']));
                    echo "<option value='" . $cpu_id . "'>" . $display . "</option>";
                }
                ?>
</select>

<?php
} else {
                echo "<span class='text-error'>No Supervisors Yet.. Try again later</span>";
            }
            ?></td>
				</tr>
				<tr>
					<td colspan="2">Kindly Note That Assigning Field Of Interest From Admin Does Not Guarantee That Allocation Was Successful. A Request Will Be Sent To Selected Supervisor And On Approval, Allocation Will Be Made!</td>
				</tr><tr><td colspan="2">
				<?php
if ($row) {
                echo '<input type="submit" value="Send Request">';
            } else {
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
} //Two actions only (assign or clear)
else {
    $getAssign = getAllStudentInChange();
    ?>
<div id="block_bg" class="block">
						 <div class="navbar navbar-inner block-header">
							 <div class="muted pull-left">Request from students on change of field</div>
						 </div>
						 <div class="block-content collapse in">
							 <div class="span12">


							 <table cellpadding="0" cellspacing="0" border="0" class="table" id="example"><thead>

							 <tr>
												 <th>Student name</th>
												 <th>Supervisor</th>
												 <th>Field Of Interest</th>
												 <th>Reason</th>
												 <th>Action</th>
										</tr>
									 </thead>
									 <tbody>

								 <?php while ($row = $getAssign->fetch_assoc()) {?>
									 <tr>
										 <td>
										 <?php echo  getStudentDetailsById($id = $row['student_id']); ?>
										 </td>
						 <td><?php  $cpu = getStudentCpuIdById($id);
						 echo getSupervisorNameByCpuId($cpu);
						 ; ?></td>
						 <td><?php  echo ucwords(strtolower(getFieldNameFromCpuID($cpu)));
						  ?></td>
						 <td><?php echo (($row['reason'])); ?></td>
										 <td><?php
										 $status = $row['admin'];

        if ($status == 0) {
            echo "<a onClick='return confirm(\"Are you sure you wanna delete this request?\")' href='" . $_SERVER['PHP_SELF'] . "?del=no&id=" . base64_encode($row['id']) . "'><button class='btn btn-danger'><i class='icon-trash'></i> Reject </button></a> --  <a onClick='return confirm(\"Are you sure you wanna accept this request?\")' href='" . $_SERVER['PHP_SELF'] . "?del=yes&id=" . base64_encode($row['id']) . "'><button class='btn btn-success'><i class='icon-check'></i> Approve  </button></a>";
        } elseif ($status == 1) {
            echo "<a href='#'><button class='btn btn-success'><i class='icon-signin'></i> Awaiting Response From Supervisor  </button></a>";

        } elseif($status == -1) {
            echo "<a href='#'><button class='btn btn-danger'><i class='icon-ban-circle'></i> Not Approved By Admin  </button></a>";

        }elseif($status == -2) {
            echo "<span class='text-error'>Supervisor Rejected This Request</span><hr/><a onClick='return confirm(\"Are you sure you wanna delete this request?\")' href='" . $_SERVER['PHP_SELF'] . "?dell=no&idd=" . base64_encode($row['id']) . "'><button class='btn btn-danger'><i class='icon-trash'></i> Delete Request </button></a> --  <a onClick='return confirm(\"Are you sure you wanna accept this request?\")' href='" . $_SERVER['PHP_SELF'] . "?dell=yes&idd=" . base64_encode($row['id']) . "'><button class='btn btn-success'><i class='icon-check'></i> Force Approve  </button></a>";

        }else{
			echo "<span class='text-success'>Supervisor Approved This Request.</span><hr/><a onClick='return confirm(\"Are you sure you wanna delete this request?\")' href='" . $_SERVER['PHP_SELF'] . "?dell=no&idd=" . base64_encode($row['id']) . "'><button class='btn btn-danger'><i class='icon-trash'></i> Delete Request </button></a> --  <a onClick='return confirm(\"Are you sure you wanna accept this request?\")' href='" . $_SERVER['PHP_SELF'] . "?dell=yes&idd=" . base64_encode($row['id']) . "'><button class='btn btn-success'><i class='icon-check'></i> Approve  </button></a>";
		}
        ?></td>


									 </tr>
								 <?php }?>

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
		<?php include 'footer.php';?>
        </div>
		<?php include 'script.php';?>
    </body>

</html>