
<?php
    if(isset($display_msg) AND is_string($display_msg) AND strlen($display_msg) > 0){ echo $display_msg; }
    $FlsMsg = session()->getFlashdata('display_msg'); if(strlen($FlsMsg) > 0) echo $FlsMsg; 
?>
<?php if(! empty($errors) AND count($errors) > 0) : ?>
    <div class="alert-danger text-center" role="alert">
        <ul class="list-unstyled"><?php foreach ($errors as $error) : ?><li><?= esc($error) ?></li><?php endforeach ?></ul>
    </div>
<?php endif; ?>

<?php $getSchool = getSchool(); ?>

<div class="row mb-4">
    <div class="col">
        <?=form_open(base_url("admin/pg/cash/in/hand/collection/create/inv?student_uid=".intval(service('request')->getGet('student_uid'))."&student_scm_id=".intval(service('request')->getGet('student_scm_id'))),['id'=>'']);?>
        <div class="ibox-content <?= isMobile() ? 'p-xxs' : 'p-xl';?>">
                <div class="row">
                    <div class="col-sm-6">
                        <span><?=myLang('Institution','প্রতিষ্ঠান');?>:</span>
                        <address>
                            <strong><?= esc($getSchool->sch_name);?></strong>
                            <?php
                                $tgLn = $getSchool->sch_tagline;
                                echo strlen($tgLn) > 0 ? '<br>' . esc($tgLn): '' ;
                            ?>
                            <br><?=myLang('EIIN','ই.আই.আই.এন');?>: <?=esc($getSchool->sch_eiin);?>
                            <br><?=myLang('Phone','ফোন');?>: <?=esc($getSchool->sch_contact);?>
                            <br><?=myLang('Email','ইমেইল');?>: <?=esc($getSchool->sch_email);?>
                            <br><?=myLang('Address','ঠিকানা');?>: <?=implode(', ',array_filter([
                                get_option('schOfficialAddressPost'),
                                get_option('schOfficialAddressPostCode'),
                                get_option('schOfficialAddressDistrict'),
                                get_option('schOfficialAddressCountry'),
                            ]));?>
                            <br>(<?=esc($getSchool->sch_address);?>)

                        </address>
                    </div>

                    <div class="col-sm-6 text-right">
                        <h4><?=myLang('Invoice No.','চালান নং-');?></h4>
                        <h4 class="text-navy">HC-<?=myLang('Not Created','তৈরি হয়নি');?></h4>
                        <p>
                            <span><strong><?=myLang('Invoice Date','ইনভয়েস তৈরির তারিখ');?>:</strong> Marh 18, ____</span><br>
                            <span><strong><?=myLang('Invoice Updated','হালনাগাদ করার তারিখ');?>:</strong> <?=myLang('Not found','পাওয়া যায়নি');?></span><br>
                            <span><strong><?=myLang('Due Date','প্রদেয় তারিখ');?>:</strong> <?=myLang('Not found','পাওয়া যায়নি');?></span>
                        </p>
                        <span><?=myLang('Student','শিক্ষার্থী');?>:</span>
                        <address>
                            <strong>
                                <?=esc(implode(' ', array_filter([
                                    get_name_initials($studentRow->student_u_name_initial),
                                    $studentRow->student_u_name_first,
                                    $studentRow->student_u_name_middle,
                                    $studentRow->student_u_name_last
                                ])));?>
                            </strong><br>
                            <abbr title="Son or Daughter">S/D</abbr> of: <?=esc($studentRow->student_u_father_name);?> & <?=esc($studentRow->student_u_mother_name);?><br>                                        
                            <?=esc(implode(', ', array_filter([
                                $studentRow->student_u_addr_road_house_no,
                                $studentRow->student_u_addr_village,
                                $studentRow->student_u_addr_post_office,
                                $studentRow->student_u_addr_zip_code,
                                $studentRow->student_u_addr_state,
                                $studentRow->student_u_addr_district,
                                get_country_list(strlen($studentRow->student_u_addr_country) < 3 ? 'BGD' : $studentRow->student_u_addr_country )
                            ])));?><br>
                            <?=myLang("Parent's Mobile",'অভিবাবকের মোবাইল');?>: <?=esc(implode(', ', array_filter([$studentRow->student_u_mobile_father,$studentRow->student_u_mobile_mother])));?><br>

                            <abbr title="Mobile">M</abbr>: <?=esc($studentRow->student_u_mobile_own);?>
                        </address>                                    
                    </div>
                </div>

                <div class="form-group border bg-light row mb-0">
                    <label class="col-sm-12 col-form-label text-center"><?=myLang('Select Admission Class/Roll for the Student Here','এখান থেকে ভর্তিকৃত বা ভর্তিযোগ্য শিক্ষার্থীর ক্লাশ/রোল নির্বাচন করুন');?></label>
                    <div class="col-sm-12 bg-light p-0">
                        <?php $scmSetVal = is_object($hcUpdateOldInvoice) ? $hcUpdateOldInvoice->hc_scm_id : intval(service('request')->getGet('student_scm_id')); ?>
                        <?=form_dropdown('hc_class_scm_id', [''=>'']+$userCLasses,[set_value('hc_class_scm_id',$scmSetVal)], ['class'=> "form-control  bg-light text-center",'required'=>'required']); ?>
                        <?= isset($errors) ? get_form_error_msg_from_array($errors,'hc_scm_id') : ''; ?>
                    </div>
                </div>
                <table class="table invoice-table">
                    <thead>
                        <tr>
                            <th><?=myLang('Fee Name','ফি-এর নাম');?></th>
                            <td class="text-right"><?=myLang('Amount','টাকার পরিমাণ');?></td>
                        </tr>
                    </thead>
                    <tbody>                    
                        <?php
                            $colRowItems = service('HandCashCollectionsModel')->get_showable_column_names();
                        ?>
                        <?php foreach($colRowItems as $tblCol => $arVal ): ?>
                            <tr>
                                <td>
                                    <!--<small>-->
                                        <?=esc( ((int)array_search($tblCol, (array)array_keys($colRowItems)) +1) . '.');?>
                                        <?=esc($arVal['lbl']);?>
                                        <?= isset($errors) ? get_form_error_msg_from_array($errors,$tblCol) : ''; ?>
                                    <!--</small>-->
                                </td>
                                <td>
                                    <?php $valIpt = (isset($hcUpdateOldInvoice) && is_object($hcUpdateOldInvoice) AND property_exists($hcUpdateOldInvoice,$tblCol)) ? (intval($hcUpdateOldInvoice->$tblCol) > 0 ? intval($hcUpdateOldInvoice->$tblCol) : '') : set_value("frm_{$tblCol}");?>
                                    <?=form_input(['name'=> "frm_{$tblCol}", 'value' => $valIpt, 'data-hcname' => "hc_{$tblCol}", 'class' => 'fillJSlocalStorageHC','type' => 'text', 'maxlength' => '6','size' => '6',]);?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                            <tr>
                                <td colspan="2" class="text-center">
                                    <small>
                                        <?=esc(myLang('Comments','মন্তব্য'));?>
                                        <?= isset($errors) ? get_form_error_msg_from_array($errors,'hc_salary_months_txt') : ''; ?>
                                    </small>
                                
                                    <?php $valIpt = (isset($hcUpdateOldInvoice) && is_object($hcUpdateOldInvoice) AND property_exists($hcUpdateOldInvoice,'hc_salary_months_txt')) ? $hcUpdateOldInvoice->hc_salary_months_txt : set_value("frm_hc_salary_months_txt");?>
                                    <?=form_input(['name'=> "frm_hc_salary_months_txt", 'value' => $valIpt, 'class' => 'form-control','type' => 'text', 'maxlength' => '200','size' => '6',]);?>
                                </td>
                            </tr>
                    </tbody>
                </table>

                <table class="table invoice-total">
                    <tbody>
                        <tr>
                            <td><?=myLang('Total Amount','মোট টাকা');?>:</td>
                            <td><span id="hc-total-amt"><?=(is_object($hcUpdateOldInvoice) AND intval($hcUpdateOldInvoice->hc_amt_total) > 0) ? intval($hcUpdateOldInvoice->hc_amt_total) : '0.00'; ?></span></td>
                        </tr>
                    </tbody>
                </table>
                <div class="text-right">
                    <?=is_object($hcUpdateOldInvoice) ? form_hidden('updating_hc_row', $hcUpdateOldInvoice->hc_id) : '';?>
                    <?=anchor('admin/pg/cash/in/hand/collection','<i class="fas fa-arrow-left"></i> ' . myLang('Go Back','পেছনে যান'),['class'=>'btn btn-secondary ' . ( isMobile() ? 'mt-1 mb-1' : '')]);?>
                    <button type="submit" name="hc_submit" value="yes" class="btn btn-primary"><i class="fa fa-money-check"></i> 
                        <?php
                            $labSbuCr = myLang('Make An Invoice','ইনভয়েস তৈরি করুন');
                            $labSbuUp = myLang('Update this Invoice','ইনভয়েস হালনাগাদ করুন');
                            echo is_object($hcUpdateOldInvoice) ? $labSbuUp : $labSbuCr;
                        ?>
                    </button>
                </div>

                <div class="well m-t text-justify">
                    <strong><?=esc(myLang('Comments','মন্তব্য'));?>:</strong>
                    <?=myLang('Please check the total amount and various payout items amount before saving the form. <strong>CAUTION:</strong> After creation of invoice, if paid, mark invoice as paid. Otherwise unpaid invoices will be deleted after few days automatically.','অনুগ্রহ করে এই ফরমটি সংরক্ষণ করার আগে আইটেম ভিত্তিক ও মোট টাকার পরিমাণ যাচাই করে দেখুন। <strong>সতর্কতা:</strong> ইনভয়েস তৈরি করার পর, টাকা সংগ্রহ করে ইনভয়েসটি পরিশোধিত হয়েছে মর্মে চিহ্নিত করুন। অন্যথায় অপরিশোধিত ইনভয়েসটি স্বয়ংক্রিয় ভাবে কিছু দিন পর মুছে যাবে  যাবে।');?>
                </div>
            </div>
        <?=form_close();?>
    </div>

    <div class="col-lg-4">
        <div class="ibox ">
            <div class="ibox-content text-center p-md">
                <div class="h6 m-b-xxs">This Student History</div>
                <div class="h6 m-b-xxs m-1"><span class="label label-primary">Invoices include paid or unpaid also</span></div>

                <table class="table table-bordered mt-2">
                    <thead>
                        <tr>
                            <th scope="col"></th>
                            <th scope="col"><?=myLang('Time','সময়');?></th>
                            <th scope="col"><?=myLang('Amount','পরিমাণ');?></th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(is_array($hc_student_history) AND count($hc_student_history) > 0 ): ?>
                            <?php foreach($hc_student_history as $hRo ): ?>
                                <?php if( ! $hRo->hc_is_paid): ?>
                                    <tr>
                                        <td colspan="5" class="p-0 m-0 mt-1"><?=myLang('You can change information of unpaid invoices.','অপরিশোধিত ইনভয়েসের তথ্য পরিবর্তন করতে পারবেন।');?></td>
                                    </tr>
                                <?php endif; ?>
                                <tr>
                                    <td class="p-0 m-0"><?=($hRo->hc_is_paid) ? 'PAID' :  anchor("admin/pg/cash/in/hand/collection/create/inv?student_uid={$hRo->scm_u_id}&update_hc_id={$hRo->hc_id}",myLang('Edit','পরিবর্তন'),['class'=>'btn btn-info btn-sm']);?></td>
                                    <td><?= time_elapsed_string($hRo->hc_updated_at);?></td>
                                    <td><?=esc(esc(number_format($hRo->hc_amt_total,2)));?></td>
                                    <td class="p-0 m-0"><?=anchor("admin/pg/cash/in/hand/collection/mark/as/paid?hc_invoice_id={$hRo->hc_id}",myLang('View','দেখুন'),['class'=>'btn btn-info btn-sm']);?></td>
                                </tr>                            
                            <?php endforeach; ?>
                        <?php else: ?>
                                <tr>
                                    <td colspan="5" class="p-0 m-0 mt-1"><?=myLang('No history found for this student.','এই শিক্ষার্থীর কোন তথ্য পাওয়া যায় নি।');?></td>
                                </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <?=$hc_student_history_pg;?>
            </div>
        </div>
        
    </div>
