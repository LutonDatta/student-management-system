<?php namespace App\Models;

use CodeIgniter\Model;

class Courses_Model extends Model{
    protected $table      = 'courses';
    protected $primaryKey = 'co_id';
    protected $returnType = 'object';
    
    protected $allowedFields    = [
        'co_title','co_code','co_excerpt'
    ];

    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;
    protected $skipValidation   = false;
    protected $createdField     = 'co_inserted_at';
    protected $updatedField     = 'co_updated_at';
    protected $deletedField     = 'co_deleted_at';
    
    protected $validationRules  = [
        'co_title'  => ['label' => 'Title',         'rules' => 'required|string|min_length[2]|max_length[60]'],
        'co_code'   => ['label' => 'Course Code',   'rules' => 'string|max_length[25]'],
        'co_excerpt'=> ['label' => 'Excerpt',       'rules' => 'string|max_length[220]'],
    ];
    
    /**
     * Find a list of courses which is associated with a specific class.
     * @param int $class_id
     * @param type $is_mandatory
     * @param int $limit
     * @param int $offset
     * @param mixed $ccm_session_year Session/year can be 2021,2021-22,2021-2022 etc
     * @return array Return empty array if no data found.
     */
    public function get_courses_by_class_id( 
            int $class_id, 
            $is_mandatory = true, 
            int $limit = 25, 
            int $offset = 0, 
            string $columns = 'courses.co_id,courses.co_title', 
            $ccm_session_year = '' 
    ){
        $cMap = service('CoursesClassesMappingModel')->withDeleted()->where([ 'ccm_class_id'=>  $class_id, 'ccm_is_compulsory' => ( $is_mandatory ? '1' : '0' ) ]);
        if(strlen( $ccm_session_year ) > 0){ $cMap->where('ccm_year_session', $ccm_session_year); } // Allow in case, we need to find courses based on sessions
        $coIds = $cMap->limit( $limit, $offset )->findColumn('ccm_course_id'); // Null or indexed array

        if( ! $coIds){ return []; } // Return empty array if no data found
        return $this->withDeleted()->select( $columns )->whereIn('courses.co_id', $coIds)->findAll( $limit, $offset );
    } /* EOM */
    
    
    public function delete_permanently( int $id = NULL ){
        return $this->delete( $id, TRUE );
    }
    
    
    public function getTableName( $withPrefix = false ){
        return $withPrefix ? $this->db->prefixTable($this->table) : $this->table;
    }
    
} /*EOC*/