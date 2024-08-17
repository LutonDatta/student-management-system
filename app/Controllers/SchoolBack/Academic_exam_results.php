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

 
class Academic_exam_results extends BaseController {
    
    
    /**
     * Which courses/book is read by which semesters/class is mapped from here.
     */
    public function exam_results_publication(){
        session_write_close();
        $data                   = $this->data;
        $class_id               = intval($this->request->getPostGet('class_id'));

        $atchCrsToCls = $this->save_submitted_exam_marks_by_course_n_student();
        if(is_object($atchCrsToCls)){return $atchCrsToCls;}else{$data = array_merge($data, $atchCrsToCls);}
        
        $data['pageTitle']      = lang('Menu.exam_results');
        $data['title']          = lang('Menu.exam_results');
        $data['loadedPage']     = 'exam_results'; // Used to automatically expand submenu and add active class 

        $data['examDtTmLst']    = service('AcademicExamDateTimeModel')->withDeleted()->select('axdts_id,axdts_class_id,axdts_session_year,axdts_type,axdts_exam_starts_at,axdts_exam_ends_at')->orderBy('axdts_id DESC')->paginate(15); 
        $data['examDtTmLstPgr'] = service('AcademicExamDateTimeModel')->pager;

        $data['Selted_xmDtTm']  = service('AcademicExamDateTimeModel')->withDeleted()->find(intval($this->request->getGet('result_for_dttm_id'))); 
        if(is_object($data['Selted_xmDtTm'])){
            $data['available_classes'] = service('ClassesAndSemestersModel')->withDeleted()->whereIn('classes_and_semesters.fcs_id',(array)@unserialize($data['Selted_xmDtTm']->axdts_class_id))->get_classes_with_parent_label_for_dropdown(false, 'clsprts', 20, false);
        }
             
        $data['Selted_class']  = service('ClassesAndSemestersModel')->withDeleted()->find(intval($this->request->getGet('result_for_class_id')));
        if(is_object($data['Selted_xmDtTm']) AND is_object($data['Selted_class'])){
            $courseIDs = service('CoursesClassesMappingModel')->withDeleted()->where('ccm_class_id',$data['Selted_class']->fcs_id)->where('ccm_year_session',$data['Selted_xmDtTm']->axdts_session_year)->limit(20)->findColumn('ccm_course_id');
            $courseNames = service('CoursesModel')->select('co_id,co_title,co_code')->withDeleted()->find($courseIDs);
            $data['available_courses'] = ['0' => 'Select course/subject from here'];
            foreach($courseNames as $coNm ){
                $data['available_courses'][$coNm->co_id] = "{$coNm->co_title} [{$coNm->co_id}] " . (strlen($coNm->co_code) > 0 ? $coNm->co_code : '');
            }
        }
    
        $data['selted_course'] = service('CoursesModel')->select('co_id')->withDeleted()->find(intval($this->request->getGet('result_for_course_id')));
        if(is_object($data['Selted_xmDtTm']) AND is_object($data['Selted_class']) AND is_object($data['selted_course'])){
            $data['available_students'] = service('CoursesClassesStudentsMappingModel')
                    ->withDeleted()
                    ->join('students','students.student_u_id = courses_classes_students_mapping.scm_u_id','LEFT')
                    ->select('scm_deleted_at,scm_id,scm_u_id,scm_c_roll,scm_status,student_u_name_initial,student_u_name_first,student_u_name_middle,student_u_name_last')
                    ->where('scm_session_year', $data['Selted_xmDtTm']->axdts_session_year)
                    ->where('scm_class_id', $data['Selted_class']->fcs_id)
                    ->orderBy('scm_c_roll ASC')
                    // CAUTON: At the time of saving, there is a great amount of load happen for loop, 
                    // so save minumum number of students at a time. 15 is enough, teacher will go to
                    // next page to store marks of next other students.
                    ->paginate(15,'store_marks'); 
            $data['available_studentsPgr'] = service('CoursesClassesStudentsMappingModel')->pager->links('store_marks');
            
            $foundScmIDlist = [];
            if(is_array($data['available_students']) AND count($data['available_students']) > 0 ){
                foreach($data['available_students'] as $scmObjx){
                    $foundScmIDlist[] = $scmObjx->scm_id;
                }
            }
            if(count($foundScmIDlist) > 0){
                $data['oldMarkOfStudents'] = [];
                $stf = service('ExamResultsModel')
                        ->where([
                            'exr_axdts_id'  => $data['Selted_xmDtTm']->axdts_id,
                        ])
                        ->whereIn('exr_scm_id',$foundScmIDlist)
                        ->orderBy('exr_id ASC');
                    $stf->groupStart();
                        $stf->where('exr_co_1_id', $data['selted_course']->co_id);
                        for($i=2; $i<=20; $i++){
                            $stf->orWhere("exr_co_{$i}_id", $data['selted_course']->co_id);
                        }
                    $stf->groupEnd();
                foreach($stf->findAll(15) as $oldR){
                    for($i=1; $i<=20; $i++){
                        if(intval($oldR->{"exr_co_{$i}_id"}) === intval($data['selted_course']->co_id)){
                            $data['oldMarkOfStudents'][intval($oldR->exr_scm_id)] = [
                                $oldR->{"exr_co_{$i}_re"},$oldR->{"exr_co_{$i}_ou"}
                            ];
                        }
                    }
                }
            }
        }
        
        echo view('SchoolBackViews/head', $data);
        echo view('SchoolBackViews/_parts/nav-left', $data);
        echo view('SchoolBackViews/_parts/nav-top', $data); 
        echo view('SchoolBackViews/academic-course/exam/exam-result-publication', $data);
        echo view('SchoolBackViews/_parts/nav-bottom', $data);
        echo view('SchoolBackViews/footer', $data);
    } // EOM
    
    
    
