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
 * Return students admission test rolls to the print view page. Get last SCM ID, currently showing in the page. 
 * and return other ids after it.
 * 
 * CAUTION: Attendance can be taken for each class/subject each calendar date. No teacher can not take attendance
 * for two day in a single date. Attendance can be changed in the current calendar date. And it can not be change after today.
 */
class Daily_attendance_update extends ResourceController {
    
    use ResponseTrait;
    
    protected $modelName = 'App\Models\Daily_attendance_Model';
    protected $format    = 'json';
    
    public function change_student_attendance_status(){
        session_write_close(); // We no longer need session. Close session and allow next request load data faster.
        
        $student_roll           = intval($this->request->getPost('attendance_roll'));
        $student_class_id       = intval($this->request->getPost('attendance_class_id'));
        $attendance_course_id   = intval($this->request->getPost('attendance_course_id'));
        $session_year           = strval($this->request->getPost('attendance_session_year'));
        $student_u_id           = intval($this->request->getPost('attendance_uid'));
        $student_scm_id         = intval($this->request->getPost('attendance_scmid'));
        $attendance_status      = strval($this->request->getPost('attendance_status')); // Must be one of 'present' or 'absent'
        
        if( ! in_array($attendance_status,['present','absent'])){ return $this->respond(['error'=>'Invalid attendance status provided. Value should be present or absent']); }
        
        $whereClause = [
            'scm_class_id'      => $student_class_id,
            'scm_session_year'  => $session_year,
            'scm_u_id'          => $student_u_id,
            'scm_status'        => 'admitted', // Students must be admitted
        ];
        
        // Keep counting separate, otherwise it is generating groupBy error
        $smCount = service('CoursesClassesStudentsMappingModel')
                // ->withDeleted() // It generates error like groupBy
                ->where($whereClause)
                ->countAllResults();
        
        if( $smCount > 1 ){
            return $this->respond(['is_attendance_updatable' => false, 'error' => "Wrong admission with duplicate data row: Same student is admitted to same class/same session multiple time. Please check CLASS/COURSE/YEAR"]);
        }
        
        
        // Validate the course ID, that have been trting to add attendance
        $scmStatus  = service('CoursesClassesStudentsMappingModel')
                // ->withDeleted() // It generates error like groupBy
                ->where($whereClause)
                ->select(implode(',',[
                    'scm_id',
                    'scm_c_roll', /* Need to show error message if Roll number not assigned. */
                    'scm_course_1,scm_course_2,scm_course_3,scm_course_4,scm_course_5',
                    'scm_course_6,scm_course_7,scm_course_8,scm_course_9,scm_course_10',
                    'scm_course_11,scm_course_12,scm_course_13,scm_course_14,scm_course_15',
                    'scm_course_op_1,scm_course_op_2,scm_course_op_3,scm_course_op_4,scm_course_op_5',
                ]))
                ->first(); // We should have only one row based on admitted student in a class/session
        
        if( ! is_object($scmStatus)){
            return $this->respond([ 'error' => "No such admitted student found based on your information. Class ID: {$student_class_id}, SID: {$student_u_id}"]);
        }
        
        if( intval($scmStatus->scm_c_roll) < 1 ){
            return $this->respond(['is_attendance_updatable' => false, 'error' => "No roll number assigned to this student. Please assign roll number first. Otherwise you can not take attendance."]);
        }
        
        $has_a_course = false; // Is requested student read in this course?
        for( $i = 0; $i < 16; $i++ ){
            if(property_exists( $scmStatus, 'scm_course_' . $i )){
                if( intval($scmStatus->{'scm_course_' . $i}) === $attendance_course_id ){
                    $has_a_course = true; // Selected student has a course for attendance
                }
            }
        }
        for( $i = 0; $i < 16; $i++ ){
            if(property_exists( $scmStatus, 'scm_course_op_' . $i )){
                if( intval($scmStatus->{'scm_course_op_' . $i}) === $attendance_course_id ){
                    $has_a_course = true; // Selected student has a course for attendance
                }
            }
        }
        if( ! $has_a_course){ return $this->respond(['error'=>'This student has not subscribed to the course you are trying to change attendance.']); }
        
        
        
        $dabStatus  = service('DailyAttendanceModel')
                ->select("dab_id,dab_is_present,dab_ins_at,dab_class_date")
                ->where([
                    'dab_scm_id'        => $scmStatus->scm_id,
                    'dab_course_id'     => $attendance_course_id,
                    'dab_class_date'    => date('Y-m-d'), // This will be used to take attandance of another day, if you change it
                ])
                ->first();
        

        
        $attendance_data = [
            // 'dab_teacher_u_id'  => service('AuthLibrary')->getLoggedInUserID(), CAUTION: Don't use it hare. If do, it will be used in where() and prevent update attendance if taken by other teacher
            'dab_scm_id'        => $student_scm_id,
            'dab_course_id'     => $attendance_course_id,
            'dab_class_date'    => date('Y-m-d'),
        ];
        
        try{
            // Attendance already taken. Present = 1, Absent = 0; null = no row found (not taken)
            if( is_object($dabStatus) AND ! is_null( $dabStatus->dab_is_present )){
                // This should not happen as we marked to take attendance for today only in select join query in $dabStatus
                if($dabStatus->dab_ins_at < date('Y-m-d H:i:s',time() - (60*60*24))){
                    return $this->respond(['error'=>'You can not change attendance after 24 hours of taking attendance.']);
                }
                // Attendance of today can not be taken tomorrow. No advance or delay attendance accepted.
                if($dabStatus->dab_class_date !== date('Y-m-d') ){
                    return $this->respond(['error'=>'If date changed, you can not change status. Today is: ' . date('Y-m-d') . ' and this attendance taken: ' . $dabStatus->dab_class_date ]);
                }
                $update_attendance = service('DailyAttendanceModel')
                        ->where('dab_id', $dabStatus->dab_id)
                        ->set([
                            'dab_is_present'    => ($attendance_status === 'present') ? '1' : '0', // Attendance status: Present = 1, Absent = 0; null = no row found (not taken)
                        ])
                        ->limit(1,0)->update(); // Returns bool
                if( ! $update_attendance ){
                    $errors = service('DailyAttendanceModel')->errors();
                }
            }else{
                $attendance_data['dab_is_present']  = ($attendance_status === 'present') ? '1' : '0';
                $insert_attendance = service('DailyAttendanceModel')->insert($attendance_data); // Returns bool
                if( ! $insert_attendance ){
                    $errors = service('DailyAttendanceModel')->errors();
                }
            }
            $return = [
                'scm_id' => $student_scm_id,
                'insert' => $insert_attendance ?? false,
                'update' => $update_attendance ?? false,
                'm_present' => ($attendance_status === 'present') ? 'p' : 'a', // 1 = Present, 0 = Absent
            ];
            
            if( ! empty($errors) AND is_array($errors) AND count($errors) > 0 ){
                $return['error'] = implode(', ', $errors ); // Return validation errors based on model validation rules.
            }
            
            return $this->respond($return);
        }catch(\Exception $e){
            // Do not show actual error. It might be: mysqli_sql_exception: Cannot add or update a child row: a foreign key constraint fails (`single_site_demo`.`ock_daily_attendance_book`, 
            return $this->respond([ 'error' => "May be something went wrong. We caught an exception. {$e->getCode()} : {$e->getMessage()}"]);
        }
    } /* EOM */
    
    
} /* EOC */ 
