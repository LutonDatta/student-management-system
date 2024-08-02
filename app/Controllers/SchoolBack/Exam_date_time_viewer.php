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

 
class Exam_date_time_viewer extends BaseController {
    
    
    /**
     * Which courses/book is read by which semesters/class is mapped from here.
     */
    public function show_exam_date_time_to_students(){
        session_write_close(); /* Can be placed before validity checking. */
        
        $data                   = $this->data;
        $data['pageTitle']      = myLang('Exam Date Time Viewer','পরীক্ষার দিনক্ষণ প্রদর্শন');
        $data['title']          = myLang('Exam Date Time Viewer','পরীক্ষার দিনক্ষণ প্রদর্শন');
        $data['loadedPage']     = 'exam_date_time_viewer'; // Used to automatically expand submenu and add active class 
        
        
        $data['xmDateTimeList']     = service('AcademicExamDateTimeModel')->orderBy('axdts_updated_at DESC')->paginate(10, 'exdtpgr');
        $data['xmDateTimeListPgr']  = service('AcademicExamDateTimeModel')->pager->links('exdtpgr','school_front_sm');
        
        
        echo view('SchoolBackViews/head', $data);
        echo view('SchoolBackViews/_parts/nav-left', $data);
        echo view('SchoolBackViews/_parts/nav-top', $data); 
        echo view('SchoolBackViews/academic-course/exam/exam-date-time-viewer', $data);
        echo view('SchoolBackViews/_parts/nav-bottom', $data);
        echo view('SchoolBackViews/footer', $data);
    } // EOM
    
    

} // EOC


