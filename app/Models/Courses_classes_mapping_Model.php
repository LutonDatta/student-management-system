<?php namespace App\Models;

use CodeIgniter\Model;

class Courses_classes_mapping_Model extends Model{
    protected $table      = 'courses_classes_mapping';
    protected $primaryKey = 'ccm_id';
    protected $returnType = 'object';
    
    protected $allowedFields    = [
        'ccm_year_session', 'ccm_class_id','ccm_course_id','ccm_is_compulsory'
    ];

    protected $useSoftDeletes   = true; 
    protected $useTimestamps    = true;
    protected $skipValidation   = false;
    protected $createdField     = 'ccm_inserted_at';
    protected $updatedField     = 'ccm_updated_at';
    protected $deletedField     = 'ccm_deleted_at';
    
    protected $validationRules  = [
        'ccm_class_id'          => ['label' => 'Mapping Class ID',         'rules' => 'integer'],
        'ccm_year_session'      => ['label' => 'Mapping Year/Session',      'rules' => 'alpha_dash'],
        'ccm_course_id'         => ['label' => 'Mapping Course ID',         'rules' => 'integer'],
        'ccm_is_compulsory'     => ['label' => 'Mapping Compulsory',         'rules' => 'integer'],
    ];
    
    public function delete_permanently( int $id = NULL ){
        return $this->delete( $id, TRUE );
    }
    
    public function getTableName( $withPrefix = false ){
        return $withPrefix ? $this->db->prefixTable($this->table) : $this->table;
    }
    
    /* Need to skip validation to add class teacher */
    public function skipValidation( bool $skip = true ){
        $this->skipValidation = $skip;
        return $this;
    } /* EOM */
    
    
    public function get_classes_names_with_parent_label_of_these_class_ids( array $class_IDs = [] ){
        if(count($class_IDs) < 1 ){ return []; } // We expect array as return value, make sure we must have at least 1 id
        
        $t      = service('ClassesAndSemestersModel')->db->DBPrefix . 'classes_and_semesters';
        $sql    =  "SELECT 
                    t.fcs_id, t.fcs_title, t.fcs_parent, 
                    t4.fcs_title AS title_4, 
                    t3.fcs_title AS title_3, 
                    t2.fcs_title AS title_2, 
                    t1.fcs_title AS title_1
                FROM $t AS t
                LEFT JOIN $t AS t4 ON t.fcs_parent = t4.fcs_id
                LEFT JOIN $t AS t3 ON t4.fcs_parent = t3.fcs_id
                LEFT JOIN $t AS t2 ON t3.fcs_parent = t2.fcs_id
                LEFT JOIN $t AS t1 ON t2.fcs_parent = t1.fcs_id
                ";
        $sql .= " AND t.fcs_id IN ( " . implode(',', $class_IDs ) . ' ) ';        
        $sql .= " LIMIT 100;";
        
        $classes = service('ClassesAndSemestersModel')->db->query($sql)->getResult();
        $simplified_class_name = [];
        foreach( $classes as $cls ){
            $title = (is_string($cls->title_1) AND strlen($cls->title_1) > 0) ? esc($cls->title_1) . ' -> ' : '';
            $title .= (is_string($cls->title_2) AND strlen($cls->title_2) > 0) ? esc($cls->title_2) . ' -> ' : '';
            $title .= (is_string($cls->title_3) AND strlen($cls->title_3) > 0) ? esc($cls->title_3) . ' -> ' : '';
            $title .= (is_string($cls->title_4) AND strlen($cls->title_4) > 0) ? esc($cls->title_4) . ' -> ' : '';
            $title .= esc($cls->fcs_title);
            $simplified_class_name[$cls->fcs_id] = $title;
        }
        return $simplified_class_name; // Useable in dropdown
    } // EOM
    
    
    
} /*EOC*/