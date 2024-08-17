<?php namespace App\Models;

use CodeIgniter\Model;

class Classes_and_semesters_Model extends Model{
    protected $table      = 'classes_and_semesters';
    protected $primaryKey = 'fcs_id';
    protected $returnType = 'object';
    
    protected $allowedFields    = [
        'fcs_parent','fcs_title','fcs_excerpt','fcs_session_starts','fcs_session_ends'
    ];

    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;
    protected $skipValidation   = false;
    protected $createdField     = 'fcs_ins_at';
    protected $updatedField     = 'fcs_upd_at';
    protected $deletedField     = 'fcs_del_at';
    
    protected $validationRules  = [
        'fcs_parent'        => ['label' => 'Parent Item',   'rules' => 'permit_empty|greater_than_equal_to[1]|max_length[11]'],
        // Use alpha_numeric_punct to allow some special cheracters in name including space like: Class XII (Rose)
        'fcs_title'         => ['label' => 'Title',         'rules' => 'required|alpha_numeric_punct|min_length[3]|max_length[150]'],
        'fcs_excerpt'       => ['label' => 'Excerpt',       'rules' => 'string|max_length[550]'],
        'fcs_session_starts'=> ['label' => 'Seesion Starts','rules' => 'string|max_length[5]'],
        'fcs_session_ends'  => ['label' => 'Session Ends',  'rules' => 'string|max_length[5]'],
    ];
    
    public function delete_permanently( int $id = NULL ){
        return $this->delete( $id, TRUE );
    }
    
    /**
     * Checks if one parent item (faculty/department) has sub items (dept/semester) under it.
     * @param int $item
     * @param object $db
     * @return boolean Return true if child item exists.
     */
    public function has_child_item_of_faculty( int $item ){
        $d = $this->select('fcs_id')->where(['fcs_parent' => $item])->first();
        return is_object( $d );
    }
    
    public function getTableName( $withPrefix = false ){
        return $withPrefix ? $this->db->prefixTable($this->table) : $this->table;
    }
    
    /**
     * Get a list of class/semester name along with its parent department/faculty name (if exists)
     * @param bool $remove_parents If false passed it will include parent items separately.
     * return array( child_class_id => parent faculty name -> dept name -> class name ,..)
     */
    public function get_classes_with_parent_label( bool $remove_parents = true, bool $esc_values = true ){
        $t      = $this->DBPrefix . 'classes_and_semesters';
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
        if($remove_parents){
            $sql .= " AND t.fcs_id NOT IN ( SELECT DISTINCT $t.fcs_parent FROM $t ) ";
        }
        
        /**
         * Generally a school needs only 50 classes max and 200 max including parent items.
         * But they may add may classes. If thousands of rows added to the database, as 
         * we are loading all of them, it may cause memory limit error. To prevent memory
         * leak error we should add LIMIT to the query. Limit should be 1000 to 3000 is good.
         */
        $sql .= " LIMIT 3000 ";
        
        $classes = $this->query($sql)->getResult();
        $simplified_class_name = [];
        foreach( $classes as $cls ){
            $title = (is_string($cls->title_1) AND strlen($cls->title_1) > 0) ? $cls->title_1 . ' -> ' : '';
            $title .= (is_string($cls->title_2) AND strlen($cls->title_2) > 0) ? $cls->title_2 . ' -> ' : '';
            $title .= (is_string($cls->title_3) AND strlen($cls->title_3) > 0) ? $cls->title_3 . ' -> ' : '';
            $title .= (is_string($cls->title_4) AND strlen($cls->title_4) > 0) ? $cls->title_4 . ' -> ' : '';
            $title .= $cls->fcs_title;
            $simplified_class_name[$cls->fcs_id] = $esc_values ? esc($title) : $title;
        }
        return $simplified_class_name; // Useable in dropdown
    }

