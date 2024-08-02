<div class="row">
    <div class="col-lg-8">
        
        <?php
            if(isset($display_msg) AND is_string($display_msg) AND strlen($display_msg) > 0){ echo $display_msg; }
            $FlsMsg = session()->getFlashdata('display_msg'); if(strlen($FlsMsg) > 0) echo $FlsMsg; 
        ?>
        
        <div class="ibox ">
            <div class="ibox-title">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="col-form-label" for="da_att_selected_class">Selected Class:</label>
                            <select class="form-control form-control-lg" id="da_att_selected_class" disabled>
                                <option value="<?=is_object($selected_cls_obj)? esc($selected_cls_obj->fcs_id) :'';?>">
                                    <?=is_object($selected_cls_obj)? esc($selected_cls_obj->title) .' ['.esc($selected_cls_obj->fcs_id).']':'No class selected';?>
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="col-form-label" for="att_sess_yrs_for_atdnce">Session/Year</label>
                            <select name="att_sess_yrs_for_atdnce" id="att_sess_yrs_for_atdnce" class="form-control">
                                <?php 
                                if(isset($attached_session_years_for_attendance) AND is_array($attached_session_years_for_attendance) AND count($attached_session_years_for_attendance) > 0 ){
                                    echo "<option value=''>Select session/year</option>";
                                    foreach($attached_session_years_for_attendance as $sessYr ){
                                        echo "<option ".
                                                (service('request')->getGet('att_sess_yrs_for_atdnce') == $sessYr ? 'selected' : '')
                                                ." value='".esc($sessYr)."'>".esc($sessYr)."</option>";
                                    }
                                }else{
                                    echo "<option value=''>No session/year found. It seemes no student admitted to this class.</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="col-form-label" for="da_att_course_subject">Course/Subject</label>
                            <select name="da_att_course_subject" id="da_att_course_subject" class="form-control">
                                <?php 
                                if(isset($attached_courses_to_this_cls) AND is_array($attached_courses_to_this_cls) AND count($attached_courses_to_this_cls) > 0 ){
                                    echo "<option value=''>Select course/subject</option>";
                                    foreach($attached_courses_to_this_cls as $obj ){
                                        $optional= boolval($obj->ccm_is_compulsory) ? '' : ' (Optional)';
                                        $co_code = strlen($obj->co_code) > 0 ? ' - ' . $obj->co_code : '';
                                        $op_name = $obj->co_title . ' [' .$obj->co_id .']' . $co_code . $optional;
                                        echo "<option value='{$obj->co_id}'>".esc($op_name)."</option>";
                                    }
                                }else{
                                    echo "<option value=''>No course/subject found</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                </div>
                <div class="ibox-tools"><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></div>
            </div>
            <div class="ibox-content">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered">
                        <?php $hRow = '<tr>
                                <th>Thumb</th>
                                <th>Name</th>
                                <th>Class Roll</th>
                                <th>Action</th>
                                <th>Present?</th>
                                <th>UID</th>
                            </tr>';?>
                        <thead><?=$hRow;?></thead>
                        <tfoot><?=$hRow;?></tfoot>
                        <tbody id="stDPresentAbsentRows"></tbody>
                    </table>
                    
                    
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="ibox ">
            <div class="ibox-content text-center p-md">
                <div class="h6 m-b-xxs"><?=today_is();?></div>             
                <hr>
                <p>
                    You can change attendance for students any time today but not tomorrow. 
                    You can not take attendance of multiple date in a single day.
                </p>
            </div>
        </div>
    
        <div class="ibox ">
            <div class="ibox-content text-center p-md">
                <div class="h6 m-b-xxs">Academic Classes Distribution</div>
                <div class="h6 m-b-xxs m-1"><span class="label label-primary">Online Admission Safety Active</span></div>
                <p>
                    Available academic faculties, classes and semesters are here. Select class from one of them to take attendance. 
                </p>
                <div class="m-t-md">
                    <div id="jstree2" class="text-left"></div>
                </div>                
            </div>
        </div>
    </div>
</div>

<!-- The following try will load class from which teachers will select his own class to take attendance -->
<link href="<?=cdn_url('jstree/dist/themes/default/style.min.css');?>"  rel="stylesheet">
<script src="<?=cdn_url('jstree/dist/jstree.min.js');?>" defer="defer"></script>
<script {csp-script-nonce}>
    document.addEventListener("DOMContentLoaded", function(){
        $('#jstree2').jstree({
            'core' : {
                'data' : {
                    'url' : function (node) {
                        return node.id === '#' ? '<?=base_url('api/v1/classes?parent_id=0');?>' : '<?=base_url('api/v1/classes?parent_id=');?>' + node.id;
                    },
                    'data' : function (node) {
                        return { 'id' : node.text };
                    }
                }
            }
        });
        $('#jstree2').jstree({"plugins":["sort"]});
        $('#jstree2').on("changed.jstree",function(e,data){ /* redirect to edit page when user click to label */
            location.href = '<?=base_url('daily/attendance/book?take_attendance_of_class_id=');?>' + data.node.id;
        });
        /* Some on change event needed. When teacher change session, we need to load correct courses based on session. */
        $('#att_sess_yrs_for_atdnce').on('change', function(){
            var sltdClass = $('#da_att_selected_class').val();
            var sltdSession = $(this).val();
            $(this).attr('disabled','disabled');
            document.location.href = '<?=base_url('daily/attendance/book?take_attendance_of_class_id=');?>'+sltdClass+'&att_sess_yrs_for_atdnce=' + sltdSession;
        });
    });
</script>
    
<script {csp-script-nonce}>
    /* Set some variable to use from daily-attendance-process.js */
    var api_ajax_setup_csrf_header  = '<?=csrf_header();?>';
    var api_ajax_setup_csrf_hash    = '<?=csrf_hash();?>';
    var api_attendance_url_load_students = '<?=base_url('api/v1/daily/class/attendance/show/students');?>';
    var api_attendance_url_change_status = '<?=base_url('api/v1/daily/class/attendance/change/status');?>';
</script>
<script src="<?=cdn_url('js/daily-attendance-process.js');?>" defer="defer"></script>