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

 
class Academic_exam_results_delete extends BaseController {
    
    public function delete_exam_result(){
        session_write_close(); 
        $trasMe = $this->move_exr_to_trash();
        if(is_object($trasMe)){ return $trasMe; }
        
        $trasMe = $this->get_back_from_trash();
        if(is_object($trasMe)){ return $trasMe; }
        
        $deleteTrash = $this->delete_exr_from_trash_permanently();
        if(is_object($deleteTrash)){ return $deleteTrash; }
        
        
        $data                   = $this->data;
        $data['pageTitle']      = 'Remove exam result data';
        $data['title']          = 'Remove exam result data';
        $data['loadedPage']     = 'delete_exam_results'; // Used to automatically expand submenu and add active class 
          
        $data['ExamResults']    = service('ExamResultsModel')
                ->select(implode(',',[
                    'exr_id','exr_axdts_id','exr_scm_id','exr_deleted_at','exr_updated_at','exr_inserted_at',
                    'scm_status', 'scm_c_roll','scm_session_year','scm_class_id','scm_u_id', // From SCM table
                    'exr_co_1_id','exr_co_2_id','exr_co_3_id','exr_co_4_id','exr_co_5_id','exr_co_6_id','exr_co_7_id','exr_co_8_id','exr_co_9_id','exr_co_10_id','exr_co_11_id','exr_co_12_id','exr_co_13_id','exr_co_14_id','exr_co_15_id','exr_co_16_id','exr_co_17_id','exr_co_18_id','exr_co_19_id','exr_co_20_id',
                    'exr_co_1_re','exr_co_2_re','exr_co_3_re','exr_co_4_re','exr_co_5_re','exr_co_6_re','exr_co_7_re','exr_co_8_re','exr_co_9_re','exr_co_10_re','exr_co_11_re','exr_co_12_re','exr_co_13_re','exr_co_14_re','exr_co_15_re','exr_co_16_re','exr_co_17_re','exr_co_18_re','exr_co_19_re','exr_co_20_re',
                    'exr_co_1_ou','exr_co_2_ou','exr_co_3_ou','exr_co_4_ou','exr_co_5_ou','exr_co_6_ou','exr_co_7_ou','exr_co_8_ou','exr_co_9_ou','exr_co_10_ou','exr_co_11_ou','exr_co_12_ou','exr_co_13_ou','exr_co_14_ou','exr_co_15_ou','exr_co_16_ou','exr_co_17_ou','exr_co_18_ou','exr_co_19_ou','exr_co_20_ou',
                    'student_u_email_own','student_u_mobile_own','student_u_name_initial','student_u_name_first','student_u_name_middle','student_u_name_last','student_u_father_name','student_u_mother_name',
                    'student_u_gender','student_u_religion','student_u_addr_country','student_u_addr_state','student_u_addr_district','student_u_addr_thana','student_u_addr_post_office','student_u_addr_zip_code','student_u_addr_village','student_u_addr_road_house_no',
                    'axdts_type','axdts_exam_starts_at','axdts_exam_ends_at','axdts_session_year','axdts_exam_routine',
                    'scm_course_op_1','scm_course_op_2','scm_course_op_3','scm_course_op_4','scm_course_op_5',
                    'scm_course_1','scm_course_2','scm_course_3','scm_course_4','scm_course_5','scm_course_6','scm_course_7','scm_course_8','scm_course_9','scm_course_10','scm_course_11','scm_course_12','scm_course_13','scm_course_14','scm_course_15',
                ]))
                ->withDeleted()
                ->orderBy('exr_id DESC')
                ->join('courses_classes_students_mapping',"exam_results.exr_scm_id = courses_classes_students_mapping.scm_id",'LEFT')
                ->join('students',"students.student_u_id = courses_classes_students_mapping.scm_u_id",'LEFT')
                ->join('exam_date_time',"exam_results.exr_axdts_id = exam_date_time.axdts_id",'LEFT')
                ->paginate(10);
        $data['ExamResultsPgr'] = service('ExamResultsModel')->pager->links();
        $data['courseNames']    = $this->get_course_names($data['ExamResults']);
        
        
        echo view('SchoolBackViews/head', $data);
        echo view('SchoolBackViews/_parts/nav-left', $data);
        echo view('SchoolBackViews/_parts/nav-top', $data); 
        echo view('SchoolBackViews/academic-course/exam/exam-result-list', $data);
        echo view('SchoolBackViews/_parts/nav-bottom', $data);
        echo view('SchoolBackViews/footer', $data);
    } // EOM
    
