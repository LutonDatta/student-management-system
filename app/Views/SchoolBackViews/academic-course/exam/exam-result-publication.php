<div class="row">
    <div class="col-lg-12">
        <?php
            if(isset($display_msg) AND is_string($display_msg) AND strlen($display_msg) > 0){ echo $display_msg; }
            $FlsMsg = session()->getFlashdata('display_msg'); if(strlen($FlsMsg) > 0) echo $FlsMsg; 
            if(isset($errors) AND is_array($errors) AND count($errors) > 0) echo get_display_msg(implode(',',$errors),'danger');
        ?>
    </div>
</div>


<div class="row">
    <div class="col-lg-12">
        <div class="ibox ">
            <?php if(isset($examDtTmLst) AND is_array($examDtTmLst) AND count($examDtTmLst) > 0 ): ?>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-12">
                        <?php
                            $options = [ '0' => myLang('Select exam date time from here','এখান থেকে পরীক্ষার দিন তারিখ নির্বাচন করুন') ];
                            foreach($examDtTmLst as $sglDt){
                                $clsx = service('ClassesAndSemestersModel')->withDeleted()->whereIn('classes_and_semesters.fcs_id',(array)@unserialize($sglDt->axdts_class_id))->get_classes_with_parent_label_for_dropdown(false, 'clsprts', 20, false);
                                if(is_array($clsx) AND count($clsx) > 0 ){
                                    $clsNames = ( implode(', ', $clsx) );
                                }else{
                                    $clsNames = 'No Class';
                                }
                                $type   = myLang('Type','প্রকার') . ': '  . get_available_class_exam_options(strval($sglDt->axdts_type),'Invalid Key');
                                $sess   = myLang('Session','শিক্ষাবর্ষ')  . ': '  .  esc($sglDt->axdts_session_year);
                                $start  = myLang('Exam Start','পরীক্ষা আরম্ভ') . ': ' . esc($sglDt->axdts_exam_starts_at);
                                $ends   = myLang('Exam Ends','পরীক্ষা সমাপ্তি') . ': ' . esc($sglDt->axdts_exam_ends_at);
                                $options[ $sglDt->axdts_id ] = $sglDt->axdts_id . ': ' . $clsNames . ' - ' . $sess . ' - ' . $type . ' - ' . $start . ' - ' . $ends;
                            }
                            echo form_dropdown('xam_dt_tm_id', $options,intval(service('request')->getGet('result_for_dttm_id')), ['class'=>'form-control','id'=>'redOnChgExDtTm','required'=>'required']);
                        ?>
                        </div>
                        <?php if(isset($examDtTmLstPgr) AND $examDtTmLstPgr->getLastPage() > 1): ?>
                            <div class="col-12"><?=$examDtTmLstPgr->links();?></div>
                        <?php endif;?>
                    </div>
                </div>
            <?php endif;?>
        
            <?php if(isset($available_classes) AND is_array($available_classes) AND count($available_classes) > 0 ): ?>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-12">
                        <?php
                            $options = [ '0' => myLang('Select class from here','Select class from here') ];
                            $options = $options + (array)$available_classes;
                            echo form_dropdown('result_for_class_id', $options ,intval(service('request')->getGet('result_for_class_id')), ['class'=>'form-control','id'=>'result_for_class_id','required'=>'required']);
                        ?>
                        </div>
                    </div>
                </div>
            <?php endif;?>
        
            <?php if(isset($available_courses) AND is_array($available_courses) AND count($available_courses) > 0 ): ?>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-12">
                        <?php
                            $options = [ '0' => myLang('Select class from here','Select class from here') ];
                            $options = $options + (array)$available_courses;
                            echo form_dropdown('result_for_course_id', $options ,intval(service('request')->getGet('result_for_course_id')), ['class'=>'form-control','id'=>'result_for_course_id','required'=>'required']);
                        ?>
                        </div>
                    </div>
                </div>
            <?php endif;?>
        </div>
    </div>
</div>


