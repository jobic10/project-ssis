<?php include('header_dashboard.php'); ?>
<?php include('supervisor_session.php'); ?>
    <body id="class_div">
		<?php include('navbar_supervisor.php'); ?>
        <div class="container-fluid">
            <div class="row-fluid">
			<?php include('supervisor_sidebar.php'); ?>
			                <div class="span9" id="content">
                     <div class="row-fluid">
					    <!-- breadcrumb -->	
					     <ul class="breadcrumb">
								<li><a href="#"><b>My Class</b></a><span class="divider">/</span></li>
								<li><a href="#">School Year: <?php echo date('Y'); ?></a></li>
						</ul>
						 <!-- end breadcrumb -->
					 
                        <!-- block -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div id="count_class" class="muted pull-right">

								</div>
                            </div>
                            <div class="block-content collapse in">
										
                      
					
							
	
									<div class="span12">
											
			<div class="alert alert-info">Click on the button to view.</div>
					
									<div class="pull-left">
											
							</div>
											<table cellpadding="0" cellspacing="0" border="0" class="table" id="example">

										<thead>
										        <tr>
												<th>ID</th>
												<th>Field Name</th>
												<th>Expected</th>
												<th>Enrolled</th>
												<th>View</th>
												</tr>
												
										</thead>
										<tbody>
							<?php 
							$get = listAllGroups();
							$id = 0;
							while ($row = $get->fetch_assoc()){
								?>
								<tr>
<td><?php echo ++$id; ?></td>
<td><?php echo $row['name']; ?></td>
<td><?php echo $row['no']; ?></td>
<td><?php echo $row['full']; ?></td>
<td> <a href="my_students.php?id=<?php echo $row['id']; ?>"><button  class="btn btn-success" ><i class="icon-check"></i> View</button></a></td>
								</tr>
<?php
							}
							?>
						   
                              
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