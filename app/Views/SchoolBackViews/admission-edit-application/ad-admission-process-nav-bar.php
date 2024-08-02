

<article class="row">
    <div class="col-lg-12">
        <?php 
            if( ! empty($already_applied) AND is_object($already_applied)){
                $spNotice = '<br>'.myLang('Special Notice: If you change any information after sending your application, update your application again going to 6th tab.','বিশেষ বিজ্ঞপ্তি: আবেদন প্রথম বার প্রেরণ করার পর যদি আপনি কোন তথ্য পরিবর্তন করেন তবে, ৬ষ্ঠ টেবে গিয়ে কোর্স সমূহ যাচাই করে আপনার আবেদন পুনরায় পাঠান।');
                $time = time_elapsed_string($already_applied->scm_inserted_at) .' ['. esc(esc($already_applied->scm_inserted_at)) .'] ' . myLang('Application update time: ','আবেদন হালনাগাদ করার সময়: ') . time_elapsed_string($already_applied->scm_updated_at) .' ['. esc(esc($already_applied->scm_updated_at)) .']';
                if($already_applied->scm_status === 'requested'){ 
                    echo get_display_msg(myLang('This student has already sent application to this class. Application time: ','এই শিক্ষার্থী ইতিমধ্যে এই ক্লাশে ভর্তির আবেদন প্রেরণ করেছেন। আবেদন প্রেরণের সময়: ') . $time . $spNotice );
                }
                else{ 
                    echo get_display_msg(myLang('This student has already registered to this class. Application time: ','এই শিক্ষার্থী ইতিমধ্যে এই ক্লাশে নিবন্ধন করেছেন। আবেদন প্রেরণের সময়: ') . $time . $spNotice );
                }
            }
        ?>
    </div>
</article>



<div class="row">
    <div class="col-lg-12 animated fadeInRight">
        <ul class="nav nav-tabs nav-fill" role="tablist">
            <li class="nav-item border">
                <a 
                    class="nav-link <?=!(in_array(service('request')->getGet('InfoPage'),['address-info','identity-info','select-courses','printable-pages','photo-editor','select-class'])) ? 'active' : '' ;?>" 
                    href="<?=base_url("admin/admission/edit/application/by/admin?InfoPage=basic-info&student_id=".intval(service('request')->getGet('student_id')));?>" role="tab">
                    <span><?= esc('1.') . ' ' . lang('Sa.edit_basic_info');?></span>
                </a>
            </li>
            <li class="nav-item border">
                <a 
                    class="nav-link <?=service('request')->getGet('InfoPage') == 'address-info' ? 'active' : '' ;?>" 
                    href="<?=base_url("admin/admission/edit/application/by/admin?InfoPage=address-info&student_id=".intval(service('request')->getGet('student_id')));?>" role="tab">
                    <span><?= esc('2.') . ' ' . lang('Sa.edit_address_info');?></span>
                </a>
            </li>
            <li class="nav-item border">
                <a 
                    class="nav-link <?=service('request')->getGet('InfoPage') == 'identity-info' ? 'active' : '' ;?>" 
                    href="<?=base_url("admin/admission/edit/application/by/admin?InfoPage=identity-info&student_id=".intval(service('request')->getGet('student_id')));?>" role="tab">
                    <span><?= esc('3.') . ' ' . lang('Sa.edit_identity_contact_info');?></span>
                </a>
            </li>
            <li class="nav-item border">
                <a 
                    class="nav-link <?=in_array(service('request')->getGet('InfoPage'),['select-courses','select-class']) ? 'active' : '';?>" 
                    href="<?=base_url("admin/admission/edit/application/by/admin?InfoPage=select-class&student_id=".intval(service('request')->getGet('student_id')));?>" role="tab">
                    <span><?= esc('4.') . ' ' . 'Class & Course Selector';?></span>
                </a>
            </li>
            <li class="nav-item border">
                <a 
                    class="nav-link <?=service('request')->getGet('InfoPage') == 'photo-editor' ? 'active' : '';?>" 
                    href="<?=base_url("admin/admission/edit/application/by/admin?InfoPage=photo-editor&student_id=".intval(service('request')->getGet('student_id')));?>" role="tab">
                    <span><?= esc('5.') . ' ' . 'Print / Photo';?></span>
                </a>
            </li>
            
        </ul>
    </div>
</div>