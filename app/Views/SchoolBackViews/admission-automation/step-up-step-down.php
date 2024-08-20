<?php
            if(isset($display_msg) AND is_string($display_msg) AND strlen($display_msg) > 0){ echo $display_msg; }
            $FlsMsg = session()->getFlashdata('display_msg'); if(strlen($FlsMsg) > 0) echo $FlsMsg; 
        ?>

<div class="row">
    <div class="col-lg-12">
        <div class="ibox">
            <div class="ibox-title">
                <h5>Automatically upgrade or downgrade students from one class to another class or session.</h5>
                <div>
                    When students pass/fail in one class you need to admit them in another class. For 
                    example if one student pass in class six he will be admitted in class seven.
                    or If a student fail in class six in 2020 he will read in the same class in 2021.
                </div>
                <div class="ibox-tools"><a class="collapse-link" href=""><i class="fa fa-chevron-up"></i></a><a class="close-link" href=""><i class="fa fa-times"></i></a></div>
            </div>
            <div class="ibox-content inspinia-timeline pb-0">
                <?=form_open('admin/admission/step/up/down',['method'=>'get']);?>
                    <div class="row">
                        <div class="form-group col-3 mb-0">
                            <label for="from_class">From Class</label>
                            <?= form_dropdown("from_class",[''=>''] + $allClasses_from, [$studentsFrom['from_class']], ['class'=>'form-control','required'=>'required']); ?>
                            <?=isset($allClasses_pgr_from) ? '<div class="pt-1">' . $allClasses_pgr_from . '</div>' : '';?>
                        </div>
                        <div class="form-group col-3 mb-0">
                            <label for="from_year">From Session/Year</label>
                            <?php 
                            $allSessions_processed = [];
                            if(is_array($allSessions_from)) foreach($allSessions_from as $sess ){
                                if(is_object($sess) AND property_exists($sess, 'scm_session_year') AND strlen($sess->scm_session_year) > 0){
                                    $allSessions_processed[$sess->scm_session_year] = $sess->scm_session_year;
                                }
                            }
                            if(count($allSessions_processed) < 1){
                                $allSessions_processed[date('Y')] = date('Y');
                            }
                            ?>
                            <?= form_dropdown("from_year", [''=>''] + $allSessions_processed, [$studentsFrom['from_year']], ['class'=>'form-control','required'=>'required']); ?>
                            <?=isset($allSessions_pgr_from) ? '<div class="pt-1">' . $allSessions_pgr_from . '</div>' : '';?>
                        </div>
                        <div class="form-group col-3 mb-0">
                            <label for="from_status">From Status</label>
                            <?= form_dropdown("from_status", get_student_class_status(), [$studentsFrom['from_status']], ['class'=>'form-control','required'=>'required']); ?>
                        </div>
                        <div class="form-group col-3 mb-0">
                            <button class="btn btn-info mt-4" type="submit"><i class="fa fa-search"></i> Find Students</button>
                        </div>
                    </div>
                <?=form_close();?>
            </div>
        </div>
    </div>
    
    
    <div class="col-lg-12 animated fadeInRight">
        <div class="table-responsive">
            <table class="table table-hover table-bordered bg-white">
                <thead class="thead-light">
                    <tr class="text-center">
                        <th scope="col" <?=tt_title('Select All Rows', 'right');?>>
                            <input type="checkbox" class="checkboxAllMarker">
                        </th>
                        <th scope="col" <?=tt_title('User ID', 'right');?>>SID</th>
                        <th scope="col" <?=tt_title('Courses Classes Students Mapping ID', 'right');?>>SCM ID</th>
                        <th scope="col" <?=tt_title('Class Roll', 'right');?>>Roll</th>
                        <th scope="col" <?=tt_title(myLang('Student Name','শিক্ষার্থীর নাম'), 'right');?>>Name</th>
                        <th scope="col" <?=tt_title('Name of Father', 'right');?>>Father</th>
                        <th scope="col" <?=tt_title('Name of Mother', 'right');?>>Mother</th>
                        <th scope="col">Condition</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(isset($students) AND is_array($students) AND count($students) > 0 ): ?>
                        
                        <?php foreach($students as $stdDta): ?>
                            <tr class="text-center">
                                <td>
                                    <input type="checkbox" class="cxbxStdMkr" data-student_id="<?=$stdDta->student_u_id;?>">
                                </td>
                                <td><?=$stdDta->student_u_id;?></td>
                                <td><?=$stdDta->scm_id;?></td>
                                <td <?=tt_title('Class Roll','left');?>><?=$stdDta->scm_c_roll;?></td>
                                <td>
                                    <?=esc(service('AuthLibrary')->getUserFullName_fromObj($stdDta,'No name'));?>
                                </td>
                                <td><?=esc($stdDta->student_u_father_name);?></td>
                                <td><?=esc($stdDta->student_u_mother_name);?></td>
                                <td class="small">
                                    <?php 
                                    if($stdDta->scm_deleted_at){
                                        echo '<span class="warning text-warning label-warning">Deleted ' . time_elapsed_string($stdDta->scm_deleted_at) . '</span>';
                                    }
                                    echo '<span class="label label-success">Active</span>';
                                    ?>
                                    <?=anchor(
                                            "print/student/admission/confirmation/form?user_id={$stdDta->student_u_id}&scm_id={$stdDta->scm_id}",
                                            '<i class="fas fa-print"></i> ' . lang('Sbs_guide.btn_print'),
                                            ['class'=>'label label-info','target'=>'_blank']);?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr class="text-center">
                            <td colspan="20">No student to list.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot class="thead-light">
                    <tr class="text-center">
                        <th scope="col" <?=tt_title('Select All Rows', 'right');?>>
                            <input type="checkbox" class="checkboxAllMarker">
                        </th>
                        <th scope="col" <?=tt_title('User ID', 'right');?>>SID</th>
                        <th scope="col" <?=tt_title('Courses Classes Students Mapping ID', 'right');?>>SCM ID</th>
                        <th scope="col" <?=tt_title('Class Roll', 'right');?>>Roll</th>
                        <th scope="col" <?=tt_title(myLang('Student Name','শিক্ষার্থীর নাম'), 'right');?>>Name</th>
                        <th scope="col" <?=tt_title('Name of Father', 'right');?>>Father</th>
                        <th scope="col" <?=tt_title('Name of Mother', 'right');?>>Mother</th>
                        <th scope="col">Condition</th>
                    </tr>
                </tfoot>
            </table>
            <?= isset($students_pgr) ? $students_pgr : '';?>
        </div>
        <div class="clearfix"></div>
    </div>
    
    <div class="col-lg-12">
        <div class="ibox">
            <div class="ibox-content inspinia-timeline">
                <?=form_open('admin/admission/step/up/down',['method'=>'post'],['sbtStdsIds'=>'yes']);?>
                    <!--Value (comma separated ids) will be added using JS. Value looks like: 23,45,56-->
                    <input type="hidden" id="valueToSubmit_studentIDs" class="d-none hide" name="studentIDs" value="">
                    <input type="hidden" class="d-none hide" name="post_from_class" value="<?= (isset($studentsFrom) AND isset($studentsFrom['from_class'])) ? esc($studentsFrom['from_class']) : '';?>">
                    <input type="hidden" class="d-none hide" name="post_from_year" value="<?= (isset($studentsFrom) AND isset($studentsFrom['from_year'])) ? esc($studentsFrom['from_year']) : '';?>">
                    <input type="hidden" class="d-none hide" name="post_from_status" value="<?= (isset($studentsFrom) AND isset($studentsFrom['from_status'])) ? esc($studentsFrom['from_status']) : '';?>">
                    
                    <div class="row">
                        <div class="form-group col-3 mb-0">
                            <label for="to_class">To Class</label>
                            <?= form_dropdown("to_class", [''=>''] + $allClasses_to, [], ['class'=>'form-control','required'=>'required']); ?>
                            <?=isset($allClasses_pgr_to) ? '<div class="pt-1">' . $allClasses_pgr_to . '</div>' : '';?>
                        </div>
                        <div class="form-group col-3 mb-0">
                            <label for="to_year">To Session/Year</label>
                            <?=form_input([
                                'name'      => 'to_year',
                                'class'     => 'form-control',
                                'required'  => 'required',
                                'type'      => 'text',
                                'placeholder'=> '1985',
                            ]);?>
                            <?=isset($allSessions_pgr) ? '<div class="pt-1">' . $allSessions_pgr . '</div>' : '';?>
                        </div>
                        <div class="form-group col-3 mb-0">
                            <label for="to_status">To Status</label>
                            <?= form_dropdown("to_status", get_student_class_status(), [], ['class'=>'form-control','required'=>'required']); ?>
                        </div>
                        <div class="form-group col-3 mb-0">
                            <div class="form-check mt-4" <?=tt_title('If you admit students in a class and move to different sections then check this box. For example students admitted to class Six and Distribute students in Section A and B of this class.');?>>
                                <input type="checkbox" name="onlySectionChange" value="on" class="form-check-input" id="onlySectionChange">
                                <label class="form-check-label" for="onlySectionChange">Only Section Change</label>
                            </div>
                            <button class="btn btn-primary  mt-2" type="submit"><i class="fa fa-save"></i> Save Changes</button>
                        </div>
                        <div class="col-12">
                            Only status will be updated in the same class if <span class='text-info'>from class and year/session</span> 
                            is same as <span class='text-info'>to class and year/session</span>. 
                            If you want to mark some students and change status then you can simply update from here.
                        </div>
                    </div>
                <?=form_close();?>
            </div>
        </div>
    </div>
    
