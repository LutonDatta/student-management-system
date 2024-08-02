<?php namespace App\Controllers\SchoolBack;

/**
 * @package    Ultra-School
 * @author     Ultra-School Dev Team
 * @copyright  2019-2020 Ultra Data Safety Solutions Pvt. Ltd.
 * @license    Private License
 * @link       https://ultra-school.com
 * @since      Version 1.0.0
 * @filesource
 */

/**
 * Automatically admit/move passed student to next class. Or Move student to the another class. When students fail,
 * we need to admit them in the next batch/next year.
 */
class Admission_automation extends BaseController {
    
    public function step_up_step_down(){
        $data                   = $this->data; 
        $data['title']          = 'Pass/Fail (Upgrade or Downgrade) - Students';
        $data['pageTitle']      = 'Pass/Fail (Upgrade or Downgrade) - Students';
        $data['loadedPage']     = 'step_up_down';
        
        $save = $this->process_submitted_from(); // Returns redirect object, error message
        if(is_object($save)){ return $save; }else{  $data = array_merge( $data, $save ); }
        
        
        $data['allClasses_from']    = service('ClassesAndSemestersModel')->get_classes_with_parent_label_for_dropdown(false,'allClassList_from',20,false);
        $data['allClasses_pgr_from']= service('ClassesAndSemestersModel')->pager->links('allClassList_from');
        // We need them separately to show correctly in the dropdown separately
        $data['allClasses_to']      = service('ClassesAndSemestersModel')->get_classes_with_parent_label_for_dropdown(true,'allClassList_to',20,false);
        $data['allClasses_pgr_to']  = service('ClassesAndSemestersModel')->pager->links('allClassList_to');
        
        $data['allSessions_from']    = service('CoursesClassesStudentsMappingModel')->distinct()->select('scm_session_year')->paginate(40,'allSessionList_from');
        $data['allSessions_pgr_from']= service('CoursesClassesStudentsMappingModel')->pager->links('allSessionList_from');
        
        // Move/admit students from 
        $data['studentsFrom']   = array(
            'from_class'        => intval($this->request->getGet('from_class')),
            'from_year'         => $this->request->getGet('from_year',FILTER_SANITIZE_STRING),
            'from_status'       => $this->request->getGet('from_status',FILTER_SANITIZE_STRING),
        );
        // If user is in the next other page we need to add selected class to the list so that user can see it always
        if($data['studentsFrom']['from_class'] > 0 AND ! isset($data['allClasses_from'][$data['studentsFrom']['from_class']])) $data['allClasses_from'][$data['studentsFrom']['from_class']] = '[Selected - ' . $data['studentsFrom']['from_class'] . ']';
        if($data['studentsFrom']['from_class'] > 0 AND ! isset($data['allClasses_to'  ][$data['studentsFrom']['from_class']])) $data['allClasses_to'  ][$data['studentsFrom']['from_class']] = '[Selected - Same as From - ' . $data['studentsFrom']['from_class'] . ']';
                
        $data['students']       = service('CoursesClassesStudentsMappingModel')->where(array(
                                        'scm_class_id'      => $data['studentsFrom']['from_class'],
                                        'scm_session_year'  => $data['studentsFrom']['from_year'],
                                        'scm_status'        => $data['studentsFrom']['from_status'],
                                    ))
                                    ->join('user_students',"scm_u_id = user_students.student_u_id",'LEFT')
                                    ->paginate(15,'students');
        $data['students_pgr']   = service('CoursesClassesStudentsMappingModel')->pager->links('students');
        
        echo view('SchoolBackViews/head', $data);
        echo view('SchoolBackViews/_parts/nav-left', $data);
        echo view('SchoolBackViews/_parts/nav-top', $data); 
        echo view("SchoolBackViews/admission-automation/step-up-step-down", $data);
        echo view('SchoolBackViews/_parts/nav-bottom', $data);
        echo view('SchoolBackViews/footer', $data);
    } // EOM
    
