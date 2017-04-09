<?php $this->load->view('admin/elements/header') ?>
<style>#wrapper{padding-left:0;}</style>
    <div class="container">    
        <div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">                    
            <div class="panel panel-info" >
                    <div class="panel-heading">
                        <div class="panel-title">Password Recovery</div>
                    </div>     

                    <div style="padding-top:30px" class="panel-body" >
                        <?php if($this->session->flashdata('errorMessage')){ ?>
                            <div id="login-alert" class="alert alert-danger col-sm-12"><?php echo $this->session->flashdata('errorMessage'); ?></div>
                        <?php } ?>
                            <div style="display:none;" id="login-alert" class="alert alert-danger col-sm-12"></div>
                        <form id="recover-password-form" class="form-horizontal" role="form" method="post" action="<?php echo site_url() ?>admin/forgot/update_password">
                        <div class="col-sm-12">
                                 <div class="form-group">
                                        <input placeholder="New Password" type="password" name="newpassword" id="newpassword" tabindex="1" class="form-control" value="">
                                    </div> 
                                    <div class="form-group">
                                        <input placeholder="Cofnirm Password" id="confirmpassword" type="password" class="form-control" name="confirmpassword">
                                    </div>   
                                <input type="hidden" value="<?php echo $this->uri->segment(4); ?>" name="tokenkey" id="tokenkey"/>
                                <div style="margin-top:10px" class="form-group">
                                    <!-- Button -->

                                    <div class="controls">
                                    <button type="submit" class="btn btn-success">Reset Password</button>
                                    <a href="<?php echo site_url('admin/login') ?>">Login</a>
                                    </div>
                                </div>

                            </div>  
                            </form>     
                        </div>                     
                    </div>  
        </div>
    </div>
        <script type="text/javascript">
            $(document).ready(function () {
                $('#recover-password-form').validate({
                    rules: {
                        newpassword: {
                            required: true,
                            minlength:6
                        },
                        confirmpassword: {
                            equalTo: "#newpassword"
                        }
                    },
                    submitHandler: function (form) {
                    $('#login-alert').show().css('color','red').html('Please wait....');
                        $.ajax({
                            url: form.action,
                            type: form.method,
                            data: $(form).serialize(),
                            success: function (response) {
                                if (response == 'success') {
                                    $('#login-alert').css('color','green').html(response);
                                } else {
                                    $('#login-alert').css('color','red').html(response);
                                }
                                
//                                $('#everything2').hide();
                            }
                        });
                    }
                });
            });
        </script>
    <?php $this->load->view('admin/elements/footer') ?>