<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element text-center">
                    <img alt="image" class="rounded-circle" src="<?=cdn_url('default-images/profile-pic-boy.png');?>" width="50" height="50"/>
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <span class="block m-t-xs font-bold text-wrap">
                            School Admin
                            <b class="caret"></b>
                        </span>
                        <span class="text-muted text-xs block">Manage your school from here.</span>
                    </a>
                    
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                        <li><a class="dropdown-item" href="<?=base_url('dashboard');?>"><i class="fa fa-laptop"></i> <?=lang('Admin_menu.dashboard');?></a></li>
                        <li><a class="dropdown-item" href="<?=base_url('user/logout');?>"><i class="fa fa-sign-out-alt"></i> <?=lang('Menu.logout');?></a></li>
                    </ul>
                </div>
                
                <div class="logo-element">U</div>
            </li>
            <?php 
            $leftMenuItems = [
                array(
                    'active'    => in_array($loadedPage,['dashboard']) ? 'active' : '',
                    'areaExp'   => 'false',
                    'menuLabel' => lang('Admin_menu.dashboard'),
                    'url'       => base_url('dashboard'),
                    'icon'      => 'fa fa-laptop',
                ),
                array(
                    'active'    => in_array($loadedPage,['academic_setup','academic_course','academic_course_dist','academic_course_teacher','attendance_book','dab_view']) ? 'active' : '',
                    'areaExp'   => in_array($loadedPage,['academic_setup','academic_course','academic_course_dist','academic_course_teacher','attendance_book','dab_view']) ? 'true' : 'false',
                    'menuLabel' => lang('Admin_menu.academic'),
                    'icon'      => 'fa fa-table',
                    'counter'   => '<i class="fa arrow"></i>',
                    'subItems'  => [
                        array(
                            'active'    => in_array($loadedPage,['academic_setup']) ? 'active' : '',
                            'url'       => base_url('admin/academic/setup'),
                            'menuLabel' => lang('Admin_menu.academic_class_n_faculty'),
                        ),
                        array(
                            'active'    => in_array($loadedPage,['academic_course']) ? 'active' : '',
                            'url'       => base_url('admin/academic/course'),
                            'menuLabel' => 'Courses / Subjects',
                        ),
                        array(
                            'active'    => in_array($loadedPage,['academic_course_dist']) ? 'active' : '',
                            'url'       => base_url('admin/academic/course/distribution'),
                            'menuLabel' => lang('Admin_menu.academic_class_wise_course'),
                        ),
                        array(
                            'active'    => in_array($loadedPage,['attendance_book']) ? 'active' : '',
                            'url'       => base_url('daily/attendance/book'),
                            'menuLabel' => lang('Admin_menu.attendance_book'),
                        ),
                        array(
                            'active'    => in_array($loadedPage,['dab_view']) ? 'active' : '',
                            'url'       => base_url('daily/attendance/book/view'),
                            'menuLabel' => lang('Admin_menu.attendance_view'),
                        ),
                    ]
                ),
                array(
                    'active'    => in_array($loadedPage,['exam_date_time','exam_routine','exam_results','exam_results_viewer','exam_date_time_viewer','exam_results_publish','exam_results_view_own','delete_exam_results']) ? 'active' : '',
                    'areaExp'   => in_array($loadedPage,['exam_date_time','exam_routine','exam_results','exam_results_viewer','exam_date_time_viewer','exam_results_publish','exam_results_view_own','delete_exam_results']) ? 'true' : 'false',
                    'menuLabel' => lang('Menu.examination'),
                    'icon'      => 'fa fa-award',
                    'counter'   => '<i class="fa arrow"></i>',
                    'subItems'  => array(
                        array(
                            'active'    => in_array($loadedPage,['exam_date_time']) ? 'active' : '',
                            'url'       => base_url('admin/academic/exam/date/time'),
                            'menuLabel' =>  lang('Menu.exam_date_time'),
                        ),array(
                            'active'    => in_array($loadedPage,['exam_routine']) ? 'active' : '',
                            'url'       => base_url('admin/academic/exam/routine'),
                            'menuLabel' =>  lang('Menu.exam_routine'),
                        ),array(
                            'active'    => in_array($loadedPage,['exam_results']) ? 'active' : '',
                            'url'       => base_url('admin/academic/exam/results'),
                            'menuLabel' =>  lang('Menu.add_exam_results'),
                        ),array(
                            'active'    => in_array($loadedPage,['exam_results_publish']) ? 'active' : '',
                            'url'       => base_url('admin/academic/exam/results/publish'),
                            'menuLabel' => myLang('Result Publish','ফল প্রকাশ'),
                        ),array(
                            'active'    => in_array($loadedPage,['delete_exam_results']) ? 'active' : '',
                            'url'       => base_url('admin/academic/exam/results/delete'),
                            'menuLabel' => myLang('Delete Result','ফলাফল মুছুন'),
                        ),array(
                            'active'    => in_array($loadedPage,['exam_date_time_viewer']) ? 'active' : '',
                            'url'       => base_url('admin/academic/exam/date/time/viewer'),
                            'menuLabel' => '<i class="fa fa-minus"></i>'. myLang('See Exam Date','পরীক্ষার তারিখ দেখুন'),
                        ),array(
                            'active'    => in_array($loadedPage,['exam_results_viewer']) ? 'active' : '',
                            'url'       => base_url('admin/academic/exam/results/viewer'),
                            'menuLabel' => '<i class="fa fa-minus"></i>'. myLang('View Results','ফলাফল দেখুন'),
                        ),array(
                            'active'    => in_array($loadedPage,['exam_results_view_own']) ? 'active' : '',
                            'url'       => base_url('admin/academic/exam/results/view/own'),
                            'menuLabel' => '<i class="fa fa-minus"></i>'. myLang('Mark Sheet','আমার নম্বরপত্র'),
                        )
                    )
                ),
                array(
                    'active'    => in_array($loadedPage,['admission_setup','admission_applications','edit_application','step_up_down','student_list_x','personal_info','personal_photo','admission_course_selector']) ? 'active' : '',
                    'areaExp'   => in_array($loadedPage,['admission_setup','admission_applications','edit_application','step_up_down','student_list_x','personal_info','personal_photo','admission_course_selector']) ? 'true' : 'false',
                    'menuLabel' => 'Student',
                    'icon'      => 'fas fa-user-graduate',
                    'counter'   => '<i class="fa arrow"></i>',
                    'subItems'  => [
                        array(
                            'active'    => in_array($loadedPage,['edit_application']) ? 'active' : '',
                            'url'       => base_url('admin/admission/edit/application/by/admin'),
                            'menuLabel' => 'Admission',
                        ),
                        array(
                            'active'    => in_array($loadedPage,['student_list_x']) ? 'active' : '',
                            'url'       => base_url('admin/admission/student/list'),
                            'menuLabel' => lang('Admin_menu.admission_student_list'),
                        ),
                        array(
                            'active'    => in_array($loadedPage,['step_up_down']) ? 'active' : '',
                            'url'       => base_url('admin/admission/step/up/down'),
                            'menuLabel' => 'Up/Downgrade (Pass/Fail)',
                        )
                    ]
                ),
                array(
                    'active'    => in_array($loadedPage,['pg_salary_setup','pg_salary_collections', 'fees_history','fees_pay','pg_targets','pg_collections','pg_summary','pg_bank_accounts','pg_balance_transfer','pg_custom_payment_collections','cash_in_hand_collections']) ? 'active' : '',
                    'areaExp'   => in_array($loadedPage,['pg_salary_setup','pg_salary_collections', 'fees_history','fees_pay','pg_targets','pg_collections','pg_summary','pg_bank_accounts','pg_balance_transfer','pg_custom_payment_collections','cash_in_hand_collections']) ? 'true' : 'false',
                    'menuLabel' => lang('Admin_menu.payment'),
                    'icon'      => 'fa fa-credit-card',
                    'counter'   => '<i class="fa arrow"></i>',
                    'subItems' => [
                        array(
                            'active'    => in_array($loadedPage,['cash_in_hand_collections']) ? 'active' : '',
                            'url'       => base_url('admin/pg/cash/in/hand/collection'),
                            'menuLabel' => myLang('Hand Cash Collection','হাতে নগদ সংগ্রহ'),
                        ),
                    ]
                ),
                array(
                    'active'    => in_array($loadedPage,['lib_books','lib_books_distributed','lib_book_bin','lib_books_new','lib_items','lib_collections']) ? 'active' : '',
                    'areaExp'   => in_array($loadedPage,['lib_books','lib_books_distributed','lib_book_bin','lib_books_new','lib_items','lib_collections']) ? 'true' : 'false',
                    'menuLabel' => lang('Admin_menu.library'),
                    'icon'      => 'fa fa-book-open',
                    'counter'   => '<i class="fa arrow"></i>',
                    'subItems'  => [
                        array(
                            'active'    => in_array($loadedPage,['lib_books']) ? 'active' : '',
                            'url'       => base_url('admin/library'),
                            'menuLabel' => lang('Admin_menu.library_books_items'),
                        ),array(
                            'active'    => in_array($loadedPage,['lib_books_distributed']) ? 'active' : '',
                            'url'       => base_url('admin/library/distributions'),
                            'menuLabel' => lang('Admin_menu.library_distributions'),
                        ),array(
                            'active'    => in_array($loadedPage,['lib_book_bin']) ? 'active' : '',
                            'url'       => base_url('admin/library/bin'),
                            'menuLabel' => lang('Admin_menu.library_recycle_bin'),
                        ),
                    ]
                ),
                
                array(
                    'active'    => in_array($loadedPage,['instBasicInfo','appear_Slider','appear_themes','appear_youtube','appear_footer_links','appear_url_name']) ? 'active' : '',
                    'areaExp'   => in_array($loadedPage,['instBasicInfo','appear_Slider','appear_themes','appear_youtube','appear_footer_links','appear_url_name']) ? 'true' : 'false',
                    'menuLabel' => lang('Admin_menu.settings'),
                    'icon'      => 'fa fa-th-large',
                    'counter'   => '<i class="fa arrow"></i>',
                    'subItems'  => [
                        array(
                            'active'    => in_array($loadedPage,['instBasicInfo']) ? 'active' : '',
                            'url'       => base_url('admin/institution/edit'),
                            'menuLabel' => lang('Admin_menu.settings_basic_info'),
                        ),
                    ]
                ),
                
                array(
                    'active'    => in_array($loadedPage,['acc_verify','acc_help_support','acc_bill_pay','acc_upd_credentials']) ? 'active' : '',
                    'areaExp'   => in_array($loadedPage,['acc_verify','acc_help_support','acc_bill_pay','acc_upd_credentials']) ? 'true' : 'false',
                    'menuLabel' => lang('Student.account'),
                    'icon'      => 'fa fa-school',
                    'counter'   => "<i class='fa arrow '></i>",
                    'subItems'  => [
                        array(
                            'active'    => in_array($loadedPage,['logged_in_user_logout_url']) ? 'active' : '',
                            'url'       => base_url('user/logout'),
                            'menuLabel' =>  '<i class="fa fa-sign-out-alt"></i>'.  lang('Menu.logout'),
                        ), 
                    ]
                ),
            ];
            
            
            
            foreach( $leftMenuItems as $mItem ){ ?>
                <li class="<?=$mItem['active'];?>">
                        <a href="<?=isset($mItem['url'])?$mItem['url']:'#';?>" aria-expanded="<?=$mItem['areaExp'];?>">
                            <i class="<?=$mItem['icon'];?>"></i>
                            <span class="nav-label"><?=$mItem['menuLabel'];?></span>
                            <?=isset($mItem['counter'])?$mItem['counter']:'';?>
                        </a>
                        <?php if(isset($mItem['subItems']) AND is_array( $mItem['subItems'] ) AND count( $mItem['subItems'] ) > 0 ): ?>
                        <ul class="nav  nav-second-level <?=($mItem['active'] != 'active')?'collapse':'';?>">
                                <?php foreach($mItem['subItems'] as $subItem ): if( isset($subItem['permission']) AND ! $subItem['permission']) continue; ?>
                                    <li class="<?=$subItem['active'];?>">
                                            <a href="<?=$subItem['url'];?>" <?=isset($subItem['3rdAreaExp']) ? $subItem['3rdAreaExp'] : '';?>>
                                                <?=$subItem['menuLabel'];?>
                                                <?=isset($subItem['counter'])?$subItem['counter']:'';?>
                                            </a>
                                            <?php if(isset($subItem['3rdLevelItems']) AND is_array( $subItem['3rdLevelItems'] ) AND count( $subItem['3rdLevelItems'] ) > 0 ): ?>
                                            <ul class="nav nav-third-level <?=($subItem['active'] != 'active')? 'collapse' : '';?>">
                                                <?php foreach($subItem['3rdLevelItems'] as $thirdItem ): if( isset($subItem['permission']) AND ! $subItem['permission']) continue; ?>
                                                    <li class="<?=$thirdItem['active'];?>">
                                                        <a href="<?=$thirdItem['url'];?>"><?=$thirdItem['menuLabel'];?></a>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                            <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                </li>
            <?php } ?>
            
        </ul>

    </div>
</nav>
