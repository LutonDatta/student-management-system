<div class="row">
    <div class="col-lg-12">
        <div class="ibox ">
                <div class="ibox-content">
                    <?php if(isset($examDtTmLst) AND is_array($examDtTmLst) AND count($examDtTmLst) > 0 ): ?>
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
                                    echo form_dropdown('xam_dt_tm_id', $options,intval(service('request')->getGet('view_result_for_dttm_id')), ['class'=>'form-control','id'=>'redOnChgExDtTm','required'=>'required']);
                                ?>
                            </div>
                            <?php if(isset($examDtTmLstPgr) AND $examDtTmLstPgr->getLastPage() > 1): ?>
                                <div class="col-12"><?=$examDtTmLstPgr->links();?></div>
                            <?php endif;?>
                        </div>

                    <?php else:?>
                    <div class="text-center">
                            <?=anchor('admin/academic/exam/date/time',myLang('Without exam date time you can not show exam results. Click here to setup exam date time.','পরীক্ষার দিনক্ষণ নির্ধারণ করা হয় নি। এখানে ক্লিক করে পরীক্ষার দিনক্ষণ নির্ধারণ করুন।'),['class'=>'btn btn-info']);?>
                        <br>
                            <?=myLang('No exam date time found. There is no examination recorded. Please contact to your teacher.','পরীক্ষার দিনক্ষণ নির্ধারণ করা হয়নি। কোন পরীক্ষার তধ্য নির্ধারিত নয়। অনুগ্রহ করে আপনার শিক্ষকের সাথে যোগাযোগ করুন।');?>
                    </div>
                    <?php endif;?>
                
                    <?php if(isset($available_classes) AND is_array($available_classes) AND count($available_classes) > 0 ): ?>
                        <div class="row">
                            <div class="col-12 pt-1">
                            <?php
                                $options = [ '0' => myLang('Select class from here','এখান থেকে ক্লাশ নির্বাচন করুন') ];
                                $options = $options + (array)$available_classes;
                                echo form_dropdown('view_result_of_class_id', $options ,intval(service('request')->getGet('view_result_of_class_id')), ['class'=>'form-control','id'=>'view_result_of_class_id','required'=>'required']);
                            ?>
                            </div>
                        </div>
                    <?php endif;?>
                </div>
            
        
            <div class="ibox-content">
                <?=form_open(base_url("admin/academic/exam/results/viewer"),['method'=>'get']);?>
                    <div class="input-group text-center">
                        <div class="input-group-prepend">
                            <input type="text" class="form-control form-control-sm" name="view_result_of_class_id" value="<?=esc($exFilter['cid']);?>" placeholder="Class ID">
                            <input type="text" class="form-control form-control-sm" name="view_result_of_session" value="<?=esc($exFilter['sess']);?>" placeholder="Session/Year">                        
                        <?= isMobile() ? '</div><div class="input-group-prepend">' : ''; ?>
                            <input type="text" class="form-control form-control-sm" name="view_result_of_class_roll" value="<?=esc($exFilter['roll']);?>" placeholder="Roll">
                            <input type="submit" name="submit" value="Filter" class="btn btn-info d-print-none ml-1 mr-1">                        
                            <a class="btn btn-info d-print-none" href="<?=base_url('admin/academic/exam/results/viewer');?>">Clear</a>               
                        </div>  
                        <a class="btn btn-info d-print-none <?=isMobile()?'m-2':' ml-1';?>" href="<?=base_url('admin/academic/exam/results/view/own');?>"><?=myLang('View Your Academic Transcript','আপনার প্রাতিষ্ঠানিক নম্বরপত্র দেখুন');?></a>
                    </div>
                <?=form_close();?>
                <div class="text-center">
                    <span class="d-none d-print-inline">Printed at: <?=date('jS M Y h:i:s a O');?></span>
                </div>
            </div>
        </div>
    </div>
</div>