</div>


<script {csp-script-nonce}>
    window.addEventListener('load', function(event){
        $('button[name="hc_submit"]').click(function(){
            $(this).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Please wait..');
        });
        
        /* Do not populate if we are updating. */
        is_updating_previous_hc_invoice = <?=is_object($hcUpdateOldInvoice) ? 'true' : 'false'; ?>
        
        if( ! is_updating_previous_hc_invoice){
            /* We might have local storage data set in previous page load. Populate in the fields. */
            populate_previous_data_to_numeric_fields(); 
        }
        
        $('.fillJSlocalStorageHC').keyup(function(){
            this.value = this.value.replace(/[^\d,-]/g,'').replace(',,','');/* Remove non digit characters */
            if( ! is_updating_previous_hc_invoice){
                localStorage.setItem($(this).data('hcname'), (isNaN(Number(this.value)) ? 0 : Number(this.value)));
            }
            setTimeout(calculate_and_display_total_amount,500);
        });
    });
    
    /* Calculate and update total payment amount */
    function calculate_and_display_total_amount(){
                document.hcTotalTkAmount = 0;
                $('.fillJSlocalStorageHC').each(function(){                    
                    var amt = $(this).val();
                    if( ! isNaN(Number(amt))){ document.hcTotalTkAmount += Number(amt); }
                });
                $('#hc-total-amt').text(parseInt(document.hcTotalTkAmount).toLocaleString('<?=myLang('en-US','bn-BD');?>',{minimumFractionDigits:2}));
    } /* EOF */
    
    function populate_previous_data_to_numeric_fields(){
        $('.fillJSlocalStorageHC').each(function(){                    
            $(this).val(localStorage.getItem($(this).data('hcname')));
            setTimeout(calculate_and_display_total_amount,500);
        });
    } /* EOF */
    
</script>