    /**
     * Returns a list of classes/semesters etc with parent label with pagination capability to show in dropdown.
     * @param bool $remove_parents
     * @param string $pageSfx
     * @param int $perPage
     * @param bool $esc Escape title. Some cases we do not need to escape, pass false to prevent double escape if needed.
     * @return type
     */
    public function get_classes_with_parent_label_for_dropdown( bool $remove_parents = true, string $pageSfx = 'clsprts', int $perPage = 20, bool $esc = true ){
        $items = $this->get_classes_with_parent_label_with_pagination( $remove_parents, $pageSfx, $perPage );
        $list = [];
        foreach($items as $itm ){
            $list[$itm->fcs_id] = $esc ? esc($itm->title) : $itm->title;
        }
        return $list;
    }
    
    
    /**
     * Return a list of class/semesters/department/faculties with pagination capability.
     * @param bool $remove_parents
     * @param string $pageSfx
     * @param int $perPage
     * @return type
     */
    public function get_classes_with_parent_label_with_pagination( bool $remove_parents = true, string $pageSfx = 'clsprts', int $perPage = 20, int $page = 0, string $selectExtras = '' ){
        if($remove_parents){
            $this->whereNotIn("$this->table.fcs_id", function($bldr){
                return $bldr
                        ->distinct()
                        ->select("{$this->DBPrefix}{$this->table}.fcs_parent")
                        ->where("{$this->DBPrefix}{$this->table}.fcs_parent >", '0') // Skip NULL values
                        ->from($this->table);
            });
        }
        
        $pager = \Config\Services::pager(null, null, false);
        $page  = $page >= 1 ? $page : $pager->getCurrentPage($pageSfx);
        
        // We may call this function from same request, so do not need to count many times, use previously counted rows to speed up
        // Speed up controller : Admission_automation->step_up_step_down() 
	$this->total_counted = (property_exists($this, 'total_counted') AND $this->total_counted > 0) 
                ? $this->total_counted 
                : $this->countAllResults(false);
        
	// Store it in the Pager library so it can be paginated in the views.
	$this->pager    = $pager->store($pageSfx, $page, $perPage, $this->total_counted);
	$offset         = ($page - 1) * $perPage;
                
        $selectCols = [
            "$this->table.fcs_id",
            "$this->table.fcs_parent",
            "$this->table.fcs_title",
            't1.fcs_title AS title_1', 
            't2.fcs_title AS title_2', 
            't3.fcs_title AS title_3', 
            't4.fcs_title AS title_4', 
            't5.fcs_title AS title_5', 
        ];
        if( strlen($selectExtras) > 0 ){ $selectCols[] = $selectExtras; }
        
        $this
                ->select($selectCols)  
                ->join("$this->table AS {$this->DBPrefix}t1","t1.fcs_id = {$this->table}.fcs_parent",'left')
                ->join("$this->table AS {$this->DBPrefix}t2","t2.fcs_id = {$this->DBPrefix}t1.fcs_parent",'left')
                ->join("$this->table AS {$this->DBPrefix}t3","t3.fcs_id = {$this->DBPrefix}t2.fcs_parent",'left')
                ->join("$this->table AS {$this->DBPrefix}t4","t4.fcs_id = {$this->DBPrefix}t3.fcs_parent",'left')
                ->join("$this->table AS {$this->DBPrefix}t5","t5.fcs_id = {$this->DBPrefix}t4.fcs_parent",'left');
        
        $classes = [];
        
        foreach( $this->findAll($perPage, $offset) as $cls ){
            $title  = (is_string($cls->title_5) AND strlen($cls->title_5) > 0) ? $cls->title_5 . ' -> ' : '';
            $title .= (is_string($cls->title_4) AND strlen($cls->title_4) > 0) ? $cls->title_4 . ' -> ' : '';
            $title .= (is_string($cls->title_3) AND strlen($cls->title_3) > 0) ? $cls->title_3 . ' -> ' : '';
            $title .= (is_string($cls->title_2) AND strlen($cls->title_2) > 0) ? $cls->title_2 . ' -> ' : '';
            $title .= (is_string($cls->title_1) AND strlen($cls->title_1) > 0) ? $cls->title_1 . ' -> ' : '';
            $title .= $cls->fcs_title . " [{$cls->fcs_id}]";
            $cls->title = $title;
            $classes[] = $cls;
        }        

        usort($classes, function($a, $b){ return strcmp($a->title, $b->title);}); // Sort based on title
        return $classes;
    }
    