    /**
     * Admit students to a new class means create/duplicate row with new status and session. (upgrade/downgrade students)
     * If class and session is same then just change status only instead of creating/duplicating rows. (change status of students)
     * 
     * @return mixed Returns string or object.  Returns redirect()->to() for successful save or error message for other errors. 
     */
    private function process_submitted_from(){
        if( $this->request->getPost('sbtStdsIds') !== 'yes') return []; 
        
        // Students will be searched from these from data
        $from_class     = (int) $this->request->getPost('post_from_class');
        $from_year      = (string) $this->request->getPost('post_from_year');
        $from_stat      = (string) $this->request->getPost('post_from_status');
        // Students will be duplicated to these new class.
        $to_class       = (int) $this->request->getPost('to_class');
        $to_year        = (string) $this->request->getPost('to_year');
        $to_stat        = (string) $this->request->getPost('to_status');
        // Student IDs to work with
        $student_ids    = (string) $this->request->getPost('studentIDs'); // A list of comma separated IDs
        
        $allowed_status = array_filter(get_student_class_status());
        if( ! array_key_exists($from_stat, $allowed_status)) return ['display_msg'=> get_display_msg ('Invalid - from status.','danger')];
        if( ! array_key_exists($to_stat, $allowed_status)) return ['display_msg'=> get_display_msg ('Invalid - to status.','danger')];
        // Just test for strlen. It must be atleast 1 character, other testing will be automatically done
        if( strlen( $from_class ) < 1 ) return ['display_msg'=> get_display_msg ('Invalid - from class.','danger')];
        if( strlen( $to_class ) < 1 ) return ['display_msg'=> get_display_msg ('Invalid - to class.','danger')];
        if( strlen( $from_year ) < 1 ) return ['display_msg'=>  get_display_msg ('Invalid - from year/session.','danger')];
        if( strlen( $to_year ) < 1 ) return ['display_msg'=>  get_display_msg ('Invalid - to year/session.','danger')];
        
        $list_ids = array_filter( explode(',', $student_ids) ); // We might have empty string, must filter it to generate empty id error
        if( count($list_ids) < 1 ){
            @session_start(); // Reverse session_write_close?
            return redirect()->to(previous_url())->with('display_msg',get_display_msg (lang('Rw.you_must_select_at_least_one_student'),'danger'));
        }
        
        /**
         * Sometimes all students are admitted to a single class and need to move them to a different class. Such as Class Six has Section A and B. 
         * All students will be admitted to Class Six and need to be admitted to Section A and B. So we will move them directly to switch them in 
         * different class.
         */
        $is_only_section_change = $this->request->getPost('onlySectionChange');
        
        // Admin requested to change status of the students. It might be, from admitted to passed or from admitted to failed
        if( ($from_class === $to_class AND $from_year === $to_year) OR ( $is_only_section_change  == 'on' ) ){
            $x_set_value = [ 'scm_status' => $to_stat ];
            if( $is_only_section_change  == 'on' ){
                $x_set_value = $x_set_value + [ 'scm_session_year' => $to_year, 'scm_class_id' => $to_class];
            }
            
            $change_status  = service('CoursesClassesStudentsMappingModel')
                    ->where([ 
                        'scm_session_year' => $from_year,
                        'scm_class_id'  => $from_class, 
                        'scm_status'    => $from_stat,
                        ])
                    ->whereIn( 'scm_u_id', $list_ids )->set($x_set_value)->update();
            if($change_status){
                               
                $affected_rows = service('CoursesClassesStudentsMappingModel')->db->affectedRows();
                @session_start(); // Reverse session_write_close?
                return redirect()->to(base_url("admin/admission/step/up/down"))->with('display_msg', get_display_msg("Status updated ($affected_rows) successfully.",'success'));
            }else{
                $msg = get_display_msg(lang('Rw.unable_to_update_status'),'danger');
                if($affected_rows < 1) $msg .= get_display_msg (implode(' ', (array) service('CoursesClassesStudentsMappingModel')->errors()),'danger');
                @session_start(); // Reverse session_write_close?
                return redirect()->to(base_url("admin/admission/step/up/down?from_class={$from_class}&from_year=".esc($from_year)."&from_status=".esc($from_stat)))->with('display_msg',$msg);
            }
        }
        
        // Admin requrested to upgrade/downgrade students from one class to another class/session.
        $optional_courses   = [];
        $mandatoray_courses = [];
        $allowed_course_ids = service('CoursesClassesMappingModel')->select('ccm_course_id,ccm_is_compulsory')->where('ccm_class_id', $to_class)->findAll(40,0); // Expected class is 15 + 5 = 20 maximum
        foreach( $allowed_course_ids as $crsObj){
            if( $crsObj->ccm_is_compulsory ){ $mandatoray_courses[] = $crsObj->ccm_course_id; }else{ $optional_courses[] = $crsObj->ccm_course_id; }
        }
        
        if(count($mandatoray_courses) < 3){
            $msg = get_display_msg(myLang("Mandatory courses not set for this class ID: ".$to_class." and session: " . esc($to_year) . ". Manage class wise course " . anchor("admin/academic/course/distribution?class_id={$to_class}&cls_wise_class_session=" . esc($to_year),'from here',['class'=>'btn btn-info']),"Class ID: ".$to_class." ও শিক্ষবর্ষ: " . esc($to_year) . " এর জন্য বাধ্যতামূলক পঠিত বিষয়সমূহ নির্ধারণ করা হয়নি। অনুগ্রহ করে ".anchor("admin/academic/course/distribution?class_id={$to_class}&cls_wise_class_session=" . esc($to_year),'এখান থেকে',['class'=>'btn btn-info'])." পঠিত বিষয়সমূহ নিশ্চিত করুন।"), 'danger');
            @session_start(); // Reverse session_write_close?
            return redirect()->to(base_url("admin/admission/step/up/down?from_class={$from_class}&from_year=".esc($from_year)."&from_status=".esc($from_stat)))->with('display_msg',$msg);
        }
        
        $saveData = ['scm_status' => $to_stat ];
        for( $i = 0; $i < 15; $i ++ ){ $saveData['scm_course_' . ($i+1)] = isset($mandatoray_courses[$i]) ? $mandatoray_courses[$i] : NULL; } /* 15 mandatory courses */
        for( $i = 0; $i < 5; $i ++ ){ $saveData['scm_course_op_' . ($i+1)] = isset($optional_courses[$i]) ? $optional_courses[$i] : NULL; }  /* 5 optional courses */
        
        $updated_rows = 0;
        $inserted_rows = 0;
        $errors = [];
        // Loop through each student IDs
        foreach($list_ids as $studentID){
            $userIdentityData = [ 
                'scm_u_id'          => $studentID,
                'scm_session_year'  => $to_year, 
                'scm_class_id'      => $to_class,
                ];
            // Admin might delete some students, make sure no duplicate is added. So add ->withDeleted()
            $existing = service('CoursesClassesStudentsMappingModel')->withDeleted()->select('scm_id')->where($userIdentityData)->first(); // We will updated if already added

            $newDataToSave = $saveData + $userIdentityData;
            
            
            
            if( is_object( $existing )){
                if( service('CoursesClassesStudentsMappingModel')->update($existing->scm_id, $newDataToSave)){
                    $updated_rows++;
                }else{
                    $errors[] = implode(' ', service('CoursesClassesStudentsMappingModel')->errors());
                }
            }else{
                if( service('CoursesClassesStudentsMappingModel')->insert($newDataToSave)){
                    $insertID = service('CoursesClassesStudentsMappingModel')->insertID();
                    $inserted_rows++;
                }else{
                    $errors[] = implode(' ', service('CoursesClassesStudentsMappingModel')->errors());
                }
            }
        }
        $msg = get_display_msg("Information saved. Number of students added is: $inserted_rows and updated $updated_rows and your request was ".count($list_ids)." students.", 'success');
        if(count($errors) > 0 ) $msg .= get_display_msg(implode(' ', $errors), 'danger'); // Add error messages, if exists any
        
        @session_start(); // Reverse session_write_close?
        return redirect()->to(base_url("admin/admission/step/up/down"))->with('display_msg',$msg);
    } /* EOM */
    
} /* EOC */
