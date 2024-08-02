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
    <div class="col-lg-12 animated fadeInRight">
            
        
            <div class="widget-text-box text-center">
                <?=anchor("student/info/print/view?apply_to_class_id=".service('request')->getGet('apply_to_class_id')."&user_id=".service('AuthLibrary')->getLoggedInUserID(),'<i class="fas fa-print"></i> ' . lang('Sa.print_your_info'),['class'=>'btn btn-success mb-2','target'=>'']);?>
                <?=anchor("print/admission/test/admit/card?apply_to_class_id=".service('request')->getGet('apply_to_class_id')."&admit_card_of=".service('AuthLibrary')->getLoggedInUserID(),'<i class="fas fa-print"></i> '.lang('Sa.print_admit_card'),['class'=>'btn btn-success mb-2','target'=>'']);?>
            </div>
            <div class="widget-text-box text-center">
                <?=anchor("student/area/personal/view?apply_to_class_id=".service('request')->getGet('apply_to_class_id'),'<i class="fa fa-eye"></i> ' . lang('Sa.view_your_applications'),['class'=>'btn btn-secondary text-center mb-2']);?>
                <a href="<?=base_url('student/area/personal/view')?>" class="btn btn-sm btn-secondary mb-2" ><i class="fa fa-eye"></i> <?=lang('Sa.btn_view_personal_info');?></a>
            </div>
        
            <div class="widget-text-box text-center">
                
                আপনি জানেন কি? সপ্তাহের ছুটির দিন সহ যেকোন সময় বেতন ও পরীক্ষার ফি  অনলাইনে মোবাইল ব্যাংকিং এর মাধ্যমে পরিশোধ করতে পারবেন ঘরে বসে থেকেই।
                <div>
                    <a href="<?=base_url('student/area/personal/printables?editinfotype=showPaymentLinks&apply_to_class_id='.service('request')->getGet('apply_to_class_id'))?>" class="btn btn-info mt-3" ><i class="fas fa-money-check"></i> <?=lang('Menu.admission_app_fee_pay');?></a>
                    <a href="<?=base_url('student/area/personal/printables?editinfotype=showPaymentLinks&apply_to_class_id='.service('request')->getGet('apply_to_class_id'))?>" class="btn btn-info mt-3" ><i class="fas fa-money-check-alt"></i> <?=lang('Menu.admission_fee_pay');?></a>
                </div>
                
            </div>
    </div>
</article>
<div class="clearfix"></div>