<div class="row">
    <div class="col-lg-12">
        <?php
            if(isset($display_msg) AND is_string($display_msg) AND strlen($display_msg) > 0){ echo $display_msg; }
            $FlsMsg = session()->getFlashdata('display_msg'); if(strlen($FlsMsg) > 0) echo $FlsMsg; 
        ?>
        
        <div class="ibox ">
            
            <div class="ibox-content p-1">
                <div class="<?=isMobile() ? 'table-responsive' : 'table-responsive-not'; ?>">
                    <table class="table table-striped">
                        <?php $colNms = ['Student Name','Roll','Class','Session','Update']; ?>
                        <thead class="thead-light"><tr><th scope="col"><?=implode('</th><th scope="col">',$colNms);?></th></tr></thead>
                        <tfoot class="thead-light"><tr><th scope="col"><?=implode('</th><th scope="col">',$colNms);?></th></tr></tfoot>
                        <tbody>
                            <?php if(count($dtListWithFilter) > 0): ?>
                                <?php foreach($dtListWithFilter as $rRow):?>
                                    <tr role="button" data-toggle="collapse" aria-expanded="false" data-target="#reco_<?=$rRow->exr_id;?>" aria-controls="#reco_<?=$rRow->exr_id;?>">
                                        <td><?=isset($teacherStudentNameList[$rRow->scm_u_id]) ? esc($teacherStudentNameList[$rRow->scm_u_id]) : 'No name';?></td>
                                        <td><?=(intval($rRow->scm_c_roll) > 0) ? intval($rRow->scm_c_roll) : '-';?></td>
                                        <td>
                                            <?=isset($classNameList[$rRow->scm_class_id]) ? esc($classNameList[$rRow->scm_class_id]) . " [$rRow->scm_class_id]" : 'No class';?>
                                        </td>
                                        <td><?=$rRow->scm_session_year;?></td>
                                        <td>
                                            <?= \App\Core\Time::parse($rRow->exr_updated_at, 'Asia/Dhaka', 'en_US')->humanize();?>
                                            <?=myLang(' by ', ' হালনাগাদ করেছেন ');?>
                                        </td>
                                    </tr>
                                    <tr class="collapse" id="reco_<?=$rRow->exr_id;?>">
                                        <td colspan="30">
                                            <?php
                                                $tmpCourseList = [];
                                                $tmpCourseListOutOf = [];
                                                for($start=1;$start<=20;$start++){ 
                                                    if(property_exists($rRow,"exr_co_{$start}_id") AND intval($rRow->{"exr_co_{$start}_id"}) > 0 ){
                                                        $tmpCourseList[ intval($rRow->{"exr_co_{$start}_id"}) ] = floatval($rRow->{"exr_co_{$start}_re"});
                                                        $tmpCourseListOutOf[ intval($rRow->{"exr_co_{$start}_id"}) ] = floatval($rRow->{"exr_co_{$start}_ou"});
                                                    }
                                                }
                                                foreach($tmpCourseList as $coID => $coRe ){
                                                    echo "<button type='button' class='btn btn-default btn-sm m-1'> " 
                                                            . ((isset($courseNameList[$coID])) ? esc($courseNameList[$coID]) : esc($coID)) 
                                                            . " > " 
                                                            . esc($coRe) 
                                                            . ((isset($tmpCourseListOutOf[$coID])) ? "/" . esc($tmpCourseListOutOf[$coID]) : '' )
                                                       . "</button>";
                                                }
                                                echo "<button type='button' class='btn btn-default btn-sm m-1 mt-0'> " . myLang('Total', 'মোট') . " : " . count($tmpCourseList) . " > " . esc(array_sum($tmpCourseList)) . "</button>";
                                            ?>      
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="30"><?=myLang('No result found.','কোন পরীক্ষার ফলাফল পাওয়া যায়নি।');?></td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <div class="text-center"><?=$dtListWithFilterPgr;?></div>
                    
                </div>
            </div>
        </div>
    </div>

    
</div>


<script {csp-script-nonce}>
    document.addEventListener("DOMContentLoaded", function(){        
        $( "#redOnChgExDtTm" ).change(function(){
            window.location.href = "<?=base_url("admin/academic/exam/results/viewer?view_result_for_dttm_id=");?>" + $(this).val();
        });
        $( "#view_result_of_class_id" ).change(function(){
            window.location.href = "<?=base_url("admin/academic/exam/results/viewer?view_result_for_dttm_id=".intval(service('request')->getGet('view_result_for_dttm_id'))."&view_result_of_class_id=");?>" + $(this).val();
        });
    });
</script>
