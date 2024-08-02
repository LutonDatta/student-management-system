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
class Daily_attendance extends ResourceController {
    
    use ResponseTrait;
    
    protected $modelName = 'App\Models\Daily_attendance_Model';
    protected $format    = 'json';
    
    public function load_class_student_rolls(){
        session_write_close(); // We no longer need session. Close session and allow next request load data faster.
        
        $varLastRoll = intval($this->request->getPost('last_row_roll'));
        $varClassIdn = intval($this->request->getPost('sele_class_id'));
        $varSessionY = strval($this->request->getPost('session_year'));
        $varSubjectF = intval($this->request->getPost('se_subject_id'));
        
        $page       = intval($this->request->getPost('page')); $page = $page > 0 ? $page : 1;
        $perPage    = 15;
        $offset     = ($page * $perPage ) - $perPage;
        
        $scmBldr = service('CoursesClassesStudentsMappingModel')
                ->where([
                    'scm_class_id'      => $varClassIdn,
                    'scm_session_year'  => $varSessionY,
                ])
                // Students must be roll number assigned, but show them on attendance page, will be prevented later when try to take attendance
                // ->where('scm_c_roll > ', 0)         
                ->where('scm_status', 'admitted')   // Students must be admitted
                ->orderBy('scm_c_roll','ASC')
                ->withDeleted()
                ->groupStart()
                    ->where('scm_course_1',$varSubjectF)
                    ->orWhere('scm_course_2',$varSubjectF)
                    ->orWhere('scm_course_3',$varSubjectF)
                    ->orWhere('scm_course_4',$varSubjectF)
                    ->orWhere('scm_course_5',$varSubjectF)
                    ->orWhere('scm_course_6',$varSubjectF)
                    ->orWhere('scm_course_7',$varSubjectF)
                    ->orWhere('scm_course_8',$varSubjectF)
                    ->orWhere('scm_course_9',$varSubjectF)
                    ->orWhere('scm_course_10',$varSubjectF)
                    ->orWhere('scm_course_11',$varSubjectF)
                    ->orWhere('scm_course_12',$varSubjectF)
                    ->orWhere('scm_course_13',$varSubjectF)
                    ->orWhere('scm_course_14',$varSubjectF)
                    ->orWhere('scm_course_15',$varSubjectF)
                    ->orWhere('scm_course_op_1',$varSubjectF)
                    ->orWhere('scm_course_op_2',$varSubjectF)
                    ->orWhere('scm_course_op_3',$varSubjectF)
                    ->orWhere('scm_course_op_4',$varSubjectF)
                    ->orWhere('scm_course_op_5',$varSubjectF)
                ->groupEnd();
        
        $countedRows = $scmBldr->countAll(false);
        if( $countedRows < 1 ){
            return $this->respond(['error'=>'No student found in this session/year of this class.']);
        }
        
        $ta = 'daily_attendance_book';
        
        // As we are returning directly to the browser through AJAX, select only specific 
        // rows not all from users table. These table might have password type data.
        $scmResults  = $scmBldr
                ->join($ta,implode(' AND ',[
                    "scm_id = $ta.dab_scm_id",
                    "$ta.dab_course_id = $varSubjectF",
                    // Attendance can be changed until date changes. Means only attendance can be changed today.
                    "$ta.dab_class_date='" . date('Y-m-d') . "'" // Change it to take advance/delay attendance
                ]),'LEFT')
                ->join('user_students','user_students.student_u_id = courses_classes_students_mapping.scm_u_id','LEFT')
                ->select(implode(',',[
                    'scm_id',
                    'scm_class_id','scm_session_year','scm_c_roll','scm_u_id',
                    
                    //'scm_course_1,scm_course_2,scm_course_3,scm_course_4,scm_course_5',
                    //'scm_course_6,scm_course_7,scm_course_8,scm_course_9,scm_course_10',
                    //'scm_course_11,scm_course_12,scm_course_13,scm_course_14,scm_course_15',
                    //'scm_course_op_1,scm_course_op_2,scm_course_op_3,scm_course_op_4,scm_course_op_5',
                    
                    "student_u_name_initial,student_u_name_first,student_u_name_middle,student_u_name_last",
                    "student_u_mobile_father,student_u_mobile_mother,student_u_father_name,student_u_mother_name",
                    "scm_updated_at,student_u_gender",
                    
                    "$ta.dab_id,$ta.dab_is_present,$ta.dab_ins_at",
                    
                    // Get courseID always in AJAX loop to use in tr data field. Will it to mark student present/absent
                    "IFNULL(dab_course_id, $varSubjectF) AS dab_course_id" 
                ]))
                ->findAll($perPage,$offset);


        foreach( $scmResults as $idx => $usr ){
            $defaultIm   =  $usr->student_u_gender == 'female' ? 'profile-thumb-girl-240x240.jpg' : 'profile-thumb-boy-240x240.jpg';
            //$usr->thumb  = $usr->u_thumbnail_id > 0 ? base_url('arf/p/'. $usr->u_thumbnail_id) : cdn_url('default-images/' . $defaultIm);
            
            $usr->thumb = cdn_url('default-images/' . $defaultIm);
            
            
            $usr->p_name = esc(implode(' ', array_filter([get_name_initials($usr->student_u_name_initial),$usr->student_u_name_first,$usr->student_u_name_middle,$usr->student_u_name_last])));
            $usr->p_gender = esc(get_gender_list($usr->student_u_gender ? $usr->student_u_gender : 'dummp-text-to-prevent-error'));
            $usr->p_is_present = $usr->dab_is_present === 1 ? 'Present' : ($usr->dab_is_present === 0 ? 'Absent' : '');
            
            $usr->is_attendance_updatable = true; // Can not change attendance status after 24 hours
            if($usr->dab_ins_at AND $usr->dab_ins_at < date('Y-m-d H:i:s',time() - (60*60*24))){
                $usr->is_attendance_updatable = false;
            }
            $scmResults[$idx] = $usr;
        }
                    
        return $this->respond($scmResults);
    } /* EOM */
    
    
} /* EOC */ 
