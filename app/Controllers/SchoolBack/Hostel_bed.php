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
        
        $saveBS = $this->book_hostel_seat_save();
        if(is_object($saveBS)){ return $saveBS; }else{ $data = array_merge($data, $saveBS); }
        
        $cancelBS = $this->book_hostel_seat_cancel_for_student();
        if(is_object($cancelBS)){ return $cancelBS; }else{ $data = array_merge($data, $cancelBS); }
        
        $cancelBR = $this->book_hostel_seat_cancel_for_rooms();
        if(is_object($cancelBR)){ return $cancelBR; }else{ $data = array_merge($data, $cancelBR); }
        
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
        
        $data['selectedRoom']    = service('HostelAndRoomsModel')->get_single_hostel_room_with_parent_label(intval($this->request->getGet('hostel_room_id')));
        
        echo view('SchoolBackViews/head', $data);
        echo view('SchoolBackViews/_parts/nav-left', $data);
        echo view('SchoolBackViews/_parts/nav-top', $data); 
        echo view('SchoolBackViews/hostel/rooms-distribution', $data);
        echo view('SchoolBackViews/_parts/nav-bottom', $data);
        echo view('SchoolBackViews/footer', $data);
    } /* EOM */
    
    
    private function book_hostel_seat_cancel_for_rooms(){
        if($this->request->getPost('hostel_cancel_room_seat') !== 'yes'){ return []; }
        
        $seat_number    = intval($this->request->getPost('hostel_seat_no'));
        $student_id     = intval($this->request->getPost('student_id'));
        $hostel_room_id = intval($this->request->getPost('hostel_room_id'));
        $hostel_room_id_rdr = intval($this->request->getPost('hostel_room_id_showing_in_page'));
        $redirectLink   = base_url("admin/hostel/bed/distribution?student_id={$student_id}&hostel_room_id={$hostel_room_id_rdr}");
        
        $isOccupied = service('HostelRoomsBookingModel')->select('hrb_id')->where('hrb_student_id',$student_id)->where('hrb_hos_id',$hostel_room_id)->where('hrb_seat_no', $seat_number)->first();
        if( ! is_object($isOccupied)){
            return redirect()->to($redirectLink)->with('display_msg', get_display_msg('This Seat is not occupied.','danger'));
        }
        $insert = service('HostelRoomsBookingModel')->delete($isOccupied->hrb_id, true);
        if($insert){
            return redirect()->to($redirectLink)->with('display_msg', get_display_msg('Seat cancelled Successful.','success'));
        }else{
            $errors = service('HostelRoomsBookingModel')->errors();
            $errStr = implode(', ', $errors);
            return redirect()->to($redirectLink)->with('display_msg', get_display_msg('Failed to cancel seat. Please try again. ' . $errStr,'danger'));
        }
        
    } /* EOM */
    
    
    private function book_hostel_seat_cancel_for_student(){
        if($this->request->getPost('hostel_cancel_sid_seat') !== 'yes'){ return []; }
        
        $seat_number    = intval($this->request->getPost('hostel_seat_no'));
        $student_id     = intval($this->request->getPost('student_id'));
        $hostel_room_id = intval($this->request->getPost('hostel_room_id'));
        $hostel_room_id_rdr = intval($this->request->getPost('hostel_room_id_showing_in_page'));
        $redirectLink   = base_url("admin/hostel/bed/distribution?student_id={$student_id}&hostel_room_id={$hostel_room_id_rdr}");
        
        $isOccupied = service('HostelRoomsBookingModel')->select('hrb_id')->where('hrb_student_id',$student_id)->where('hrb_hos_id',$hostel_room_id)->where('hrb_seat_no', $seat_number)->first();
        if( ! is_object($isOccupied)){
            return redirect()->to($redirectLink)->with('display_msg', get_display_msg('This Seat is not occupied.','danger'));
        }
        $insert = service('HostelRoomsBookingModel')->delete($isOccupied->hrb_id, true);
        if($insert){
            return redirect()->to($redirectLink)->with('display_msg', get_display_msg('Seat cancelled Successful.','success'));
        }else{
            $errors = service('HostelRoomsBookingModel')->errors();
            $errStr = implode(', ', $errors);
            return redirect()->to($redirectLink)->with('display_msg', get_display_msg('Failed to cancel seat. Please try again. ' . $errStr,'danger'));
        }
        
    } /* EOM */
    
    private function book_hostel_seat_save(){
        if($this->request->getPost('hostel_room_book') !== 'yes'){ return []; }
        
        $seat_number    = intval($this->request->getPost('seat_number'));
        $student_id     = intval($this->request->getPost('student_id'));
        $hostel_room_id = intval($this->request->getPost('hostel_room_id'));
        $redirectLink   = base_url("admin/hostel/bed/distribution?student_id={$student_id}&hostel_room_id={$hostel_room_id}");
        
        $student        = service('StudentsModel')->find($student_id);
        if( ! is_object($student)){
            return redirect()->to($redirectLink)->with('display_msg', get_display_msg('Invalid Student ID','danger'));
        }
        
        $hostel_room    = service('HostelAndRoomsModel')->find($hostel_room_id);
        if( ! is_object($hostel_room)){
            return redirect()->to($redirectLink)->with('display_msg', get_display_msg('Invalid Hostel Room ID','danger'));
        }
        
        $isOccupied = service('HostelRoomsBookingModel')->where('hrb_hos_id',$hostel_room_id)->where('hrb_seat_no', $seat_number)->first();
        if(is_object($isOccupied)){
            return redirect()->to($redirectLink)->with('display_msg', get_display_msg('This Seat is already occupied.','danger'));
        }
        $insert = service('HostelRoomsBookingModel')->insert([
                                            'hrb_hos_id'    => $hostel_room_id,
                                            'hrb_seat_no'   => $seat_number,
                                            'hrb_student_id'=> $student_id
                                        ]);
        if($insert){
            return redirect()->to($redirectLink)->with('display_msg', get_display_msg('Seat Booking Successful.','success'));
        }else{
            $errors = service('HostelRoomsBookingModel')->errors();
            $errStr = implode(', ', $errors);
            return redirect()->to($redirectLink)->with('display_msg', get_display_msg('Failed to book seat. Please try again. ' . $errStr,'danger'));
        }
    } /* EoF */
    
} // End class


