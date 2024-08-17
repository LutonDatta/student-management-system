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

class Admission_edit_application extends BaseController {
    
    public function edit_admission_application(){
        session_write_close(); 
        $data                       = $this->data; 
        $data['title']              = 'Student Admission';
        $data['pageTitle']          = 'Student Admission';
        $data['loadedPage']         = 'edit_application';
        
        $data['updating_student']   = service('StudentsModel')->withDeleted()->find(intval($this->request->getPostGet('student_id')));
        
        // CAUTON: Some person admin/teacher do not go to apply to classes. But want to update personal info. So allow them
        $saveUserData = $this->save_submitted_profile_information_basic_info($data['updating_student']);
        if( is_object($saveUserData) ){ return $saveUserData; }else{ $data = array_merge( $data, $saveUserData ); }
            
        $saveUserData = $this->save_submitted_profile_information_address_info($data['updating_student']);
        if( is_object($saveUserData) ){ return $saveUserData; }else{ $data = array_merge( $data, $saveUserData ); }

        // CAUTON: Some person admin/teacher do not go to apply to classes. But want to update personal info. So allow them
        $saveUserData = $this->save_submitted_profile_information_identity_info($data['updating_student']);
        if( is_object($saveUserData) ){ return $saveUserData; }else{ $data = array_merge( $data, $saveUserData ); }

        
        /* Save/update courses for new/old admission */
        $svSltdCrses = $this->save_selected_courses($data['updating_student']);
        if(is_object($svSltdCrses)){ return $svSltdCrses; }else{ $data = array_merge( $data, $svSltdCrses ); }
        
        /* Save/update courses for new/old admission */
        $svSltdCrses = $this->delete_incomplete_user_row();
        if(is_object($svSltdCrses)){ return $svSltdCrses; }else{ $data = array_merge( $data, $svSltdCrses ); }
        
        
        $types = ['address-info','basic-info','identity-info','photo-editor','select-courses','select-class'];
        $editT = $this->request->getGet('InfoPage');
        
        if($editT == 'select-class'){
            // Get a list of classes, to allow admin to select a class to get a student admitted
            $data['admitAbleClasses']   = service('ClassesAndSemestersModel')->get_classes_with_parent_label_for_dropdown(true, 'clsprts', 20, true);
            $data['admitAbleClassesPgr']= service('ClassesAndSemestersModel')->pager->links('clsprts');
        }
        
        // This is final stage, here we must need a valid USER OBJECT and CLASS OBJECT
        if($editT == 'select-courses'){
            if( ! is_object($data['updating_student'])){
                @session_start(); return redirect()->to(base_url("admin/admission/edit/application/by/admin"))->with('display_msg',get_display_msg('Please update student basic and other information first. User Student ID not found.','danger'));       
            }
            $data['selectedClass'] = service('ClassesAndSemestersModel')->get_single_class_with_parent_label(intval($this->request->getGet('class_to_admit_in')));
            if( ! is_object( $data['selectedClass'] )){
                $user_id = is_object($data['updating_student']) ? intval($data['updating_student']->student_u_id) : 0;
                @session_start(); return redirect()->to(base_url("admin/admission/edit/application/by/admin?InfoPage=select-class&student_id={$user_id}"))->with('display_msg',get_display_msg('Please select class and session to admit in. Valid Class ID not found.','danger'));
            }
            $data['selectedSession'] = strval($this->request->getGet('session_to_admit_in'));
            $data = $this->load_necessary_data_for_courses_selector_page($data, $data['selectedClass'], $data['selectedSession']);
        }
                
        $data['inAdStuList'] = service('StudentsModel')
                ->select('student_u_id,student_u_name_initial,student_u_name_first,student_u_name_middle,student_u_name_last,student_u_father_name,student_u_date_of_birth,student_u_gender,student_u_religion')
                ->whereNotIn("student_u_id", function($b){ return $b->select("courses_classes_students_mapping.scm_u_id")->from('courses_classes_students_mapping');})
                ->paginate(15,'inCompleteAdmission');
        $data['inAdStuListPgr'] = service('StudentsModel')->pager->links('inCompleteAdmission');
                
        echo view('SchoolBackViews/head', $data);
        echo view('SchoolBackViews/_parts/nav-left', $data);
        echo view('SchoolBackViews/_parts/nav-top', $data); 
        echo view('SchoolBackViews/admission-edit-application/ad-profile-editor-' . (in_array($editT,$types) ? $editT : 'basic-info'), $data);
        echo view('SchoolBackViews/_parts/nav-bottom', $data);
        echo view('SchoolBackViews/footer', $data);
    } /* EOM */
    
