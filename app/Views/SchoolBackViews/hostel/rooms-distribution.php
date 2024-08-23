<?php
    if(isset($display_msg) AND is_string($display_msg) AND strlen($display_msg) > 0){ echo $display_msg; }
    $FlsMsg = session()->getFlashdata('display_msg'); if(strlen($FlsMsg) > 0) echo $FlsMsg; 
?>

<div class="row">
    <div class="col-lg-6">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-title p-2 border">
                        <?=form_open('admin/hostel/bed/distribution',['method'=>'get']);?>
                            <div class="form-row align-items-center">
                                <div class="col-auto"><label for="student_id">Student ID:</label></div>
                                <div class="col-auto"><?=form_input('student_id',intval(service('request')->getGet('student_id')),['class'=>'form-control', 'id'=>'student_id']);?></div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                                </div>
                            </div>
                        <?=form_close();?>
                    </div>
                </div>
            </div>
            
            <?php if(isset($selectedStudent) AND is_object($selectedStudent)) : ?>
                <div class="col-lg-12">
                    <div class="ibox">
                        <div class="ibox-title p-0 border">
                            <table class="table table-hover table-bordered bg-white m-0">
                                <thead class="thead-light">
                                    <tr class="text-center"><th scope="col">Selected Student</th></tr>
                                </thead>
                                <tbody>
                                    <tr class="text-center">
                                        <td>
                                            <?php
                                            echo '<strong>'.service('AuthLibrary')->getUserFullName_fromObj($selectedStudent,'No name').'</strong>';
                                            echo '<br>Student ID (SID): ' . $selectedStudent->student_u_id;
                                            echo '<br>Father Name: ' . $selectedStudent->student_u_father_name;
                                            echo '<br>Mother Name: ' . $selectedStudent->student_u_mother_name;
                                            ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif;?>
            
            <div class="col-lg-12">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered bg-white">
                        <?php 
                        $tr_thead_tfoot = '<tr class="text-center">';
                            $tr_thead_tfoot .= '<th scope="col">Roll</th>';
                            $tr_thead_tfoot .= '<th scope="col">Name</th>';
                            $tr_thead_tfoot .= '<th scope="col">Class</th>';
                            $tr_thead_tfoot .= '<th scope="col">Session/Status</th>';
                            $tr_thead_tfoot .= '<th scope="col">SID</th>';
                        $tr_thead_tfoot .= '</tr>';
                        ?>
                        <thead class="thead-light"><?php echo $tr_thead_tfoot; ?></thead>
                        <tfoot class="thead-light"><?php echo $tr_thead_tfoot; ?></tfoot>
                        <tbody>
                            <?php if(isset($students_list) AND is_array($students_list) AND count($students_list) > 0 ): ?>
                                <?php foreach($students_list as $stdDta): ?>
                                    <tr class="text-center">
                                        <td><?=esc($stdDta->scm_c_roll);?></td>
                                        <td>
                                            <?php
                                                $fnm = implode(' & ', array_filter([trim($stdDta->student_u_father_name),trim($stdDta->student_u_mother_name)]));
                                                $stdname = service('AuthLibrary')->getUserFullName_fromObj($stdDta,'No name');
                                                $sondot = ($stdDta->student_u_gender == 'male') ? 'son of' : ( ($stdDta->student_u_gender == 'female') ? 'daughter of' : '');
                                                if(strlen($fnm) > 0){ $stdname .= " ($sondot ".$fnm.')';}
                                                echo anchor("admin/hostel/bed/distribution?student_id={$stdDta->student_u_id}",esc($stdname),['class'=>'']);
                                            ?>
                                            <?php 
                                                if($stdDta->scm_deleted_at){
                                                    echo '<span class="btn label label-warning">Deleted ' . time_elapsed_string($stdDta->scm_deleted_at) . '</span>';
                                                }
                                            ?>
                                            
                                        </td>
                                        <td><?=service('ClassesAndSemestersModel')->get_single_class_with_parent_label($stdDta->scm_class_id)->title ." [".intval($stdDta->scm_class_id)."]";?></td>
                                        <td>
                                            <?=esc($stdDta->scm_session_year);?> /
                                            <?=isset(get_student_class_status()[$stdDta->scm_status]) ? get_student_class_status()[$stdDta->scm_status]: '';?>
                                        </td>
                                        <td><?=esc($stdDta->student_u_id);?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr class="text-center"><td colspan="20">No student found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <?= isset($studentsLstPgr) ? $studentsLstPgr : '';?>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    
</div>
