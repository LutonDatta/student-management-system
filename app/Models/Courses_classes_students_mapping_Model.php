<?php namespace App\Models;

use CodeIgniter\Model;

class Courses_classes_students_mapping_Model extends Model{
    protected $table      = 'courses_classes_students_mapping';
    protected $primaryKey = 'scm_id';
    protected $returnType = 'object';
    
    protected $allowedFields    = [
        'scm_u_id','scm_session_year','scm_class_id','scm_status',
        'scm_course_1','scm_course_2','scm_course_3','scm_course_4','scm_course_5','scm_course_6','scm_course_7',
        'scm_course_8','scm_course_9','scm_course_10','scm_course_11','scm_course_12','scm_course_13','scm_course_14','scm_course_15',
        'scm_course_op_1','scm_course_op_2','scm_course_op_3','scm_course_op_4','scm_course_op_5',
        'scm_deleted_at','scm_c_roll'
    ];

    protected $useSoftDeletes   = true; 
    protected $useTimestamps    = true;
    protected $skipValidation   = false;
    protected $createdField     = 'scm_inserted_at';
    protected $updatedField     = 'scm_updated_at';
    protected $deletedField     = 'scm_deleted_at';
    
    protected $validationRules  = [
        'scm_u_id'          => ['label' => 'UserID',        'rules' => 'integer'],
        'scm_session_year'  => ['label' => 'Session/Year',  'rules' => 'alpha_dash'],
        'scm_class_id'      => ['label' => 'Class ID',      'rules' => 'integer'],
        'scm_status'        => ['label' => 'Status',        'rules' => 'alpha_dash'],
        'scm_course_1'      => ['label' => 'Course 1',      'rules' => 'integer'],
        'scm_course_2'      => ['label' => 'Course 2',      'rules' => 'integer'],
        'scm_course_3'      => ['label' => 'Course 3',      'rules' => 'integer'],
        'scm_course_4'      => ['label' => 'Course 4',      'rules' => 'integer|permit_empty'],
        'scm_course_5'      => ['label' => 'Course 5',      'rules' => 'integer|permit_empty'],
        'scm_course_6'      => ['label' => 'Course 6',      'rules' => 'integer|permit_empty'],
        'scm_course_7'      => ['label' => 'Course 7',      'rules' => 'integer|permit_empty'],
        'scm_course_8'      => ['label' => 'Course 8',      'rules' => 'integer|permit_empty'],
        'scm_course_9'      => ['label' => 'Course 9',      'rules' => 'integer|permit_empty'],
        'scm_course_10'     => ['label' => 'Course 10',     'rules' => 'integer|permit_empty'],
        'scm_course_11'     => ['label' => 'Course 11',     'rules' => 'integer|permit_empty'],
        'scm_course_12'     => ['label' => 'Course 12',     'rules' => 'integer|permit_empty'],
        'scm_course_13'     => ['label' => 'Course 13',     'rules' => 'integer|permit_empty'],
        'scm_course_14'     => ['label' => 'Course 14',     'rules' => 'integer|permit_empty'],
        'scm_course_15'     => ['label' => 'Course 15',     'rules' => 'integer|permit_empty'],
        'scm_course_op_1'   => ['label' => 'Optional Course 1',     'rules' => 'integer|permit_empty'],
        'scm_course_op_2'   => ['label' => 'Optional Course 2',     'rules' => 'integer|permit_empty'],
        'scm_course_op_3'   => ['label' => 'Optional Course 3',     'rules' => 'integer|permit_empty'],
        'scm_course_op_4'   => ['label' => 'Optional Course 4',     'rules' => 'integer|permit_empty'],
        'scm_course_op_5'   => ['label' => 'Optional Course 5',     'rules' => 'integer|permit_empty'],
    ];
    
    /**
     * In some cases we need to save data without validation if data source is our own safe source. For example
     * we are just removing all class ids when we remove class from a row of a student.
     */
    public function clear_validation_rules(){
        $this->validationRules = [];
        return $this;
    }
    
    public function delete_permanently( $id = NULL ){
        return $this->delete( $id, TRUE );
    }
    
    public function getTableName( $withPrefix = false ){
        return $withPrefix ? $this->db->prefixTable($this->table) : $this->table;
    }
    
} /*EOC*/