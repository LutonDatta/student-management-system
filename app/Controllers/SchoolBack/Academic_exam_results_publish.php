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

 
class Academic_exam_results_publish extends BaseController {
    
    public function of_a_student_of_a_year(){
        session_write_close();
        $data                   = $this->data;
        $data['pageTitle']      = lang('Menu.exam_results');
        $data['title']          = lang('Menu.exam_results');
        $data['loadedPage']     = 'exam_results_publish'; // Used to automatically expand submenu and add active class 
          
        // We must have SCM ID to find the student to process exam results for.
        $scm_id = intval($this->request->getPostGet('result_pub_std_scm_id'));
        $uid_id = intval($this->request->getPostGet('result_pub_std_uid'));
        $sessYr = urldecode($this->request->getPostGet('result_pub_std_sess'));
        
        $data['studentSCM'] = service('CoursesClassesStudentsMappingModel')
                ->join('students','students.student_u_id = scm_u_id','LEFT')
                ->withDeleted()->find($scm_id);
        if(! is_object($data['studentSCM'])){
            $cls = service('CoursesClassesMappingModel')->select('ccm_class_id,ccm_year_session')
                    ->orderBy('ccm_id','DESC')->limit(1,0)->first();
            
            if(is_object($cls)){
                $countIfStudentsExists = service('CoursesClassesStudentsMappingModel')->where('scm_class_id', $cls->ccm_class_id)->where('scm_session_year', $cls->ccm_year_session)->countAllResults();
                if($countIfStudentsExists > 0){
                    return redirect()->to(base_url('admin/admission/student/list?year='.(is_object($cls) ? urlencode($cls->ccm_year_session) : '').'&class='.(is_object($cls) ? $cls->ccm_class_id : '')))->with('display_msg',get_display_msg(myLang('Please select student to publish exam results for.','অনুগ্রহ করে শিক্ষার্থী নির্বাচন করুন যার ফলাফল প্রশাক করতে চান।'),'warning'));
                }
            }
            return redirect()->to(base_url('admin/admission/student/list'))->with('display_msg',get_display_msg(myLang('Please select student to publish exam results for.','অনুগ্রহ করে শিক্ষার্থী নির্বাচন করুন যার ফলাফল প্রশাক করতে চান।'),'warning'));
        }
            
        $data['ExamResults']= service('ExamResultsModel')->where('exr_scm_id', $scm_id)->orderBy('exr_id DESC')->findAll(15);
        $data['courseNames']= $this->get_course_names( array_merge( $data['ExamResults'], [$data['studentSCM']] ) );
        
        echo view('SchoolBackViews/head', $data);
        echo view('SchoolBackViews/_parts/nav-left', $data);
        echo view('SchoolBackViews/_parts/nav-top', $data); 
        echo view('SchoolBackViews/academic-course/exam/exam-result-publish-for-year', $data);
        echo view('SchoolBackViews/_parts/nav-bottom', $data);
        echo view('SchoolBackViews/footer', $data);
    } // EOM
    
    
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
        $courseNms = service('CoursesModel')->select('co_id,co_title,co_code')->withDeleted()->find($courseIDs);
        
        $namesWithId = [];
        if(is_array($courseNms) AND count($courseNms) > 0 ) foreach( $courseNms as $coObj ){
            $namesWithId[intval($coObj->co_id)] = $coObj->co_title . " [" . $coObj->co_id . (strlen($coObj->co_code) > 0 ? '/'.$coObj->co_code : '') . "]";
        }
        return $namesWithId;
    } /* EOM */
    
} // EOC


