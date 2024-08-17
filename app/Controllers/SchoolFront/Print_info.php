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
 * Only printable form.
 * Admin can print students information.
 * Student can print his own information.
 */
class Print_info extends BaseController {
    
    public function print_students_information(){ 
        session_write_close(); 
        
        $user_id   = intval($this->request->getGet('user_id')); /* Print information of this user */
        
        $student_info = service('StudentsModel')->limit(1)->withDeleted()->find($user_id);
        if( ! is_object($student_info)){
            return view('err/general-error', [
                    'error_title'   => 'Invalid ID error',
                    'error_header'  => 'Invalid user ID',
                    'error_message' => 'User ID that you have supplied is wrong. You can not see information of it..'
                ]);
        }
        
        $data                   = $this->data;
        $data['title']          = "Print information [" . service('AuthLibrary')->getLoggedInUserID()."]";
        $data['metaDescription']= "Online admission ";
        $data['metaKeywords']   = 'Online admission powered by Ultra School';        
        $data['udr']            = $student_info;
        
        $classTo = service('ClassesAndSemestersModel')->get_single_class_with_parent_label(intval($this->request->getGet('apply_to_class_id')));
        if( ! is_object($classTo)){
            $data['display_msg'] = get_display_msg(myLang('Invalid class ID provided.','যে ক্লাশ আইডি পাওয়া গেছে তা সঠিক নয়।'),'danger');
        }else{
            $data['classData']      = $classTo;
            $data['classCoursesMan']= service('CoursesModel')->get_courses_by_class_id( $classTo->fcs_id, true ); // Mandatory courses
            $data['classCoursesOpt']= service('CoursesModel')->get_courses_by_class_id( $classTo->fcs_id, false ); // Optional courses
            $data['classApplication'] = service('CoursesClassesStudentsMappingModel')
                        ->join('students','students.student_u_id = courses_classes_students_mapping.scm_u_id','LEFT')
                        //->withDeleted() // Trigger groupBy error
                        ->where([ 
                            'scm_u_id'          => $student_info->student_u_id, 
                            'scm_class_id'      => $classTo->fcs_id,
                            'scm_session_year'  => urldecode(service('request')->getGet('sess_year'))
                        ])->first();
            $data['classCoursesStd'] = $this->get_already_saved_course_ids($data['classApplication']);
        }
        
        return view('SchoolFrontViews/print-info/print-student-information', $data );
    } // EOM
    
    /**
     * Show selected courses using this ids.
     */
    private function get_already_saved_course_ids($row){
        $ids = [];
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
