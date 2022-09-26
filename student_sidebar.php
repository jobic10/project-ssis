<?php
if (!isset($file_access))
die("Direct Access To File Not Allowed");
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<div class="span3" id="sidebar">
<img id="avatar" class="img-polaroid" src="uploads/<?php echo getPassport($_SESSION['id'],"student");?>">
		<ul class="nav nav-list bs-docs-sidenav nav-collapse collapse">
			<li <?php if ($currentPage == 'dashboard_student.php' && !isset($_GET['change'])) echo ' class="active"'; ?> ><a href="dashboard_student.php"><i class="icon-chevron-right"></i><i class="icon-group"></i>&nbsp;My Class</a></li>
			<li <?php if ($currentPage == 'student_notification.php') echo ' class="active"'; ?> >
				<a href="student_notification.php"><i class="icon-chevron-right"></i><i class="icon-info-sign"></i>&nbsp;Notification
				</a>
			</li>
			
			<li <?php if ($currentPage == 'student_message.php' || $currentPage == 'sent_message_student.php') echo ' class="active"'; ?> >
			<a href="student_message.php"><i class="icon-chevron-right"></i><i class="icon-envelope-alt"></i>&nbsp;Message
					<!-- <span class="badge badge-important">0</span> -->
			</a>
			</li>
			<?php 	
			$get_id = 0;
			$id = getIdFromSession($_SESSION['id'],'student');
			if	(canThisIdRequestForChange($id)){?>
			 <li <?php if ($currentPage == 'dashboard_student.php' && isset($_GET['change'])) echo ' class="active"'; ?>> <a href="dashboard_student.php?change=yes"><i class="icon-chevron-right"></i><i class="icon-suitcase"></i>&nbsp;Change Request</a></li>
			
			<?php }?>
			
				<li  <?php if ($currentPage == 'progress.php') echo ' class="active"'; ?> ><a href="progress.php<?php echo '?id='.$get_id; ?>"><i class="icon-chevron-right"></i><i class="icon-bar-chart"></i>&nbsp;My Progress</a></li>
				<li  <?php if ($currentPage == 'downloadables.php') echo ' class="active"'; ?> ><a href="downloadables.php"><i class="icon-chevron-right"></i><i class="icon-file"></i>&nbsp;Downloadables</a><li  <?php if ($currentPage == 'announcements.php') echo ' class="active"'; ?> ><a href="announcements.php"><i class="icon-chevron-right"></i><i class="icon-info-sign"></i>&nbsp;Announcements</a></li>
				
				
		</ul>
					<?php /* include('search_other_class.php');  */?>	
</div>