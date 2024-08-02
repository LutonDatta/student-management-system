<div class="row">
    <div class="col-lg-12">
        <?php
            if(isset($display_msg) AND is_string($display_msg) AND strlen($display_msg) > 0){ echo $display_msg; }
            $FlsMsg = session()->getFlashdata('display_msg'); if(strlen($FlsMsg) > 0) echo $FlsMsg; 
        ?>
        <div class="ibox ">
            <div class="ibox-content">
 
                        <?php 
                            $tr_thead_tfoot = '<tr class="text-center">';
                                $tr_thead_tfoot .= '<th scope="col" class="text-left">'.myLang('Class','শ্রেণি').'</th>';
                                $tr_thead_tfoot .= '<th scope="col">'.myLang('Start','আরম্ভ').' / '.  myLang('End','সমাপ্তি').'</th>';
                            $tr_thead_tfoot .= '</tr>';
                        ?>

                        <article class="mt-3 table-responsive">
                            <table class="table table-sm table-bordered">
                                <tbody>
                                    <?php if( isset($xmDateTimeList) AND is_array( $xmDateTimeList ) AND count( $xmDateTimeList ) > 0 ) : ?>
                                        <?php foreach( $xmDateTimeList as $dt ) : ?>
                                            <tr>
                                                <td colspan="2" class="p-0">
                                                    <?php 
                                                        $courseIds  = @unserialize($dt->axdts_exam_routine);
                                                        $clsx       = service('ClassesAndSemestersModel')
                                                                        ->withDeleted()
                                                                        ->whereIn('classes_and_semesters.fcs_id',@unserialize($dt->axdts_class_id))
                                                                        ->get_classes_with_parent_label_for_dropdown();

                                                        if(is_array($clsx) AND count($clsx) > 0 ){
                                                            $courseIDsForFastQuery = []; /* Get course IDs in a single place so the we can retrieve them from sql server in a single query */
                                                            foreach($clsx as $classID => $classTitle ){
                                                                if(isset($courseIds["class_{$classID}"]) AND is_array($courseIds["class_{$classID}"])){
                                                                    foreach($courseIds["class_{$classID}"] as $coID => $exDate ){
                                                                        $courseIDsForFastQuery[intval(ltrim($coID,'co_'))] = 'Dummy title, will be updated later from sql query';
                                                                    }
                                                                }
                                                            }
                                                            // Retrieve all course data using a single query (we might have duplicate course id in this array
                                                            if(count($courseIDsForFastQuery) > 0 ){
                                                                $crs = service('CoursesModel')->select('co_id,co_title,co_code')->withDeleted()->find(array_keys($courseIDsForFastQuery));
                                                                if(is_array($crs) AND count($crs) > 0 ){
                                                                    foreach($crs as $crsObj ){
                                                                        $courseIDsForFastQuery[$crsObj->co_id] = $crsObj->co_title . " [{$crsObj->co_id}]" . ( strlen($crsObj->co_code) > 0 ? " ({$crsObj->co_code})" : '');
                                                                    }
                                                                }
                                                            }

                                                                foreach($clsx as $classID => $classTitle ): ?>
                                                                    <?php 
                                                                    // $classTitle is already escaped
                                                                    echo "<div class='text-left jumbotron p-2 mb-0'>";
                                                                            $urlViewx = "admin/academic/exam/results/viewer?view_result_of_session=". urlencode($dt->axdts_session_year)."&view_result_of_class_id=".intval($classID)."&view_result_for_dttm_id=" . intval($dt->axdts_id);
                                                                            echo "<div class='float-right'>".anchor($urlViewx,myLang('View Result','ফলাফল দেখুন'),['class'=>'btn btn-info btn-sm']) ."</div>";
                                                                            echo "<strong class='text-center'>".myLang('Class','শ্রেণি').': '.$classTitle."</strong>";
                                                                    echo "</div>";
                                                                    ?>
                                                                    <div class="m-2">
                                                                        <?=myLang('Session','শিক্ষাবর্ষ');?>:
                                                                        <?=esc(esc($dt->axdts_session_year));?>
                                                                        <br>

                                                                        <?=myLang('Type','ধরণ');?>:
                                                                        <?=esc(get_available_class_exam_options(strval($dt->axdts_type),'Invalid Key'));?>
                                                                        <br>

                                                                        <?=myLang('Start','আরম্ভ');?>:
                                                                        <?=esc(esc($dt->axdts_exam_starts_at));?>
                                                                        <?php $colCss = (strtotime(strval($dt->axdts_exam_starts_at)) > time()) ? 'btn-info' : 'btn-secondary'; ?>
                                                                        <span class="<?=$colCss;?> pl-1 pr-1"><?=esc(App\Core\Time::parse(strval($dt->axdts_exam_starts_at), 'Asia/Dhaka','en-US')->humanize());?></span>
                                                                        <br>
                                                                        <?=myLang('End','সমাপ্তি');?>:
                                                                        <?=esc(esc($dt->axdts_exam_ends_at));?>
                                                                        <?php $colCss = (strtotime(strval($dt->axdts_exam_ends_at)) > time()) ? 'btn-info' : 'btn-secondary'; ?>
                                                                        <span class="<?=$colCss;?> pl-1 pr-1"><?=App\Core\Time::parse(strval($dt->axdts_exam_ends_at), 'Asia/Dhaka','en-US')->humanize();?></span>
                                                                    </div>

                                                                    <?php 
                                                                    if(isset($courseIds["class_{$classID}"]) AND is_array($courseIds["class_{$classID}"])){
                                                                        echo '<table class="table table-hover mt-2 mb-4">';
                                                                            echo '<tbody>';
                                                                                foreach($courseIds["class_{$classID}"] as $coID => $exDate ){
                                                                                    $crsTlt = isset($courseIDsForFastQuery[intval(ltrim($coID,'co_'))]) ? $courseIDsForFastQuery[intval(ltrim($coID,'co_'))] : 'No course title found';
                                                                                            if(strlen($exDate) > 5){
                                                                                                $ix = App\Core\Time::parse(strval($exDate), 'Asia/Dhaka','en-US');
                                                                                                $isExpi = (strtotime(strval($exDate)) < time()) ? '<span class="btn-secondary"> ('. myLang('Ended','সমাপ্ত') .') </span>' : '<span class="btn-info"> ('. myLang('Comming','আসছে').') </span>';
                                                                                                $exDateP = $ix->humanize() . ' / ' . date('M d, Y h:i a',$ix->getTimestamp());
                                                                                            }
                                                                                    echo '<tr><td>'.esc($crsTlt).'</td><td>'.esc(esc($exDateP)). ' ' . $isExpi . '</td></tr>';
                                                                                }
                                                                            echo '<tbody>';
                                                                        echo '</table>';
                                                                    }
                                                                    ?>

                                                                <?php endforeach; 
                                                        }else{
                                                            echo 'No class';
                                                        }
                                                    ?>

                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <tr>
                                            <td scope="row" colspan="10"  class="text-center"><?=myLang('No information found.','কোন তথ্য পাওয়া যায় নি');?></td>
                                        </tr>
                                    <?php endif;?>
                                </tbody>
                            </table>
                            <div class="col-12">
                                    <?= isset($xmDateTimeListPgr) ? $xmDateTimeListPgr : '';?>
                            </div>
                        </article>

                        <div class="text-center mt-4">
                        <?php 
                                echo anchor("admin/academic/exam/date/time", myLang('Update exam date time','পরীক্ষার দিনক্ষণ পরিবর্তন করুন'), ['class' =>'btn btn-info m-1']);
                        ?>
                        </div>
                
            </div>
        </div>
    </div>
</div>