    private function delete_exr_from_trash_permanently(){
        if($this->request->getPost('delete_ex_from_trash_permanently') !== 'yes') return; 
        $exr_id = intval($this->request->getPost('del_from_trash_exr_id'));
        $pgId = intval($this->request->getPost('delete_ex_result_pg'));
        $exr_ob = service('ExamResultsModel')->select('exr_id,exr_deleted_at')->withDeleted()->find($exr_id);
        if( ! is_object($exr_ob)){
            @session_start(); // Reverse session_write_close?
            return redirect()->to(base_url("admin/academic/exam/results/delete?page=$pgId"))->with('display_msg',get_display_msg( "Exam result (ID:{$exr_id}) not found.",'danger'));
        }
        if( ! $exr_ob->exr_deleted_at){
            @session_start(); // Reverse session_write_close?
            return redirect()->to(base_url("admin/academic/exam/results/delete?page={$pgId}#iboxXrID{$exr_id}"))->with('display_msg',get_display_msg( "Exam result (ID:{$exr_id}) is not in trash. Please send item to trash and wait 7 days for safety.",'danger'));
        }
        
        
        // For data safety, as we don't have authorization method, no student can be deleted immeiately, need 7 days. SOP will remove deleted rows periodically to keep database freash.
        if( (time() - (7*24*60*60)) < strtotime($exr_ob->exr_deleted_at) ){
            $waitTimeSec = strtotime($exr_ob->exr_deleted_at) + (7*24*60*60) - time();
            $waitTimeMin = round($waitTimeSec / 60);
            $waitTimeHour = round($waitTimeMin / 60);
            $waitTimeDay = round($waitTimeHour / 24);
            $waitString = ($waitTimeDay > 0) ? "$waitTimeDay days" : ( ($waitTimeHour > 0) ? "$waitTimeHour hours" : "$waitTimeMin minutes" );
            $msg = get_display_msg("For data safety, you can not delete exam result data immediately. Please wait $waitString and then delete permanently.",'danger');
            @session_start(); // Reverse session_write_close?
            return redirect()->to(base_url("admin/academic/exam/results/delete?page={$pgId}#iboxXrID{$exr_id}"))->with('display_msg',$msg);
        }
        
        
        $del = service('ExamResultsModel')->withDeleted()->where('exr_deleted_at < ',date('Y-m-d H:i:s',strtotime('-7 days')))->limit(1)->delete($exr_id, true);
        if($del){
            @session_start(); // Reverse session_write_close?
            return redirect()->to(base_url("admin/academic/exam/results/delete?page={$pgId}"))->with('display_msg',get_display_msg( "Exam result (ID:{$exr_id}) deleted permanently.",'success'));
        }else{
            @session_start(); // Reverse session_write_close?
            return redirect()->to(base_url("admin/academic/exam/results/delete?page={$pgId}#iboxXrID{$exr_id}"))->with('display_msg',get_display_msg( "Exam result (ID:{$exr_id}) failed to delete permanently.",'danger'));
        }
    } /* EOM */
    
    
    
    private function get_back_from_trash(){
        if($this->request->getPost('get_back_from_trash') !== 'yes') return; 
        $exr_id = intval($this->request->getPost('get_back_from_trash_exr_id'));
        $pgId = intval($this->request->getPost('get_back_from_trash_pg'));
        $exr_ob = service('ExamResultsModel')->select('exr_id,exr_deleted_at')->withDeleted()->find($exr_id);
        if( ! is_object($exr_ob)){
            @session_start(); // Reverse session_write_close?
            return redirect()->to(base_url("admin/academic/exam/results/delete?page=$pgId"))->with('display_msg',get_display_msg( "Exam result (ID:{$exr_id}) not found.",'danger'));
        }
        if( ! $exr_ob->exr_deleted_at){
            @session_start(); // Reverse session_write_close?
            return redirect()->to(base_url("admin/academic/exam/results/delete?page={$pgId}#iboxXrID{$exr_id}"))->with('display_msg',get_display_msg( "Exam result (ID:{$exr_id}) is not in trash.",'danger'));
        }
        
        $del = service('ExamResultsModel')->withDeleted()->limit(1)->update($exr_id, ['exr_deleted_at'=>NULL]);
        if($del){
            @session_start(); // Reverse session_write_close?
            return redirect()->to(base_url("admin/academic/exam/results/delete?page={$pgId}#iboxXrID{$exr_id}"))->with('display_msg',get_display_msg( "Exam result (ID:{$exr_id}) recycled successfully.",'success'));
        }else{
            @session_start(); // Reverse session_write_close?
            return redirect()->to(base_url("admin/academic/exam/results/delete?page={$pgId}#iboxXrID{$exr_id}"))->with('display_msg',get_display_msg( "Exam result (ID:{$exr_id}) failed to recycle.",'danger'));
        }
    } /* EOM */
    
