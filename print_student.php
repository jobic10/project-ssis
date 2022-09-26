<?php include('header_dashboard.php'); ?>
<?php include('supervisor_session.php'); ?>
<?php $get_id = $_GET['id']; 
if (countById($get_id) == -1){
	die("You are not authorized to view this");
};
?>
    <body>
		<?php include('navbar_supervisor.php'); ?>
        <div class="container-fluid">
            <div class="row-fluid">
				<?php include('supervisor_sidebar.php'); ?>
                <div class="span9" id="content">
                     <div class="row-fluid">
						<div class="pull-right">
						
							<a id="print" onclick="window.print()"  class="btn btn-success"><i class="icon-print"></i> Print Student List</a>
						</div>
						<?php include('my_students_breadcrums.php'); ?>
                        <!-- block -->
                        <div id="block_bg" class="block">
                            <div class="navbar navbar-inner block-header">
                                <div id="" class="">
					<h4 align='center'><?php echo "Full List Of Students With Field Of Interest <b>".ucwords(strtolower(getFieldNameFromCpuID($get_id)))."</b>";?></h4>
								</div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
						
												<table cellpadding="0" cellspacing="0" border="0" class="table" id="">
							
										<thead>
										        <tr>
												<th>SN</th>
												<th>Matric</th>
												<th>Last Name</th>
												<th>First Name</th>
												</tr>
												
										</thead>
										<tbody>
											
									<?php echo getListOfStudentsByCpuId($_GET['id']) ?>
						   
                              
										</tbody>
									</table>
										
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