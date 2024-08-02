<div class="row">
    <div class="col-lg-12">
        <?php
            if(isset($display_msg) AND is_string($display_msg) AND strlen($display_msg) > 0){ echo $display_msg; }
            $FlsMsg = session()->getFlashdata('display_msg'); if(strlen($FlsMsg) > 0) echo $FlsMsg; 
            if(isset($errors) AND is_array($errors) AND count($errors) > 0) echo get_display_msg(implode(',',$errors),'danger');
        ?>
    </div>
</div>

<?php if(isset($examDtTmLst)): ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="row">
                        <div class="col">
                        <?php
                            echo form_open(base_url("admin/academic/exam/routine"),['method'=>'get']);
                                $options = [ '0' => myLang('Select exam date time from here','এখান থেকে পরীক্ষার দিন তারিখ নির্বাচন করুন') ];
                                if(is_array($examDtTmLst) AND count($examDtTmLst) > 0 ){
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
                                }
                                echo form_dropdown('xam_dt_tm_id', $options,[], ['class'=>'form-control','id'=>'redOnChgExDtTm','required'=>'required']);
                            echo form_close();
                        ?>
                        </div>
                        <?php if(isset($examDtTmLstPgr) AND $examDtTmLstPgr->getLastPage() > 1): ?>
                            <div class="col"><?=$examDtTmLstPgr->links();?></div>
                        <?php endif;?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif;?>

<?php if(is_object($Selted_xmDtTm)): ?>
    <div class="row  border-bottom white-bg dashboard-header mb-3">
        <div class="col-12">
            <h3>Exam routine setup</h3>
        </div>
        <div class="col">
            
            <ul class="list-group clear-list m-t">
                <li class="list-group-item fist-item">
                    <span class="float-right"><?=$Selted_xmDtTm->axdts_id;?></span>
                    <span class="label label-info"><?=esc(1);?></span> Selected Exam Date Time ID:
                </li>
                <li class="list-group-item">
                    <span class="float-right"><?=str_replace('],',']<br>',($Selted_xmDtTm->clsNames));?></span>
                    <span class="label label-success"><?=esc(2);?></span> Exam for Classes:
                </li>
                <li class="list-group-item">
                    <span class="float-right"><?=get_available_class_exam_options(strval($Selted_xmDtTm->axdts_type),'Invalid Key');?></span>
                    <span class="label label-primary"><?=esc(3);?></span> <?=myLang('Type','প্রকার') . ': ';?>
                </li>
            </ul>
            
        </div>
        <div class="col">
            <ul class="list-group clear-list m-t">
                <li class="list-group-item fist-item">
                    <span class="float-right"><?=esc(esc($Selted_xmDtTm->axdts_session_year));?></span>
                    <span class="label label-default"><?=esc(4);?></span> <?=myLang('Session','শিক্ষাবর্ষ').':';?>
                </li>
                <li class="list-group-item">
                    <span class="float-right">
                        <?=App\Core\Time::parse(strval($Selted_xmDtTm->axdts_exam_starts_at), 'Asia/Dhaka','en-US')->humanize();?>
                        (<?=esc(esc($Selted_xmDtTm->axdts_exam_starts_at));?>)
                    </span>
                    <span class="label label-primary"><?=esc(5);?></span> <?=myLang('Exam Start','পরীক্ষা আরম্ভ').':';?>
                </li>
                <li class="list-group-item">
                    <span class="float-right">
                        <?=App\Core\Time::parse(strval($Selted_xmDtTm->axdts_exam_ends_at), 'Asia/Dhaka','en-US')->humanize();?>
                        (<?=esc(esc($Selted_xmDtTm->axdts_exam_ends_at));?>)
                    </span>
                    <span class="label label-primary"><?=esc(6);?></span> <?=myLang('Exam Ends','পরীক্ষা সমাপ্তি').':';?>
                </li>
            </ul>
        </div>
        <div class="col-12">
            <div class="text-center">
                <?=anchor('admin/academic/exam/routine',' <i class="fas fa-undo"></i> ' . myLang('Change','পরিবর্তন করুন'),['class'=>'btn btn-sm btn-info']);?>
                <?=anchor(base_url('admin/academic/exam/date/time/viewer'),' <i class="fas fa-eye"></i> ' . myLang('View Routine','রুটিন দেখুন'),['class'=>'btn btn-sm btn-info']);?>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if(is_object($Selted_xmDtTm)): ?>
