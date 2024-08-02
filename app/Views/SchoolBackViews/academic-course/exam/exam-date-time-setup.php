<div class="row">
    <?php
        if(isset($display_msg) AND is_string($display_msg) AND strlen($display_msg) > 0){ echo $display_msg; }
        $FlsMsg = session()->getFlashdata('display_msg'); if(strlen($FlsMsg) > 0) echo $FlsMsg; 
    ?>
    <?php if(isset($errors) AND is_array($errors) AND count($errors) > 0) echo get_display_msg(implode(',',$errors),'danger'); ?>
    
    <?=(is_object($updateExamDT)) ? get_display_msg(myLang('You are updating previous data.','আপনি আগের তথ্যাবলী হালনাগাদ করছেন।') . ' ID: ' . esc(intval($updateExamDT->axdts_id)),'warning text-center') : '';?>
    
    <div class="col-lg-12">
        <div class="ibox ">
            <div class="ibox-title pr-3">
                <h5><?=myLang('Determine examination data time from here','এখান থেকে পরীক্ষার দিনক্ষণ নির্ধারণ করুন');?></h5>
            </div>
            <div class="ibox-content">
                <?=form_open(base_url("admin/academic/exam/date/time"),['method'=>'post']);?>
                <?=(is_object($updateExamDT)) ? form_hidden('updateExamID',$updateExamDT->axdts_id) : '';?>
                
                <div class="row">
                    <div class="col">
                        <div class="form-group mb-0">
                            <label class="col-form-label" for="selectMulClasIds">
                                <?=myLang('Select Class','ক্লাশ নির্বাচন করুন');?> <?=anchor('admin/academic/setup','<i class="far fa-edit"></i>',['class'=>'ml-3']);?>
                            </label>
                            
                            <?php 
                                if( count($allClassItems) < 1) $allClassItems = [];
                                //$allClassItems  = ['' => myLang('Select Class','ক্লাশ নির্বাচন করুন')] + $allClassItems;
                                $class_id_sel   = is_object($updateExamDT) ? @unserialize(strval($updateExamDT->axdts_class_id)) : (service('request')->getPost('exam_class_id') ?? '');
                                // echo form_dropdown('exam_class_id[]', $allClassItems, $class_id_sel, ['class'=>'form-control chosenselect','required'=>'required','multiple'=>'multiple','tabindex'=>'4','data-placeholder'=>myLang('Select Class','ক্লাশ নির্বাচন করুন')]);
                                echo form_multiselect('exam_class_id[]', $allClassItems, (array)$class_id_sel, ['class'=>'form-control chosenselect','required'=>'required','id'=>'selectMulClasIds','data-placeholder'=>myLang('Select Class','ক্লাশ নির্বাচন করুন')]);
                            ?>
                        </div>
                        <?= get_form_error_msg_from_array( isset($errors) ? $errors : [], 'axdts_class_id');?>
                    </div>
                    <div class="col">
                        <div class="form-group mb-0">
                            <label class="col-form-label">Enter Session/Year</label>
                            <input type="text" name="exam_session" required="required" class="form-control" value="<?=set_value('exam_session',is_object($updateExamDT) ? preg_replace( "/\s+/", "",$updateExamDT->axdts_session_year) : '');?>">
                        </div>
                        <?= get_form_error_msg_from_array( isset($errors) ? $errors : [], 'axdts_session_year');?>
                    </div>
                    <div class="col">
                        <div class="form-group mb-0">
                            <label class="col-form-label"><?=myLang('Exam Type','পরীক্ষার ধরণ');?></label>
                            <?= form_dropdown('exam_type', get_available_class_exam_options(), set_value('exam_type',is_object($updateExamDT) ? $updateExamDT->axdts_type : ''), ['class'=>'form-control','required'=>'required']);?>
                        </div>
                        <?= get_form_error_msg_from_array( isset($errors) ? $errors : [], 'axdts_type');?>
                    </div>
                    <div class="col">
                        <div class="form-group mb-0">
                            <label class="col-form-label">Exam start at</label>
                            <input type="text" name="exam_starts" required="required" autocomplete="off" class="form-control datetimepkerad" value="<?=set_value('exam_starts',is_object($updateExamDT) ? $updateExamDT->axdts_exam_starts_at : '');?>">
                        </div>
                        <?= get_form_error_msg_from_array( isset($errors) ? $errors : [], 'axdts_exam_starts_at');?>
                    </div>
                    <div class="col">
                        <div class="form-group mb-0">
                            <label class="col-form-label">Exam ends at</label>
                            <input type="text" name="exam_ends" required="required" autocomplete="off" class="form-control datetimepkerad" value="<?=set_value('exam_ends',is_object($updateExamDT) ? $updateExamDT->axdts_exam_ends_at : '');?>">
                        </div>
                        <?= get_form_error_msg_from_array( isset($errors) ? $errors : [], 'axdts_exam_ends_at');?>
                    </div>
                    <div class="col pt-4 pl-1">
                        <button class="btn btn-primary btn-sm" type="submit" name="save_exam_date_time" value="yes"><i class="fa fa-save"></i> <?=(is_object($updateExamDT)) ? myLang('Update','হালনাগাদ করুন') : myLang('Save','সংরক্ষণ করুন'); ?></button>
                        <?=(!is_object($updateExamDT))?'':anchor('admin/academic/exam/date/time','<i class="fa fa-plus" aria-hidden="true"></i> '.myLang('New','নতুন'),['class'=>'btn btn-secondary btn-sm mt-1 ml-1']);?>
                    </div>
                </div>
                <?=form_close();?>
            </div>
        </div>
    </div>

    
    <div class="col-lg-12 animated fadeInRight">
        <div class="ibox ">
            <div class="ibox-title">
                <h5><?=myLang('Already Determined Date Time','ইতিমধ্যে নির্ধারিত দিন তারিখ সমূহ');?></h5>
                <?=anchor("admin/academic/exam/date/time/viewer",' <i class="fa fa-share" aria-hidden="true"></i> ' . myLang('View','দেখুন'),['class'=>'btn btn-sm btn-primary']);?>
            </div>
            <div class="ibox-content table-responsive mb-0">
                <table class="table table-hover table-bordered bg-white pb-0">
                    <?php 
                    $tr_thead_tfoot = '<tr class="text-center">';
                        $tr_thead_tfoot .= '<th scope="col">ID</th>';
                        $tr_thead_tfoot .= '<th scope="col" class="text-left">'.myLang('Class','শ্রেণি').'</th>';
                        $tr_thead_tfoot .= '<th scope="col">'.myLang('Session','শিক্ষাবর্ষ').'</th>';
                        $tr_thead_tfoot .= '<th scope="col">'.myLang('Type','প্রকার').'</th>';
                        $tr_thead_tfoot .= '<th scope="col">'.myLang('Exam Start','পরীক্ষা আরম্ভ').'</th>';
                        $tr_thead_tfoot .= '<th scope="col">'.myLang('Exam Ends','পরীক্ষা সমাপ্তি').'</th>';
                        $tr_thead_tfoot .= '<th scope="col">Inserted</th>';
                        $tr_thead_tfoot .= '<th scope="col">Updated</th>';
                        $tr_thead_tfoot .= '<th scope="col">Deleted</th>';
                    $tr_thead_tfoot .= '</tr>';
                    ?>
                    <thead class="thead-light"><?php echo $tr_thead_tfoot; ?></thead>
                    <tfoot class="thead-light"><?php echo $tr_thead_tfoot; ?></tfoot>
                    <tbody>
                        <?php if(isset($xmDateTimeList) AND is_array($xmDateTimeList) AND count($xmDateTimeList) > 0): ?>
                            <?php foreach($xmDateTimeList as $dt) : ?>
                                <tr>
                                    <td class="text-center"><?=(esc($dt->axdts_id));?></td>
                                    <td>
                                        
                                        <?php 
                                            $clsx = service('ClassesAndSemestersModel')->withDeleted()->whereIn('classes_and_semesters.fcs_id',(array)@unserialize($dt->axdts_class_id))->get_classes_with_parent_label_for_dropdown(false,false);
                                            if(is_array($clsx) AND count($clsx) > 0 ){
                                                $namsLbl = ( implode(', ', $clsx) );
                                                echo anchor("admin/academic/exam/date/time?updateExamDateTimeID=".esc($dt->axdts_id),'<i class="fa fa-edit" aria-hidden="true"></i> ' . $namsLbl,['class'=>'']);
                                            }else{
                                                echo 'No Class';
                                            }
                                        ?>
                                        
                                            <?= anchor("admin/academic/exam/routine?selected_xam_dt_tm_id=".esc($dt->axdts_id),'<i class="fa fa-edit" aria-hidden="true"></i> ' . lang('Menu.exam_routine'),['class'=>'btn btn-sm btn-info m-1']);?>
                                            <?=anchor(base_url('admin/academic/exam/date/time/viewer'),' <i class="fas fa-eye"></i> ' . myLang('View Routine','রুটিন দেখুন'),['class'=>'btn btn-sm btn-info m-1']);?>
                                        
                                    </td>
                                    <td class="text-center"><?=esc(esc($dt->axdts_session_year));?></td>
                                    <td class="text-center"><?=esc(get_available_class_exam_options(strval($dt->axdts_type),'Invalid Key'));?></td>
                                    <td class="text-center">
                                        <?=esc(esc($dt->axdts_exam_starts_at));?>
                                        <div class="label-info pl-1 pr-1"><?=esc(App\Core\Time::parse(strval($dt->axdts_exam_starts_at), 'Asia/Dhaka','en-US')->humanize());?></div>
                                    </td>
                                    <td class="text-center">
                                        <?=esc(esc($dt->axdts_exam_ends_at));?>
                                        <div class="label-info pl-1 pr-1"><?=esc(App\Core\Time::parse(strval($dt->axdts_exam_ends_at), 'Asia/Dhaka','en-US')->humanize());?></div>
                                    </td>
                                    <td class="text-center">
                                        <?=esc(App\Core\Time::parse(strval($dt->axdts_inserted_at), 'Asia/Dhaka','en-US')->humanize());?>
                                    </td>
                                    <td class="text-center">
                                        <?=esc(App\Core\Time::parse(strval($dt->axdts_updated_at), 'Asia/Dhaka','en-US')->humanize());?>
                                    </td>
                                    <td class="text-center">
                                        <?php 
                                        if($dt->axdts_deleted_at){
                                            echo anchor('admin/academic/exam/date/time?delPermanent_ExamDateTimeID='.esc($dt->axdts_id),'<i class="fa fa-trash" aria-hidden="true"></i> '.myLang('Delete','মুছুন'),['class'=>'btn btn-sm btn-danger']);
                                            echo anchor('admin/academic/exam/date/time?recycle_ExamDateTimeID='.esc($dt->axdts_id),'<i class="fa fa-recycle" aria-hidden="true"></i> '.myLang('Recycle','ফিরান'),['class'=>'btn btn-sm btn-primary']);
                                        }else{
                                            echo anchor('admin/academic/exam/date/time?remTemporarily_ExamDateTimeID='.esc($dt->axdts_id),'<i class="fa fa-trash" aria-hidden="true"></i> '.myLang('Remove','সরান'),['class'=>'btn btn-sm btn-warning']);
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <td colspan="10" class="text-center"><?=myLang('No information found.','কোন তথ্য পাওয়া যায় নি');?></td>
                        <?php endif;?>
                    </tbody>
                </table>
                <?=$xmDateTimeListPgr;?>
            </div>
        </div>
    </div>
</div>






<script src="<?= cdn_url('js/show-spinner-on-submit.js');?>"></script>
<link href="<?=cdn_url('bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css');?>" rel="stylesheet" media="screen">
<script src="<?=cdn_url('bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js');?>" defer="defer" type="text/javascript"></script>
<script {csp-script-nonce}>
    document.addEventListener("DOMContentLoaded", function(){
        $('.datetimepkerad').datetimepicker({format: 'yyyy-mm-dd hh:ii:ss',autoclose: 1,showMeridian: 1,fontAwesome: true});
    });
</script>

<script src="<?= cdn_url('chosen_v1.8.7/chosen.jquery.min.js');?>" defer="defer"></script>
<link href="<?=cdn_url('chosen_v1.8.7/chosen.min.css');?>" rel="stylesheet" media="screen">
<script {csp-script-nonce}>
    document.addEventListener("DOMContentLoaded", function(){
        $(".chosenselect").chosen();
    });
</script>
