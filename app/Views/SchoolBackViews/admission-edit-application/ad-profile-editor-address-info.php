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



<?=form_open(base_url("admin/admission/edit/application/by/admin?InfoPage=address-info&student_id=".intval(service('request')->getGet('student_id'))),['id'=>'editorForm']);?>

<article class="row">
    
    <div class="col-xl-6 animated fadeInRight">
        <div class="mail-box-header">
            <h3>
                <?=lang('Sa.edit_address_info');?>
                <?php $uid = intval(service('request')->getget('student_id')); if($uid > 0){ echo "(SID: $uid)"; } ?>
            </h3>
        </div>
        <div class="mail-box">
            <div class="mail-body">  
                <div class="form-group row">
                    <label for="country" class="col-sm-3 col-form-label"><?=lang('Sa.form_country');?></label>
                    <div class="col-sm-9">
                        <?php $selected = set_value('country',(!empty($updating_student) AND is_object($updating_student))?$updating_student->student_u_addr_country:'');
                        echo form_dropdown('country', get_country_list(),$selected,['placeholder'=>'','class'=>"form-control",'id'=>'country']); ?>
                        <?php if(isset($errors)) echo get_form_error_msg_from_array( $errors,'student_u_addr_country'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="state" class="col-sm-3 col-form-label"><?=lang('Sa.form_state');?></label>
                    <div class="col-sm-9">
                        <?=form_input('state',set_value('state',(!empty($updating_student) AND is_object($updating_student))?$updating_student->student_u_addr_state:''),['placeholder'=>'','class'=>"form-control",'id'=>'state'],'text');?>
                        <?php if(isset($errors)) echo get_form_error_msg_from_array( $errors,'student_u_addr_state'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="district" class="col-sm-3 col-form-label">
                        <?=lang('Sa.form_district');?>
                    </label>
                    <div class="col-sm-9">
                        <?=form_input('district',set_value('district',(!empty($updating_student) AND is_object($updating_student))?$updating_student->student_u_addr_district:''),['placeholder'=>'','class'=>"form-control",'id'=>'district'],'text');?>
                        <?php if(isset($errors)) echo get_form_error_msg_from_array( $errors,'student_u_addr_district'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="thana" class="col-sm-3 col-form-label">
                        <?=lang('Sa.form_thana_subdistrict');?>
                    </label>
                    <div class="col-sm-9">
                        <?=form_input('thana',set_value('thana',(!empty($updating_student) AND is_object($updating_student))?$updating_student->student_u_addr_thana:''),['placeholder'=>'','class'=>"form-control",'id'=>'thana'],'text');?>
                        <?php if(isset($errors)) echo get_form_error_msg_from_array( $errors,'student_u_addr_thana'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="post_office" class="col-sm-3 col-form-label">
                        <?=lang('Sa.form_post_offi');?>/Zip
                    </label>
                    <div class="col-sm-9">
                        <?=form_input('post_office',set_value('post_office',(!empty($updating_student) AND is_object($updating_student))?$updating_student->student_u_addr_post_office:''),['placeholder'=>'','class'=>"form-control",'id'=>'post_office'],'text');?>
                        <?php if(isset($errors)) echo get_form_error_msg_from_array( $errors,'student_u_addr_post_office'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="post_code" class="col-sm-3 col-form-label">
                        <?=lang('Sa.form_post_code');?>/Zip Code
                    </label>
                    <div class="col-sm-9">
                        <?=form_input('post_code',set_value('post_code',(!empty($updating_student) AND is_object($updating_student))?$updating_student->student_u_addr_zip_code:''),['placeholder'=>'','class'=>"form-control",'id'=>'post_code'],'text');?>
                        <?php if(isset($errors)) echo get_form_error_msg_from_array( $errors,'student_u_addr_zip_code'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="village" class="col-sm-3 col-form-label"><?=lang('Sa.form_vill_area');?></label>
                    <div class="col-sm-9">
                        <?=form_input('village',set_value('village',(!empty($updating_student) AND is_object($updating_student))?$updating_student->student_u_addr_village:''),['placeholder'=>'','class'=>"form-control",'id'=>'village'],'text');?>
                        <?php if(isset($errors)) echo get_form_error_msg_from_array( $errors,'student_u_addr_village'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="road_house" class="col-sm-3 col-form-label"><?=lang('Sa.form_road_house');?></label>
                    <div class="col-sm-9">
                        <?=form_input('road_house',set_value('road_house',(!empty($updating_student) AND is_object($updating_student))?$updating_student->student_u_addr_road_house_no:''),['placeholder'=>'','class'=>"form-control",'id'=>'road_house'],'text');?>
                        <?php if(isset($errors)) echo get_form_error_msg_from_array( $errors,'student_u_addr_road_house_no'); ?>
                    </div>
                </div>
                
            </div>        
            <div class="mail-body text-center">                
                <button type="submit" name="saveProfileInfo_address" value="yes" class="btn btn-primary" ><i class="fa fa-save"></i> Save Address</button>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</article>


<?=form_close();?>


<script src="<?=cdn_url('bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js');?>" defer='defer'></script>
<link rel="stylesheet" href="<?=cdn_url('bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css');?>"/>


