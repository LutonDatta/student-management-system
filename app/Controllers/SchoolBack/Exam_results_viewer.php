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

 
class Exam_results_viewer extends BaseController {
    
    
    /**
     * Which courses/book is read by which semesters/class is mapped from here.
     */
    public function show_exam_results(){
        session_write_close(); /* Can be placed before validity checking. */
        
        $data                   = $this->data;
        $data['pageTitle']      = myLang('Exam Results Viewer','পরীক্ষার ফলাফল প্রদর্শন');
        $data['title']          = myLang('Exam Results Viewer','পরীক্ষার ফলাফল প্রদর্শন');
        $data['loadedPage']     = 'exam_results_viewer'; // Used to automatically expand submenu and add active class 
        
        $data['examDtTmLst']    = service('AcademicExamDateTimeModel')->withDeleted()->select('axdts_id,axdts_class_id,axdts_session_year,axdts_type,axdts_exam_starts_at,axdts_exam_ends_at')->orderBy('axdts_id DESC')->paginate(15); 
        $data['examDtTmLstPgr'] = service('AcademicExamDateTimeModel')->pager;
            
        $data['Selted_xmDtTm']  = service('AcademicExamDateTimeModel')->withDeleted()->find(intval($this->request->getGet('view_result_for_dttm_id'))); 
        if(is_object($data['Selted_xmDtTm'])){
            $data['available_classes'] = service('ClassesAndSemestersModel')->withDeleted()->whereIn('classes_and_semesters.fcs_id',(array)@unserialize($data['Selted_xmDtTm']->axdts_class_id))->get_classes_with_parent_label_for_dropdown(false, 'clsprts', 20, false);
        }
        
        // Filter Level 2 - by normal class roll session
        $view_of_class_id   = intval($this->request->getPostGet('view_result_of_class_id'));
        $view_of_class_roll = intval($this->request->getPostGet('view_result_of_class_roll'));
        $view_of_session    = urldecode($this->request->getPostGet('view_result_of_session'));
        
        // Show filter data in filter form
        $data['exFilter'] = [ 
            'cid'   => $view_of_class_id > 0 ? $view_of_class_id : '', 
            'roll'  => $view_of_class_roll > 0 ? $view_of_class_roll : '', 
            'sess'  => $view_of_session,
        ];
        
        $dtListWithFilter = service('ExamResultsModel')
                ->withDeleted()
                ->select(implode(',',[
                    'exr_id','exr_axdts_id','exr_scm_id','exr_deleted_at','exr_updated_at','exr_inserted_at',
                    'scm_c_roll','scm_session_year','scm_class_id','scm_u_id', // From SCM table
                    'exr_co_1_id','exr_co_2_id','exr_co_3_id','exr_co_4_id','exr_co_5_id','exr_co_6_id','exr_co_7_id','exr_co_8_id','exr_co_9_id','exr_co_10_id','exr_co_11_id','exr_co_12_id','exr_co_13_id','exr_co_14_id','exr_co_15_id','exr_co_16_id','exr_co_17_id','exr_co_18_id','exr_co_19_id','exr_co_20_id',
                    'exr_co_1_re','exr_co_2_re','exr_co_3_re','exr_co_4_re','exr_co_5_re','exr_co_6_re','exr_co_7_re','exr_co_8_re','exr_co_9_re','exr_co_10_re','exr_co_11_re','exr_co_12_re','exr_co_13_re','exr_co_14_re','exr_co_15_re','exr_co_16_re','exr_co_17_re','exr_co_18_re','exr_co_19_re','exr_co_20_re',
                    'exr_co_1_ou','exr_co_2_ou','exr_co_3_ou','exr_co_4_ou','exr_co_5_ou','exr_co_6_ou','exr_co_7_ou','exr_co_8_ou','exr_co_9_ou','exr_co_10_ou','exr_co_11_ou','exr_co_12_ou','exr_co_13_ou','exr_co_14_ou','exr_co_15_ou','exr_co_16_ou','exr_co_17_ou','exr_co_18_ou','exr_co_19_ou','exr_co_20_ou',
                ]))
                ->join('courses_classes_students_mapping',"exam_results.exr_scm_id = courses_classes_students_mapping.scm_id",'LEFT')
                ->orderBy('exr_updated_at DESC'); 
        // Filter level 1 
        if(is_object($data['Selted_xmDtTm'])){ $dtListWithFilter->where('exr_axdts_id', $data['Selted_xmDtTm']->axdts_id); }
        
        // Filter level 2
        if($view_of_class_id > 0){ $dtListWithFilter->where('courses_classes_students_mapping.scm_class_id', $view_of_class_id); }
        if($view_of_class_roll > 0){ $dtListWithFilter->where('courses_classes_students_mapping.scm_c_roll', $view_of_class_roll); }
        if(strlen($view_of_session) > 1){ $dtListWithFilter->where('courses_classes_students_mapping.scm_session_year', $view_of_session); }
        $dtListWithFilterData = $dtListWithFilter->paginate(15,'filter_by_exdt');
        
        // Find last update teacher name, Separate (don't use JOIN) query to use less resource/memory
        $teacherStudentIds = [];
        $classListIDs = []; // Used to see readable class names
        $courseListIDs = []; // Used to see readable course name with result
        foreach($dtListWithFilterData as $tObj){
            $teacherStudentIds[] = intval($tObj->scm_u_id); // Student name - who participated exam
            $classListIDs[] = intval($tObj->scm_class_id); // Class of exam where students get admitted
            for($start=1;$start<=20;$start++){
                if(property_exists($tObj,"exr_co_{$start}_id") AND intval($tObj->{"exr_co_{$start}_id"}) > 0 ){
                    $courseListIDs[] = intval($tObj->{"exr_co_{$start}_id"});
                }
            }
        }
        
        $teacherStudentIds = array_unique($teacherStudentIds);// If same person update, one id will be found in many rows
        $data['teacherStudentNameList'] = [];
        if(count($teacherStudentIds) > 0){
            $tList = service('UserStudentsModel')->select('student_u_id,student_u_name_initial,student_u_name_first,student_u_name_middle,student_u_name_last')->find($teacherStudentIds);
            foreach($tList as $aStd){ $data['teacherStudentNameList'][$aStd->student_u_id] = implode(' ', array_filter([get_name_initials($aStd->student_u_name_initial),$aStd->student_u_name_first,$aStd->student_u_name_middle,$aStd->student_u_name_last ])); }
        }
        
        $classListIDs = array_unique($classListIDs);// remove duplicate IDs to get data easily from db
        $data['classNameList'] = [];
        if(count($classListIDs) > 0){
            $data['classNameList'] = service('ClassesAndSemestersModel')->get_class_list_with_parent_label_by_class_id_list($classListIDs);
        }
        
        
        $courseListIDs = array_unique($courseListIDs);// If same person update, one id will be found in many rows
        $data['courseNameList'] = [];
        if(count($courseListIDs) > 0){
            $tList = service('CoursesModel')->select('co_id,co_title,co_code')->find($courseListIDs);
            foreach($tList as $aStd){ $data['courseNameList'][$aStd->co_id] = $aStd->co_title . (strlen($aStd->co_code) > 0 ? " [$aStd->co_code]" : ''); }
        }
        
        $data['dtListWithFilter'] = $dtListWithFilterData;
        $data['dtListWithFilterPgr'] = service('ExamResultsModel')->pager->links('filter_by_exdt');

             
        echo view('SchoolBackViews/head', $data);
        echo view('SchoolBackViews/_parts/nav-left', $data);
        echo view('SchoolBackViews/_parts/nav-top', $data); 
        echo view('SchoolBackViews/academic-course/exam/exam-result-viewer', $data);
        echo view('SchoolBackViews/_parts/nav-bottom', $data);
        echo view('SchoolBackViews/footer', $data);
    } // EOM
    
    

} // EOC


