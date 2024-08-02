<div class="row">
    <?php
        if(isset($display_msg) AND is_string($display_msg) AND strlen($display_msg) > 0){ echo $display_msg; }
        $FlsMsg = session()->getFlashdata('display_msg'); if(strlen($FlsMsg) > 0) echo $FlsMsg; 
    ?>
   
    <div class="col-lg-12 p-1">
            <div class="mail-box-header">
                <?=form_open(base_url('admin/academic/exam/results/delete?page='), ['method'=>'get']);?> 
                    <div class="input-group">
                            <?=form_input([
                                    'name'          => 'timeHcRangeStart',
                                    'class'         => 'form-control form-control-sm datetimepkerad',
                                    'value'         => (isset($fsv) AND is_array($fsv) AND isset($fsv['trs'])) ? $fsv['trs'] : '',
                                    'placeholder'   => 'From Time',
                                    'autocomplete'  => 'off',
                                    'title'         => 'This field work with update time.',
                            ]);?>
                            <?=form_input([
                                    'name'          => 'timeHcRangeEnd',
                                    'class'         => 'form-control form-control-sm datetimepkerad',
                                    'value'         => (isset($fsv) AND is_array($fsv) AND isset($fsv['tre'])) ? $fsv['tre'] : '',
                                    'placeholder'   => 'To Time',
                                    'autocomplete'  => 'off',
                                    'title'         => 'This field work with update time.',
                            ]);?>
                        
                        <?php if(isMobile()): ?>
                            </div>
                            <div class="input-group">
                        <?php endif; ?>
                        
                            <input type="number" class="form-control form-control-sm" name="student_id" value="<?=(isset($fsv) AND is_array($fsv) AND isset($fsv['sid']) AND $fsv['sid'] > 0) ? esc($fsv['sid']) : '';?>" placeholder="Student ID">
                            
                        <?php if(isMobile()): ?>
                            </div>
                            <div class="input-group">
                        <?php endif; ?>
                            
                            <?=form_dropdown('rows_number', ['5'=>'5','10'=>'10','20'=>'20','30'=>'30','40'=>'40'], (isset($fsv) AND is_array($fsv) AND isset($fsv['ism'])) ? esc($fsv['ism']) : '10', ['class'=>'custom-select form-control-sm pt-1']);?>
                            <?=form_dropdown('is_paid', ['all'=>'Paid/Unpaid','1'=>'Paid','0'=>'Unpaid'], (isset($fsv) AND is_array($fsv) AND isset($fsv['isp'])) ? esc($fsv['isp']) : '', ['class'=>'custom-select form-control-sm pt-1']);?>
                    
                        <?php if(isMobile()): ?>
                            </div>
                            <div class="input-group">
                        <?php endif; ?>
                        
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                            <a href="<?=base_url('admin/academic/exam/results/delete?page=');?>" class="btn btn-sm btn-secondary">Clear</a>
                        </div>
                    </div>
                <?=form_close();?>
            </div> 
        
        <div class="mt-5">
            <?=isset($ExamResultsPgr) ? $ExamResultsPgr : '';?>
        </div>
        
        <?php if(isset($ExamResults) AND is_array($ExamResults) AND count($ExamResults) > 0 ): ?>
            <?php foreach($ExamResults as $bl): ?>
                
                <div class="ibox-content table-responsive p-1 mt-1 mb-5" id="iboxXrID<?=intval($bl->exr_id);?>">
                    <?php if($bl->exr_deleted_at): ?>
                        <?=form_open(base_url("admin/academic/exam/results/delete"));?>
                            <div class="text-center d-print-none">
                                <?=form_hidden('delete_ex_from_trash_permanently', 'yes');?>
                                <?=form_hidden('delete_ex_result_pg', service('request')->getGet('page'));?>
                                <button type="submit" name="del_from_trash_exr_id" value="<?=intval($bl->exr_id);?>" class="btn btn-danger m-0 mb-1">
                                    <i class="fas fa-trash"></i> 
                                    Delete EXR ID Permanently : <?=intval($bl->exr_id);?> -  
                                    <?=esc(implode(' ', array_filter([get_name_initials($bl->student_u_name_initial),$bl->student_u_name_first,$bl->student_u_name_middle,$bl->student_u_name_last ])));?>
                                </button>
                            </div>
                        <?=form_close();?>
                        <?=form_open(base_url("admin/academic/exam/results/delete"));?>
                            <div class="text-center d-print-none">
                                <?=form_hidden('get_back_from_trash', 'yes');?>
                                <?=form_hidden('get_back_from_trash_pg', service('request')->getGet('page'));?>
                                <button type="submit" name="get_back_from_trash_exr_id" value="<?=intval($bl->exr_id);?>" class="btn btn-success m-0 mb-1">
                                    <i class="fas fa-recycle"></i> 
                                    Get Back: Deleted <?= time_elapsed_string($bl->exr_deleted_at);?>
                                </button>
                            </div>
                        <?=form_close();?>
                    <?php else: ?>
                        <?=form_open(base_url("admin/academic/exam/results/delete"));?>
                            <div class="text-center d-print-none">
                                <?=form_hidden('move_whole_ex_result_to_trash', 'yes');?>
                                <?=form_hidden('move_whole_ex_result_pg', service('request')->getGet('page'));?>
                                <button type="submit" name="send_to_trash_exr_id" value="<?=intval($bl->exr_id);?>" class="btn btn-warning m-0 mb-1">
                                    <i class="fas fa-trash"></i> 
                                    Trash Result Marks of EXR ID : <?=intval($bl->exr_id);?> -  
                                    <?=esc(implode(' ', array_filter([get_name_initials($bl->student_u_name_initial),$bl->student_u_name_first,$bl->student_u_name_middle,$bl->student_u_name_last ])));?>
                                </button>
                            </div>
                        <?=form_close();?>
                    <?php endif; ?>
                    
                    <table class="table table-bordered mb-0">
                        <tbody>
                                <tr>
                                    <th scope='col'><?=myLang('EXR','EXR');?></th>
                                    <th scope='col'><?=myLang('SCM','SCM');?></th>
                                    
                                    
                                    <th scope='col'><?=myLang('Created','তৈরি');?></th>
                                    <th scope='col'><?=myLang('Updated','হালনাগাদ');?></th>
                                    
                                    <th scope='col'><?=myLang('Status','Status');?></th>
                                    <th scope='col'><?=myLang('Roll','Roll');?></th>
                                    
                                    <th scope='col'><?=myLang('Session','Session');?></th>
                                    <th scope='col'><?=myLang('Class','Class');?></th>
                                </tr>
                                <tr>
                                    <td class="text-center" scope="row"><?=esc($bl->exr_id);?></td>
                                    <td class="text-center" scope="row"><?=esc($bl->exr_scm_id);?></td>
                                    <td class="text-center" scope="row"><?= time_elapsed_string($bl->exr_inserted_at);?></td>
                                    <td class="text-center" scope="row"><?= time_elapsed_string($bl->exr_updated_at);?></td>
                                    <td class="text-center" scope="row"><?= esc( get_student_class_status()[$bl->scm_status] );?></td>
                                    <td class="text-center" scope="row"><?= esc($bl->scm_c_roll);?></td>

                                    <td class="text-center" scope="row"><?= esc($bl->scm_session_year);?></td>
                                    <td class="text-center" scope="row">
                                        <?php $clsObjF = service('ClassesAndSemestersModel')->withDeleted()->get_single_class_with_parent_label(intval($bl->scm_class_id)); ?>
                                        <?= is_object($clsObjF) ? esc($clsObjF->title) .' ['.intval($bl->scm_class_id).']'  : '';?>
                                    </td>
                                </tr>
                                <tr class="text-center">
                                    <?php $colsHcTab = [
                                        // Column Name                              Title Tooltip
                                        [ myLang('Student Address','Student Address'), myLang('',''), 2 ],
                                        [ myLang('Exam Started','Exam Started'), myLang('','') ],
                                        [ myLang('Exam Ended','Exam Ended'), myLang('','') ],
                                        [ myLang('Exam Type','Exam Type'), myLang('','') ],
                                        [ myLang('Gender','Gender'), myLang('','') ],
                                        [ myLang('Father','Father'), myLang('','') ],
                                        [ myLang('Mother','Mother'), myLang('','') ],
                                    ]; ?>
                                    <?php foreach( $colsHcTab as $tabTdHc ){ echo "<th data-toggle='tooltip' colspan='".(isset($tabTdHc[2]) ? $tabTdHc[2] : 1)."' scope='col' title='{$tabTdHc[1]}'>{$tabTdHc[0]}</th>"; } ?>
                                </tr>
                                <tr>
                                    <td class="text-center" colspan="2" scope="row">
                                        <?=esc(implode(', ',array_filter([
                                            $bl->student_u_addr_road_house_no,
                                            $bl->student_u_addr_village,
                                            $bl->student_u_addr_post_office,
                                            $bl->student_u_addr_zip_code,
                                            $bl->student_u_addr_district,
                                            $bl->student_u_addr_state, 
                                            get_country_list(strlen($bl->student_u_addr_country)),
                                        ])));?>
                                    </td>
                                    <td class="text-center" scope="row"><?= explode(' ',$bl->axdts_exam_starts_at)[0];?></td>
                                    <td class="text-center" scope="row"><?= explode(' ',$bl->axdts_exam_ends_at)[0];?></td>
                                    <td class="text-center" scope="row"><?=get_available_class_exam_options(strval($bl->axdts_type),'Invalid Key');?></td>
                                    <td class="text-center" scope="row"><?=esc(get_gender_list($bl->student_u_gender ? $bl->student_u_gender : 'dummy-text-to-prevent-error'));?></td>
                                    <td class="text-center" scope="row"><?=esc($bl->student_u_father_name);?></td>
                                    <td class="text-center" scope="row"><?=esc($bl->student_u_mother_name);?></td>
                                </tr>
                                <tr><th colspan="8" class="text-center" >Admitted Courses</th></tr>
                                <?php 
                                    $marksBySubject = [ /* Course ID => Obtained Marks Percentage */];
                                    for($i=1; $i<=20; $i++){
                                        if(property_exists($bl, "exr_co_{$i}_id")){
                                            $course_id = intval($bl->{"exr_co_{$i}_id"});
                                            $obtained_mark = floatval($bl->{"exr_co_{$i}_re"});
                                            $outOf_marks = floatval($bl->{"exr_co_{$i}_ou"});
                                            $outOf_marks = ($outOf_marks < 1 ) ? 100 : $outOf_marks;

                                            if($obtained_mark > 0){
                                                $obtained_percentage = number_format(( $obtained_mark / $outOf_marks ) * 100, 2);
                                                $marksBySubject[$course_id] = [ 
                                                    'course_id' => $course_id,
                                                    'course'    => isset($courseNames[$course_id]) ? $courseNames[$course_id] : 'Invalid Course',
                                                    'percentage' => $obtained_percentage,
                                                    'LG' => get_exam_grade_by_percent(floatval($obtained_percentage),'LG'),
                                                    'GP' => get_exam_grade_by_percent(floatval($obtained_percentage),'GP'),
                                                    'M_obtained' => $obtained_mark,
                                                    'M_outOf' => $outOf_marks,
                                                ];
                                            }
                                        }
                                    }

                                    // Process mandatory courses
                                    $marksOfManadatorySubjects = [];
                                    for($i=1;$i<15;$i++){
                                        if(property_exists($bl, "scm_course_{$i}")){
                                            $mandatory_course_id = intval($bl->{"scm_course_{$i}"});
                                            if(isset($marksBySubject[$mandatory_course_id])){
                                                $marksOfManadatorySubjects[$mandatory_course_id] = $marksBySubject[$mandatory_course_id];
                                            }
                                        }
                                    }
                                    // Process Optional courses
                                    $marksOfOptionalSubjects = [];
                                    for($i=1;$i<15;$i++){
                                        if(property_exists($bl, "scm_course_op_{$i}")){
                                            $optional_course_id = intval($bl->{"scm_course_op_{$i}"});
                                            if(isset($marksBySubject[$optional_course_id])){
                                                $marksOfOptionalSubjects[$optional_course_id] = $marksBySubject[$optional_course_id];
                                            }
                                        }
                                    }
                                ?>
                                <tr>
                                    <td colspan="8" class="p-0">
                                        <table class="table table-bordered mb-0">
                                            <tbody>
                                                <tr class="text-center">
                                                    <th>Course ID</th>
                                                    <th>Course</th>
                                                    <th>Marks</th>
                                                    <th>Percentage</th>
                                                    <th>Letter Grade</th>
                                                    <th>Grade Point</th>
                                                    <th>Out of Marks</th>
                                                    <th>Course Type</th>
                                                </tr>
                                                <?php if(is_array($marksOfManadatorySubjects) AND count($marksOfManadatorySubjects) > 0): ?>
                                                    <?php foreach($marksOfManadatorySubjects as $tR): ?>
                                                        <tr class="text-center">
                                                            <td><?=esc($tR['course_id']);?></td>
                                                            <td><?=esc($tR['course']);?></td>
                                                            <td><?=esc($tR['M_obtained']);?></td>
                                                            <td><?=esc($tR['percentage']);?></td>
                                                            <td><?=esc($tR['LG']);?></td>
                                                            <td><?=esc($tR['GP']);?></td>
                                                            <td><?=esc($tR['M_outOf']);?></td>
                                                            <td>Mandatory</td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    No marks found for mandatory course.
                                                <?php endif; ?>
                                                <?php if(is_array($marksOfOptionalSubjects) AND count($marksOfOptionalSubjects) > 0): ?>
                                                    <?php foreach($marksOfOptionalSubjects as $tR): ?>
                                                        <tr class="text-center">
                                                            <td><?=esc($tR['course_id']);?></td>
                                                            <td><?=esc($tR['course']);?></td>
                                                            <td><?=esc($tR['M_obtained']);?></td>
                                                            <td><?=esc($tR['percentage']);?></td>
                                                            <td><?=esc($tR['LG']);?></td>
                                                            <td><?=esc($tR['GP']);?></td>
                                                            <td><?=esc($tR['M_outOf']);?></td>
                                                            <td>Optional</td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    No marks found for optional course.
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                        </tbody>
                    </table>
                </div>
            <?php endforeach;?>
        <?php else: ?>
            <?php $dm = myLang('No exam result found.','হাতে পরীক্ষার ফলাফল পাওয়া যায় নি'); echo get_display_msg($dm,'info');?>
            <div class="text-center"><?=anchor('admin/academic/exam/results','Add Exam Marks Here',['class'=>'btn btn-sm btn-info']);?></div>
        <?php endif;?>
        <div class="mt-1">
            <?=isset($ExamResultsPgr) ? $ExamResultsPgr : '';?>
        </div>
    </div>
</div>



<script {csp-script-nonce}>
    document.addEventListener("DOMContentLoaded", function(){
        $(".btnShowSpinOnClk").click(function(){
            var btn = $(this);
            var new_html = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ' + btn.text();
            btn.html(new_html);
        });
    });
</script>


<link href="<?=cdn_url('bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css');?>" rel="stylesheet" media="screen">
<script src="<?=cdn_url('bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js');?>" defer="defer" type="text/javascript"></script>
<script {csp-script-nonce}>
    document.addEventListener("DOMContentLoaded", function(){
        $('.datetimepkerad').datetimepicker({format: 'yyyy-mm-dd hh:ii:ss',autoclose: 1,showMeridian: 1,fontAwesome: true});
    });
</script>
