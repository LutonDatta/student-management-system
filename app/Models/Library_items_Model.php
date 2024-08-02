<?php namespace App\Models;

use CodeIgniter\Model;

class Library_items_Model extends Model{
    protected $table      = 'library_items';
    protected $primaryKey = 'bk_id';

    protected $returnType = 'object';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'bk_title','bk_code','bk_excerpt','bk_deleted_at',
        ];

    protected $useTimestamps = true;
    protected $createdField  = 'bk_inserted_at';
    protected $updatedField  = 'bk_updated_at';
    protected $deletedField  = 'bk_deleted_at';

    protected $skipValidation     = false;
    
    
    protected $validationRules  = [
        'bk_title'  => ['label' => 'Title',  'rules' => 'required|min_length[2]|max_length[70]|alpha_numeric_space'],
        'bk_excerpt'=> ['label' => 'Excerpt', 'rules' => 'required|string|max_length[700]'],
        'bk_code'   => ['label' => 'Code',    'rules' => 'required|alpha_dash'],
    ];
    
    public function getTableName( $withPrefix = false ){
        return $withPrefix ? $this->db->prefixTable($this->table) : $this->table;
    }
    
    public function delete_permanently( int $id = NULL ){
        return $this->delete( $id, TRUE );
    }
    
    public function get_all_items_with_pagination($request){
        $perPage        = 15;
        $pagerGroup     = 'library_items_books';
        $page           = intval( service('request')->getGet("page_$pagerGroup")); $page = $page < 1 ? 1 : $page; // Page number must be 1 or more
        $offset         = $page * $perPage - $perPage;
        
        $map            = $this->prefixTable('library_items_quantities');
        $data['items']  = $this
                ->select(implode(',',array(
                    "bk_id, MIN(bk_title), MIN(bk_code), MIN(bk_excerpt), MIN(bk_deleted_at), MIN(bk_updated_at), MIN(bk_inserted_at)",
                    "COUNT(lq_id) AS num_rows, SUM(lq_turnover) AS qun_turnover",
                )))
                ->join($map,"bk_id = lq_bk_id", 'left')
                ->groupBy("bk_id")
                ->orderBy("bk_id DESC")
                ->findAll($perPage,$offset);
        
        // Paginate do not work with groupBy, paginate again to overwrite previous invalid links
        $this->paginate($perPage,$pagerGroup);
        $data["pager"]  = $this->pager->links($pagerGroup);
        $data["page"]  = $page;
        return $data;
    }
    
    
} // EOC