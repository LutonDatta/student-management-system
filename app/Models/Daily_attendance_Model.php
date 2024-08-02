<?php namespace App\Models;

use CodeIgniter\Model;

class Daily_attendance_Model extends Model{
    protected $table            = 'daily_attendance_book';
    protected $primaryKey       = 'dab_id';
    
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;

    protected $createdField     = 'dab_ins_at';
    protected $updatedField     = 'dab_upd_at';
    protected $deletedField     = 'dab_del_at';

    protected $skipValidation     = false;
    
    protected $allowedFields = [ 
        'dab_scm_id',
        'dab_course_id',
        'dab_is_present',
        'dab_class_date'
    ];

    
    protected $validationRules  = [
        'dab_scm_id'        => ['label' => 'DAB SCM ID',    'rules' => 'required|intval|min_length[1]|max_length[10]'],
        'dab_course_id'     => ['label' => 'DAB Course ID', 'rules' => 'required|intval|min_length[1]|max_length[10]'],
        'dab_is_present'    => ['label' => 'DAB Is Present','rules' => 'required|intval|exact_length[1]'],
        'dab_class_date'    => ['label' => 'DAB Class Date','rules' => 'required|valid_date[Y-m-d]'],
    ];
    
    public function getTableName( $withPrefix = false ){
        return $withPrefix ? $this->db->prefixTable($this->table) : $this->table;
    }
    
} // EOC