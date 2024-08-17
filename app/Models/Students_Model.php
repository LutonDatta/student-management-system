<?php namespace App\Models;

use CodeIgniter\Model;

class Students_Model extends Model{
    protected $table            = 'students';
    protected $primaryKey       = 'student_u_id';
    
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;

    protected $createdField     = 'student_u_inserted_at';
    protected $updatedField     = 'student_u_updated_at';
    protected $deletedField     = 'student_u_deleted_at';

    protected $skipValidation     = false;
    
    protected $allowedFields = [
        'student_u_id', /* Allow primary key, otherwise we can not import rows to this table. */
        'student_u_email_own',
        'student_u_mobile_own','student_u_mobile_father','student_u_mobile_mother',
        'student_u_addr_thana', /* It is thana or subdistrict */
        'student_u_name_initial','student_u_name_first','student_u_name_middle','student_u_name_last',
        'student_u_father_name','student_u_mother_name',
        'student_u_nid_no','student_u_birth_reg_no','student_u_date_of_birth',
        'student_u_gender','student_u_religion',
        'student_u_addr_country','student_u_addr_state','student_u_addr_district',
        'student_u_addr_post_office','student_u_addr_zip_code','student_u_addr_village',
        'student_u_addr_road_house_no',
        'student_u_deleted_at', // Allow SOP to trash and Untrash
    ];

    
    protected $validationRules  = [];
    
    
    public function getTableName( $withPrefix = false ){
        return $withPrefix ? $this->db->prefixTable($this->table) : $this->table;
    }
    
} // EOC