    private function move_exr_to_trash(){
        if($this->request->getPost('move_whole_ex_result_to_trash') !== 'yes') return; 
        $exr_id = intval($this->request->getPost('send_to_trash_exr_id'));
        $pgId = intval($this->request->getPost('move_whole_ex_result_pg'));
        $exr_ob = service('ExamResultsModel')->select('exr_id,exr_deleted_at')->withDeleted()->find($exr_id);
        if( ! is_object($exr_ob)){
            @session_start(); // Reverse session_write_close?
            return redirect()->to(base_url("admin/academic/exam/results/delete?page=$pgId"))->with('display_msg',get_display_msg( "Exam result (ID:{$exr_id}) not found.",'danger'));
        }
        if($exr_ob->exr_deleted_at){
            @session_start(); // Reverse session_write_close?
            return redirect()->to(base_url("admin/academic/exam/results/delete?page={$pgId}#iboxXrID{$exr_id}"))->with('display_msg',get_display_msg( "Exam result (ID:{$exr_id}) is already deleted.",'danger'));
        }
        
        $del = service('ExamResultsModel')->withDeleted()->limit(1)->delete($exr_id);
        if($del){
            @session_start(); // Reverse session_write_close?
            return redirect()->to(base_url("admin/academic/exam/results/delete?page={$pgId}#iboxXrID{$exr_id}"))->with('display_msg',get_display_msg( "Exam result (ID:{$exr_id}) deleted successfully.",'success'));
        }else{
            @session_start(); // Reverse session_write_close?
            return redirect()->to(base_url("admin/academic/exam/results/delete?page={$pgId}#iboxXrID{$exr_id}"))->with('display_msg',get_display_msg( "Exam result (ID:{$exr_id}) failed to delete.",'danger'));
        }
    } /* EOM */
    
    private function get_course_names( $scmExamResultRows ){
        $coIDs = []; // We will retrieve course/subject names from db using these IDs
        foreach($scmExamResultRows as $oneExamResRow){
            for($i=1;$i<=20;$i++){
                if(property_exists($oneExamResRow,"exr_co_{$i}_id")){ $coIDs[] = intval($oneExamResRow->{"exr_co_{$i}_id"}); }
                // Get course/subject names also, those are taken by students when they are admitted to the class, used to show if no mark recorded
                if(property_exists($oneExamResRow,"scm_course_{$i}")){ $coIDs[] = intval($oneExamResRow->{"scm_course_{$i}"}); }
                if(property_exists($oneExamResRow,"scm_course_op_{$i}")){ $coIDs[] = intval($oneExamResRow->{"scm_course_op_{$i}"}); }
            }
        }
        $courseIDs = array_unique(array_filter($coIDs)); // Remove duplicate ids and false type values
        if( count($courseIDs) < 1 ) return [];
        $courseNms = service('CoursesModel')->select('co_id,co_title,co_code')->withDeleted()->find($courseIDs);
        
        $namesWithId = [];
        if(is_array($courseNms) AND count($courseNms) > 0 ) foreach( $courseNms as $coObj ){
            $namesWithId[intval($coObj->co_id)] = $coObj->co_title . " [" . $coObj->co_id . (strlen($coObj->co_code) > 0 ? '/'.$coObj->co_code : '') . "]";
        }
        return $namesWithId;
    } /* EOM */
    
} // EOC


