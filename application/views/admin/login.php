<?php $this->load->view('admin/elements/header') ?>
<style>#wrapper{padding-left:0;}</style>
    <div class="container">    
        <div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">                    
            <div class="panel panel-info" >
                    <div class="panel-heading">
                        <div class="panel-title">Sign In</div>
                    </div>     

                    <div style="padding-top:30px" class="panel-body" >
                        <?php if($this->session->flashdata('errorMessage')){ ?>
                            <div id="login-alert" class="alert alert-danger col-sm-12"><?php echo $this->session->flashdata('errorMessage'); ?></div>
                        <?php } ?>
                            <div style="display:none;" id="login-alert" class="alert alert-danger col-sm-12"></div>
                        <form id="loginform" class="form-horizontal" role="form" method="post" action="<?php echo site_url() ?>admin/login/checkLoggedIn">
                        <div class="col-sm-12">
                                 <div class="form-group">
                                        <input type="text" name="email" id="email" tabindex="1" class="form-control" placeholder="email" value="">
                                    </div> 
                                    <div class="form-group">
                                        <input id="login-password" type="password" class="form-control" name="password" placeholder="password">
                                    </div>   

                                <div style="margin-top:10px" class="form-group">
                                    <!-- Button -->

                                    <div class="controls">
                                    <button type="submit" class="btn btn-success">Login</button>
                                    </div>
                                </div>

</div>
                                <div class="form-group">
                                    <div class="col-md-12 control">
                                        <div style="border-top: 1px solid#888; padding-top:15px; font-size:85%" >
                                        <a href="#" onClick="$('#loginbox').hide(); $('#signupbox').show()">
                                            Forgot Your password?
                                        </a>
                                        </div>
                                    </div>
                                </div>    
                                <input type="hidden" name="javascript_enable" id="javascript_enable" value="yes"  />
                            </form>     
                        </div>                     
                    </div>  
        </div>
        <div id="signupbox" style="display:none; margin-top:50px" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <div class="panel-title">Recover Your password</div>
                        </div>  
                        <div class="panel-body" >
                            <form id="password-recovery" class="form-horizontal" role="form" method="post" action="<?= site_url() ?>admin/forgot/forgotpassword">
                                
                                <div id="signupalert" style="display:none" class="alert alert-danger">
                                </div>
                                <input type="hidden" name="javascript_enable" id="javascript_enable" value="yes"  />
                                <div class="form-group">
                                    <label for="email" class="col-md-3 control-label">Email</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="email_forgot" placeholder="Email Address">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <!-- Button -->                                        
                                    <div class="col-md-offset-3 col-md-9">
                                        <button id="btn-signup" type="submit" class="btn btn-info"><i class="icon-hand-right"></i> Recover</button>
                                        <a id="signinlink" class="btn btn-info" href="#" onclick="$('#signupbox').hide(); $('#loginbox').show()">Back</a>
                                    </div>
                                </div>
                                
                            </form>
                         </div>
                    </div>    
                
         </div> 
    </div>
        <script type="text/javascript">
            $(document).ready(function () {
                $('#loginform').validate({
                    rules: {
                        email: {
                            required: true,
                            email: true
                        },
                        password: {
                            required: true,
                            minlength: 5
                        }
                    },
                    submitHandler: function (form) {
                        $('#login-alert').show().html('Please wait....');
                        $.ajax({
                            url: form.action,
                            type: form.method,
                            data: $(form).serialize(),
                            success: function (response) {
                                if (response == 'success') {
                                    window.location.href = "<?php echo site_url() . 'admin/home' ?>";
                                } else {
                                    $('#login-alert').html(response);
                                }
                            }
                        });
                    }
                });

                $('#password-recovery').validate({
                    rules: {
                        email_forgot: {
                            required: true,
                            email: true
                        }
                    },
                    submitHandler: function (form) {
                    $('#signupalert').show().css('color','red').html('Please wait....');
                        $.ajax({
                            url: form.action,
                            type: form.method,
                            data: $(form).serialize(),
                            success: function (response) {
                                if (response == 'success') {
                                    $('#signupalert').css('color','green').html(response);
                                } else {
                                    $('#signupalert').css('color','red').html(response);
                                }
                                
                            }
                        });
                    }
                });
            });
        </script>
    <?php $this->load->view('admin/elements/footer') ?>