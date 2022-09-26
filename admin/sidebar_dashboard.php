<?php
if (!isset($file_access)) {
    die("Direct Access To File Not Allowed");
}

$currentPage = basename($_SERVER['PHP_SELF']);
?>

<div class="span3" id="sidebar">
                    <ul class="nav nav-list bs-docs-sidenav nav-collapse collapse">
                        <li   <?php if ($currentPage == 'dashboard.php') {
    echo ' class="active"';
}
?> > <a href="dashboard.php"><i class="icon-chevron-right"></i><i class="icon-home"></i>&nbsp;Dashboard</a> </li>
						<li   <?php if ($currentPage == 'fields.php') {
    echo ' class="active"';
}
?>  >
                            <a href="fields.php"><i class="icon-chevron-right"></i><i class="icon-list-alt"></i> Fields</a>
                        </li>
						<li   <?php if ($currentPage == 'requests.php') {
    echo ' class="active"';
}
?> >
                            <a href="requests.php"><i class="icon-chevron-right"></i><i class="icon-group"></i> Request</a>
                        </li>
					<li   <?php if ($currentPage == 'change_of_field.php') {
    echo ' class="active"';
}
?> >
                            <a href="change_of_field.php"><i class="icon-chevron-right"></i><i class="icon-group"></i> Change Request <?php echo countChangeFieldRequest(); ?></a>
                        </li>
						<li   <?php if ($currentPage == 'students.php') {
    echo ' class="active"';
}
?> >
                            <a href="students.php"><i class="icon-chevron-right"></i><i class="icon-group"></i> Students</a>
                        </li>
						<li   <?php if ($currentPage == 'supervisors.php') {
    echo ' class="active"';
}
?> >
                            <a href="supervisors.php"><i class="icon-chevron-right"></i><i class="icon-group"></i> Supervisors</a>
                        </li>
                        <li   <?php if ($currentPage == 'resetProgress.php') {
    echo ' class="active"';
}
?> >
                            <a href="resetProgress.php"><i class="icon-chevron-right"></i><i class="icon-group"></i> Reset Student Progress</a>
                        </li>

                        <li   <?php if ($currentPage == 'special_assign.php') {
    echo ' class="active"';
}
?> >
                            <a href="special_assign.php"><i class="icon-chevron-right"></i><i class="icon-list"></i> Assign Special</a>
                        </li>
                        <li   <?php if ($currentPage == 'failed_assign.php') {
    echo ' class="active"';
}
?> >
                            <a href="failed_assign.php"><i class="icon-chevron-right"></i><i class="icon-list"></i> Failed Assign <?php

echo ((getFailed()->num_rows > 0) ? '<span class="badge badge-important">' . getFailed()->num_rows . '</span>' : '');

?> </a>

                        </li>
                        <li   <?php if ($currentPage == 'report.php') {
    echo ' class="active"';
}
?> >
                            <a href="report.php"><i class="icon-chevron-right"></i><i class="icon-credit-card"></i> Report  </a>

                        </li>
						<li   <?php if ($currentPage == 'log.php' && $_GET['type'] == 'student') {
    echo ' class="active"';
}
?> >
                            <a href="log.php?type=student"><i class="icon-chevron-right"></i><i class="icon-file"></i> Student Log</a>
                        </li>
						<li   <?php if ($currentPage == 'log.php' && $_GET['type'] == 'supervisor') {
    echo ' class="active"';
}
?> >
                            <a href="log.php?type=supervisor"><i class="icon-chevron-right"></i><i class="icon-file"></i> Supervisor Log</a>
                        </li>
						<li   <?php if ($currentPage == 'log.php' && $_GET['type'] == 'admin') {
    echo ' class="active"';
}
?> >
                            <a href="log.php?type=admin"><i class="icon-chevron-right"></i><i class="icon-file"></i> Admin Log</a>
                        </li>
                        <li   <?php if ($currentPage == 'settings.php') {
    echo ' class="active"';
}
?> >
                            <a href="settings.php"><i class="icon-chevron-right"></i><i class="icon-magic"></i> Settings</a>
                        </li>
                    </ul>
                </div>