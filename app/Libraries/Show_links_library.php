<?php namespace App\Libraries;


class Show_links_library {
    
    /**
     * 
     * @param type $showingPage
     * @param type $showOnAllDevices Show on all devices? not only mobile? pass TRUE as value.
     */
    public function show_links_on_mobile_devices( $showingPage = '', $showOnAllDevices = false ){
        
        /* For better navigation on mobile devices, show some links in the buttom of the page. */
        if($showOnAllDevices || service('request')->getUserAgent()->isMobile()):
            ?>
                <div class="row">
                    <div class="col-lg-12 text-center mt-4 mb-4">
                        <?php
                            $menuXList = [                    
                                'admin/admission/edit/application/by/admin' => [
                                    myLang('Get students listed by admission','ভর্তির মাধ্যমে শিক্ষার্থীদের তালিকবদ্ধ করুন'), 
                                    'fas fa-user-graduate', 
                                ],
                                'admin/admission/student/list' => [
                                    lang('Admin.class_wise_student_list'), 
                                    'fa fa-laptop', 
                                ],
                                'dashboard' => [
                                    lang('Admin_menu.dashboard'), 
                                    'fa fa-list-alt', 
                                ],
                            ];
                            foreach($menuXList as $mxLink => $mxData ){
                                if(isset($mxData[2]) AND $mxData[2] === false ) continue; // Current user has no purmission to see this link
                                
                                $icn = (strlen($mxData[1]) < 1) ? '' : '<i class="'.$mxData[1].'"></i> ';
                                echo anchor($mxLink, $icn . lang($mxData[0]), ['class'=>'btn btn-outline-info m-1']);
                            }
                        ?>
                    </div>
                </div>
            <?php 
        endif;
        
    } /* EOM */
    
} /* EOC */
