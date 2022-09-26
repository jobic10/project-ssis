
<?php
if (!isset($file_access))
die("Direct Access To File Not Allowed");
?><div class="row-fluid">
                        <!-- block -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">Search for student</div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
								<form method="post">
										<div class="control-group">
                                          <div class="controls">
                                            <input name="matric" class="input focused" id="focusedInput" type="text" placeholder = "Matriculation Number" required>
                                          </div>
                                        </div>
										
									
											<div class="control-group">
                                          <div class="controls">
												<button name="fetch" class="btn btn-info"><i class="icon-plus-sign icon-large"></i> Fetch Details</button>

                                          </div>
                                        </div>
                                </form>
								</div>
                            </div>
                        </div>
                        <!-- /block -->
                    </div><?php

?>