    private function load_necessary_data_for_courses_selector_page( $data, $clsObj, $session ){
        $admit_to_class_id = is_object($clsObj) ? intval($clsObj->fcs_id) : 0;
        $admit_to_session  = strval($session);
        $data['coursesUnderClassM']             = service('CoursesModel')->withDeleted()->get_courses_by_class_id($admit_to_class_id,true,25,0,'courses.co_id,courses.co_title',$admit_to_session);
        $data['coursesUnderClassO']             = service('CoursesModel')->withDeleted()->get_courses_by_class_id($admit_to_class_id,false,25,0,'courses.co_id,courses.co_title',$admit_to_session);
        $data['coursesUnderClassAlreadySaved']  = $this->get_already_saved_course_ids($data['updating_student'],$admit_to_class_id,$admit_to_session);
        return $data;    
    } /* EOM */
    
    private function delete_incomplete_user_row(){
        if($this->request->getPost('dInAdStudent') !== 'yes' ) return []; else $data = []; 
        $uid = intval($this->request->getPost('delStuUID'));
        if($uid < 1 ){
            @session_start(); return redirect()->to(base_url('admin/admission/edit/application/by/admin'))->with('display_msg', get_display_msg('Invalid user ID.','danger'));
        }
        $del = service('StudentsModel')->delete($uid,true);
        if($del){
            @session_start(); return redirect()->to(base_url('admin/admission/edit/application/by/admin'))->with('display_msg', get_display_msg('Unnecessary data removed successfully.','success'));
        }else{
            @session_start(); return redirect()->to(base_url('admin/admission/edit/application/by/admin'))->with('display_msg', get_display_msg('Unnecessary data unable to remove.','danger'));
        }
    } // EOF
    
    
    /**
     * Show selected courses using this ids.
     */
    private function get_already_saved_course_ids( $user, $admit_to_class_id, $admit_to_session ){
        $ids = [];
        $row = service('CoursesClassesStudentsMappingModel')
                // ->withDeleted() // might generate error isn't in GROUP BY 
                ->where([
                'scm_u_id'          => $user->student_u_id,
                'scm_session_year'  => $admit_to_session,
                'scm_class_id'      => $admit_to_class_id,   
            ])->first(); 
        if( is_object( $row ) ){
            for($i=1;$i<=5;$i++){
                $col = $row->{'scm_course_op_' . $i };
                if( intval( $col ) > 0 ) $ids[] = intval( $col );
            }
            for($i=1;$i<=15;$i++){
                $col = $row->{'scm_course_' . $i };
                if( intval( $col ) > 0 ) $ids[] = intval( $col );
            }
        }
        return $ids;
    } /* EOM */
//    
    private function save_selected_courses( $user ){
        if($this->request->getPost('saveCourse') !== 'yes' ) return []; else $data = []; 
        
        $class_to_admit_in = intval($this->request->getPost('class_to_admit_in'));
        $session_to_admit_in = strval($this->request->getPost('session_to_admit_in'));
        
        $courses        = (array) $this->request->getPost('admissionCourseSelection');
        $courses        = array_unique($courses,SORT_NUMERIC);
        
        if( ! is_array( $courses ) )    return ['display_msg' => get_display_msg( 'Selected course items is not an array.','danger')];
        if( count( $courses ) < 3 )     return ['display_msg' => get_display_msg( 'You must select at least 3 courses.','danger')];
        
        // Validate requested coures IDs are allowed by admin
        $findids = service('CoursesClassesMappingModel')
                ->select('ccm_course_id,ccm_is_compulsory')
                ->where('ccm_class_id', $class_to_admit_in);
        if(strlen($session_to_admit_in) > 0){
            $findids = $findids->where('ccm_year_session', $session_to_admit_in);
        }

        $allowed_course_ids = $findids->findAll(40,0);
        
        $allowed_clean_ids = [];
        foreach( $allowed_course_ids as $ob ){ $allowed_clean_ids[$ob->ccm_course_id] = $ob->ccm_is_compulsory; }
        
        // Loop through items, we accept only 15 mandatory and 5 opional courses for a single class
        $optional_courses   = [];
        $mandatoray_courses = [];
        foreach( $courses as $crs ){
            $crs = intval($crs);
            if( ! array_key_exists($crs, $allowed_clean_ids)) continue; // This id is not allowed
            if(intval($allowed_clean_ids[$crs]) > 0){
                $mandatoray_courses[] = $crs;
            }else{
                $optional_courses[] = $crs;
            }
        }
        
        if( count( $optional_courses ) > 5 )    return ['display_msg' => get_display_msg( 'You can not select more then 5 optional courses.','danger')];
        if( count( $mandatoray_courses ) > 15 ) return ['display_msg' => get_display_msg( 'You can not select more then 15 mendatory courses.','danger')];
        if( count( $mandatoray_courses ) < 3 ) return ['display_msg' => get_display_msg( 'You must select at least 3 mandatory courses.','danger')];
        
        /**
         * CAUTION: Always keep course IDs in ascending order from smaller to larger. This order will always be same
         * if student select the same courses for his class. We save exam results marks by column name, so must prevent
         * students to remove/add courses if exam results are saved to the DB. 
         **/
        sort($mandatoray_courses); sort($optional_courses);
        
        $saveMe = [
            'scm_u_id'          => $user->student_u_id,
            'scm_session_year'  => $session_to_admit_in,
            'scm_class_id'      => $class_to_admit_in,
            'scm_status'        => 'admitted', // As teacher added this student, so mark him admitted, to reduce extra marking work
        ];
        // Admin might delete this application, make sure that user can not apply for the same post again. So add ->withDeleted()
        // We will updated if already admitted, if applied, update previous data
        $existing = service('CoursesClassesStudentsMappingModel')->withDeleted()->select('scm_id')->where($saveMe)->limit(1,0)->first();
        
        
        for( $i = 0; $i < 15; $i ++ ){ /* 15 mandatory courses */
            $saveMe['scm_course_' . ($i+1)] = isset($mandatoray_courses[$i]) ? $mandatoray_courses[$i] : NULL;
        }
        for( $i = 0; $i < 5; $i ++ ){ /* 5 optional courses */
            $saveMe['scm_course_op_' . ($i+1)] = isset($optional_courses[$i]) ? $optional_courses[$i] : NULL;
        }
        
        if( is_object( $existing )){
            // Remove previously saved data to add or update new data. if we do not remove all previously added data then we can not remove only one item form the selected list.
            $clear_previous_data = [];
            for( $i = 0; $i < 15; $i ++ ){ $clear_previous_data['scm_course_' . ($i+1)] = NULL; }
            for( $i = 0; $i < 5; $i ++ ){ $clear_previous_data['scm_course_op_' . ($i+1)] = NULL; }
            service('CoursesClassesStudentsMappingModel')->clear_validation_rules()->update($existing->scm_id, $clear_previous_data);
        
            if( service('CoursesClassesStudentsMappingModel')->update($existing->scm_id, $saveMe)){
                                
                $display_msgx   = get_display_msg('Student admission updated successfully.','success');
                $rtrToUrl       = "admin/admission/edit/application/by/admin?InfoPage=photo-editor&scm_id={$existing->scm_id}&student_id={$user->student_u_id}";
                @session_start(); return redirect()->to(base_url($rtrToUrl))->with('display_msg', $display_msgx);
            }else{
                $data['display_msg'] = get_display_msg('Unable to update courses.' . implode(' ', service('CoursesClassesStudentsMappingModel')->errors()),'danger');
            }
        }else{
            if( service('CoursesClassesStudentsMappingModel')->insert($saveMe)){
                $insertID       = service('CoursesClassesStudentsMappingModel')->insertID();     
                
                $display_msgx   = get_display_msg('Student admitted successfully.','success');
                $rtrToUrl       = "admin/admission/edit/application/by/admin?InfoPage=photo-editor&scm_id={$insertID}&student_id={$user->student_u_id}";
                @session_start(); return redirect()->to(base_url($rtrToUrl))->with('display_msg', $display_msgx);
            }else{
                $data['display_msg'] = get_display_msg('Unable to save courses.' . implode(' ', service('CoursesClassesStudentsMappingModel')->errors()),'danger');
            }
        }
        
        return $data;
    } /* EOM */
//    
    
//    
    private function save_submitted_profile_information_basic_info( $user ){
        if($this->request->getPost('saveProfileInfo_basic_admin') !== 'yes'){ return []; } else $data = []; 
        $user_id = is_object($user) ? intval($user->student_u_id) : 0;
        
        $save_data = [
            'student_u_name_initial'    => $this->request->getPost('name_initials'),
            'student_u_name_first'      => $this->request->getPost('name_f'),
            'student_u_name_middle'     => $this->request->getPost('name_m'),
            'student_u_name_last'       => $this->request->getPost('name_l'),
            'student_u_father_name'     => $this->request->getPost('name_father'),
            'student_u_mother_name'     => $this->request->getPost('name_mother'),
            'student_u_gender'          => $this->request->getPost('gender'),
            'student_u_religion'        => $this->request->getPost('religion'),
            'student_u_date_of_birth'   => date('Y-m-d H:i:s', strtotime( $this->request->getPost('dofb') ) ),
        ];
        if( $user_id > 0 ){
            if( ! service('StudentsModel')->update($user_id, $save_data )){
                $dbErros = (array) service('StudentsModel')->errors();
                return [ 'errors' => $dbErros, 'display_msg'=> get_display_msg('Failed to update information. ' . implode(', ',$dbErros),'danger')];
            }
        }else{
            if( ! service('StudentsModel')->insert($save_data)){
                $dbErros = (array) service('StudentsModel')->errors();
                return [ 'errors' => $dbErros, 'display_msg'=> get_display_msg('Failed to insert information. ' . implode(', ',$dbErros),'danger')];
            }
            // We have found new userID, Caution: next processing will be depend on it.
            $user_id = service('StudentsModel')->insertID(); // Added to redirect url, to update next information to this row.
        }
        @session_start(); // Reverse session_write_close?
        return redirect()
                ->to(base_url("admin/admission/edit/application/by/admin?student_id={$user_id}&InfoPage=address-info"))
                ->with('display_msg',get_display_msg('User basic information has been updated successfully.','success'));       
    } /* EOM */
//    
//    
    private function save_submitted_profile_information_address_info(  $user ){
        if($this->request->getPost('saveProfileInfo_address') !== 'yes'){ return []; } else $data = []; 
        $user_id = is_object($user) ? intval($user->student_u_id) : 0;
        
        $save_data = [
            'student_u_addr_country'    => $this->request->getPost('country'),
            'student_u_addr_state'      => $this->request->getPost('state'),
            'student_u_addr_district'   => $this->request->getPost('district'),
            'student_u_addr_thana'      => $this->request->getPost('thana'),
            'student_u_addr_post_office'=> $this->request->getPost('post_office'),
            'student_u_addr_zip_code'   => $this->request->getPost('post_code'),
            'student_u_addr_village'    => $this->request->getPost('village'),
            'student_u_addr_road_house_no'  => $this->request->getPost('road_house'),
        ];
        if( $user_id > 0 ){
            if( ! service('StudentsModel')->update($user_id, $save_data )){
                $dbErros = (array) service('StudentsModel')->errors();
                return [ 'errors' => $dbErros, 'display_msg'   => get_display_msg('Failed to update user information. ' . implode(', ', $dbErros ),'danger') ];
            }
        }else{
            if( ! service('StudentsModel')->insert($save_data)){
                $dbErros = (array) service('StudentsModel')->errors();
                return [ 'errors' => $dbErros, 'display_msg'=> get_display_msg('Failed to insert information. ' . implode(', ',$dbErros),'danger')];
            }
            // We have found new userID, Caution: next/previous page update will be depend on it.
            $user_id = service('StudentsModel')->insertID(); // Added to redirect url, to update next/previous page info to this row.
        }
        
        @session_start(); // Reverse session_write_close?
        return redirect()
                ->to(base_url("admin/admission/edit/application/by/admin?student_id={$user_id}&InfoPage=identity-info"))
                ->with('display_msg',get_display_msg(myLang('User address information has been updated successfully.','ঠিকানা সম্পর্কিত তথ্যাবলী সফলভাবে সংরক্ষণ করা হয়েছে।'),'success'));
    } /* EOM */
//    
//    
    private function save_submitted_profile_information_identity_info( $user ){
        if($this->request->getPost('saveProfileInfo_identity_admin') !== 'yes'){ return []; } else $data = []; 
        $user_id = is_object($user) ? intval($user->student_u_id) : 0;
        
        $save_data = [
            'student_u_mobile_own'      => $this->request->getPost('student_mobile'),
            'student_u_email_own'       => $this->request->getPost('student_email'),
            'student_u_nid_no'          => $this->request->getPost('nid'),
            'student_u_birth_reg_no'    => $this->request->getPost('b_reg'),
            'student_u_mobile_father'   => $this->request->getPost('mobile_fa'),
            'student_u_mobile_mother'   => $this->request->getPost('mobile_ma'),
        ];
        
        if( $user_id > 0 ){
            if( ! service('StudentsModel')->update($user_id, $save_data )){
                $dbErros =  (array) service('StudentsModel')->errors();
                return [ 'errors' => $dbErros, 'display_msg'   => get_display_msg('Failed to update user information. ' . implode(', ', $dbErros ),'danger') ];
            }
        }else{
            if( ! service('StudentsModel')->insert($save_data)){
                $dbErros = (array) service('StudentsModel')->errors();
                return [ 'errors' => $dbErros, 'display_msg'=> get_display_msg('Failed to insert contact information. ' . implode(', ',$dbErros),'danger')];
            }
            // We have found new userID, Caution: next/previous page update will be depend on it.
            $user_id = service('StudentsModel')->insertID(); // Added to redirect url, to update next/previous page info to this row.
        }
        
        @session_start(); // Reverse session_write_close to show display message.
        return redirect()
                ->to(base_url("admin/admission/edit/application/by/admin?student_id={$user_id}&InfoPage=select-class"))
                ->with('display_msg',get_display_msg(myLang('User identity and contact information has been updated successfully.','পরিচয় ও যোগাযোগ সম্পর্কিত তথ্যাবলী সফলভাবে সংরক্ষণ করা হয়েছে।'),'success'));
    } /* EOM */
//    
} /* EOC */