<?php if(isset($available_students) AND is_array($available_students) AND count($available_students) > 0 ): ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox-content">
                <?=form_open(base_url('admin/academic/exam/results'),['class'=>'','method'=>'post'],[
                            'result_for_dttm_id'    => intval(service('request')->getGet('result_for_dttm_id')),
                            'result_for_class_id'   => intval(service('request')->getGet('result_for_class_id')),
                            'result_for_course_id'  => intval(service('request')->getGet('result_for_course_id')),
                            'result_pager_page_no'  => intval(service('request')->getGet('page_store_marks')),
                        ]);?>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col" class="text-center"><?=myLang('Class Roll','শ্রেণি রোল');?></th>
                                <th scope="col" class="text-left">
                                    <?=myLang('Name','Name');?>/
                                    <?=myLang('Status','Status');?>
                                </th>
                                <th scope="col" class="text-left"><?=myLang('Obtained Mark/Out Of','প্রাপ্ত নম্বর/মোট নম্বর');?></th>
                                <th scope="col" class="text-center"><?=myLang('UID','UID');?></th>
                            </tr>
                        </thead>
                        <tbody>
                                <?php foreach($available_students as $aStd): ?>
                                    <tr>
                                        <th class="text-center" scope="row"><?=intval($aStd->scm_c_roll);?></th>
                                        <td class="text-left">
                                            <?=esc(implode(' ', array_filter([get_name_initials($aStd->student_u_name_initial),$aStd->student_u_name_first,$aStd->student_u_name_middle,$aStd->student_u_name_last ])));?>
                                            <span class="label-success pl-1 pr-1 text-right"><?php $xt = get_student_class_status(false); echo isset($xt[$aStd->scm_status]) ? $xt[$aStd->scm_status] :'';?> </span>
                                            <?php 
                                                if($aStd->scm_deleted_at){
                                                    echo '<span class="label-warning">Deleted ' . time_elapsed_string($aStd->scm_deleted_at) . '</span>';
                                                }
                                            ?>
                                        </td>
                                        <td class="text-center p-0">
                                            <div class="input-group text-center">
                                                <div class="input-group-prepend">
                                                    <?=form_input([
                                                        'name'      => "stdr_obtained_marks[".esc($aStd->scm_id)."]",
                                                        'class'     => 'form-control form-control-sm m-0',
                                                        'type'      => 'text',
                                                        'size'      => '4',
                                                        'value'     => (isset($oldMarkOfStudents) AND is_array($oldMarkOfStudents) AND isset($oldMarkOfStudents[$aStd->scm_id])) ? esc($oldMarkOfStudents[$aStd->scm_id][0]): '',
                                                        'placeholder' => myLang('Obtained Mark','প্রাপ্ত নম্বর')
                                                    ]);?>
                                                </div>
                                                <div class="input-group-append">
                                                    <?=form_input([
                                                        'name'      => "stdr_out_of_marks[".esc($aStd->scm_id)."]",
                                                        'class'     => 'form-control form-control-sm m-0',
                                                        'type'      => 'text',
                                                        'size'      => '4',
                                                        'value'     => (isset($oldMarkOfStudents) AND is_array($oldMarkOfStudents) AND isset($oldMarkOfStudents[$aStd->scm_id])) ? esc($oldMarkOfStudents[$aStd->scm_id][1]): '100',
                                                        'placeholder' => myLang('Out Of','মোট নম্বর')
                                                    ]);?>
                                                </div>
                                            </div>
                                            
                                            <?=isset($errors) ? get_form_error_msg_from_array($errors,'') : '';?>
                                        </td>
                                        <td class="text-center"><?=intval($aStd->scm_u_id);?></td>
                                    </tr>
                                <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                    <div class=" text-center">    
                        <button type="submit" name="submit_marks_result" value="yes" class="btn btn-primary m-4"><?=myLang('Save Marks','নম্বর সংরক্ষণ করুন');?></button>
                    </div>
                <?=form_close();?>
                <?=$available_studentsPgr;?>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-info text-center">
        <?php 
            $pClsID = intval(service('request')->getGet('result_for_class_id')); 
            echo ($pClsID < 1) ? myLang('Select class to see student list. ','শিক্ষার্থীদের তালিকা দেখার জন্য সঠিক ক্লাশ নির্বাচন করুন। ') : myLang('Class ID','ক্লাশ আইডি') . ": $pClsID ";
            $pCrsID = intval(service('request')->getGet('result_for_course_id'));
            echo ($pCrsID < 1) ? myLang('Select course/subject. ','কোর্স/বিষয় নির্বাচন করুন। ') : myLang('Course/Subject ID','কোর্স/বিষয় আইডি') . ": $pCrsID "; 
            echo ($pClsID > 0) ? myLang('No student found in this class. ','এই ক্লাশে কোন শিক্ষার্থী পাওয়া যায়নি। ') : '';
        ?>
    </div>
<?php endif; ?>

<?php if(empty($examDtTmLst) OR ! is_array($examDtTmLst) OR count($examDtTmLst) < 1 ): ?>
    <div class='text-center'>
        No exam date time found. Make sure exam date time and routine is set correctly.
        <?=anchor('admin/academic/exam/date/time','Set exam date time', ['class' =>'btn btn-warning']);?>
    </div>
<?php endif;?>




<script src="<?= cdn_url('js/show-spinner-on-submit.js');?>"></script>
<link href="<?=cdn_url('bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css');?>" rel="stylesheet" media="screen">
<script src="<?=cdn_url('bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js');?>" defer="defer" type="text/javascript"></script>
<script {csp-script-nonce}>
    document.addEventListener("DOMContentLoaded", function(){        
        $( "#redOnChgExDtTm" ).change(function(){
            window.location.href = "<?=base_url("admin/academic/exam/results?result_for_dttm_id=");?>" + $(this).val();
        });
        $( "#result_for_class_id" ).change(function(){
            window.location.href = "<?=base_url("admin/academic/exam/results?result_for_dttm_id=".intval(service('request')->getGet('result_for_dttm_id'))."&result_for_class_id=");?>" + $(this).val();
        });
        $( "#result_for_course_id" ).change(function(){
            window.location.href = "<?=base_url("admin/academic/exam/results?result_for_dttm_id=".intval(service('request')->getGet('result_for_dttm_id'))."&result_for_class_id=".intval(service('request')->getGet('result_for_class_id'))."&result_for_course_id=");?>" + $(this).val();
        });
        
        
    });
</script>