<div class="row">
    <div class="col-lg-12">
        <div class="ibox ">
            <div class="ibox-content">
                <?=form_open(base_url("admin/academic/exam/routine"),['method'=>'post']);?>
                    <?php
                        $ClsWiseCourses  = $Selted_xmDtTm->classWiseCourses;
                        if(is_array($ClsWiseCourses)){
                            echo '<div class="row">';
                                foreach($ClsWiseCourses as $clsList): ?>
                                    <div class="col">
                                        <div class="table-responsive col">
                                            <?= "<strong class='text-center bg-light btn-block p-3'>".esc($clsList['class_name'])."</strong>";?>
                                            <table class="table table-hover mb-0">
                                                <?php $hRow =   '<tr>
                                                                    <th>Exam Course Name</th>
                                                                    <th>Exam Date Time</th>
                                                                </tr>';?>
                                                <thead><?=$hRow;?></thead>
                                                <tbody>
                                                    <?php
                                                    foreach($clsList['courses'] as $crs){
                                                        $cCode      = (strlen($crs->co_code) > 0) ? '(' . $crs->co_code . ')' : '';
                                                        $cId        = (strlen($crs->co_id) > 0) ? '[' . $crs->co_id . ']' : '';
                                                        $cExcerpt   = (strlen($crs->co_excerpt) > 0) ? '{' . $crs->co_excerpt . '}' : '';
                                                        $lbl        = esc( " $crs->co_title $cId $cCode $cExcerpt " ) . ( $crs->co_deleted_at ? '<span="text-danger"> Deleted </span>' : '' );
                                                        ?>
                                                        <tr>
                                                            <td><?= $lbl; ?></td>
                                                            <td>
                                                                <?php 
                                                                    $routine    = @unserialize($Selted_xmDtTm->axdts_exam_routine);
                                                                    $classList  = isset($routine['class_' . intval($clsList['class_id'])]) ?  $routine['class_' . intval($clsList['class_id']) ] : [];
                                                                    $previousVal= (is_array($classList) AND isset($classList['co_'.intval($crs->co_id)])) ? $classList['co_'.intval($crs->co_id)] : '';
                                                                    $inputName  = 'examDateTime['.intval($clsList['class_id']).']['.intval($crs->co_id).']';
                                                                ?>
                                                                <!-- Exam DateTime -> Class ID -> Courses -->
                                                                <input name="<?=$inputName;?>" value="<?=set_value($inputName,$previousVal);?>" type="text" class="form-control datetimepkerad" autocomplete="off">
                                                                <div class="bg-light text-center">
                                                                <?php 
                                                                    if($previousVal){
                                                                        $ix = App\Core\Time::parse(strval($previousVal), 'Asia/Dhaka','en-US');
                                                                        echo $ix->humanize() . ' / ';
                                                                        echo date('F d, Y h:i a',$ix->getTimestamp());
                                                                    }
                                                                ?>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    } 
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                <?php endforeach;
                            echo '</div>';
                        }
                    ?>
                    <div class="col-12 text-center border-top">
                        <input name="dateTimeSetupID" value="<?=intval($Selted_xmDtTm->axdts_id);?>" type="hidden">
                        <button class="btn btn-primary mt-3" name="svExDtTmRoutine" value="yes" type="submit"><i class="fa fa-save"></i> <?=lang('Admin.save_info');?></button>
                    </div>
                <?=form_close();?>
            </div>
        </div>
    </div>
</div>
<?php endif;?>        




<script src="<?= cdn_url('js/show-spinner-on-submit.js');?>"></script>
<link href="<?=cdn_url('bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css');?>" rel="stylesheet" media="screen">
<script src="<?=cdn_url('bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js');?>" defer="defer" type="text/javascript"></script>
<script {csp-script-nonce}>
    document.addEventListener("DOMContentLoaded", function(){
        $('.datetimepkerad').datetimepicker({format: 'yyyy-mm-dd hh:ii:ss',autoclose: 1,showMeridian: 1,fontAwesome: true});
        
        $( "#redOnChgExDtTm" ).change(function(){
            window.location.href = "<?=base_url("admin/academic/exam/routine?selected_xam_dt_tm_id=");?>" + $(this).val();
        });
    });
</script>
