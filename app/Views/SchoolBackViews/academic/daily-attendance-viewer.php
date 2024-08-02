<div class="row">
    <div class="col-lg-12">
        <?php
            if(isset($display_msg) AND is_string($display_msg) AND strlen($display_msg) > 0){ echo $display_msg; }
            $FlsMsg = session()->getFlashdata('display_msg'); if(strlen($FlsMsg) > 0) echo $FlsMsg; 
        ?>
        
        <div class="ibox ">
            <div class="ibox-content">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <input type="text" id="atViFilter_cla_id" class="form-control form-control-sm" value="" placeholder="Class ID" <?=tt_title('Class ID');?>>
                        <input type="text" id="atViFilter_coSuID" class="form-control form-control-sm" value="" placeholder="Course/Subject ID" <?=tt_title('Course/Subject ID');?>>
                    <?= isMobile() ? '</div><div class="input-group-prepend">' : ''; ?>
                        <input type="text" id="atViFilter_sessYr" class="form-control form-control-sm" value="" placeholder="Session/Year" <?=tt_title('Session/Year');?>>
                        <input type="text" id="atViFilter_stu_id" class="form-control form-control-sm" value="" placeholder="Student ID" <?=tt_title('Student ID');?>>
                    <?= isMobile() ? '</div><div class="input-group-prepend">' : ''; ?>
                        <input type="text" id="atViFilter_c_roll" class="form-control form-control-sm" value="" placeholder="Roll" <?=tt_title('Class Roll');?>>
                    <?= isMobile() ? '</div><div class="input-group-prepend">' : ''; ?>
                        <input type="text" id="atViFilter_v_date" class="form-control form-control-sm date" data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-autoclose="true" placeholder="Date" <?=tt_title('Date');?>>
                        <input type="text" id="atViFilter_v_mnth" class="form-control form-control-sm date" data-provide="datepicker" data-date-format="mm"  data-date-autoclose="true" data-date-min-view-mode="1" placeholder="Month" <?=tt_title('Month');?>>
                    <?= isMobile() ? '</div><div class="input-group-prepend">' : ''; ?>
                        <input type="text" id="atViFilter_v_year" class="form-control form-control-sm date" data-provide="datepicker" data-date-format="yyyy" data-date-autoclose="true" data-date-min-view-mode="2" placeholder="Year" <?=tt_title('Year');?>>
                        <a href="<?=base_url('daily/attendance/book/view');?>" class="btn btn-info d-print-none">Clear</a>
                    </div>                    
                </div>
                <div class="text-center">
                    <span class="d-none d-print-inline">Printed at: <?=date('jS M Y h:i:s a O');?></span>
                </div>
            </div>
            <div class="ibox-content">
                <div class="<?=isMobile() ? 'table-responsive' : 'table-responsive-not'; ?>">
                    <table class="display" id="mainAttendanceViewerTable">
                        <?php $hRow =   '<tr>
                                            <th>UID</th>
                                            <th>Thumb</th>
                                            <th>Name</th>
                                            <th title="Class Roll">Roll</th>
                                            <th title="Class Name">Class</th>
                                            <th title="Course Name">Course</th>
                                            <th>Status <i class="fa fa-question-circle text-navy"></i>
                                            </th>
                                            <th>Date</th>
                                            <th class="d-print-none">Action</th>
                                        </tr>';?>
                        <thead><?=$hRow;?></thead>
                        <tfoot><?=$hRow;?></tfoot>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-12 d-print-none">
        <div class="row">
            <div class="col-lg-4">
                <div class="ibox ">
                    <div class="ibox-content text-center p-md">
                        <div class="h6 m-b-xxs">Academic Classes Distribution</div>
                        <p>Select class from one of them to view attendance.</p>
                        <div class="m-t-md">
                            <div id="jstree2" class="text-left"></div>
                        </div>             
                    </div>
                </div>
            </div>
        
            <div class="col-lg-4">
                <div class="ibox ">
                    <div class="ibox-content text-center p-md">
                        <div class="h6 m-b-xxs">Available Courses</div>
                        <p>Filter your result selecting class</p>
                        <div id="jstreeCourses" class="text-left"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="ibox ">
                    <div class="ibox-content text-center p-md">
                        <div class="h6 m-b-xxs">Sessions/Years</div>
                        <p>Filter result based on Session/Year</p>
                        <div id="jstreeSessionYr" class="text-left"></div>              
                    </div>
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
                    'data':function (node){return {'id':node.text};}
                }
            }
        });
        $('#jstree2').jstree({"plugins":["sort"]});
        $('#jstree2').on("changed.jstree",function(e,data){$('#atViFilter_cla_id').val(data.node.id).change();});
        
        $('#jstreeCourses').jstree({
            'core' : {
                'data' : {
                    'url' : function (node) {
                        return '<?=base_url('api/v1/courses');?>';
                    },
                    'data':function (node){return {'id':node.text};}
                }
            }
        });
        $('#jstreeCourses').jstree({"plugins":["sort"]});
        $('#jstreeCourses').on("changed.jstree",function(e,data){$('#atViFilter_coSuID').val(data.node.id).change();});
        
        
        $('#jstreeSessionYr').jstree({
            'core' : {
                'data' : {
                    'url' : function (node) {
                        return '<?=base_url('api/v1/viewable/sessions/yrs');?>';
                    },
                    'data':function (node){return {'id':node.text};}
                }
            }
        });
        $('#jstreeSessionYr').jstree({"plugins":["sort"]});
        $('#jstreeSessionYr').on("changed.jstree",function(e,data){$('#atViFilter_sessYr').val(data.node.id).change();});
        
    });
</script>
    
<script {csp-script-nonce}>
    /* Set some variable to use from daily-attendance-viewer.js */
    var api_ajax_setup_csrf_header  = '<?=csrf_header();?>';
    var api_ajax_setup_csrf_hash    = '<?=csrf_hash();?>';
    var api_attendance_viwer_url    = '<?=base_url('api/v1/daily/class/attendance/show/history');?>';
</script>
<script src="<?=cdn_url('js/daily-attendance-viewer.js');?>" defer="defer"></script>

<script src="<?=cdn_url('bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js');?>" defer='defer'></script>
<link rel="stylesheet" href="<?=cdn_url('bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css');?>"/>

<link href="<?=cdn_url('data-tables/data-tables-1.11.3/css/jquery.dataTables.min.css');?>" rel="stylesheet" type="text/css">
<script src="<?=cdn_url('data-tables/data-tables-1.11.3/js/jquery.dataTables.min.js');?>" defer="defer" charset="utf8" type="text/javascript"></script>

<style {csp-style-nonce}>
    /* Data table loading/processing text design. */
    .dataTables_wrapper .dataTables_processing {
        background:lightgray;
        border: 1px solid gray;
        padding: 10px;
        font-size: 110%;
    }
</style>