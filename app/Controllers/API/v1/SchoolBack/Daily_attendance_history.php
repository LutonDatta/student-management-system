<?php namespace App\Controllers\API\v1\SchoolBack;

/**
 * @package    Ultra-School
 * @author     Ultra-School Dev Team
 * @copyright  2019-2020 Ultra Data Safety Solutions Pvt. Ltd.
 * @license    Private License
 * @link       https://ultra-school.com
 * @since      Version 1.0.0
 * @filesource
 */

use CodeIgniter\RESTful\ResourceController;
use App\Models\Daily_attendance_Model;
use CodeIgniter\API\ResponseTrait;

/**
 * Show attendance data to the admin in backend.
 */
class Daily_attendance_history extends ResourceController {
    
    use ResponseTrait;
    
    protected $modelName = 'App\Models\Daily_attendance_Model';
    protected $format    = 'json';
    
    public function attendance_history_viewer(){
        session_write_close(); // We no longer need session. Close session and allow next request load data faster.
        
        $t_dab      = 'daily_attendance_book';
        
        $filterByClassID    = intval($this->request->getPost('atViFilter_cla_id'));
        $filterByCourseID   = intval($this->request->getPost('atViFilter_coSuID'));
        $filterBySessionYr  = strval($this->request->getPost('atViFilter_sessYr'));
        $filterByStudentID  = intval($this->request->getPost('atViFilter_stu_id'));
        $filterByClsRoll    = intval($this->request->getPost('atViFilter_c_roll'));
        $filterByDate       = strval($this->request->getPost('atViFilter_v_date')); // Specific Date: 2021-10-25
        $filterByMonth      = intval($this->request->getPost('atViFilter_v_mnth')); // All rows in a month: from 2021-10-01 to 2021-10-31 (depends on year below)
        $filterByYear       = intval($this->request->getPost('atViFilter_v_year')); // All rows in a year: 2021-01-01 to 2021-12-31
        
        /* Search by month, CAUTION: EXCLUDE year if exists, IT IS ADDED TO THE SEARCH BY YEAR QUERY.*/
        /* Search capability based on month, include year if exists. Show attendance of a month from 1st date of month to last date of month. */
        if($filterByMonth > 0 AND $filterByYear < 1000 ){
            $filterByMonth_2_char   = (strlen($filterByMonth) < 2) ? '0' . $filterByMonth : $filterByMonth;
            $filterByMonth_Start    = date('Y') . "-{$filterByMonth_2_char}-01";
            $filterByMonth_Ends     = date('Y') . "-{$filterByMonth_2_char}-31";
        }
        
        /* Search capability based on year, include month if exists. Show attendance of an year from 1st jan to 31st december. */
        if($filterByYear > 999){ 
            $filterByMonthS_1  = ($filterByMonth > 0) ? ((strlen($filterByMonth) <2) ? '0' . $filterByMonth : $filterByMonth )  : "01";// From january, if month not supplied
            $filterByMonthS_2  = ($filterByMonth > 0) ? ((strlen($filterByMonth) <2) ? '0' . $filterByMonth : $filterByMonth )  : "12";// To december, if month not supplied
            $filterByYearStart = "{$filterByYear}-{$filterByMonthS_1}-01";
            $filterByYearEnds  = "{$filterByYear}-{$filterByMonthS_2}-31";
        }
        
        // CAUTION: Do not JOIN with any other table. It will consume much time. Because we might have 1 crore rows in a single attendance table
        $scmBldr = service('DailyAttendanceModel')
                ->select(implode(',',[
                    "scm_u_id,scm_c_roll,scm_class_id",
                    "scm_updated_at,student_u_gender",
                    "student_u_name_initial,student_u_name_first,student_u_name_middle,student_u_name_last",
                    "dab_id,dab_is_present",
                    "dab_class_date,dab_ins_at,dab_upd_at,dab_course_id",
                    'co_title,co_code','fcs_title'
                ]))
                ->join('courses_classes_students_mapping',"$t_dab.dab_scm_id = courses_classes_students_mapping.scm_id",'LEFT')
                ->join('students',"students.student_u_id = courses_classes_students_mapping.scm_u_id",'LEFT')
                ->join('courses',"$t_dab.dab_course_id = courses.co_id",'LEFT')
                ->join('classes_and_semesters',"courses_classes_students_mapping.scm_class_id = classes_and_semesters.fcs_id",'LEFT')
                ->withDeleted();
        
        $counted_Unfiltered_Rows = $scmBldr->countAllResults(false); // Total rows without filter
        
        $scmBldr = $scmBldr->orderBy("courses_classes_students_mapping.scm_c_roll",'ASC'); // ORDER? Implement it.
                        
                
        if(strlen($filterByDate) >0){ $scmBldr = $scmBldr->where('dab_class_date', $filterByDate); } // Search by specific date - first priority
        if($filterByMonth > 0 AND $filterByYear < 1000){ $scmBldr = $scmBldr->where('dab_class_date >=', $filterByMonth_Start)->where('dab_class_date <=', $filterByMonth_Ends); } // Search by month, EXCLUDE year if exists, IT IS ADDED TO THE NEXT LINE.
        if($filterByYear > 999){    $scmBldr = $scmBldr->where('dab_class_date >=', $filterByYearStart)->where('dab_class_date <=', $filterByYearEnds); } // Search by year, include month if exists
        if($filterByCourseID >0){   $scmBldr = $scmBldr->where('dab_course_id', $filterByCourseID); }
        if($filterByStudentID >0){  $scmBldr = $scmBldr->where('scm_u_id', $filterByStudentID); }
        if($filterByClsRoll >0){    $scmBldr = $scmBldr->where('scm_c_roll', $filterByClsRoll); }
        if($filterByClassID >0){    $scmBldr = $scmBldr->where('scm_class_id', $filterByClassID); }
        if(strlen($filterBySessionYr) >0){ $scmBldr = $scmBldr->where('scm_session_year', $filterBySessionYr); }
        
        $counted_Filtered_Rows = $scmBldr->countAllResults(false);
        
        
        $perPage    = intval($this->request->getPost('length')); 
        $perPage    = ($perPage > 100) ? 100 : $perPage; // Maximum row limit is 100;
        $offset     = intval($this->request->getPost('start')); // From 0 to ..
        
        // As we are returning directly to the browser through AJAX, select only specific 
        // rows not all from users table. These table might have password type data.
        $scmResults  = $scmBldr->findAll($perPage,$offset);

        
        $return = array(
            // 'error'             => 'Error message, if exists, sent to the DataTable. Do not include if not exists.',
            'draw'              => intval($this->request->getPost('draw')), // For proper sequence, sent by JS, JS sent requests in a sequences like: 1,2,3,4...
            'recordsTotal'      => $counted_Unfiltered_Rows,
            'recordsFiltered'   => $counted_Filtered_Rows,
            'data'              => array()
        );
        
        foreach($scmResults as $idx => $obj ){
            $defaultIm   =  $obj->student_u_gender == 'female' ? 'profile-thumb-girl-240x240.jpg' : 'profile-thumb-boy-240x240.jpg';            
            $usrImThumb = cdn_url('default-images/' . $defaultIm);
            
            $return['data'][] = [
                'uid'           => $obj->scm_u_id, // dab_teacher_u_id
                'thumb'         => "<img width='40' src='$usrImThumb' alt='' class='m-0 p-0'>",
                'roll'          => $obj->scm_c_roll,
                'name'          => implode(' ', array_filter([
                                    get_name_initials($obj->student_u_name_initial),
                                    $obj->student_u_name_first,
                                    $obj->student_u_name_middle,
                                    $obj->student_u_name_last
                                ])),
                'class_name'    => $obj->fcs_title ." [" . $obj->scm_class_id . "]",
                'course_name'   => $obj->co_title .' ['.$obj->dab_course_id.'] ' . (strlen($obj->co_code) > 0 ? ' - ' . $obj->co_code : ''),
                'is_present'    => is_null($obj->dab_is_present) ? '--' : (intval($obj->dab_is_present) ? "<i class='fa fa-check text-navy' title='Present'></i> Present" : "<i class='fa fa-times text-danger' title='Absent'></i> Absent"),
                'date'          => $obj->dab_class_date,
                'action'        => "<div class='delete_attendance_row_on_click' data-dabid='{$obj->dab_id}'><i class='fas fa-trash'></i></div>"
            ];
        } 
        
        return $this->respond($return);
    } /* EOM */
    
    
} /* EOC */ 
