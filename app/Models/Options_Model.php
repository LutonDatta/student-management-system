<?php namespace App\Models;

use CodeIgniter\Model;

class Options_Model extends Model{
    protected $table            = 'options';
    protected $primaryKey       = 'option_id';
    
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;

    protected $createdField     = 'option_inserted_at';
    protected $updatedField     = 'option_updated_at';
    protected $deletedField     = 'option_deleted_at';

    protected $skipValidation     = false;
    
    protected $allowedFields = [ 'option_id','option_key','option_value','option_deleted_at','option_updated_at','option_inserted_at' ];

    
    protected $validationRules  = [ ];
    
    
    public function delete_permanently( $id = NULL ){
        return $this->delete($id, TRUE);
    }
    
    /**
     * Allow any method from controller to change permanent deletion settings.
     * @return $this
     */
    public function force_permanent_deletion(){
        $this->useSoftDeletes = false;
        return $this;
    }
    
    public function getTableName( $withPrefix = false ){
        return $withPrefix ? $this->db->prefixTable($this->table) : $this->table;
    }
    
} // EOC