    private function save_submitted_exam_marks_by_course_n_student(){
        if($this->request->getPost('submit_marks_result') !== 'yes') return [];
        
        $xmrs_dttm_id      = intval(service('request')->getGetPost('result_for_dttm_id'));
        $xmrs_class_id     = intval(service('request')->getGetPost('result_for_class_id'));
        $xmrs_course_id    = intval(service('request')->getGetPost('result_for_course_id'));
        $pager_page_no     = intval(service('request')->getGetPost('result_pager_page_no'));
        
        $rdrURL = base_url("admin/academic/exam/results?result_for_dttm_id={$xmrs_dttm_id}&result_for_class_id={$xmrs_class_id}&result_for_course_id={$xmrs_course_id}&page_store_marks={$pager_page_no}");
        
        // Validate exam date time ID, it separates various types of exam of a student SCM ID
        $exDtTm = service('AcademicExamDateTimeModel')->find($xmrs_dttm_id);
        if( ! is_object($exDtTm)){
            @session_start(); // Reverse session_write_close?
            return redirect()->to($rdrURL)->with('display_msg',get_display_msg('Invalid exam date time ID found.','danger'));
        }
        
        // Validate class ID assigned to exam date time settings
        $exDtTmClasses = @unserialize($exDtTm->axdts_class_id);
        if( ! is_array($exDtTmClasses) OR ! in_array($xmrs_class_id, array_map('intval',$exDtTmClasses))){
            @session_start(); // Reverse session_write_close?
            return redirect()->to($rdrURL)->with('display_msg',get_display_msg("Class ID ({$xmrs_class_id}) not found exam date time settings.",'danger'));
        }
        
        $scm_out_array          = (array)$this->request->getPost('stdr_out_of_marks');
        $scm_ids_array          = (array)$this->request->getPost('stdr_obtained_marks');
        $scm_ids_array_filtered = array_filter($scm_ids_array);
        $scm_ids                = array_map('intval',array_keys($scm_ids_array_filtered)); // Find a list of SMC IDs list like [1,5,9...]

        // Make sure we have found scm_id to save marks
        if( count($scm_ids) < 1 ){
            @session_start(); // Reverse session_write_close?
            return redirect()->to($rdrURL)->with('display_msg',get_display_msg("You must submit obtained mark of one student.",'danger'));
        }

        // Validate scm_ids are correct, not from hackers, we have these admitted students?
        $exScmIdsArray = service('CoursesClassesStudentsMappingModel')
                ->select('scm_id,scm_session_year,scm_class_id,scm_course_1,scm_course_2,scm_course_3,scm_course_4,scm_course_5,scm_course_6,scm_course_7,scm_course_8,scm_course_9,scm_course_10,scm_course_11,scm_course_12,scm_course_13,scm_course_14,scm_course_15,scm_course_op_1,scm_course_op_2,scm_course_op_3,scm_course_op_4,scm_course_op_5')
                ->find($scm_ids);
        
        if(count($exScmIdsArray) < 1 ){
            @session_start(); // Reverse session_write_close?
            return redirect()->to($rdrURL)->with('display_msg',get_display_msg("Your submitted students are not of this institution.",'danger'));
        }

        
        $saveErrorMessages =[];
        $saveSuccessCount = 0;
        $saveSkipIDCount = 0;
        $saveValidStudentCount = count($exScmIdsArray);
        $saveTotalStudentCountUnfiltered = count($scm_ids_array);
        
        foreach($exScmIdsArray as $scmObj){
            if($scmObj->scm_session_year !== $exDtTm->axdts_session_year){
                $saveSkipIDCount++;
                $saveErrorMessages[] = 'Session year not match ' . esc($scmObj->scm_session_year) . ' and ' . esc($exDtTm->axdts_session_year);
                continue; // Invalid year/session. Requested year not matched with saved year, hackers are trying to update, just skip
            }
            
            if(intval($scmObj->scm_class_id) !== $xmrs_class_id){
                $saveSkipIDCount++;
                $saveErrorMessages[] = 'Class ID not match for ' . esc($scmObj->scm_class_id) . ' and ' . esc($xmrs_class_id);
                continue; // Hackers are trting sending different data id, otherwise it should match
            }
            
            
            $admittedCourses = array_filter([$scmObj->scm_course_1,$scmObj->scm_course_2,$scmObj->scm_course_3,$scmObj->scm_course_4,$scmObj->scm_course_5,$scmObj->scm_course_6,$scmObj->scm_course_7,$scmObj->scm_course_8,$scmObj->scm_course_9,$scmObj->scm_course_10,$scmObj->scm_course_11,$scmObj->scm_course_12,$scmObj->scm_course_13,$scmObj->scm_course_14,$scmObj->scm_course_15,$scmObj->scm_course_op_1,$scmObj->scm_course_op_2,$scmObj->scm_course_op_3,$scmObj->scm_course_op_4,$scmObj->scm_course_op_5]);
            $admittedCoursesFiltered = array_unique(array_values(array_map('intval',$admittedCourses)));
            sort($admittedCoursesFiltered); // Just make an order for various students
            
            if( ! in_array($xmrs_course_id, $admittedCoursesFiltered)){
                $saveSkipIDCount++;
                $saveErrorMessages[] = 'Course ID (' . esc($xmrs_course_id) . ') not match passed. Make sure this student is admitted to this course.';
                continue; // Student not admitted to the class where, admin trying to add exam result
            }
            
            if( ! isset($scm_ids_array[$scmObj->scm_id])){
                $saveSkipIDCount++;
                $saveErrorMessages[] = 'SCM ID not submit passed ' . esc($scmObj->scm_id);
                continue; // Marks not found based on this scm_id 
            }
            
            // Update if old row found, otherwise insert new row
            $rowSearchFields = [ 'exr_scm_id' => $scmObj->scm_id, 'exr_axdts_id'  => $exDtTm->axdts_id ];
            $existsRow = service('ExamResultsModel')
                                ->where($rowSearchFields)
                                // ->withDeleted() // Generate group by error isn't in GROUP BY
                                ->orderBy('exr_id ASC')
                                ->first();
            
            
            if(is_object($existsRow)){
                    // All other 19 course marks to be stored from here, after first course, We must have minimum 1 course mark saved
                    // Loop through 15+5= 20 courses to check if course is already saved, if found update, otherwise store new result 
                    for($i=1; $i<=20; $i++){
                            $crsMarkUp = [
                                "exr_co_{$i}_re" => $scm_ids_array[$scmObj->scm_id],
                                "exr_co_{$i}_ou" => isset($scm_out_array[$scmObj->scm_id]) ? intval($scm_out_array[$scmObj->scm_id]) : '100'
                            ];
                            if(intval($existsRow->{"exr_co_{$i}_id"}) === $xmrs_course_id ){ // Marks for this course already saved, update new data
                                $upMark = service('ExamResultsModel')->skipValidation()->where($rowSearchFields)->limit(1,0)->update($existsRow->exr_id, $crsMarkUp);
                            }elseif($existsRow->{"exr_co_{$i}_id"} === NULL ){
                                $crsMarkUp["exr_co_{$i}_id"] = $xmrs_course_id;
                                $upMark = service('ExamResultsModel')->skipValidation()->where($rowSearchFields)->limit(1,0)->update($existsRow->exr_id, $crsMarkUp);
                            }else{
                                continue; // Do nothing, Ignore
                            }
                            if($upMark){
                                $saveSuccessCount++;
                            }else{
                                $saveErrorMessages = array_merge($saveErrorMessages, service('ExamResultsModel')->errors());
                            }
                            break; // Caution: Must stop at first time matching NULL, otherwise it will store to multiple column
                    }                
            }else{
                    // When first course (out of 15+5=20 courses) result added
                    $fields = $rowSearchFields;

                    $fields['exr_co_1_id'] = $xmrs_course_id;
                    $fields['exr_co_1_re'] = $scm_ids_array[$scmObj->scm_id];
                    $fields['exr_co_1_ou'] = isset($scm_out_array[$scmObj->scm_id]) ? intval($scm_out_array[$scmObj->scm_id]) : '100';

                    if( ! service('ExamResultsModel')->insert($fields)){
                        $saveErrorMessages = array_merge($saveErrorMessages, service('ExamResultsModel')->errors());
                    }else{
                        $saveSuccessCount++;
                    }
            }            
        } // End foreach

        $saveMsg = get_display_msg("Storing students marks. Successfully saved marks for $saveSuccessCount students. Skipped storing marks for $saveSkipIDCount students. $saveValidStudentCount Valid student found from your request. You sent request to store marks for $saveTotalStudentCountUnfiltered students.",'success');
        $saveMsg .= ( count($saveErrorMessages) > 0 ) ? get_display_msg(implode(' ', $saveErrorMessages), 'danger') : ''; 
        
        @session_start(); // Reverse session_write_close?
        return redirect()->to($rdrURL)->with('display_msg', $saveMsg);
    } /* EOM */
    

} // EOC


