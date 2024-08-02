<div class="row">
    <?php
        if(isset($display_msg) AND is_string($display_msg) AND strlen($display_msg) > 0){ echo $display_msg; }
        $FlsMsg = session()->getFlashdata('display_msg'); if(strlen($FlsMsg) > 0) echo $FlsMsg; 
    ?>
    
    <div class="col-lg-12">
        <div class="ibox-content">
            <?=form_open(base_url('admin/pg/cash/in/hand/collection'),['method'=>'POST']);?>
                <div class="row">
                    <div class="col-sm-4">
                        Select student from here. Enter student user ID:
                    </div>
                    <div class="col-sm-4">
                        <?= form_input("mobile_email_uidx", set_value("mobile_email_uidx"), ['class'=>'form-control','autocomplete'=>'off','placeholder'=>'Student ID']); ?>
                    </div>
                    <div class="col-sm-4">
                        <button class="btn btn-primary btn-sm <?=isMobile() ? 'mt-1 mb-1' : '';?>" type="submit"><i class="fa fa-save"></i> <?=myLang('Submit','উপস্থাপন করুন');?></button>
                        <?=anchor(base_url('admin/admission/student/list'),'<i class="fas fa-user-graduate"></i> '.myLang('Student list',' শিক্ষার্থীদের তালিকা'),['class'=>'btn btn-info btn-sm']);?>
                        <?=anchor(base_url('admin/pg/cash/in/hand/collection'),'<i class="fas fa-sync-alt"></i>',['class'=>'btn btn-info btn-sm btnShowSpinOnClk']);?>
                    </div>
                </div>
            <?=form_close();?>
        </div>
    </div>
    
    
    <div class="col-lg-12 mt-4">
            <div class="mail-box-header">
                <?=form_open(base_url('admin/pg/cash/in/hand/collection'), ['method'=>'get']);?> 
                    <div class="input-group">
                            <?=form_input([
                                    'name'          => 'timeHcRangeStart',
                                    'class'         => 'form-control form-control-sm datetimepkerad',
                                    'value'         => (isset($fsv) AND is_array($fsv) AND isset($fsv['trs'])) ? $fsv['trs'] : '',
                                    'placeholder'   => 'From Time',
                                    'autocomplete'  => 'off',
                                    'title'         => 'This field work with update time.',
                            ]);?>
                            <?=form_input([
                                    'name'          => 'timeHcRangeEnd',
                                    'class'         => 'form-control form-control-sm datetimepkerad',
                                    'value'         => (isset($fsv) AND is_array($fsv) AND isset($fsv['tre'])) ? $fsv['tre'] : '',
                                    'placeholder'   => 'To Time',
                                    'autocomplete'  => 'off',
                                    'title'         => 'This field work with update time.',
                            ]);?>
                        
                        <?php if(isMobile()): ?>
                            </div>
                            <div class="input-group">
                        <?php endif; ?>
                        
                            <input type="number" class="form-control form-control-sm" name="student_id" value="<?=(isset($fsv) AND is_array($fsv) AND isset($fsv['sid']) AND $fsv['sid'] > 0) ? esc($fsv['sid']) : '';?>" placeholder="Student ID">
                            
                    
                        <?php if(isMobile()): ?>
                            </div>
                            <div class="input-group">
                        <?php endif; ?>
                        
                            
                            <?=form_dropdown('rows_number', ['5'=>'5','10'=>'10','20'=>'20','30'=>'30','40'=>'40'], (isset($fsv) AND is_array($fsv) AND isset($fsv['ism'])) ? esc($fsv['ism']) : '10', ['class'=>'custom-select form-control-sm pt-1']);?>
                            <?=form_dropdown('is_paid', ['all'=>'Paid/Unpaid','1'=>'Paid','0'=>'Unpaid'], (isset($fsv) AND is_array($fsv) AND isset($fsv['isp'])) ? esc($fsv['isp']) : '', ['class'=>'custom-select form-control-sm pt-1']);?>

                    
                        <?php if(isMobile()): ?>
                            </div>
                            <div class="input-group">
                        <?php endif; ?>
                        
                        
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                            <a href="<?=base_url('admin/pg/cash/in/hand/collection');?>" class="btn btn-sm btn-secondary">Clear</a>
                        </div>
                    </div>
                <?=form_close();?>
            </div> 
        
        
        
            <div class="ibox-content table-responsive p-1">
                <?php 
                    $colsHcTab = [
                        // Column Name          Title Tooltip
                        [ myLang('HC ID','HC ID'), myLang('Invoice ID','ইনভয়েস ID') ],
                        [ myLang('Created','তৈরি'), myLang('Invoice Created in this date','ইনভয়েসটি এই তারিখে তৈরি করা হয়েছে') ],
                        [ myLang('Updated','হালনাগাদ'), myLang('Invoice last updated in this date','ইনভয়েসটি এই তারিখে সের্বশেষ হালনাগাদ করা হয়েছে') ],
                        [ myLang('Amount','পরিমাণ'), myLang('Amount','পরিমাণ') ],
                        [ myLang('Is Paid','পরিশোধিত কি?'), myLang('Is Paid','পরিশোধিত কি?') ],
                        [ myLang('Details','বিস্তারিত'), myLang('See Details','বিস্তারিত দেখুন') ],
                        [ myLang('Comment','মন্তব্য'), myLang('Comment','মন্তব্য') ],
                    ]; 
                ?>
                <table class="table table-bordered table-hover mb-0">
                    <thead>
                        <tr class="text-center">
                            <?php foreach( $colsHcTab as $tabTdHc ){ echo "<th data-toggle='tooltip' scope='col' title='{$tabTdHc[1]}'>{$tabTdHc[0]}</th>"; } ?>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr class="text-center">
                            <?php foreach( $colsHcTab as $tabTdHc ){ echo "<th data-toggle='tooltip' scope='col' title='{$tabTdHc[1]}'>{$tabTdHc[0]}</th>"; } ?>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php if(is_array($hc_history) AND count($hc_history) > 0 ): ?>
                            <?php $stat_sum_paid = 0;$stat_sum_unpaid = 0; ?>
                            <?php foreach($hc_history as $bl): ?>
                                <?php 
                                    if($bl->hc_is_paid){
                                        $stat_sum_paid += floatval($bl->hc_amt_total);
                                    }else{
                                        $stat_sum_unpaid += floatval($bl->hc_amt_total);
                                    }
                                ?>
                                <tr>
                                    <td class="text-center" scope="row"><?=esc($bl->hc_id);?></td>
                                    <td class="text-center" scope="row"><?= time_elapsed_string($bl->hc_inserted_at);?></td>
                                    <td class="text-center" scope="row"><?= time_elapsed_string($bl->hc_updated_at);?></td>
                                    <td class="text-center" scope="row"><?= esc(number_format($bl->hc_amt_total,2));?></td>
                                    <td class="text-center" scope="row">
                                        <?php if($bl->hc_is_paid): ?>
                                            <?='<i class="fa fa-check text-navy"></i> PAID';?>
                                        <?php elseif($bl->hc_deleted_at): ?>
                                            <?=form_open(base_url('admin/pg/cash/in/hand/collection'));?>
                                                <button type="submit" name="delete_permanently_trash_unpaid_hc_inv_submit" value="<?=intval($bl->hc_id);?>" class="btn text-danger p-0 m-0" data-toggle="tooltip" title="<?=myLang('Delete permanenetly','স্থায়ী ভাবে মুছে ফেলুন');?>"><i class="fas fa-trash"></i></button>
                                                <?="<span class='text-muted'><i class='fa fa-times text-danger'></i> UNPAID</span>";?>
                                                <?="<span class='label-warning'>".myLang('Deleted ' . time_elapsed_string($bl->hc_deleted_at), time_elapsed_string($bl->hc_deleted_at). ' মুছা হয়েছে')."</span>";?>
                                                <button type="submit" name="un_trash_unpaid_hc_inv_submit" value="<?=intval($bl->hc_id);?>" class="btn text-info p-0 m-0" data-toggle="tooltip" title="<?=myLang('Get Back Trash Invoice','ট্রাশ থেকে ফিরিয়ে নিন');?>"><i class="fas fa-trash"></i></button>
                                            <?=form_close();?>
                                        <?php else: ?>
                                            <?=form_open(base_url('admin/pg/cash/in/hand/collection'));?>
                                                <?="<span class='text-muted'><i class='fa fa-times text-danger'></i> UNPAID</span>";?>
                                                <button type="submit" name="trash_unpaid_hc_inv_submit" value="<?=intval($bl->hc_id);?>" class="btn text-warning p-0 m-0" data-toggle="tooltip" title="<?=myLang('Delete','মুছুন');?>"><i class="fas fa-trash"></i></button>
                                            <?=form_close();?>
                                        <?php endif;?>
                                    </td>
                                    <td class="text-center p-0" scope="row"><?=anchor(base_url('admin/pg/cash/in/hand/collection/mark/as/paid?hc_invoice_id=' . esc($bl->hc_id)),lang('Sa.view_details'),['class'=>'btn btn-info btn-block btnShowSpinOnClk']);?></td>
                                    <td class="text-center" scope="row"><?=esc($bl->hc_salary_months_txt);?></td>
                                </tr>
                            <?php endforeach;?>
                                <tr class="strong">
                                    <td class="text-center" scope="row" colspan="9">
                                        <?= myLang('Total ','মোট ') . 'PAID: ' . esc(number_format($stat_sum_paid,2));?>
                                        <?= myLang('Total ','মোট ') . 'UNPAID: ' . esc(number_format($stat_sum_unpaid,2));?>
                                    </td>
                                </tr>
                                
                                
                        <?php else: ?>
                            <tr><td colspan="25" class="text-center"><?=myLang('No hand cash collection payment invoice found.','হাতে নগদ সংগ্রহের কোন ইনভয়েস পাওয়া যায় নি');?></td></tr>
                        <?php endif;?>
                    </tbody>
                </table>
                <div>
                    
                </div>
            </div>
            <div class="mt-1">
                <?=isset($hc_history_pg) ? $hc_history_pg : '';?>
            </div>
    </div>
</div>



<script {csp-script-nonce}>
    document.addEventListener("DOMContentLoaded", function(){
        $(".btnShowSpinOnClk").click(function(){
            var btn = $(this);
            var new_html = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ' + btn.text();
            btn.html(new_html);
        });
    });
</script>


<link href="<?=cdn_url('bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css');?>" rel="stylesheet" media="screen">
<script src="<?=cdn_url('bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js');?>" defer="defer" type="text/javascript"></script>
<script {csp-script-nonce}>
    document.addEventListener("DOMContentLoaded", function(){
        $('.datetimepkerad').datetimepicker({format: 'yyyy-mm-dd hh:ii:ss',autoclose: 1,showMeridian: 1,fontAwesome: true});
    });
</script>
