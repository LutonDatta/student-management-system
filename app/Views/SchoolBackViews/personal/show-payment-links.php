<div class="row">
    <?php
            if(isset($display_msg) AND is_string($display_msg) AND strlen($display_msg) > 0){ echo $display_msg; }
            $FlsMsg = session()->getFlashdata('display_msg'); if(strlen($FlsMsg) > 0) echo $FlsMsg; 
        ?>
</div>


<?php   
    // Load admission application NAV bar in few application process prat page from a single file.
    echo view('SchoolBackViews/personal/admission-process-nav-bar');
?>


<article class="row">
    <?php if( isset($selectedClass) AND is_object($selectedClass)) : ?>
        <div class="col-lg-12 animated fadeInRight">
            <div class="widget-text-box text-center">
                আপনি জানেন কি? সপ্তাহের ছুটির দিন সহ যেকোন সময় বেতন ও পরীক্ষার ফি  অনলাইনে মোবাইল ব্যাংকিং এর মাধ্যমে পরিশোধ করতে পারবেন ঘরে বসে থেকেই।
                
            </div>
        </div>
    <?php else: ?>
        <div class="col text-center pt-2">
            <?= anchor('admission',myLang('Valid class not found. Select Class.','ভর্তিযোগ্য ক্লাশ পাওয়া যায়নি। সঠিক ক্লাশ নির্বাচন করুন। '),['class'=>'btn btn-warning']);?>
        </div>
    <?php endif; ?>
</article>


<article class="row mt-3 table-responsive">
    <div class="col-12 animated">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr class="text-center">
                        <th data-toggle="tooltip" title="Invoice ID" scope="col">Invoice ID</th>
                        <th data-toggle="tooltip" title="User ID" scope="col">SID</th>
                        <th data-toggle="tooltip" title="Payment Target Identity" scope="col">Class ID</th>
                        <th data-toggle="tooltip" title="Invoice Created in this date" scope="col">Created</th>
                        <th data-toggle="tooltip" title="Invoice Created in this date" scope="col">Updated</th>
                        <th data-toggle="tooltip" scope="col">Amount</th>
                        <th data-toggle="tooltip" title="Pay or edit invoice. " scope="col">Status</th>
                        <th data-toggle="tooltip" scope="col">Transaction ID</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(is_array($mainFee) AND count($mainFee) > 0 ): ?>
                        <?php foreach($mainFee as $bl): ?>
                            <tr>
                                <td title="<?=esc($bl->amf_id);?>" class="text-center p-0" scope="row"><?=esc($bl->amf_id);?></td>
                                <td title="<?=esc($bl->amf_paid_by_n_for);?>" class="text-center p-0" scope="row"><?=esc($bl->amf_paid_by_n_for);?></td>
                                <td title="<?=esc($bl->amf_class_id);?>" class="text-center p-0" scope="row"><?=esc($bl->amf_class_id);?></td>
                                <td title="<?=esc($bl->amf_inserted_at);?>" class="text-center p-0" scope="row"><?= time_elapsed_string($bl->amf_inserted_at);?> [<?=esc(esc($bl->amf_inserted_at));?>]</td>
                                <td title="<?=esc($bl->amf_inserted_at);?>" class="text-center p-0" scope="row"><?= time_elapsed_string($bl->amf_updated_at);?> [<?=esc(esc($bl->amf_updated_at));?>]</td>
                                <td title="<?=number_format($bl->amf_amt_total,2);?>" class="text-center p-0" scope="row"><?= esc(number_format($bl->amf_amt_total,2));?></td>
                                <td class="text-center <?=($bl->amf_is_paid) ? '' : 'p-0';?>" scope="row"><?= ($bl->amf_is_paid) ? 'PAID' : 'UNPAID'; ?></td>
                                <td class="text-center p-0" scope="row"><?=esc($bl->amf_txn_num);?></td>
                            </tr>
                        <?php endforeach;?>
                    <?php else: ?>
                            <tr><td colspan="8" class="text-center"><?=myLang('No Admission Main Fee invoice found.','ভর্তি ফি প্রদানের কোন ইনভয়েস পাওয়া যায়নি।');?></td></tr>
                    <?php endif;?>
                
                    <?php if(is_array($applFee) AND count($applFee) > 0 ): ?>
                        <?php foreach($applFee as $bl): ?>
                            <tr>
                                <td title="<?=esc($bl->aaf_id);?>" class="text-center p-0" scope="row"><?=esc($bl->aaf_id);?></td>
                                <td title="<?=esc($bl->aaf_paid_by_n_for);?>" class="text-center p-0" scope="row"><?=esc($bl->aaf_paid_by_n_for);?></td>
                                <td title="<?=esc($bl->aaf_class_id);?>" class="text-center p-0" scope="row"><?=esc($bl->aaf_class_id);?></td>
                                <td title="<?=esc($bl->aaf_inserted_at);?>" class="text-center p-0" scope="row"><?= time_elapsed_string($bl->aaf_inserted_at);?> [<?=esc(esc($bl->aaf_inserted_at));?>]</td>
                                <td title="<?=esc($bl->aaf_inserted_at);?>" class="text-center p-0" scope="row"><?= time_elapsed_string($bl->aaf_updated_at);?> [<?=esc(esc($bl->aaf_updated_at));?>]</td>
                                <td title="<?=number_format($bl->aaf_amt_total,2);?>" class="text-center p-0" scope="row"><?= esc(number_format($bl->aaf_amt_total,2));?></td>
                                <td class="text-center <?=($bl->aaf_is_paid) ? '' : 'p-0';?>" scope="row"><?= ($bl->aaf_is_paid) ? 'PAID' : 'UNPAID'; ?></td>
                                <td class="text-center p-0" scope="row"><?=esc($bl->aaf_txn_num);?></td>
                            </tr>
                        <?php endforeach;?>
                    <?php else: ?>
                            <tr><td colspan="8" class="text-center"><?=myLang('No Admission Application Fee invoice found.','ভর্তির আবেদন ফি প্রদানের কোন ইনভয়েস পাওয়া যায়নি।');?></td></tr>
                    <?php endif;?>
                </tbody>
            </table>
        </div>
    </div>
</article>




