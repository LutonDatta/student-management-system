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
class Admitted_student_list extends BaseController {
    
    public function class_wise_list(){
        
        // Perform the following actions from get request, do not need to return any value, show error message from event
        $this->move_student_to_trash(); 
        $this->get_back_student_from_trash(); 
        $this->erase_student_from_trash_permanently(); 
        
        $data                       = $this->data;
        $data['title']              = lang('Admin.class_wise_student_list');
        $data['pageTitle']          = lang('Admin.class_wise_student_list');
        $data['loadedPage']         = 'student_list_x';
        $data['srcData']            = array(
            'status'                =>  strval($this->request->getGet('status')),
            'session_year'          =>  strval($this->request->getGet('year')),
            'class_id'              =>  intval( $this->request->getGet('class'))
        );
        
        $data['clsList']            = service('ClassesAndSemestersModel')->get_classes_with_parent_label_for_dropdown(false,'clsList',20,false);
        $data['allSessions']        = (array) service('CoursesClassesStudentsMappingModel')
                                    ->asArray()
                                    ->distinct()
                                    ->findColumn('scm_session_year');
        $data['selectionNotice']    = array_key_exists($data['srcData']['class_id'], $data['clsList']) 
                                        ? get_display_msg( 
                                                'You are showing students of <strong>' . $data['clsList'][$data['srcData']['class_id']] 
                                                . '</strong> of Session <strong>' . $data['srcData']['session_year'] 
                                                .'</strong> with status <strong>'. (get_student_class_status(false)[$data['srcData']['status']] ?? '(No Status)').'</strong>'
                                            ,'info')
                                        : get_display_msg('Please select class, session to show student list.','warning d-print-none');
        
        $students_list_obj = service('CoursesClassesStudentsMappingModel')
                ->select(implode(',',[
                    'student_u_id','student_u_name_initial','student_u_name_first','student_u_name_middle','student_u_name_last',
                    'student_u_father_name','student_u_mother_name', 'student_u_email_own','student_u_mobile_own',
                    'scm_id','scm_session_year','scm_c_roll','scm_deleted_at',
                    'scm_class_id','scm_session_year','scm_status',
                ]))
                ->orderBy('scm_id','DESC')
                ->join('user_students','user_students.student_u_id = courses_classes_students_mapping.scm_u_id','left');
        
        
        if(strlen($data['srcData']['status']) > 0 ){
            $students_list_obj->where('scm_status', $data['srcData']['status']);
        }
        if(strlen($data['srcData']['session_year']) > 0 ){
            $students_list_obj->where('scm_session_year', $data['srcData']['session_year']);
        }
        if($data['srcData']['class_id'] > 0 ){
            $students_list_obj->where('scm_class_id', $data['srcData']['class_id']);
        }
                      
        $data['students_list']  = $students_list_obj->withDeleted()->paginate(15,'front_student_list');
        $data['studentsLstPgr'] = service('CoursesClassesStudentsMappingModel')->pager->links('front_student_list');
        
        echo view('SchoolBackViews/head', $data);
        echo view('SchoolBackViews/_parts/nav-left', $data);
        echo view('SchoolBackViews/_parts/nav-top', $data); 
        echo view("SchoolBackViews/admitted-student-list/student-list-with-filter", $data);
        echo view('SchoolBackViews/_parts/nav-bottom', $data);
        echo view('SchoolBackViews/footer', $data);
    } // EOM
    
    private function move_student_to_trash(){        
        $scm_id = intval($this->request->getGet('move_student_scm_id_to_trash'));
        if( $scm_id < 1 ) { return null; }
        
        $obj = service('CoursesClassesStudentsMappingModel')->withDeleted()->find($scm_id);
        if( ! is_object($obj)){
            return $this->set_flash_message(get_display_msg("You are trying to trash item ({$scm_id}). But we failed to get this item to trash it.",'danger'));
        }
        
        if($obj->scm_deleted_at){
            return $this->set_flash_message(get_display_msg("This item is already in trash ({$scm_id}).",'danger'));
        }
        
        $markingTrash = service('CoursesClassesStudentsMappingModel')->withDeleted()->delete($scm_id);
        if($markingTrash){
            return $this->set_flash_message(get_display_msg("This item ({$scm_id}) moved to trash successfully.",'success'));
        }else{
            return $this->set_flash_message(get_display_msg("Failed to move item ({$scm_id}) to trash.",'danger'));
        }
    } // EOM 
    
