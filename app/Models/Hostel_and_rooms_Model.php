<?php namespace App\Models;

use CodeIgniter\Model;

class Hostel_and_rooms_Model extends Model{
    protected $table      = 'hostel_and_rooms';
    protected $primaryKey = 'hos_id';
    protected $returnType = 'object';
    
    protected $allowedFields    = [
        'hos_parent','hos_title','hos_excerpt','hos_capacity'
    ];

    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;
    protected $skipValidation   = false;
    protected $createdField     = 'hos_ins_at';
    protected $updatedField     = 'hos_upd_at';
    protected $deletedField     = 'hos_del_at';
    
    protected $validationRules  = [
        'hos_parent'        => ['label' => 'Parent Item',   'rules' => 'permit_empty|greater_than_equal_to[1]|max_length[11]'],
        'hos_capacity'      => ['label' => 'Capacity',      'rules' => 'permit_empty|greater_than_equal_to[1]|max_length[11]'],
        // Use alpha_numeric_punct to allow some special cheracters in name including space like: Class XII (Rose)
        'hos_title'         => ['label' => 'Title',         'rules' => 'required|alpha_numeric_punct|min_length[3]|max_length[150]'],
        'hos_excerpt'       => ['label' => 'Excerpt',       'rules' => 'string|max_length[550]'],
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
        $d = $this->select('hos_id')->where(['hos_parent' => $item])->first();
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
    public function get_hostel_room_with_parent_label( bool $remove_parents = true, bool $esc_values = true, bool $return_row_obj = false ){
        $t      = $this->DBPrefix . $this->table;
        $sql    =  "SELECT 
                    t.hos_id, t.hos_title, t.hos_parent, t.hos_capacity,
                    t4.hos_title AS title_4, 
                    t3.hos_title AS title_3, 
                    t2.hos_title AS title_2, 
                    t1.hos_title AS title_1
                FROM $t AS t
                LEFT JOIN $t AS t4 ON t.hos_parent = t4.hos_id
                LEFT JOIN $t AS t3 ON t4.hos_parent = t3.hos_id
                LEFT JOIN $t AS t2 ON t3.hos_parent = t2.hos_id
                LEFT JOIN $t AS t1 ON t2.hos_parent = t1.hos_id
                ";
        if($remove_parents){
            $sql .= " WHERE t.hos_id NOT IN ( SELECT {$t}.hos_parent FROM $t WHERE {$t}.hos_parent IS NOT NULL ) ";
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
            $title = (is_string($cls->title_1) AND strlen($cls->title_1) > 0) ?  ($esc_values ? esc($cls->title_1) : $cls->title_1) . ' -> ' : '';
            $title .= (is_string($cls->title_2) AND strlen($cls->title_2) > 0) ? ($esc_values ? esc($cls->title_2) : $cls->title_2) . ' -> ' : '';
            $title .= (is_string($cls->title_3) AND strlen($cls->title_3) > 0) ? ($esc_values ? esc($cls->title_3) : $cls->title_3) . ' -> ' : '';
            $title .= (is_string($cls->title_4) AND strlen($cls->title_4) > 0) ? ($esc_values ? esc($cls->title_4) : $cls->title_4) . ' -> ' : '';
            $title .= $esc_values ? esc($cls->hos_title) : $cls->hos_title;
            if($return_row_obj){
                $cls->title = $title;
                $simplified_class_name[] = $cls;
            }else{
                $simplified_class_name[$cls->hos_id] = $title;
            }
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
    public function get_hostel_room_with_parent_label_for_dropdown( bool $remove_parents = true, string $pageSfx = 'clsprts', int $perPage = 20, bool $esc = true, int $pageNumber = 0, string $selectColumns = '' ){
        $items = $this->get_hostel_room_with_parent_label_with_pagination( $remove_parents, $pageSfx, $perPage, $pageNumber, $selectColumns, $esc);
        $list = [];
        foreach($items as $itm ){
            $list[$itm->hos_id] = $itm->title;
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
    public function get_hostel_room_with_parent_label_with_pagination( bool $remove_parents = true, string $pageSfx = 'clsprts', int $perPage = 20, int $pageNumber = 0, string $selectExtras = '', bool $esc = true, bool $addIdToTitle = false ){
        if($remove_parents){
            $this->whereNotIn("$this->table.hos_id", function($bldr){
                return $bldr
                        ->distinct()
                        ->select("{$this->DBPrefix}{$this->table}.hos_parent")
                        ->where("{$this->DBPrefix}{$this->table}.hos_parent >", '0') // Skip NULL values
                        ->from($this->table);
            });
        }
        
        $pager = \Config\Services::pager(null, null, false);
        $page  = $pageNumber >= 1 ? $pageNumber : $pager->getCurrentPage($pageSfx);
        
        // We may call this function from same request, so do not need to count many times, use previously counted rows to speed up
        // Speed up controller : Admission_automation->step_up_step_down() 
	$this->total_counted = (property_exists($this, 'total_counted') AND $this->total_counted > 0) ? $this->total_counted : $this->countAllResults(false);
        
	// Store it in the Pager library so it can be paginated in the views.
	$this->pager    = $pager->store($pageSfx, $page, $perPage, $this->total_counted);
	$offset         = ($page - 1) * $perPage;
                
        $selectCols = [
            "$this->table.hos_id", "$this->table.hos_parent", "$this->table.hos_capacity", "$this->table.hos_title",
            't1.hos_title AS title_1', 
            't2.hos_title AS title_2', 
            't3.hos_title AS title_3', 
            't4.hos_title AS title_4', 
            't5.hos_title AS title_5', 
        ];
        if( strlen($selectExtras) > 0 ){ $selectCols[] = $selectExtras; }
        
        $this
                ->select($selectCols)  
                ->join("$this->table AS {$this->DBPrefix}t1","t1.hos_id = {$this->table}.hos_parent",'left')
                ->join("$this->table AS {$this->DBPrefix}t2","t2.hos_id = {$this->DBPrefix}t1.hos_parent",'left')
                ->join("$this->table AS {$this->DBPrefix}t3","t3.hos_id = {$this->DBPrefix}t2.hos_parent",'left')
                ->join("$this->table AS {$this->DBPrefix}t4","t4.hos_id = {$this->DBPrefix}t3.hos_parent",'left')
                ->join("$this->table AS {$this->DBPrefix}t5","t5.hos_id = {$this->DBPrefix}t4.hos_parent",'left');
        
        $classes = [];
        
        foreach( $this->findAll($perPage, $offset) as $cls ){
            $title  = (is_string($cls->title_5) AND strlen($cls->title_5) > 0) ? ($esc ? esc($cls->title_5) : $cls->title_5) . ' -> ' : '';
            $title .= (is_string($cls->title_4) AND strlen($cls->title_4) > 0) ? ($esc ? esc($cls->title_4) : $cls->title_4) . ' -> ' : '';
            $title .= (is_string($cls->title_3) AND strlen($cls->title_3) > 0) ? ($esc ? esc($cls->title_3) : $cls->title_3) . ' -> ' : '';
            $title .= (is_string($cls->title_2) AND strlen($cls->title_2) > 0) ? ($esc ? esc($cls->title_2) : $cls->title_2) . ' -> ' : '';
            $title .= (is_string($cls->title_1) AND strlen($cls->title_1) > 0) ? ($esc ? esc($cls->title_1) : $cls->title_1) . ' -> ' : '';
            $title .= ($esc ? esc($cls->hos_title) : $cls->hos_title) . ($addIdToTitle ? " [{$cls->hos_id}]" : '');
            $cls->title = $title;
            $classes[] = $cls;
        }        

        usort($classes, function($a, $b){ return strcmp($a->title, $b->title);}); // Sort based on title
        return $classes;
    }
    
    public function get_single_hostel_room_with_parent_label( int $class_id, string $selectColumns = '' ){
        $selectCols = [
            "$this->table.hos_id",
            "MAX({$this->DBPrefix}{$this->table}.hos_parent) AS hos_parent",
            "MAX({$this->DBPrefix}{$this->table}.hos_title) AS hos_title",
            "MAX({$this->DBPrefix}t1.hos_title) AS title_1", 
            "MAX({$this->DBPrefix}t2.hos_title) AS title_2", 
            "MAX({$this->DBPrefix}t3.hos_title) AS title_3", 
            "MAX({$this->DBPrefix}t4.hos_title) AS title_4", 
            "MAX({$this->DBPrefix}t5.hos_title) AS title_5", 
        ];
        if(strlen($selectColumns) > 0){ $selectCols[] = $selectColumns; }
        
        $cls = $this
                ->select($selectCols)
                ->join("$this->table AS {$this->DBPrefix}t1","t1.hos_id = {$this->table}.hos_parent",'left')
                ->join("$this->table AS {$this->DBPrefix}t2","t2.hos_id = {$this->DBPrefix}t1.hos_parent",'left')
                ->join("$this->table AS {$this->DBPrefix}t3","t3.hos_id = {$this->DBPrefix}t2.hos_parent",'left')
                ->join("$this->table AS {$this->DBPrefix}t4","t4.hos_id = {$this->DBPrefix}t3.hos_parent",'left')
                ->join("$this->table AS {$this->DBPrefix}t5","t5.hos_id = {$this->DBPrefix}t4.hos_parent",'left')
                ->where("$this->table.hos_id", $class_id)
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
            $title .= $cls->hos_title;
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
    function is_this_hostel_room_id_can_be_parent( int $parent_class_id  ){
        
        if( $parent_class_id < 1 ){
            return true; // zero '0' can be parent item. Root item has a parent ID 0 
        }
        
        $t = $this->DBPrefix .  $this->table;
        $sql =  "SELECT
                    t1.hos_parent AS parent_1, 
                    t2.hos_parent AS parent_2, 
                    t3.hos_parent AS parent_3, 
                    t4.hos_parent AS parent_4 
                FROM $t AS t1
                    LEFT JOIN $t AS t2 ON t1.hos_parent = t2.hos_id
                    LEFT JOIN $t AS t3 ON t2.hos_parent = t3.hos_id
                    LEFT JOIN $t AS t4 ON t3.hos_parent = t4.hos_id
                 AND t1.hos_id = $parent_class_id LIMIT 1;";

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

    
    public function get_hostel_room_list_with_parent_label_by_id_list( array $class_ids ){
        $selectCols = [
            "$this->table.hos_id",
            "{$this->DBPrefix}{$this->table}.hos_parent AS hos_parent",
            "{$this->DBPrefix}{$this->table}.hos_title AS hos_title",
            "{$this->DBPrefix}t1.hos_title AS title_1", 
            "{$this->DBPrefix}t2.hos_title AS title_2", 
            "{$this->DBPrefix}t3.hos_title AS title_3", 
            "{$this->DBPrefix}t4.hos_title AS title_4", 
            "{$this->DBPrefix}t5.hos_title AS title_5", 
        ];
        
        $clsListBuilder = $this
                ->select($selectCols)
                ->join("$this->table AS {$this->DBPrefix}t1","t1.hos_id = {$this->table}.hos_parent",'left')
                ->join("$this->table AS {$this->DBPrefix}t2","t2.hos_id = {$this->DBPrefix}t1.hos_parent",'left')
                ->join("$this->table AS {$this->DBPrefix}t3","t3.hos_id = {$this->DBPrefix}t2.hos_parent",'left')
                ->join("$this->table AS {$this->DBPrefix}t4","t4.hos_id = {$this->DBPrefix}t3.hos_parent",'left')
                ->join("$this->table AS {$this->DBPrefix}t5","t5.hos_id = {$this->DBPrefix}t4.hos_parent",'left')
                ->whereIn("$this->table.hos_id", $class_ids);
                
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
            $title .= $cls->hos_title;
            $classListNames[intval($cls->hos_id)] = $title;
        }
        return $classListNames;
    } // EOM
    
    
} // End class  