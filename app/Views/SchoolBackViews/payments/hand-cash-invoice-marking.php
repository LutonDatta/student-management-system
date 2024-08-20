
<?php
    if(isset($display_msg) AND is_string($display_msg) AND strlen($display_msg) > 0){ echo $display_msg; }
    $FlsMsg = session()->getFlashdata('display_msg'); if(strlen($FlsMsg) > 0) echo $FlsMsg; 
?>
<?php if(! empty($errors) AND count($errors) > 0) : ?>
    <div class="alert-danger text-center" role="alert">
        <ul class="list-unstyled"><?php foreach ($errors as $error) : ?><li><?= esc($error) ?></li><?php endforeach ?></ul>
    </div>
<?php endif; ?>

<div class="row mb-4">
    <div class="col-lg-8">
        
        <div class="ibox-content <?= isMobile() ? 'p-xxs' : 'p-xl';?>">
                <div class="row">
                    <div class="col-sm-12 m-0 p-0 text-center">
                        <div class='border h6 p-0 m-0 <?=($hcRow->hc_is_paid) ? 'bg-success' : 'bg-warning';?>'><?=($hcRow->hc_is_paid) ? 'PAID' : 'UNPAID'; ?></div>
                    </div>
                    <div class="col-sm-6">
                        <h4 class="text-navy mb-0"><?php // echo myLang('Invoice No.','চালান নং-');?> #HC-<?=esc(sprintf('%05d',$hcRow->hc_id));?></h4>
                        <!--<span><?=myLang('Institution','প্রতিষ্ঠান');?>:</span>-->
                        <address>
                            <strong><?php $schNm = getSchool()->sch_name; echo (strlen($schNm) < 0) ? 'No school name found' : esc($schNm);?></strong>
                            <?php $tgLn = getSchool()->sch_tagline; echo strlen($tgLn) > 0 ? '<br>' . esc($tgLn): '' ; ?>
                            <br><?=myLang('Phone','ফোন');?>: <?=esc(getSchool()->sch_contact);?>
                            <br><?=myLang('Email','ইমেইল');?>: <?=esc(getSchool()->sch_email);?>
                            <br><?=myLang('Address','ঠিকানা');?>: <?=implode(', ',array_filter([
                                get_option('schOfficialAddressPost'),
                                get_option('schOfficialAddressPostCode'),
                                get_option('schOfficialAddressDistrict'),
                                get_option('schOfficialAddressCountry'),
                            ]));?>
                            <br>(<?=esc(getSchool()->sch_address);?>)
                        </address>
                        
                        <p class="pt-0">
                            <span><strong><?=myLang('Invoice Date','ইনভয়েস তৈরির তারিখ');?>:</strong> <?=date('D M j, Y, g:i a',strtotime($hcRow->hc_inserted_at));?></span><br>
                            <span><strong><?=myLang('Invoice Updated','হালনাগাদ করার তারিখ');?>:</strong> <?=date('D M j, Y, g:i a',strtotime($hcRow->hc_updated_at));?></span><br>
                        </p>
                    </div>

                    <div class="col-sm-6 text-right">
                        
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
                            <?=myLang("Parent's Mobile",'অভিবাবকের মোবাইল');?>: <?=esc(esc(implode(', ', array_filter([$studentRow->student_u_mobile_father,$studentRow->student_u_mobile_mother]))));?><br>

                            <abbr title="Mobile">M</abbr>: <?=esc(esc($studentRow->student_u_mobile_own));?>
                            
                            <div class="border-top mt-2">
                                <?=myLang('Class','ক্লাশ');?>: <?=$clsData->title;?>
                                <?=($clsData->scm_deleted_at) ? '<span class="label-warning">(' . myLang('Deleted '.time_elapsed_string($clsData->scm_deleted_at),time_elapsed_string($clsData->scm_deleted_at).' ডিলিট করা হয়েছে') .')</span>'  : '';?>
                                <br><?=myLang('Session','সেশন');?>: <?=esc($clsData->scm_session_year);?>
                                <br><?=myLang('Status','স্ট্যাটাস');?>: 
                                    <?php $stats = get_student_class_status(); echo isset($stats[$clsData->scm_status]) ? esc($stats[$clsData->scm_status]) : '';?>
                                <br><?=myLang('Class Roll','ক্লাশ রোল');?>: <?=esc($clsData->scm_c_roll);?>
                                <?=myLang('FCS ID','FCS ID');?>: <?=esc($clsData->fcs_id);?>,
                                <?=myLang('SCM ID','SCM ID');?>: <?=esc($clsData->scm_id);?>,
                                <?=myLang('SCM SID','SCM SID');?>: <?=esc($clsData->scm_u_id);?>
                            </div>
                        </address>                                    
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
                            <?php if(isset($hcRow) AND property_exists($hcRow, $tblCol) AND intval($hcRow->$tblCol) === 0) continue; ?>
                            <tr>
                                <td>
                                    <!--<small>-->
                                        <?=esc( ((int)array_search($tblCol, (array)array_keys($colRowItems)) +1) . '.');?>
                                        <?=esc($arVal['lbl']);?>
                                    <!--</small>-->
                                </td>
                                <td><?=esc(esc($hcRow->$tblCol));?></td>
                            </tr>
                        <?php endforeach; ?>
                            <tr>
                                <td colspan="2" class="text-center">
                                    <small><?=esc(myLang('Comments','মন্তব্য'));?></small>
                                    <?php $cmtHcValFromDb = (isset($hcRow) && is_object($hcRow) AND property_exists($hcRow,'hc_salary_months_txt')) ? $hcRow->hc_salary_months_txt : '';?>
                                    <input type="text" class="w-100 text-center" disabled="disabled" value="<?=esc($cmtHcValFromDb);?>">
                                </td>
                            </tr>
                    </tbody>
                </table>

                <table class="table invoice-total">
                    <tbody>
                        <tr>
                            <td><?=myLang('Total Amount','মোট টাকা');?>:</td>
                            <td><?=esc(esc($hcRow->hc_amt_total));?></td>
                        </tr>
                    </tbody>
                </table>
            
                <?=form_open(base_url("admin/pg/cash/in/hand/collection/mark/as/paid"),['id'=>'']);?>
                    <?=form_hidden('mark_this_invoice_id',$hcRow->hc_id );?>
                    <div class="text-center d-print-none">
                        <?=anchor('admin/pg/cash/in/hand/collection','<i class="fas fa-arrow-left"></i> ' . myLang('Go Back','পেছনে যান'),['class'=>'btn btn-secondary']);?>
                        <?php if($hcRow->hc_is_paid): ?>
                            <?php if( (strtotime($hcRow->hc_updated_at) + (18 * 60 * 60 )) > time() ): ?>
                                <button type="submit" name="hc_marking_submit" value="mark_as_unpaid" class="btn btn-warning <?=isMobile() ? 'mt-1 mb-1' : '';?>"><i class="fa fa-money-check"></i> <?=myLang('Mark As UnPaid','পরিশোধিত হয়নি মর্মে নিশ্চিত করুন');?></button>
                            <?php endif; ?>
                            <?=myLang('PAID','PAID');?>
                            <?=anchor("admin/pg/cash/in/hand/collection/create/inv?student_uid=",'<i class="far fa-edit"></i> ' . myLang('Create new Invoice','নতুন ইনভয়েস তৈরি করুন'),['class'=>'btn btn-secondary ' . ( isMobile() ? 'mt-1 mb-1' : '') ]);?>
                        <?php else: ?>
                            <?php //if( (strtotime($hcRow->hc_updated_at) + (18 * 60 * 60 )) > time() ): ?>
                                <button type="submit" name="hc_marking_submit" value="mark_as_paid" class="btn btn-primary <?=isMobile() ? 'mt-1 mb-1' : '';?>"><i class="far fa-check-circle"></i> <?=myLang('Mark As Paid','পরিশোধিত হয়েছে মর্মে নিশ্চিত করুন');?></button>
                            <?php //endif; ?>
                            <?=anchor("admin/pg/cash/in/hand/collection/create/inv?student_uid={$studentRow->student_u_id}&update_hc_id={$hcRow->hc_id}",'<i class="far fa-edit"></i> ' . myLang('Edit','পরিবর্তন করুন'),['class'=>'btn btn-secondary']);?>
                        <?php endif; ?>  
                        <button type="button" id="printBoxBtnx" class="btn btn-secondary ml-1"><i class="fas fa-print"></i> Print</buttonon>
                    </div>
                <?=form_close();?>

                <div class="well m-t text-justify d-print-none">
                    <strong><?=esc(myLang('Comments','মন্তব্য'));?>:</strong>
                    <?=myLang('After creation of invoice, if paid, mark invoice as paid. Otherwise unpaid invoices will be deleted automatically few days later.','ইনভয়েস তৈরি করার পর, টাকা সংগ্রহ করে ইনভয়েসটি পরিশোধিত হয়েছে মর্মে চিহ্নিত করুন। অন্যথায় অপরিশোধিত ইনভয়েসটি কিছু দিন পর স্বয়ংক্রিয় ভাবে ডিলিট হয়ে যাবে।');?>
                </div>
                <div class="text-center">
                    <span class="d-none d-print-inline">Printed at: <?=date('jS M Y h:i:s a O');?></span>
                </div>
            </div>
        
        
        
    </div>

    <div class="col-lg-4 d-print-none">
        <div class="ibox ">
            <div class="ibox-content text-center p-md">
                <?php service('ShowLinksLibrary')->show_links_on_mobile_devices(isset($loadedPage) ? $loadedPage : (isset($showingPage) ? $showingPage : ''), true); ?>            
            </div>
        </div>
        
    </div>
</div>


<script {csp-script-nonce}>
    window.addEventListener('load', function(event){
        $('button[name="hc_submit"]').click(function(){
            $(this).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Please wait..');
        });
    });
    
</script>

<script {csp-script-nonce}>document.addEventListener('DOMContentLoaded',function(){document.getElementById('printBoxBtnx').addEventListener('click',function(){window.print();});});</script>

