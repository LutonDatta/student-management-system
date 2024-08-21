<?php
            if(isset($display_msg) AND is_string($display_msg) AND strlen($display_msg) > 0){ echo $display_msg; }
            $FlsMsg = session()->getFlashdata('display_msg'); if(strlen($FlsMsg) > 0) echo $FlsMsg; 
        ?>

<div class="row">
    <div class="col-lg-12 d-print-none">
        <div class="ibox">
            <div class="ibox-title pr-2">
                <?=form_open('admin/admission/student/list',['method'=>'get']);?>
                    <article class="row">
                        <div class="col-sm-12 col-md-6 col-lg-3">
                                <div class="input-group">
                                    <div class="input-group-prepend"><label class="input-group-text" for="srcClass">Class</label></div>
                                    <?=form_dropdown('class',[''=>'']+$clsList,[$srcData['class_id']],['class'=>'custom-select','id'=>'srcClass']);?>
                                </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-3">
                                <div class="input-group">
                                    <div class="input-group-prepend"><label class="input-group-text" for="srcSession">Session</label></div>
                                    <?php $aln = []; foreach($allSessions as $v){ $aln[$v]=$v;} if(count($aln) < 1){ $aln[date('Y')] = date('Y');} ?>                    
                                    <?= form_dropdown('year',[''=>'']+$aln,[$srcData['session_year']],['class'=>'custom-select','id'=>'srcSession']);?>
                                </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-3">
                                <div class="input-group">
                                    <div class="input-group-prepend"><label class="input-group-text" for="srcStatus">Status</label></div>
                                    <?=form_dropdown('status',get_student_class_status(true),[$srcData['status']?$srcData['status']:''],['class'=>'custom-select','id'=>'srcStatus']);?>
                                </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-3">
                            <div class="input-group">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
                                <button type="button" id="printBoxBtnx" class="btn btn-info ml-1"><i class="fas fa-print"></i> Print</button>
                                <?=anchor('admin/admission/student/list', '<i class="fas fa-shapes"></i> '. myLang('Clear','সকল'),['class'=>'btn btn-sm btn-info m-1']);?>
                            </div>
                        </div>
                    </article>
                <?=form_close();?>
            </div>
        </div>
    </div>
    
    
    <div class="col-lg-12 animated fadeInRight">
        <?php echo $selectionNotice; ?>
        <div class="table-responsive">
            <table class="table table-hover table-bordered bg-white">
                <?php 
                $tr_thead_tfoot = '<tr class="text-center">';
                    $tr_thead_tfoot .= '<th scope="col" class="d-print-none" ' . tt_title('Select All Rows', 'right') . '><input type="checkbox" class="checkboxAllMarker"></th>';
                    $tr_thead_tfoot .= '<th scope="col">Photo</th>';
                    $tr_thead_tfoot .= '<th scope="col" ' . tt_title('Class Roll', 'right') . '>Roll</th>';
                    $tr_thead_tfoot .= '<th scope="col" ' . tt_title('Student Name', 'right') . '>Name</th>';
                    $tr_thead_tfoot .= '<th scope="col" ' . tt_title('Name of Father', 'right') . '>Father</th>';
                    $tr_thead_tfoot .= '<th scope="col" ' . tt_title('Name of Mother', 'right') . '>Mother</th>';
                    
                    $tr_thead_tfoot .= '<th scope="col">Class ID</th>';
                    $tr_thead_tfoot .= '<th scope="col">Session</th>';
                    $tr_thead_tfoot .= '<th scope="col">Status</th>';
                    $tr_thead_tfoot .= '<th scope="col">Mobile/Email</th>';
                    $tr_thead_tfoot .= '<th scope="col" class="d-print-none">Do</th>';
                    
                    $tr_thead_tfoot .= '<th scope="col" ' . tt_title('Student ID', 'right') . '>SID</th>';
                    $tr_thead_tfoot .= '<th scope="col" ' . tt_title('Courses Classes Students Mapping ID', 'right') . '>SCM ID</th>';
                    
                $tr_thead_tfoot .= '</tr>';
                ?>
                <thead class="thead-light"><?php echo $tr_thead_tfoot; ?></thead>
                <tfoot class="thead-light"><?php echo $tr_thead_tfoot; ?></tfoot>
                <tbody>
                    <?php if(isset($students_list) AND is_array($students_list) AND count($students_list) > 0 ): ?>
                        
                        <?php foreach($students_list as $stdDta): ?>
                            <tr class="text-center">
                                <td class="d-print-none">
                                    <input type="checkbox" class="cxbxStdMkr" data-student_id="<?=$stdDta->student_u_id;?>">
                                </td>
                                <td class="p-0">
                                    <?php $thumb = cdn_url('default-images/profile-pic-boy.png'); ?>
                                    <a href="<?=$thumb;?>" target="_blank" data-toggle="modal" data-target="#showThumbModal" data-turl="<?=$thumb;?>">
                                        <img width="40" src="<?=$thumb;?>" alt="Image" class="m-0 p-0">
                                    </a>
                                </td>
                                <td title="Click to change roll">
                                    <a data-toggle="modal" data-target="#showRollModal" data-mroll="<?=$stdDta->scm_c_roll;?>" id="mscmid_<?=$stdDta->scm_id;?>" data-mscmid="<?=$stdDta->scm_id;?>">
                                        <?=$stdDta->scm_c_roll;?>
                                    </a>
                                </td>
                                <td>
                                    <?=esc(service('AuthLibrary')->getUserFullName_fromObj($stdDta,'No name'));?>
                                    <?php 
                                        if($stdDta->scm_deleted_at){
                                            echo '<span class="btn label label-warning">Deleted ' . time_elapsed_string($stdDta->scm_deleted_at) . '</span>';
                                        }
                                    ?>
                                </td>
                                <td><?=esc($stdDta->student_u_father_name);?></td>
                                <td><?=esc($stdDta->student_u_mother_name);?></td>
                                <td><?=esc($stdDta->scm_class_id);?></td>
                                <td><?=esc($stdDta->scm_session_year);?></td>
                                <td><?=isset(get_student_class_status()[$stdDta->scm_status]) ? get_student_class_status()[$stdDta->scm_status]: '';?></td>
                                <td><?=esc(implode(' / ',array_filter([strval($stdDta->student_u_mobile_own),strval($stdDta->student_u_email_own)])));?></td>
                                <td class="d-print-none">
                                    <a class="btn btn-secondary text-white" data-toggle="modal" data-target="#showActionsModal" 
                                        data-actroll="<?=intval($stdDta->scm_c_roll);?>" 
                                        data-actscmid="<?=intval($stdDta->scm_id);?>"
                                        data-actsid="<?=intval($stdDta->student_u_id);?>"
                                        data-actclsid="<?=intval($stdDta->scm_class_id);?>"
                                        data-actsessyr="<?=intval($stdDta->scm_session_year);?>"
                                        data-actname="<?=esc(service('AuthLibrary')->getUserFullName_fromObj($stdDta,'No name'));?>"
                                    >Actions</a>
                                </td>
                                <td><?=$stdDta->student_u_id;?></td>
                                <td>
                                    <?=$stdDta->scm_id;?>
                                    <?php 
                                        $rF = '';
                                        foreach(['class','year','status'] as $fi){ $v = service('request')->getGet($fi); if(strlen($v) >0) $rF .= "&{$fi}={$v}"; }
                                        
                                        if($stdDta->scm_deleted_at){
                                            echo anchor("admin/admission/student/list?erase_student_scm_id_from_trash_permanently={$stdDta->scm_id}{$rF}", '<i class="fas fa-trash"></i>',['class'=>'btn btn-sm btn-danger d-print-none','title'=>'Delete permanently.','data-toggle'=>'tooltip']);
                                            echo anchor("admin/admission/student/list?get_back_student_scm_id_from_trash={$stdDta->scm_id}{$rF}", '<i class="fas fa-trash"></i>',['class'=>'btn btn-sm btn-info d-print-none','title'=>'Recycle from trash','data-toggle'=>'tooltip']);
                                        }else{
                                            echo anchor("admin/admission/student/list?move_student_scm_id_to_trash={$stdDta->scm_id}{$rF}", '<i class="fas fa-trash"></i>',['class'=>'btn btn-sm btn-warning d-print-none','title'=>'Move to trash','data-toggle'=>'tooltip']);
                                        }
                                    ?>
                                </td>
                                
                            </tr>
                        <?php endforeach; ?>
                            <!-- This model show a littl large image of the student when user click on the user small thumbnail in the row -->
                            <div class="modal fade" id="showThumbModal" tabindex="-1" aria-labelledby="showThumbModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Thumbnail</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                          </div>
                                        <div class="modal-body text-center"><img src="<?=$thumb;?>" alt="Image" class="modal_tUrl_img m-0 p-0 mx-auto img-thumbnail img-fluid"></div>
                                        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button></div>
                                    </div>
                                </div>
                            </div>
                            <script {csp-script-nonce}>
                                document.addEventListener("DOMContentLoaded", function(){
                                    $('#showThumbModal').on('show.bs.modal', function(event){
                                        var button = $(event.relatedTarget); // Button that triggered the modal
                                        var imgUrl = button.data('turl'); // Extract info from data-* attributes
                                        $(this).find('.modal_tUrl_img').attr("src",imgUrl);
                                    });
                                });
                            </script>
                    <?php else: ?>
                        <tr class="text-center">
                            <td colspan="20">No student found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <?= isset($studentsLstPgr) ? $studentsLstPgr : '';?>
        </div>
        <div class="clearfix"></div>
    </div>
       
