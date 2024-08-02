<?php namespace App\Models;

use CodeIgniter\Model;

class Academic_exam_date_time_Model extends Model{
    protected $table            = 'exam_date_time';
    protected $primaryKey       = 'axdts_id';
    
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;

    protected $createdField     = 'axdts_inserted_at';
    protected $updatedField     = 'axdts_updated_at';
    protected $deletedField     = 'axdts_deleted_at';

    protected $skipValidation     = false;
    
    protected $allowedFields = [
        'axdts_id','axdts_class_id','axdts_session_year','axdts_type',
        'axdts_exam_starts_at','axdts_exam_ends_at',
        'axdts_deleted_at','axdts_updated_at','axdts_inserted_at',
        'axdts_exam_routine'
    ];

    
    protected $validationRules  = [
        'axdts_exam_starts_at'  => ['label' => 'Exam starts at',    'rules' => 'required|valid_date[Y-m-d H:i:s]'],
        'axdts_exam_ends_at'    => ['label' => 'Exam ends at',      'rules' => 'required|valid_date[Y-m-d H:i:s]'],
        'axdts_class_id'        => ['label' => 'Class ID',          'rules' => 'required|min_length[1]|max_length[255]'], // Serialized data of class IDs
        'axdts_session_year'    => ['label' => 'Session/Year',      'rules' => 'required|alpha_dash|max_length[9]'],
        'axdts_type'            => ['label' => 'Exam Type',         'rules' => 'required|in_list[1st_mid,2nd_mid,3rd_mid,4th_mid,pretest,test,sem_fin,final,mcq,assign,presen,ses_chan,yr_chan,others]'],
        // This row will be updated from another page, later
        'axdts_exam_routine'    => ['label' => 'Exam Routine',      'rules' => 'permit_empty|max_length[32000]'], // Serialized data of class IDs
    ];
    
    
    public function getTableName( $withPrefix = false ){
        return $withPrefix ? $this->db->prefixTable($this->table) : $this->table;
    }
    
    
} /*EOC*/