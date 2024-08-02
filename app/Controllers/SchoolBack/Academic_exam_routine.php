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

 
class Academic_exam_routine extends BaseController {
    
    
    /**
     * Which courses/book is read by which semesters/class is mapped from here.
     */
    public function routine_setup(){
        session_write_close(); 
        $data                   = $this->data;
        $class_id               = intval($this->request->getPostGet('class_id'));

        $atchCrsToCls = $this->save_submitted_exam_routine_date_time();
        if(is_object($atchCrsToCls)){return $atchCrsToCls;}else{$data = array_merge($data, $atchCrsToCls);}
        
        $data['pageTitle']      = lang('Menu.exam_routine');
        $data['title']          = lang('Menu.exam_routine');
        $data['loadedPage']     = 'exam_routine'; // Used to automatically expand submenu and add active class 
        
        $data['Selted_xmDtTm']  = service('AcademicExamDateTimeModel')->withDeleted()->find(intval($this->request->getGet('selected_xam_dt_tm_id'))); 
        if(is_object($data['Selted_xmDtTm'])){
            $classes = service('ClassesAndSemestersModel')->withDeleted()->whereIn('classes_and_semesters.fcs_id',(array)@unserialize($data['Selted_xmDtTm']->axdts_class_id))->get_classes_with_parent_label_for_dropdown(false, 'clsprts', 20, false);
            $data['Selted_xmDtTm']->courses = []; // Courses is different for different classes. Even one course might be in different class. So keep them separate.
            if( is_array($classes) AND count($classes) > 0 ){
                $data['Selted_xmDtTm']->clsNames = esc( implode(', ', $classes) );
                foreach($classes as $SepClsID => $SepClsName ){
                    // Make sure class IDs has been selected for the selected session (not mandatory), otherwise we might have a lot of non selected classes.
                    $courseIDs = service('CoursesClassesMappingModel')->withDeleted()->where('ccm_class_id',$SepClsID)->where('ccm_year_session',$data['Selted_xmDtTm']->axdts_session_year)->limit(20)->findColumn('ccm_course_id');
                    // Allow admin not to set courses all the time, as same courses are keept for longer years to a same class.
                    if( ! (is_array($courseIDs) AND count($courseIDs) > 1 ) ){
                        $courseIDs = service('CoursesClassesMappingModel')->withDeleted()->where('ccm_class_id',$SepClsID)->limit(20)->findColumn('ccm_course_id');
                    }
                    $data['Selted_xmDtTm']->classWiseCourses[] = array(
                        'class_id'      => $SepClsID,
                        'class_name'    => $SepClsName,
                        'courses'       =>  service('CoursesModel')->select('co_id,co_title,co_code,co_excerpt,co_deleted_at')->withDeleted()->find($courseIDs)
                    );
                }
            }else{
                $data['Selted_xmDtTm']->clsNames = 'No Class';
            }
        }else{
            $data['examDtTmLst']    = service('AcademicExamDateTimeModel')->withDeleted()->select('axdts_id,axdts_class_id,axdts_session_year,axdts_type,axdts_exam_starts_at,axdts_exam_ends_at')->orderBy('axdts_id DESC')->paginate(15); 
            $data['examDtTmLstPgr'] = service('AcademicExamDateTimeModel')->pager;
        }
        
        
        echo view('SchoolBackViews/head', $data);
        echo view('SchoolBackViews/_parts/nav-left', $data);
        echo view('SchoolBackViews/_parts/nav-top', $data); 
        echo view('SchoolBackViews/academic-course/exam/exam-routine-setup', $data);
        echo view('SchoolBackViews/_parts/nav-bottom', $data);
        echo view('SchoolBackViews/footer', $data);
    } // EOM
    
    
    private function save_submitted_exam_routine_date_time(){
        if($this->request->getPost('svExDtTmRoutine') !== 'yes') return [];
        
        $classWise_courses  = (array) $this->request->getPost('examDateTime'); // Timestamp, class wise courses date time
        $ExamDtTmSetUpId    = intval($this->request->getPost('dateTimeSetupID')); 
        $ExamDtTmSetUpObj   = service('AcademicExamDateTimeModel')->withDeleted()->find($ExamDtTmSetUpId); 
        
        if( ! is_object($ExamDtTmSetUpObj)){
            @session_start();
            return redirect()->to(base_url('admin/academic/exam/routine'))->with('display_msg',get_display_msg(myLang('No valid date time setup ID found. Please select proper examination setup.','সঠিক দিন তারিখ আইডি পাওয়া যায় নি।  অনুগ্রহ করে সঠিক পরীক্ষার সেটআপ নির্বাচন করুন।'),'danger'));
        }
        
        $classWiseCourseFiltered = [];
        foreach( $classWise_courses as $classID => $course_exam_time ){
            if( intval($classID) < 1 ) continue; // Validate class ID
             $validatedDateTimeArray = array_filter($course_exam_time, function($time){
                if(strlen(trim($time)) !== 19) return false; // Invalid date time format. Expected format 19 cha: 2023-03-31 10:30:46
                
                $date = \DateTime::createFromFormat('Y-m-d H:i:s', $time);
		return (bool) $date && \DateTime::getLastErrors()['warning_count'] === 0 && \DateTime::getLastErrors()['error_count'] === 0;
            });
            if(count($validatedDateTimeArray) > 0){
                foreach($validatedDateTimeArray as $coID => $examDate ){
                    $validatedDateTimeArray['co_' . $coID] = $examDate;
                    unset($validatedDateTimeArray[$coID]);
                }
                $classWiseCourseFiltered[ 'class_' . intval($classID) ] = $validatedDateTimeArray;
            }
        }
        if(count($classWiseCourseFiltered) < 1 ){
            @session_start();
            return redirect()->to(base_url('admin/academic/exam/routine'))->with('display_msg',get_display_msg(myLang('No valid date found for any course. Please select date time for courses.','কোন কর্সের জন্য সঠিক দিন তারিখ পাওয়া যায়নি। অনুগ্রহ করে সকল কর্সের জন্য দিন তারিখ নির্ধারণ করুন।'),'danger'));
        }
        
        $update = service('AcademicExamDateTimeModel')->withDeleted()->update( $ExamDtTmSetUpObj->axdts_id,['axdts_exam_routine'=>serialize($classWiseCourseFiltered)]);
        if($update ){
            $display_msg = get_display_msg( "Routine Updated Successfully.",'success');
        }else{
            $errorsXt = implode(', ', service('AcademicExamDateTimeModel')->errors());
            $display_msg = get_display_msg('Error: ' . $errorsXt,'danger');
        }
        @session_start(); // Reverse session_write_close?
        return redirect()->to(base_url("admin/academic/exam/routine?selected_xam_dt_tm_id={$ExamDtTmSetUpObj->axdts_id}"))->with('display_msg',$display_msg);
    } /* EOM */
    
} // EOC


