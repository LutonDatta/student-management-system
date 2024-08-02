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


class Daily_attendance extends BaseController {
    
    /**
     * Add/update sessions, classes, batches, morning/evening shifts etc.
     */
    public function attendance_book(){
        
        session_write_close(); // We no longer need session. Close session and allow next request load data faster.
        
        $data  = $this->data; // We have some pre populated data here
        
        $data['pageTitle']      = lang('Dba.dba');
        $data['title']          = lang('Dba.dba');
        $data['loadedPage']     = 'attendance_book'; // Used to automatically expand submenu and add active class 
        
        $data['selected_cls_obj'] = service('ClassesAndSemestersModel')->get_single_class_with_parent_label(intval($this->request->getGet('take_attendance_of_class_id')));
        if(is_object($data['selected_cls_obj'])){   
            $data['attached_session_years_for_attendance'] = $aSesYrsFrAtndce = service('CoursesClassesStudentsMappingModel')
                    ->distinct()->limit(40,0)
                    ->findColumn('scm_session_year'); // Null or indexed array of values
            
            $setdSessYr = $this->request->getGet('att_sess_yrs_for_atdnce');
            
            if(is_array($aSesYrsFrAtndce) AND count($aSesYrsFrAtndce) > 0 AND in_array($setdSessYr, $aSesYrsFrAtndce)){                        
                        
                $data['attached_courses_to_this_cls'] = service('CoursesClassesMappingModel')
                        ->where('ccm_year_session', $setdSessYr)
                        ->where('courses_classes_mapping.ccm_class_id', $data['selected_cls_obj']->fcs_id )
                        ->join('courses', 'courses.co_id = courses_classes_mapping.ccm_course_id', 'LEFT')                       
                        ->findAll( 25, 0 );
            }else{
                $data['display_msg'] = get_display_msg('Please select session/year for taking attendance.','danger');
            }
        }else{
            $data['display_msg'] = get_display_msg('No class found. Please select class for taking attendance.','danger');
        }
        
        
        echo view('SchoolBackViews/head', $data);
        echo view('SchoolBackViews/_parts/nav-left', $data);
        echo view('SchoolBackViews/_parts/nav-top', $data); 
        echo view('SchoolBackViews/academic/daily-attendance-book', $data);
        echo view('SchoolBackViews/_parts/nav-bottom', $data);
        echo view('SchoolBackViews/footer', $data);
    }
    
} // EOC


