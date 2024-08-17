<?php namespace App\Controllers\SchoolFront;

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
 * After admission of a student, we need to print confirmed admission form of the admitted student.
 */
class Print_student_admission extends BaseController {
    
    public function confirmation_form(){
        session_write_close(); 
        
        $user_id   = intval($this->request->getGet('user_id')); /* Print information of this user */
        
        $student_info = service('StudentsModel')->find($user_id);
        if( ! is_object($student_info)){
            return view('err/general-error', [
                    'error_title'   => 'Invalid ID error',
                    'error_header'  => 'Invalid user ID',
                    'error_message' => 'User ID that you have supplied is wrong. You can not see information of it..'
                ]);
        }
        
        $data                   = $this->data;
        $data['title']          = "Print Admission Confirmation Form";
        $data['metaDescription']= "Online Admission Confirmation Form ";
        $data['metaKeywords']   = 'Online Admission Confirmation Form powered by Ultra School';
        
        $data['scmList']        = service('CoursesClassesStudentsMappingModel')
                ->withDeleted()
                ->where('scm_u_id',$user_id)
                ->orderBy('scm_id','DESC')
                ->paginate(20, 'print_view_scm');
        foreach($data['scmList'] as $idx => $scmObj){
            $class = service('ClassesAndSemestersModel')->get_single_class_with_parent_label($scmObj->scm_class_id);
            if(is_object($class)){
                $data['scmList'][$idx] = (object) array_merge( (array) $scmObj, (array)$class );
            }
        }
        $data['selectedSCM'] = $application = service('CoursesClassesStudentsMappingModel')->withDeleted()->find(intval($this->request->getGet('scm_id')));
        $data['udr']            = $student_info;
        
        if( ! is_object($data['selectedSCM'])){
            $data['display_msg'] = get_display_msg('SCM not provided or invalid ID privided. Please select from the top of the page. Make sure that User ID is correct before submission.','danger');
        }else{
            $data['oneApplication'] = [
                'cls_appi' => $application,
                'cls_data' => service('ClassesAndSemestersModel')->get_single_class_with_parent_label($application->scm_class_id),
                'cls_mand' => service('CoursesModel')->get_courses_by_class_id( $application->scm_class_id, true ), // Mandatory courses
                'cls_opti' => service('CoursesModel')->get_courses_by_class_id( $application->scm_class_id, false ), // Optional courses
                'cls_sltd' => $this->get_already_saved_course_ids(
                        (object)[
                            'ao_year'           =>$application->scm_session_year,
                            'ao_target_class'   =>$application->scm_class_id
                        ],$student_info),
            ];
        }
        
        return view('SchoolFrontViews/print-info/student-admission-confirmation-form', $data );
    } // EOM
    
    /**
     * Show selected courses using this ids.
     */
    private function get_already_saved_course_ids($admitToClass, $user){
        $ids = [];
        $row = service('CoursesClassesStudentsMappingModel')->where([
                'scm_u_id'          => $user->student_u_id,
                'scm_session_year'  => $admitToClass->ao_year,
                'scm_class_id'      => $admitToClass->ao_target_class,   
                'scm_status'        => 'admitted',
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
    } // EOM
   
} // EOC
