<?php namespace App\Models;

use CodeIgniter\Model;

class Library_items_quantities_Model extends Model{
    protected $table      = 'library_items_quantities';
    protected $primaryKey = 'lq_id';

    protected $returnType = 'object';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'lq_bk_id','lq_distributed_to','lq_distributed_at',
        'lq_returned_by','lq_returned_at','lq_is_distributed','lq_turnover','lq_serial_number'
        ];

    protected $useTimestamps = true;
    protected $createdField  = 'lq_inserted_at';
    protected $updatedField  = 'lq_updated_at';
    protected $deletedField  = 'lq_deleted_at';

    protected $skipValidation     = false;
    
    
    protected $validationRules  = [
        'lq_bk_id'          => ['label' => 'Item ID',       'rules' => 'required|numeric'],
        'lq_distributed_to' => ['label' => 'Distributed To','rules' => 'numeric|permit_empty'],
        'lq_returned_by'    => ['label' => 'Returned By',   'rules' => 'numeric|permit_empty'],
    ];
    
    public function getTableName( $withPrefix = false ){
        return $withPrefix ? $this->db->prefixTable($this->table) : $this->table;
    }
    
    /**
     * Mark any book collected after distribution.
     */
    public function mark_book_as_collected( $req ){
        $return = [];
        $present = $this->find( intval($req->getGet('markMeCollected')) );
        
        if(is_object($present)){
            if( ! $present->lq_distributed_to ){
                $return['display_msg'] = get_display_msg('Item not distributed. Please distribute first.','danger');
            }else{
                $svData = [
                    'lq_is_distributed' => '0',
                    'lq_returned_by'    => $present->lq_distributed_to,
                    'lq_returned_at'    => date('Y-m-d H:i:s'),
                ];

                if($this->set($svData)->update($present->{$this->primaryKey})){
                    $return['display_msg'] = get_display_msg('Item marked as collected.','success');
                }else{
                    $return['display_msg'] = get_display_msg('Failed to mark as item as collected.','danger');
                }
            }
        }
        return $return;
    }
    
    function delete_all_copies_of_a_book( int $book_id ){
        $this->useSoftDeletes = false;
        $delPer = $this->where('lq_bk_id',$book_id)->delete();
    }
    
    
    public function delete_permanently( int $id = NULL ){
        return $this->delete( $id, TRUE );
    }
} // EOC