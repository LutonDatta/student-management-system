<div class="row">
    <div class="col-lg-12">
        <?php
            if(isset($display_msg) AND is_string($display_msg) AND strlen($display_msg) > 0){ echo $display_msg; }
            $FlsMsg = session()->getFlashdata('display_msg'); if(strlen($FlsMsg) > 0) echo $FlsMsg; 
            if(isset($errors) AND is_array($errors) AND count($errors) > 0) echo get_display_msg(implode(',',$errors),'danger');
        ?>
    </div>
</div>

<?php $hide_marksheet_box = [];?>
<?php $getSchool = getSchool(); ?>

<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="ibox-content border">
            <div class="row">
                <div class="col-12 text-center mb-3 pb-3 border-bottom">
                    <div class="h4 mt-0 mb-0 pt-0 li"><?=esc(get_option('instNameEn'));?></div>
                    <div class="h6 mt-0 mb-0 pt-0 li">
                        <?=implode(',',array_filter([
                            get_option('schOfficialAddressPostCode'),
                            get_option('schOfficialAddressPost'),
                            get_option('schOfficialAddressDistrict'),
                            get_option('schOfficialAddressCountry'),
                            ]));?>
                    </div>
                    <div class="h6 mt-0 mb-0 pt-0 li">
                        EIIN: <?=esc($getSchool->sch_eiin);?>,
                        Contact: <?=esc($getSchool->sch_contact);?>,
                    </div>
                </div>
                
                <div class="col-4">
                    <table class="table table-bordered text-left table-sm">
                        <tbody>
                            <?php $clsObjF = service('ClassesAndSemestersModel')->withDeleted()->get_single_class_with_parent_label(intval($studentSCM->scm_class_id)); ?>
                                
                            <tr><td class="p-0">Class Name:</td><td class="p-0"><?= is_object($clsObjF) ? esc($clsObjF->title) : '';?></td></tr>
                            <tr><td class="p-0">Class Roll:</td><td class="p-0"><?=esc($studentSCM->scm_c_roll);?></td></tr>
                            <tr><td class="p-0">Session/Year:</td><td class="p-0"><?=esc($studentSCM->scm_session_year);?></td></tr>
                            <tr><td class="p-0">Status:</td><td class="p-0"><?= esc( get_student_class_status()[$studentSCM->scm_status] );?></td></tr>
                            
                            <tr><td class="p-0 text-center" colspan="5">SID: <?=esc($studentSCM->scm_u_id);?>, SCM ID: <?=esc($studentSCM->scm_id);?>, Class ID: <?=esc($studentSCM->scm_class_id);?></td></tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="col-5 text-center">
                    <div class="h5">ACADEMIC TRANSCRIPT</div>
                    <div>of</div>
                    <div class="h6">
                        <?=is_object($studentSCM) ? esc(trim(get_name_initials($studentSCM->student_u_name_initial) . ' ' . $studentSCM->student_u_name_first . ' ' . $studentSCM->student_u_name_middle . ' ' . $studentSCM->student_u_name_last)) : 'No name found'; ?>
                    </div>
                    <div>
                        <?=($studentSCM->student_u_gender === 'male') ? 'Son of' : 'Daughter of';?>
                        <span class=""><?=is_object($studentSCM) ? esc($studentSCM->student_u_father_name) : 'No father name found';?></span>
                        &
                        <span class=""><?=is_object($studentSCM) ? esc($studentSCM->student_u_mother_name) : 'No mother name found';?></span>
                    </div>
                </div>
                
                <div class="col-3">
                    <table class="table table-bordered text-center table-sm">
                        <tbody>
                            <tr><td class="p-0">LG</td><td class="p-0">% if Marks</td><td class="p-0">GP</td></tr>
                            <tr><td class="p-0">A+</td><td class="p-0">80-100</td><td class="p-0">5.0</td></tr>
                            <tr><td class="p-0">A</td><td class="p-0">70-79</td><td class="p-0">4.0</td></tr>
                            <tr><td class="p-0">A-</td><td class="p-0">60-69</td><td class="p-0">3.5</td></tr>
                            <tr><td class="p-0">B</td><td class="p-0">50-59</td><td class="p-0">3.0</td></tr>
                            <tr><td class="p-0">C</td><td class="p-0">40-49</td><td class="p-0">2.0</td></tr>
                            <tr><td class="p-0">D</td><td class="p-0">33-39</td><td class="p-0">1.0</td></tr>
                            <tr><td class="p-0">F</td><td class="p-0">0-32</td><td class="p-0">0.0</td></tr>
                        </tbody>
                    </table>
                </div>
                
                <?php if(count($ExamResults) > 0 ) : foreach($ExamResults as $ExamTermOne):  ?>
                    <?php 
                        $marksBySubject = [ /* Course ID => Obtained Marks Percentage */];
                        for($i=1; $i<=20; $i++){
                            if(property_exists($ExamTermOne, "exr_co_{$i}_id")){
                                $course_id = intval($ExamTermOne->{"exr_co_{$i}_id"});
                                $obtained_mark = floatval($ExamTermOne->{"exr_co_{$i}_re"});
                                $outOf_marks = floatval($ExamTermOne->{"exr_co_{$i}_ou"});
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
                            if(property_exists($studentSCM, "scm_course_{$i}")){
                                $mandatory_course_id = intval($studentSCM->{"scm_course_{$i}"});
                                if(isset($marksBySubject[$mandatory_course_id])){
                                    $marksOfManadatorySubjects[$mandatory_course_id] = $marksBySubject[$mandatory_course_id];
                                }
                            }
                        }
                        // Process Optional courses
                        $marksOfOptionalSubjects = [];
                        for($i=1;$i<15;$i++){
                            if(property_exists($studentSCM, "scm_course_op_{$i}")){
                                $optional_course_id = intval($studentSCM->{"scm_course_op_{$i}"});
                                if(isset($marksBySubject[$optional_course_id])){
                                    $marksOfOptionalSubjects[$optional_course_id] = $marksBySubject[$optional_course_id];
                                }
                            }
                        }
                        
                        // Calculate GPA 
                        $gpaCalculate_total = 0;
                        $gpaCalculate_divBy = 0;
                        foreach($marksOfManadatorySubjects as $calM){
                            $gpFloat = floatval($calM['GP']);
                            if($gpFloat > 0){
                                $gpaCalculate_total += $gpFloat;
                            }
                            $gpaCalculate_divBy++;
                        }
                        $gpa_without_optional = $gpaCalculate_total/$gpaCalculate_divBy;
                        
                        // Calculate GPA Optional Subjects
                        $gpaCalculate_total_op = 0;
                        $gpaCalculate_divBy_op = 0;
                        foreach($marksOfOptionalSubjects as $calM){
                            $gpFloat = floatval($calM['GP']);
                            if($gpFloat > 0){
                                $gpaCalculate_total_op += $gpFloat;
                            }
                            $gpaCalculate_divBy_op++;
                        }
                        $gpa_for_optional_subjects = ($gpaCalculate_total_op/($gpaCalculate_divBy_op < 1 ? 1 : $gpaCalculate_divBy_op))-2;
                        $gpa_for_optional_subjects = ($gpa_for_optional_subjects > 0 ) ? $gpa_for_optional_subjects : 0;
                        
                        $gpa_total_with_optional_subject = $gpa_without_optional+$gpa_for_optional_subjects;
                        $gpa_total_with_optional_subject = ($gpa_total_with_optional_subject > 5) ? 5.0 : $gpa_total_with_optional_subject;
                    ?>
                    <?php  
                        $dt = service('AcademicExamDateTimeModel')->find($ExamTermOne->exr_axdts_id); 
                        $node_id = $dt->axdts_id . '-' . $dt->axdts_type;
                        $hide_marksheet_box[$node_id] = get_available_class_exam_options(strval($dt->axdts_type),'Invalid Key') . '-' . esc($dt->axdts_session_year);
                    ?>
                    <div class="col-12 mb-2" id="sheet_<?=esc($node_id);?>">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <td colspan="10" class="text-center">
                                        <?=myLang('Type','প্রকার') . ': ' . esc(get_available_class_exam_options(strval($dt->axdts_type),'Invalid Key'));?>,
                                        <?=myLang('Session','শিক্ষাবর্ষ') . ': ' . esc(esc($dt->axdts_session_year));?>,
                                        <?=myLang('Exam Start','পরীক্ষা আরম্ভ') . ': ' . esc(esc($dt->axdts_exam_starts_at));?> <span class="label-info pl-1 pr-1"><?=esc(App\Core\Time::parse(strval($dt->axdts_exam_starts_at), 'Asia/Dhaka','en-US')->humanize());?></span>
                                    </td>
                                </tr>
                                <tr class="text-center">
                                    <td>Course ID</td>
                                    <td>Name of Subjects</td>
                                    <td>Percentage</td>
                                    <td>Letter Grade</td>
                                    <td>Grade Point</td>
                                    <td>GPA <br>(Without Additional Subject)</td>
                                    <td>GPA</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $loop_count = 0; foreach($marksOfManadatorySubjects as $co_id => $mn1) : $loop_count++; ?>
                                <tr>
                                    <td class="text-center"><?=esc($co_id);?></td>
                                    <td class="text-center"><?=esc($mn1['course']);?></td>
                                    <td class="text-center"><?=esc($mn1['percentage']);?></td>
                                    <td class="text-center"><?=esc($mn1['LG']);?></td>
                                    <td class="text-center"><?=esc($mn1['GP']);?></td>
                                    <?php if($loop_count === 1 ): ?>
                                        <td class="text-center align-content-center" rowspan="<?=count($marksOfManadatorySubjects);?>"><?=esc(number_format($gpa_without_optional,2));?></td>
                                        <td class="text-center align-content-center" rowspan="<?=count($marksOfManadatorySubjects) + 1 + count($marksOfOptionalSubjects);?>"><?=esc(number_format($gpa_total_with_optional_subject,2));?></td>
                                    <?php endif;?>
                                </tr>
                                <?php endforeach; ?>
                                
                                
                                <?php if(count($marksOfOptionalSubjects) > 0 ) : ?>
                                    <tr>
                                        <td colspan="5">Optional subjects</td>
                                        <td class="text-center">GP Above 2</td>
                                    </tr>
                                    <?php $loop_count = 0; foreach($marksOfOptionalSubjects as $co_id => $mn1) : $loop_count++; ?>
                                        <tr>
                                            <td class="text-center"><?=esc($co_id);?></td>
                                            <td class="text-center"><?=esc($mn1['course']);?></td>
                                            <td class="text-center"><?=esc($mn1['percentage']);?></td>
                                            <td class="text-center"><?=esc($mn1['LG']);?></td>
                                            <td class="text-center"><?=esc($mn1['GP']);?></td>
                                            <?php if($loop_count === 1 ): ?>
                                                <td class="text-center align-content-center" rowspan="<?=count($marksOfOptionalSubjects);?>"><?=esc($gpa_for_optional_subjects);?></td>
                                            <?php endif;?>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="text-center border m-2 label-info">
                            <?=myLang('No exam taken for this student or no result added for this student.','এই শিক্ষার্থীর কোন পরীক্ষার ফলাফল  পাওয়া যায়নি।');?>
                                    <a href="<?=base_url('admin/academic/exam/results');?>" class="btn btn-sm btn-secondary">Add Result</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>                
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="text-center">
            <div class="mb-3 d-print-none">
                <div><?=myLang('Do not want to see all of them? Click to hide them.','সবগুলো কি দেখতে চাইছেন না? তাহলে যে কোনটি লুকাতে নিচে টেপ করুন।');?></div>
                <?php foreach($hide_marksheet_box as $hideID => $hideX) : ?>
                    <div class="border btn btn-secondary hide_exam_on_click" role="button" data-showing="yes" data-hideid="<?=esc($hideID);?>"><?=esc($hideX);?></div>
                <?php endforeach;?>
                <script {csp-script-nonce}>
                    document.addEventListener('DOMContentLoaded',function(){
                        $('.hide_exam_on_click').click(function(c,v,b){
                            var $id = $(this).data('hideid');
                            var $sw = $(this).data('showing');
                            if($sw ==='yes'){
                                $(this).data('showing','no');
                                $(this).removeClass('btn-secondary');
                                $('#sheet_'+$id).slideUp();
                            }else{
                                $(this).data('showing','yes');
                                $(this).addClass('btn-secondary');
                                $('#sheet_'+$id).slideDown();
                            }
                        });
                    });
                </script>
            </div>
                
            <span class="d-none d-print-inline">Printed at: <?=date('jS M Y h:i:s a O');?> by SID: <?=intval(service('AuthLibrary')->getLoggedInUserID());?></span>
            <button class="btn btn-info d-print-none" id="printBoxBtnx"><i class="fas fa-print"></i> Print</button>
            <script {csp-script-nonce}>document.addEventListener('DOMContentLoaded',function(){document.getElementById('printBoxBtnx').addEventListener('click',function(){window.print();});});</script>            
        </div>
    </div>
</div>