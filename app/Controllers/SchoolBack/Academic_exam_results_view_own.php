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

 
class Academic_exam_results_view_own extends BaseController {
    
    public function show_my_own_marksheet(){
        session_write_close(); /* Can be placed before validity checking. */
        
        $data                   = $this->data;
        $data['pageTitle']      = lang('Menu.exam_results');
        $data['title']          = lang('Menu.exam_results');
        $data['loadedPage']     = 'exam_results_view_own'; // Used to automatically expand submenu and add active class 
          
        $data['studentAllSCMList'] = service('CoursesClassesStudentsMappingModel')->select('scm_id,scm_session_year,scm_class_id,scm_status')->withDeleted()->where('scm_u_id', intval(service('request')->getGet('student_id')))->limit(20,0)->findAll();
        
        // We must have SCM ID to find the student to process exam results for.
        $scm_id = intval($this->request->getPostGet('marksheet_own_view_scm_id'));
        $data['studentSCM'] = service('CoursesClassesStudentsMappingModel')
                ->join('students','students.student_u_id = courses_classes_students_mapping.scm_u_id','LEFT')
                ->withDeleted()->find($scm_id);
        if( ! is_object($data['studentSCM'])){
            if( is_array($data['studentAllSCMList']) AND (count($data['studentAllSCMList']) > 0) ){
                // If no SCMID not found in get request, find a default SCM row
                $data['studentSCM'] = service('CoursesClassesStudentsMappingModel')
                        ->join('students','students.student_u_id = courses_classes_students_mapping.scm_u_id','LEFT')
                        ->withDeleted()->find($data['studentAllSCMList'][0]->scm_id);
            }
        }
        
        
        if(is_object($data['studentSCM'])){
            $data['ExamResults']= service('ExamResultsModel')->where('exr_scm_id', $data['studentSCM']->scm_id)->orderBy('exr_id DESC')->findAll(15);
            $data['courseNames']= $this->get_course_names( array_merge( $data['ExamResults'], [$data['studentSCM']] ) );
        }
        
        
        echo view('SchoolBackViews/head', $data);
        echo view('SchoolBackViews/_parts/nav-left', $data);
        echo view('SchoolBackViews/_parts/nav-top', $data); 
        echo view('SchoolBackViews/academic-course/exam/exam-result-own-transcript', $data);
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


