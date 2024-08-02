<article class="row">
    <div class="col-lg-12 animated fadeInRight">
        <?php
            if(isset($display_msg) AND is_string($display_msg) AND strlen($display_msg) > 0){ echo $display_msg; }
            $FlsMsg = session()->getFlashdata('display_msg'); if(strlen($FlsMsg) > 0) echo $FlsMsg; 
        ?>
    </div>
</article>

<?php   
    // Load admission application NAV bar in few application process prat page from a single file.
    echo view('SchoolBackViews/admission-edit-application/ad-admission-process-nav-bar');
?>



<?=form_open(base_url("admin/admission/edit/application/by/admin?InfoPage=identity-info&student_id=".intval(service('request')->getGet('student_id'))),['id'=>'editorForm']);?>

<article class="row">
   
    <div class="col-xl-6 animated fadeInRight">
        <div class="mail-box-header">
            <h3>
                <?=lang('Sa.edit_identity_contact_info');?>
                <?php $uid = intval(service('request')->getget('student_id')); if($uid > 0){ echo "(UID: $uid)"; } ?>
            </h3>
        </div>
        <div class="mail-box">
            <div class="mail-body">  
                
                <div class="form-group row">
                    <label for="nid" class="col-sm-3 col-form-label"><?=lang('Sa.form_nid');?></label>
                    <div class="col-sm-9">
                        <?=form_input('nid',set_value('nid',(!empty($updating_student) AND is_object($updating_student))?$updating_student->student_u_nid_no:''),['placeholder'=>'','class'=>"form-control",'id'=>'nid'],'text');?>
                        <?php if(isset($errors)) echo get_form_error_msg_from_array( $errors,'student_u_nid_no'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="b_reg" class="col-sm-3 col-form-label">
                        <?=lang('Sa.form_birth_reg');?>
                    </label>
                    <div class="col-sm-9">
                        <?=form_input('b_reg',set_value('b_reg',(!empty($updating_student) AND is_object($updating_student))?$updating_student->student_u_birth_reg_no:''),['placeholder'=>'','class'=>"form-control",'id'=>'b_reg'],'text');?>
                        <?php if(isset($errors)) echo get_form_error_msg_from_array( $errors,'student_u_birth_reg_no'); ?>
                    </div>
                </div>
                
                
                <div class="form-group row">
                    <label for="email_l" class="col-sm-3 col-form-label">Email of Student</label>
                    <div class="col-sm-9">
                        <?php 
                        // School admin can change email/mobile only if it is not verified.
                        echo form_input('student_email',set_value('student_email',(!empty($updating_student) AND is_object($updating_student))?strval($updating_student->student_u_email_own):''),['placeholder'=>'','class'=>"form-control",'id'=>'student_email'],'text');
                        if(isset($errors)) echo get_form_error_msg_from_array( $errors,'student_email');
                        ?>
                    </div>
                </div>
                
                
                <div class="form-group row">
                    <label for="mobile_1" class="col-sm-3 col-form-label">Mobile of Student</label>
                    <div class="col-sm-9">
                        <?php 
                            
                        // School admin can change email/mobile only if it is not verified.
                        echo form_input('student_mobile',set_value('student_mobile',(!empty($updating_student) AND is_object($updating_student))?strval($updating_student->student_u_mobile_own):''),['placeholder'=>'','class'=>"form-control",'id'=>'student_mobile'],'text');
                        if(isset($errors)) echo get_form_error_msg_from_array( $errors,'student_mobile');
                        ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="mobile_fa" class="col-sm-3 col-form-label"><?=lang('Sa.form_mob_father');?></label>
                    <div class="col-sm-9">
                        <?=form_input('mobile_fa',set_value('mobile_fa',(!empty($updating_student) AND is_object($updating_student))?$updating_student->student_u_mobile_father:''),['placeholder'=>'','class'=>"form-control",'id'=>'mobile_1'],'text');?>
                        <?php if(isset($errors)) echo get_form_error_msg_from_array( $errors,'student_u_mobile_father'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="mobile_ma" class="col-sm-3 col-form-label"><?=lang('Sa.form_mob_mother');?></label>
                    <div class="col-sm-9">
                        <?=form_input('mobile_ma',set_value('mobile_ma',(!empty($updating_student) AND is_object($updating_student))?$updating_student->student_u_mobile_mother:''),['placeholder'=>'','class'=>"form-control",'id'=>'mobile_1'],'text');?>
                        <?php if(isset($errors)) echo get_form_error_msg_from_array( $errors,'student_u_mobile_mother'); ?>
                    </div>
                </div>
            </div>
        
            <div class="mail-body text-center">                
                <button type="submit" name="saveProfileInfo_identity_admin" value="yes" class="btn btn-primary" ><i class="fa fa-save"></i> Save Identity & Contact Info</button>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</article>


<?=form_close();?>


<script src="<?=cdn_url('bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js');?>" defer='defer'></script>
<link rel="stylesheet" href="<?=cdn_url('bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css');?>"/>


