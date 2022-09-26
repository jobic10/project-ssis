<?php include 'header.php'; ?>
<?php include 'session.php'; ?>

<body>
    <?php include 'navbar.php'; ?>
    <div class="container-fluid">
        <div class="row-fluid">
            <?php include 'sidebar_dashboard.php';


            if (isset($_GET['id'])) {
                $id = base64_decode(@$_GET['id']);
                $status = @$_GET['status'];
                $changeStatus = changeSettingsById($id, $status);
                if ($changeStatus == 1) {
            ?>
            <script>
            alert("Status Changed");
            window.location = "<?php echo $_SERVER['PHP_SELF']; ?>";
            </script>
            <?php
                } elseif ($changeStatus == -1) {
                ?>
            <script>
            alert("Something about you is not right");
            window.location = "<?php echo $_SERVER['PHP_SELF']; ?>";
            </script>
            <?php
                } else {
                ?>
            <script>
            alert("Unknown Error Occured");
            window.location = "<?php echo $_SERVER['PHP_SELF']; ?>";
            </script>
            <?php
                }
                exit;
            }

            ?>
            <div class="span3" id="adduser">
                <div class="row-fluid">
                    <!-- block -->
                    <div class="block">
                        <div class="navbar navbar-inner block-header">
                            <div class="muted pull-left">Edit Profile</div>

                        </div>
                        <div class="block-content collapse in">
                            <div class="span12">
                                <form method="post">
                                    <table>
                                        <caption>Skip field(s) you'd like to use as it is</caption>

                                        <tr>
                                            <td>First Name</td>
                                            <td>
                                                <input class="input focused" minlength="5" name="firstname" type="text"
                                                    required placeholder="<?php echo getAdminDetails('firstname'); ?>"
                                                    value="<?php echo getAdminDetails('firstname'); ?>">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Last Name</td>
                                            <td>
                                                <input class="input focused" minlength="5" name="lastname" type="text"
                                                    required placeholder="<?php echo getAdminDetails('lastname'); ?>"
                                                    value="<?php echo getAdminDetails('lastname'); ?>">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Email</td>
                                            <td>
                                                <input class="input focused" minlength="5" name="email" type="email"
                                                    required placeholder="<?php echo getAdminDetails('email'); ?>"
                                                    value="<?php echo getAdminDetails('email'); ?>">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Phone</td>
                                            <td>
                                                <input class="input focused" minlength="11" maxlength='11' name="phone"
                                                    type="text" required
                                                    placeholder="<?php echo getAdminDetails('phone'); ?>"
                                                    value="<?php echo getAdminDetails('phone'); ?>">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan='2' class="text-center text-info">
                                                <em><strong>Login Credentials</strong></em>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td>Username</td>
                                            <td>
                                                <input class="input focused" minlength="5" name="username" type="text"
                                                    required placeholder="<?php echo getAdminDetails('username'); ?>"
                                                    value="<?php echo getAdminDetails('username'); ?>">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>New Password</td>
                                            <td>
                                                <input class="input focused" minlength="5" name="newpassword"
                                                    type="text" placeholder="New password (if any)">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Old Password</td>
                                            <td>
                                                <input class="input focused" minlength="5" name="oldpassword"
                                                    type="password" required placeholder="To save changes">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan='2'>
                                                <button name="save" class="btn btn-info"><i
                                                        class="icon-upload icon-large"></i> Update</button>
                                            </td>
                                        </tr>
                                    </table>

                                </form>


                            </div>
                        </div>
                    </div>
                    <!-- /block -->
                </div>
            </div>
            <div class="span6" id="">
                <div class="row-fluid">
                    <!-- block -->
                    <div id="block_bg" class="block">
                        <div class="navbar navbar-inner block-header">
                            <div class="muted pull-left">Supervisor List</div>
                            <?php
                            if (isset($_POST['save'])) {
                                echo "<hr/>";
                                $username = @$_POST['username'];
                                $first_name = @$_POST['firstname'];
                                $last_name = @$_POST['lastname'];
                                $old_password = @$_POST['oldpassword'];
                                $password = @$_POST['newpassword'];
                                $email = @$_POST['email'];
                                $phone = @$_POST['phone'];
                                $edit = editAdmin($username, $password, $first_name, $last_name, $email, $phone, $old_password);
                                if ($edit == 1) {
                                    echo "<h3><font color='green'>Profile Updated!</font></h3>";
                                } elseif ($edit == -1) {
                                    echo "<h3><font color='red'>Fill Form Properly</font></h3>";
                                } elseif ($edit == -2) {
                                    echo "<h3><font color='red'>Old Password Not Correct</font></h3>";
                                } else {
                                    echo "<h3><font color='red'>Oh Snap! Unknown Error Has Occured.</font></h3>";
                                }
                            }
                            ?>
                        </div>
                        <div class="block-content collapse in">
                            <div class="span12">

                                <form action="" method="post">
                                    <table cellpadding="0" cellspacing="0" border="0" class="table" id="example">
                                        <a data-toggle="modal" href="#supervisor_delete" id="delete"
                                            class="btn btn-danger" name=""><i class="icon-trash icon-large"></i></a>
                                        <?php include 'modal_delete.php'; ?>
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>Category</th>
                                                <th>Value</th>
                                                <th>Action</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php

                                            echo getSettings();
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
        <?php include 'footer.php'; ?>
    </div>
    <?php include 'script.php'; ?>
</body>

</html>