</div>

<script {csp-script-nonce}>
    document.addEventListener("DOMContentLoaded", function(){        
        /* Check/Uncheck if click is made on header/footer checkboox */
        $('.checkboxAllMarker').click(function(){
            $(".checkboxAllMarker").prop('checked',this.checked);
            $(".cxbxStdMkr").prop('checked',this.checked);
            student_id_update_input_value(true, this.checked, this);
        });
        /* Check/Uncheck if click is made on each row checkboox item */
        $('.cxbxStdMkr').click(function(){
            student_id_update_input_value(false, this.checked, this);
            var alRw = $("input[class='cxbxStdMkr']").length;
            var slRw = $("input[class='cxbxStdMkr']:checked").length;
            if( alRw > 0 && alRw === slRw ){
                $(".checkboxAllMarker").prop('checked', true);
            }else{
                $(".checkboxAllMarker").prop('checked', false);
            }
        });
    });
    /* Use student ID and send it to the input field to send it to the server. */
    function student_id_update_input_value( multi, is_checked, obj ){
        if(multi){
            if(is_checked){
                var std_id_arr = $('.cxbxStdMkr:checked').map(function(){return $(this).data("student_id");}).get();
                $('#valueToSubmit_studentIDs').val(std_id_arr.join(',')); /* Add all ids in the input */
            }else{
                $('#valueToSubmit_studentIDs').val(''); /* All items removed. */
            }
        }else{
            var old_ids = $('#valueToSubmit_studentIDs').val().split(',').map(Number).filter(function(i){return i;});
            var new_id = $(obj).data("student_id");
            if(is_checked){
                old_ids.push(new_id);
                $('#valueToSubmit_studentIDs').val(old_ids.join(',')); /* Add new ID */
            }else{
                var remove_id_idx = old_ids.indexOf(new_id);
                if (remove_id_idx > -1){old_ids.splice(remove_id_idx, 1);}
                $('#valueToSubmit_studentIDs').val(old_ids.join(',')); /* Add one id in the input */
            }
        }
    }
</script>