    private function get_back_student_from_trash(){        
        $scm_id = intval($this->request->getGet('get_back_student_scm_id_from_trash'));
        if( $scm_id < 1 ) { return null; }
        
        $obj = service('CoursesClassesStudentsMappingModel')->withDeleted()->find($scm_id);
        if( ! is_object($obj)){
            return $this->set_flash_message(get_display_msg("You are trying to trash item ({$scm_id}). But we failed to get this item to trash it.",'danger'));
        }
        
        if( ! $obj->scm_deleted_at){
            return $this->set_flash_message(get_display_msg("This item is not ({$scm_id}) in trash.",'danger'));
        }

        $markingTrash = service('CoursesClassesStudentsMappingModel')->withDeleted()->update($scm_id, ['scm_deleted_at' => NULL ]);
        if($markingTrash){
            return $this->set_flash_message(get_display_msg("This item ({$scm_id}) recycled from trash successfully.",'success'));
        }else{
            return $this->set_flash_message(get_display_msg("Failed to recycle item ({$scm_id}) from trash.",'danger'));
        }
    } // EOM 
    
    private function erase_student_from_trash_permanently(){        
        $scm_id = intval($this->request->getGet('erase_student_scm_id_from_trash_permanently'));
        if( $scm_id < 1 ) { return null; }
        
        $obj = service('CoursesClassesStudentsMappingModel')->withDeleted()->find($scm_id);
        if( ! is_object($obj)){
            return $this->set_flash_message(get_display_msg("You are trying to trash item ({$scm_id}). But we failed to get this item to trash it.",'danger'));
        }
        
        if( ! $obj->scm_deleted_at){
            return $this->set_flash_message(get_display_msg("This item ({$scm_id}) is not in trash.",'danger'));
        }
        
        $hcInvCount = service('HandCashCollectionsModel')->withDeleted()->where('hc_scm_id', $scm_id)->countAllResults();
        if($hcInvCount > 0){
            return $this->set_flash_message(get_display_msg("This student has hand cash invoice. Please delete hand cash invoice first.",'danger'));
        }
        
        $hcInvCount = service('ExamResultsModel')->withDeleted()->where('exr_scm_id', $scm_id)->countAllResults();
        if($hcInvCount > 0){
            return $this->set_flash_message(get_display_msg("This student has exam result added. Please delete exam result of this student first.",'danger'));
        }
        
        // For data safety, as we don't have authorization method, no student can be deleted immeiately, need 7 days. SOP will remove deleted rows periodically to keep database freash.
        if( (time() - (7*24*60*60)) < strtotime($obj->scm_deleted_at) ){
            $waitTimeSec = strtotime($obj->scm_deleted_at) + (7*24*60*60) - time();
            $waitTimeMin = round($waitTimeSec / 60);
            $waitTimeHour = round($waitTimeMin / 60);
            $waitTimeDay = round($waitTimeHour / 24);
            $waitString = ($waitTimeDay > 0) ? "$waitTimeDay days" : ( ($waitTimeHour > 0) ? "$waitTimeHour hours" : "$waitTimeMin minutes" );
            return $this->set_flash_message(get_display_msg("For data safety, you can not delete student data immediately. Please wait $waitString and then delete permanently.",'danger'));
        }
        
        $eraseTrash = service('CoursesClassesStudentsMappingModel')->withDeleted()->where('scm_deleted_at < ',date('Y-m-d H:i:s',strtotime('-7 days')))->delete($scm_id, true);
        if($eraseTrash){
            return $this->set_flash_message(get_display_msg("Student data ({$scm_id}) removed from trash permanently.",'success'));
        }else{
            return $this->set_flash_message(get_display_msg("Failed to remove item ({$scm_id}) from trash.",'danger'));
        }
        var_dump($scm_id);
        var_dump($obj);
    } // EOM 
    
    private function set_flash_message( string $msg ){
        @session_start(); // Rreopen session if already closed to write in session
        session()->setFlashdata('display_msg', $msg );
        session_write_close();  // Our writing to session done. So allow other request of the same browser(JSON request) load data faster.
        return null;
    } // EOF 
    
} // EOC
