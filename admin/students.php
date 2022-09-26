<?php include('header.php'); ?>
<?php include('session.php'); ?>
<?php 
if (isset($_POST['del_student'],$_POST['selector'])){
    $id=$_POST['selector'];
    $del = delStudents($id);
    if ($del == 1){
        echo "<script>alert('Action Completed'); </script>";
    }elseif ($del == -1){
        echo "<script>alert('Sorry, one or more students has/have already been assigned. Deletion is not posible'); </script>";

    }else{
        echo "<script>alert('Check The Student(s) You\'d Like To Delete'); </script>";

    }
}



if (isset($_GET['id'])){
    $id = base64_decode(@$_GET['id']);
    $status = @$_GET['status'];
    $changeStatus = changeStatusById($id,$status,"students");
    if ($changeStatus == 1){
        ?>
<script>alert("Status Changed");
window.location = "<?php echo $_SERVER['PHP_SELF'];?>";</script>
        <?php
    }elseif($changeStatus == -1){
        ?>
<script>alert("Something about you is not right");
window.location = "<?php echo $_SERVER['PHP_SELF'];?>";</script>
        <?php
    }elseif ($changeStatus == -2){
?><script>alert("Record Does Not Exist");
window.location = "<?php echo $_SERVER['PHP_SELF'];?>";</script>
<?php
    }  else{
        ?>
<script>alert("Unknown Error Occured");
window.location = "<?php echo $_SERVER['PHP_SELF'];?>";</script>
        <?php
    }
exit;
}

?>
    <body>
		<?php include('navbar.php'); ?>
        <div class="container-fluid">
            <div class="row-fluid">
				<?php require('sidebar_dashboard.php'); ?>
				<div class="span3" id="adduser">
				<?php include('add_student.php'); ?>		   			
				</div>
                <div class="span6" id="">
                     <div class="row-fluid">
                        <!-- block -->
                        <div id="block_bg" class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">Student List</div>
                                <?php
                            if (isset($_POST['save'])) {
                                    echo "<hr/>";
                                $matric = @$_POST['regno'];
                                $add = addStudent($matric);
                                if ($add == 1){
                                  echo "<h3><font color='green'>New Student Has Been Created Successfully</font></h3>";
                                }elseif($add == -1){
                                  echo "<h3><font color='red'>Fill Form Properly</font></h3>";
                                }elseif($add == -2){
                                  echo "<h3><font color='red'>Oh Snap! A Student Already Exists With The Matric Number</font></h3>";
                                }else{
                                  echo "<h3><font color='red'>Oh Snap! Unknown Error Has Occured.</font></h3>";
                                }
                            }
                            ?>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
                             <?php   if (isset($_POST['uploadfile'])){
$file = 'file';
    $file = "file";
    $saveField = saveFieldInBulk($file,"student");
    if (strlen($saveField)> 10){
      echo $saveField;
    }else{
        ?>
        <script>
        alert("Fill Form Properly\nUpload Only CSV Files");
        </script>
        <?php	
    }
    }?>
  									<form action="" method="post">
  									<table cellpadding="0" cellspacing="0" border="0" class="table" id="example">
									<a data-toggle="modal" href="#student_delete" id="delete"  class="btn btn-danger" name=""><i class="icon-trash icon-large"></i></a>
									<?php include('modal_delete.php'); ?>
										<thead>
										    <tr>
                                    <th></th>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Matric No</th>
                                    <th>Action</th>

                                    <th></th>
                                </tr>
										</thead>
										<tbody>
												 <?php
                                    
                                    echo getStudents();
                                    ?>
                               
										</tbody>
									</table>
									</form>
                                </div>
                            </div>
                        </div>
                        <!-- /block -->
                    </div>


                </div>
            </div>
		<?php include('footer.php'); ?>
        </div>
		<?php include('script.php'); ?>
    </body>

</html>