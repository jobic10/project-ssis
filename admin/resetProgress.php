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


if (isset($_GET['del'])){
    $progress_id = intval($_GET['del']);
    echo delProgress($progress_id);
}

?>
<body>
<?php include('navbar.php'); ?>
<div class="container-fluid">
<div class="row-fluid">
<?php require('sidebar_dashboard.php'); ?>
<div class="span3" id="adduser">
<?php include('search.php'); ?>		   			
</div>
<div class="span6" id="">
<div class="row-fluid">
<!-- block -->
<div id="block_bg" class="block">
    <div class="navbar navbar-inner block-header">
        <div class="muted pull-left">Progress List</div>
        <?php
    if (isset($_POST['fetch'])) {
        
            echo "<hr/>";
        $matric = @$_POST['matric'];
        $add = searchStudent($matric);
        if ($add[0] == 0){
            echo "<h3><font color='red'>No record found!</font></h3>";
            exit;
        }elseif ($add[0] == -1){
            echo "<h3><font color='red'>Matric Number Invalid!</font></h3>";
exit;
        }
    }
    ?>
    </div>
    <div class="block-content collapse in">
        <div class="span12">
        <?php if (isset($_POST['fetch']) &&   $add[0] == 1){
            if ($add[1]->num_rows < 1){
                echo "<h3><font color='red'>Student is yet to start with project proposal!</font></h3>";
                exit;
            }
            ?>
            <table cellpadding="0" cellspacing="0" border="0" class="table" id="example">
            
        
                <thead>
                    <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Chapter</th>
            <th>Status</th>
            <th>Action</th>

        </tr>
                </thead>
                <tbody>
                            <?php
                            $sn = 0;
            while($row = $add[1]->fetch_assoc()){
                ?>
                <tr>
                    <td><?php echo ++$sn;?></td>
            <td><?php echo substr(getStudentDetailsById($row['student_id']),0,30);?></td>
            <td><?php 
            $chapter = $row['chapter'];
                $chap_title = (($chapter == 0) ? 'Proposal' : (($chapter == 6) ? "Full Project" : "Chapter $chapter"));
                echo $chap_title;
            ?></td>
            <td><?php
            $status = $row['status'];
            if ($status == 0){
                echo "No Response From Supervisor Yet!";
                $link = '--No Action--';
            }elseif ($status == 1){
                echo "Approved";
                $link = "<a href='".$_SERVER['PHP_SELF']."?del=".$row['id']."'><button onClick='return confirm(\"You sure you want to carry out this action?\")' class='btn btn-danger'><i class='icon icon-trash'></i> Delete This!</button></a>";
            }else{
                echo "Not Approved. Awaiting fresh upload from student";
                $link = '--No Action--';
            }
            ?></td>
            <td>

            <?php echo $link; ?>
            </td>
                <?php
            }
            ?>
                </tr>
                </tbody>
            </table>
        <?php } ?>
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