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



<article class="row">
    
    <div class="col-xl-6 animated fadeInRight">
        <?=form_open(base_url("admin/admission/edit/application/by/admin?InfoPage=basic-info&student_id=".intval(service('request')->getGet('student_id'))),['id'=>'editorForm']);?>
            <div class="mail-box-header">
                <h3>
                    Basic Information of Student
                    <?php $uid = intval(service('request')->getget('student_id')); if($uid > 0){ echo "(UID: $uid)"; } ?>
                </h3>
            </div>
            <div class="mail-box">
                <div class="mail-body">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label"  for="first_name">
                            <?=myLang('Student Name','শিক্ষার্থীর নাম');?>
                            <i class="fa fa-asterisk text-danger small" aria-hidden="true"></i>
                        </label>
                        <div class="col-sm-9">
                            <div class="input-group mb-2">
                                    <?php 
                                    $u_n_f = (!empty($updating_student) AND is_object($updating_student))?$updating_student->student_u_name_first:'';
                                    $u_n_m = (!empty($updating_student) AND is_object($updating_student))?$updating_student->student_u_name_middle:'';
                                    $u_n_l = (!empty($updating_student) AND is_object($updating_student))?$updating_student->student_u_name_last:'';
                                    $u_n_i = (!empty($updating_student) AND is_object($updating_student))?$updating_student->student_u_name_initial:'';
                                    ?>
                                    <?=form_dropdown('name_initials',get_name_initials(),set_value('name_initials', $u_n_i),['class'=>"custom-select"])?>
                                    <?=form_input('name_f',set_value('name_f', $u_n_f),['class'=>'form-control','placeholder'=>'first','aria-label'=>"First name",'required'=>'required','id'=>'first_name']);?>
                                    <?=form_input('name_m',set_value('name_m', $u_n_m),['class'=>'form-control','placeholder'=>'middle','aria-label'=>"Middle name"]);?>
                                    <?=form_input('name_l',set_value('name_l', $u_n_l),['class'=>'form-control','placeholder'=>'last','aria-label'=>"Last name"]);?>
                            </div> 
                            <?php if(isset($errors)) echo get_form_error_msg_from_array( $errors,'student_u_name_first'); ?>
                            <?php if(isset($errors)) echo get_form_error_msg_from_array( $errors,'student_u_name_middle'); ?>
                            <?php if(isset($errors)) echo get_form_error_msg_from_array( $errors,'student_u_name_last'); ?>
                            <?php if(isset($errors)) echo get_form_error_msg_from_array( $errors,'student_u_name_initial'); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="name_father" class="col-sm-3 col-form-label">
                            <?=lang('Sa.form_father_name');?>
                            <i class="fa fa-asterisk text-danger small" aria-hidden="true"></i>
                        </label>
                        <div class="col-sm-9">
                            <?=form_input('name_father',set_value('name_father',(!empty($updating_student) AND is_object($updating_student))?$updating_student->student_u_father_name:''),['placeholder'=>'','required'=>'required','class'=>"form-control",'id'=>'name_father'],'text');?>
                            <?php if(isset($errors)) echo get_form_error_msg_from_array( $errors,'student_u_father_name'); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="name_mother" class="col-sm-3 col-form-label">
                            <?=lang('Sa.form_mother_name');?>
                            <i class="fa fa-asterisk text-danger small" aria-hidden="true"></i>
                        </label>
                        <div class="col-sm-9">
                            <?=form_input('name_mother',set_value('name_mother',(!empty($updating_student) AND is_object($updating_student))?$updating_student->student_u_mother_name:''),['placeholder'=>'','required'=>'required','class'=>"form-control",'id'=>'name_mother'],'text');?>
                            <?php if(isset($errors)) echo get_form_error_msg_from_array( $errors,'student_u_mother_name'); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="gender" class="col-sm-3 col-form-label"><?=lang('Sa.form_gender');?></label>
                        <div class="col-sm-9">
                            <?= form_dropdown('gender',get_gender_list(),set_value('gender',(!empty($updating_student) AND is_object($updating_student))?$updating_student->student_u_gender:''),['class'=>"form-control",'id'=>'gender'])?>
                            <?php if(isset($errors)) echo get_form_error_msg_from_array( $errors,'student_u_gender'); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="religion" class="col-sm-3 col-form-label"><?=lang('Sa.form_religion');?></label>
                        <div class="col-sm-9">
                            <?= form_dropdown('religion',get_religion_list(),set_value('religion',(!empty($updating_student) AND is_object($updating_student))?$updating_student->student_u_religion:''),['class'=>"form-control",'id'=>'religion'])?>
                            <?php if(isset($errors)) echo get_form_error_msg_from_array( $errors,'student_u_religion'); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-3 h5" for="dofb">
                            <?=lang('Sa.form_dob');?>
                            <i class="fa fa-asterisk text-danger small" aria-hidden="true"></i>
                        </label>
                        <div class="input-group date col-sm-9" data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-autoclose="true">
                            <?=form_input([
                                'name'      => 'dofb',
                                'value'     => set_value('dofb',(!empty($updating_student) AND is_object($updating_student))?($updating_student->student_u_date_of_birth > '1000-01-01 00:00:01' ? explode(' ',$updating_student->student_u_date_of_birth)[0] :''):''),
                                'type'      => 'text',
                                'class'     => 'form-control',
                                'id'        => 'dofb',
                                'required'  => 'required',
                                'maxlength' => '9',
                                'data-toggle' => 'tooltip',
                                'data-placement'=>'right',
                                'placeholder'=> 'yyyy-mm-dd',
                                'autocomplete'=>'off',
                            ]);?>
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        </div>
                        <?= get_form_error_msg_from_array(! empty($errors) AND is_array($errors) ? $errors : [], 'student_u_date_of_birth');?>
                    </div>

                </div>

                <div class="mail-body text-center">                
                    <button type="submit" name="saveProfileInfo_basic_admin" value="yes" class="btn btn-primary" ><i class="fa fa-save"></i> Save Basic Information</button>
                </div>
                <div class="clearfix"></div>
            </div>
        <?=form_close();?>
    </div>
    
    
    <?php if(is_array($inAdStuList) AND count($inAdStuList) > 0 ): ?>
        <div class="col-xl-6 animated fadeInRight">
            <div class="mail-box-header text-center pl-0 pr-0">
                <h4>Start from where you left</h4>
                <div>Here is a list of incomplete admission students. You left without completing the admission process earlier. Select one from them if needed.</div>
                <article class="mt-3 table-responsive">
                    <table class="table table-sm table-bordered">
                        <tbody>
                            <tr class="text-center">
                                <th>UID</th>
                                <th>Name</th>
                                <th>Father</th>
                                <th>Birth</th>
                                <th>Gender/Religion</th>
                                <th></th>
                            </tr>
                            <?php  foreach($inAdStuList as $lx): ?>
                                <?php 
                                // We are working on this student
                                if(is_object($updating_student) AND intval($updating_student->student_u_id) === intval($lx->student_u_id)) continue;
                                ?>
                                <tr>
                                    <td><?=esc($lx->student_u_id);?></td>
                                    <td><?=esc(service('AuthLibrary')->getUserFullName_fromObj($lx));?></td>
                                    <td><?=esc($lx->student_u_father_name);?></td>
                                    <td><?=date('d F Y', strtotime($lx->student_u_date_of_birth));?></td>
                                    <td>
                                        <?=esc(get_gender_list($lx->student_u_gender ? $lx->student_u_gender : 'dummp-text-to-prevent-error'));?> /
                                        <?=esc(get_religion_list($lx->student_u_religion ? $lx->student_u_religion : 'dummp-text-to-prevent-error'));?>
                                    </td>
                                    <td class="p-0">
                                        <?=anchor("admin/admission/edit/application/by/admin?InfoPage=basic-info&student_id={$lx->student_u_id}",'<i class="fa fa-edit" aria-hidden="true"></i>',['class'=>'btn btn-success p-2']);?>
                                        <?=form_open('admin/admission/edit/application/by/admin?ok=del',['method'=>'post','class'=>'m-0 p-0'],['delStuUID' => $lx->student_u_id]);?>
                                            <?=form_button([
                                                'type' => 'submit','name' => 'dInAdStudent','value' => 'yes',
                                                'content' => '<i class="fas fa-trash"></i>',
                                                'class' => 'btn btn-warning p-2',
                                            ]);?>
                                        <?=form_close();?> 
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="ml-2"><?=$inAdStuListPgr;?></div>
                </article>
            </div>
        </div>
    <?php endif; ?>
</article>



<script src="<?=cdn_url('bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js');?>" defer='defer'></script>
<link rel="stylesheet" href="<?=cdn_url('bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css');?>"/>