</div>


<script {csp-script-nonce}>document.addEventListener('DOMContentLoaded',function(){document.getElementById('printBoxBtnx').addEventListener('click',function(){window.print();});});</script>

<div class="modal fade" id="showActionsModal" tabindex="-1" aria-labelledby="showActionsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title" id="showActionsModalLabel">Student Actions</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
            <div id="modelDivAddLinksStudents" class="modal-body text-center">
                <!--Keep empty, will add button later-->
            </div>
            <div class="modal-header border-top text-center"><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button></div>
        </div>
    </div>
</div>

<!-- This model will allow admin to update roll of a student -->
<div class="modal fade" id="showRollModal" tabindex="-1" aria-labelledby="showRollModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title" id="exampleModalLabel">Update Roll</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
            <div class="modal-body text-center">
                <div class="show_error_msg_roll" id="show_error_msg_roll"></div>
                <?=form_input('newRoll','',['class'=>"form-control text-center mb-2",'id'=>'newRollToSave', 'autocomplete'=>"off"],'text');?>
                <button type="button" id="newRollToSaveSubmit" class="btn btn-primary" ><i class="fa fa-save"></i> Save</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script {csp-script-nonce}>
    document.addEventListener("DOMContentLoaded", function(){
        $('#showActionsModal').on('hide.bs.modal', function(event){
            $('#modelDivAddLinksStudents').html(''); /* Hide when hidden, otherwise, buttons will be duplicated for other students. */
        });
        
        $('#showActionsModal').on('show.bs.modal', function(event){
            var actRT       = $(event.relatedTarget); 
            var actRoll     = actRT.data('actroll');    
            var actSID      = actRT.data('actsid');     
            var actName     = actRT.data('actname');    
            var actScmId    = actRT.data('actscmid');   
            var actClsID    = actRT.data('actclsid');   
            var actSessYr   = actRT.data('actsessyr');  
            
            $.each([ 
                {
                    url: '<?=base_url('print/admission/test/admit/card?apply_to_class_id=actClsID&admit_card_of=actSID&sess_year=actSessYr');?>',
                    fa: 'fas fa-print', txt: 'Admit Card', class: 'm-1 btn btn-success'
                },{
                    url: '<?=base_url('student/info/print/view?apply_to_class_id=actClsID&user_id=actSID&sess_year=actSessYr');?>',
                    fa: 'fas fa-print', txt: 'Student Information', class: 'm-1 btn btn-info'
                },{
                    url: '<?=base_url('print/student/admission/confirmation/form?scm_id=actScmId&user_id=actSID');?>',
                    fa: 'fas fa-print', txt: 'Confirmation Form', class: 'm-1 btn btn-success'
                },{
                    url: '<?=base_url('admin/pg/cash/in/hand/collection/create/inv?student_scm_id=actScmId&student_uid=actSID');?>',
                    fa: 'fas fa-solid fa-wallet', txt: 'Hand Cash', class: 'm-1 btn btn-secondary'
                },{
                    url: '<?=base_url('admin/academic/exam/results/publish?result_pub_std_scm_id=actScmId&result_pub_std_uid=actSID&result_pub_std_sess=actSessYr');?>',
                    fa: 'fas fa-solid fa-id-badge', txt: 'Result Publish', class: 'm-1 btn btn-info'
                },{
                    url: '<?=base_url('admin/academic/exam/results/view/own?student_id=actSID');?>',
                    fa: 'fas fa-solid fa-id-badge', txt: 'Mark Sheet - Multi', class: 'm-1 btn btn-success'
                }
            ], function( index, val ) {
                $('#showActionsModalLabel').html('Student Actions - ' + actName );
                var url = (val.url).replace('actClsID',actClsID).replace('actSID',actSID).replace('actSessYr',actSessYr).replace('actScmId',actScmId);
                $('#modelDivAddLinksStudents').append($('<a href="'+url+'" class="'+val.class+'"><i class="'+val.fa+'"></i> '+val.txt+'</a>'));
            });
        });
        
        $.ajaxSetup({headers: { '<?=csrf_header();?>': '<?=csrf_hash();?>' }});
        $('#showRollModal').on('show.bs.modal', function(event){
            var bClkd = $(event.relatedTarget); // Button that triggered the modal
            var cRoll = bClkd.data('mroll'); // Extract info from data-* attributes
            var scmId = bClkd.data('mscmid'); // Extract info from data-* attributes
            
            $(this).find('#newRollToSave').val(cRoll);
            
            $(this).find('#newRollToSaveSubmit').click(function(e){
                jQuery('#newRollToSaveSubmit').unbind();
                $('#showRollModal').modal('hide');
                
                $('#mscmid_' + scmId).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
                var newRoll = $('#newRollToSave').val();
                jQuery.ajax({
                    url: '<?=base_url('api/v1/update/class/roll');?>',
                    method: 'POST', dataType: 'json', /* Expecting from server */
                    contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                    xhrFields: { withCredentials: true },
                    data: { 'new_roll':newRoll,'old_roll':cRoll,'scm_id':scmId }
                }).done(function(dta, textStatus, jqXHR){
                    if(dta.ok){
                        $('#mscmid_' + dta.scm_id).html(dta.new_roll); /* New roll updated.*/
                        $('#mscmid_' + dta.scm_id).data('mroll',dta.new_roll); /* New roll updated.*/
                    }else{
                        $('#mscmid_' + dta.scm_id).html(dta.old_roll); /* Failed to update new roll. show old roll.*/
                        if(dta.hasOwnProperty('error')){
                            $($('#mscmid_' + dta.scm_id)).closest('td').append(dta.error);
                        }
                    }
                }).always(function(jqXHR, textStatus){
                    if(jqXHR.status === 0 ){var ajaxEqErr = 'Failed to connect to the server. May be internet connection error.';}
                    if(jqXHR.status === 500 ){var ajaxEqErr = 'Internal server error. Something error happened in the server.';}
                    if(jqXHR.status === 401 ){var ajaxEqErr = 'Unauthorized! Please login with right permission. Please check if your session time expired, please login again.';}
                    if(textStatus==='parsererror'){var ajaxEqErr = 'Server returned wrong data. It might be 404 error.';}

                    if(typeof(jqXHR.responseJSON) !== 'undefined'){
                        if( typeof(jqXHR.responseJSON.message) !== 'undefined' && jqXHR.responseJSON.message.length > 0){
                            if( typeof(ajaxEqErr) !== 'undefined' && ajaxEqErr.length > 0){
                                var ajaxEqErr = ajaxEqErr + ' ' + jqXHR.responseJSON.message;
                            }else{
                                var ajaxEqErr = jqXHR.responseJSON.message;
                            }
                        }
                    }
                    if( typeof(ajaxEqErr) !== 'undefined' && ajaxEqErr.length > 0){
                        $('#showRollModal').modal('show');/* Open modal .*/
                        $('#show_error_msg_roll').addClass('text-danger').html(ajaxEqErr); /* Show error message. */
                    }
                });  
            });
        });
    });
</script>