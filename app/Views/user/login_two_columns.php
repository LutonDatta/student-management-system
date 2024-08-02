<div class="gray-bg h-100">
    <div class="loginColumns animated fadeInDown <?= isMobile() ? 'pt-1' : 'pt-5'; ?>">
        <?php
            if(isset($display_msg) AND is_string($display_msg) AND strlen($display_msg) > 0){ echo $display_msg; }
            $FlsMsg = session()->getFlashdata('display_msg'); if(strlen($FlsMsg) > 0) echo $FlsMsg; 
        ?>
        <?php
            // CSRF error message, if exists
            if(isset($error) AND is_string($error) AND strlen($error) > 0){ echo $error; }
            $FlsMsg = session()->getFlashdata('error'); if(strlen($FlsMsg) > 0) echo get_display_msg($FlsMsg,'danger'); 
        ?>
        <div class="row">
            
            <div class="col-md-6">
                <div class="ibox-content">
                    <div class="text-center display-4"><i class="fas fa-sign-in-alt"></i></div>
                    <?= form_open(base_url('user/login'), ['class'=>'m-t','role'=>'form','id'=>'lginfrm']); ?>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="text" 
                                   name="login_email_or_id" 
                                   value="<?=set_value('login_email_or_id', '', TRUE);?>" 
                                   class="form-control" 
                                   autocomplete="off" 
                                   placeholder="Email" 
                                   required="required">
                        </div>
                        <div class="form-group">
                            <label><?=lang('Logging.password');?></label>
                            <input type="password" 
                                   name="login_password" 
                                   class="form-control" 
                                   autocomplete="off" 
                                   placeholder="<?=lang('Logging.password');?>" 
                                   required="required">
                        </div>
                        <button type="submit" class="btn btn-primary block full-width m-b"><i class="fas fa-sign-in-alt"></i> <?=lang('Logging.login');?></button>

                        <a class="btn btn-sm btn-white btn-block" href="<?=base_url();?>"><i class="fas fa-home"></i> <?=lang('Logging.go_home');?></a>
                        <?=form_hidden('loginSubmitPost', 'yes');?>
                    </form>
                    <p class="m-t text-center">
                        <small><?=lang('Logging.login_bottom_text');?></small>
                    </p>
                </div>
            </div>
            
            <div class="col-md-6 text-justify">
                <h2 class="font-bold"><?=lang('Logging.welcome');?></h2>
                <p>This is a student management system (SMS). This SSMS is built for schools. You can login  here using your email address and password given in the configuration file. </p>
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-md-6"><?=lang('Student.copyright');?> 
             &copy;  <?=lang('Student.udsspvtltd');?></div>
            <div class="col-md-6 text-right"><small> <?=(Date('Y'));?></small></div>
            <div class="col-md-12">
            </div>
        </div>
        
        
    </div>
</div>

<script {csp-script-nonce}>
    document.addEventListener("DOMContentLoaded", function(){
        $("#lginfrm").submit(function(event){
            var btn = $('button[type="submit"]');var new_html = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>' + btn.text();btn.html(new_html);btn.prop('disabled', true);
        });
    });
</script>