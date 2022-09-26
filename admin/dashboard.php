<?php include('header.php'); ?>

<body>
    <?php include('navbar.php') ?>
    <div class="container-fluid">
        <div class="row-fluid">
            <?php include('sidebar_dashboard.php'); ?>
            <!--/span-->
            <div class="span9" id="content">
                <div class="row-fluid"></div>

                <div class="row-fluid">

                    <!-- block -->
                    <div id="block_bg" class="block">
                        <div class="navbar navbar-inner block-header">
                            <div class="muted pull-left">Quick Database Reports</div>
                        </div>
                        <div class="block-content collapse in">
                            <div class="span12">



                                <div class="span3">
                                    <div class="chart" data-percent="<?php echo countSupervisors(); ?>">
                                        <?php echo countSupervisors(); ?></div>
                                    <div class="chart-bottom-heading"><strong>Supervisors</strong>

                                    </div>
                                </div>
                                <div class="span3">
                                    <div class="chart" data-percent="<?php echo countRegSupervisors(); ?>">
                                        <?php echo countRegSupervisors(); ?></div>
                                    <div class="chart-bottom-heading"><strong>Registered Supervisors</strong>

                                    </div>
                                </div>
                                <div class="span3">
                                    <div class="chart" data-percent="<?php echo countUnRegSupervisors(); ?>">
                                        <?php echo countUnRegSupervisors(); ?></div>
                                    <div class="chart-bottom-heading"><strong>Unregistered Supervisors</strong>

                                    </div>
                                </div>




                                <div class="span3">
                                    <div class="chart" data-percent="<?php echo countFields(); ?>">
                                        <?php echo countFields() ?></div>
                                    <div class="chart-bottom-heading"><strong>Number Of Fields</strong>

                                    </div>
                                </div>
                                <div class="span3">
                                    <div class="chart" data-percent="<?php echo countStudents(); ?>">
                                        <?php echo countStudents(); ?></div>
                                    <div class="chart-bottom-heading"><strong>Students</strong>

                                    </div>
                                </div>


                                <div class="span3">
                                    <div class="chart" data-percent="<?php echo countRegStudents() ?>">
                                        <?php echo countRegStudents() ?></div>
                                    <div class="chart-bottom-heading"><strong>Registered Students</strong>

                                    </div>
                                </div>


                                <div class="span3">
                                    <div class="chart" data-percent="<?php echo countUnRegStudents(); ?>">
                                        <?php echo countUnRegStudents() ?></div>
                                    <div class="chart-bottom-heading"><strong>Unregistered Students</strong>

                                    </div>
                                </div>
                                <div class="span3">
                                    <div class="chart" data-percent="<?php echo countAdmins(); ?>">
                                        <?php echo countAdmins(); ?></div>
                                    <div class="chart-bottom-heading"><strong>No of Admin </strong>

                                    </div>
                                </div>


                            </div>
                        </div>
                        <!-- /block -->

                    </div>
                </div>




            </div>
        </div>

        <?php include('footer.php'); ?>
    </div>
    <?php include('script.php'); ?>
</body>

</html>