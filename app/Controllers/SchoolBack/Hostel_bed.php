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


class Hostel_bed extends BaseController {
    
    public function rooms_distribution(){
        $data                       = $this->data; // We have some pre populated data here
        $data['pageTitle']          = 'Hostel Rooms Distribution';
        $data['title']              = 'Hostel Rooms Distribution';
        $data['loadedPage']         = 'hostel_bed_ditribution';// Used to automatically expand submenu and add active class 
        
        $singleSid = intval($this->request->getGet('student_id')); // SID, previously u_id
        $students_obj = service('CoursesClassesStudentsMappingModel')
                ->select(implode(',',[
                    'student_u_id','student_u_name_initial','student_u_name_first','student_u_name_middle','student_u_name_last','student_u_gender',
                    'student_u_father_name','student_u_mother_name', 'scm_c_roll','scm_deleted_at','scm_class_id','scm_session_year','scm_status',
                ]))
                ->orderBy('scm_id','DESC')
                ->join('students','students.student_u_id = courses_classes_students_mapping.scm_u_id','left');
        if($singleSid > 0 ){ 
            $students_obj->where('student_u_id', $singleSid);
            $data['selectedStudent'] = service('StudentsModel')->select('student_u_id,student_u_name_initial,student_u_name_first,student_u_name_last,student_u_name_middle,student_u_mother_name,student_u_father_name')->find($singleSid);
        }                      
        $data['students_list']  = $students_obj->withDeleted()->paginate(15,'hostel_student_list');
        $data['studentsLstPgr'] = service('CoursesClassesStudentsMappingModel')->pager->links('hostel_student_list');
        
        $data['hostelRooms']    = service('HostelAndRoomsModel')->get_hostel_room_with_parent_label_with_pagination(true,'hostel_room_list');
        $data['hostelRoomsPgr'] = service('HostelAndRoomsModel')->pager->links('hostel_room_list');
        
        echo view('SchoolBackViews/head', $data);
        echo view('SchoolBackViews/_parts/nav-left', $data);
        echo view('SchoolBackViews/_parts/nav-top', $data); 
        echo view('SchoolBackViews/hostel/rooms-distribution', $data);
        echo view('SchoolBackViews/_parts/nav-bottom', $data);
        echo view('SchoolBackViews/footer', $data);
    } /* EOM */
    
    
} // End class