    public function get_single_class_with_parent_label( int $class_id, string $selectColumns = '' ){
        $selectCols = [
            "$this->table.fcs_id",
            "MAX({$this->DBPrefix}{$this->table}.fcs_parent) AS fcs_parent",
            "MAX({$this->DBPrefix}{$this->table}.fcs_title) AS fcs_title",
            "MAX({$this->DBPrefix}t1.fcs_title) AS title_1", 
            "MAX({$this->DBPrefix}t2.fcs_title) AS title_2", 
            "MAX({$this->DBPrefix}t3.fcs_title) AS title_3", 
            "MAX({$this->DBPrefix}t4.fcs_title) AS title_4", 
            "MAX({$this->DBPrefix}t5.fcs_title) AS title_5", 
        ];
        if(strlen($selectColumns) > 0){ $selectCols[] = $selectColumns; }
        
        $cls = $this
                ->select($selectCols)
                ->join("$this->table AS {$this->DBPrefix}t1","t1.fcs_id = {$this->table}.fcs_parent",'left')
                ->join("$this->table AS {$this->DBPrefix}t2","t2.fcs_id = {$this->DBPrefix}t1.fcs_parent",'left')
                ->join("$this->table AS {$this->DBPrefix}t3","t3.fcs_id = {$this->DBPrefix}t2.fcs_parent",'left')
                ->join("$this->table AS {$this->DBPrefix}t4","t4.fcs_id = {$this->DBPrefix}t3.fcs_parent",'left')
                ->join("$this->table AS {$this->DBPrefix}t5","t5.fcs_id = {$this->DBPrefix}t4.fcs_parent",'left')
                ->where("$this->table.fcs_id", $class_id)
                ->withDeleted()
                ->first();
        
        if( ! is_object($cls)){
            return null; // If id is wrong, we have null value here
        }
                    
            $title  = (is_string($cls->title_5) AND strlen($cls->title_5) > 0) ? $cls->title_5 . ' > ' : '';
            $title .= (is_string($cls->title_4) AND strlen($cls->title_4) > 0) ? $cls->title_4 . ' > ' : '';
            $title .= (is_string($cls->title_3) AND strlen($cls->title_3) > 0) ? $cls->title_3 . ' > ' : '';
            $title .= (is_string($cls->title_2) AND strlen($cls->title_2) > 0) ? $cls->title_2 . ' > ' : '';
            $title .= (is_string($cls->title_1) AND strlen($cls->title_1) > 0) ? $cls->title_1 . ' > ' : '';
            $title .= $cls->fcs_title;
            $cls->title = $title;
        return $cls;
    }
    
    
    /**
     * Checks for valid parent item. Is a specific class can be a parent of another class.
     * 1. Check maximum depth.
     * 2. Check if valid ID row exists.
     * @param int $parent_item It can be 0 if no parent item set.
     * @return boolean
     */    
    function is_this_class_id_can_be_parent( int $parent_class_id  ){
        
        if( $parent_class_id < 1 ){
            return true; // zero '0' can be parent item. Root item has a parent ID 0 
        }
        
        $t = $this->DBPrefix . 'classes_and_semesters';
        $sql =  "SELECT
                    t1.fcs_parent AS parent_1, 
                    t2.fcs_parent AS parent_2, 
                    t3.fcs_parent AS parent_3, 
                    t4.fcs_parent AS parent_4 
                FROM $t AS t1
                    LEFT JOIN $t AS t2 ON t1.fcs_parent = t2.fcs_id
                    LEFT JOIN $t AS t3 ON t2.fcs_parent = t3.fcs_id
                    LEFT JOIN $t AS t4 ON t3.fcs_parent = t4.fcs_id
                 AND t1.fcs_id = $parent_class_id LIMIT 1;";

        $parent = $this->query($sql)->getRow();
        if( ! is_object($parent)){
            // Valid parent ID must return an object as it have a row in database
            return false; // Invalid parent ID
        }
        if(intval($parent->parent_4) > 0 ){
            // 4th parent item is also a sub item of another item, so
            // it can not be a parent item of another item.
            return false;
        }
        // Valid parent item found and it is in a proper level.
        // We do not accept an item as parent item which is 4th sub item of another item
        return true;
    }

    
    public function get_class_list_with_parent_label_by_class_id_list( array $class_ids ){
        $selectCols = [
            "$this->table.fcs_id",
            "{$this->DBPrefix}{$this->table}.fcs_parent AS fcs_parent",
            "{$this->DBPrefix}{$this->table}.fcs_title AS fcs_title",
            "{$this->DBPrefix}t1.fcs_title AS title_1", 
            "{$this->DBPrefix}t2.fcs_title AS title_2", 
            "{$this->DBPrefix}t3.fcs_title AS title_3", 
            "{$this->DBPrefix}t4.fcs_title AS title_4", 
            "{$this->DBPrefix}t5.fcs_title AS title_5", 
        ];
        
        $clsListBuilder = $this
                ->select($selectCols)
                ->join("$this->table AS {$this->DBPrefix}t1","t1.fcs_id = {$this->table}.fcs_parent",'left')
                ->join("$this->table AS {$this->DBPrefix}t2","t2.fcs_id = {$this->DBPrefix}t1.fcs_parent",'left')
                ->join("$this->table AS {$this->DBPrefix}t3","t3.fcs_id = {$this->DBPrefix}t2.fcs_parent",'left')
                ->join("$this->table AS {$this->DBPrefix}t4","t4.fcs_id = {$this->DBPrefix}t3.fcs_parent",'left')
                ->join("$this->table AS {$this->DBPrefix}t5","t5.fcs_id = {$this->DBPrefix}t4.fcs_parent",'left')
                ->whereIn("$this->table.fcs_id", $class_ids);
                
        $clsList = $clsListBuilder->withDeleted()->find();
        
        if( ! is_array($clsList)){
            return null; // If id is wrong, we have null value here
        }
                 
        $classListNames = [];
        foreach($clsList as $cls){
            $title  = (is_string($cls->title_5) AND strlen($cls->title_5) > 0) ? $cls->title_5 . ' > ' : '';
            $title .= (is_string($cls->title_4) AND strlen($cls->title_4) > 0) ? $cls->title_4 . ' > ' : '';
            $title .= (is_string($cls->title_3) AND strlen($cls->title_3) > 0) ? $cls->title_3 . ' > ' : '';
            $title .= (is_string($cls->title_2) AND strlen($cls->title_2) > 0) ? $cls->title_2 . ' > ' : '';
            $title .= (is_string($cls->title_1) AND strlen($cls->title_1) > 0) ? $cls->title_1 . ' > ' : '';
            $title .= $cls->fcs_title;
            $classListNames[intval($cls->fcs_id)] = $title;
        }
        return $classListNames;
    } // EOM
    
    
} // End class  