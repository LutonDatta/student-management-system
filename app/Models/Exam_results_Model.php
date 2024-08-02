<?php namespace App\Models;

use CodeIgniter\Model;

class Exam_results_Model extends Model{
    protected $table            = 'exam_results';
    protected $primaryKey       = 'exr_id';
    
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;

    protected $createdField     = 'exr_inserted_at';
    protected $updatedField     = 'exr_updated_at';
    protected $deletedField     = 'exr_deleted_at';

    
    protected $skipValidation     = false;
    
    protected $allowedFields = [
        'exr_scm_id','exr_axdts_id',
        'exr_deleted_at','exr_updated_at','exr_inserted_at',
        'exr_co_1_id','exr_co_1_re','exr_co_1_ou','exr_co_2_id','exr_co_2_re','exr_co_2_ou',
        'exr_co_3_id','exr_co_3_re','exr_co_3_ou','exr_co_4_id','exr_co_4_re','exr_co_4_ou',
        'exr_co_5_id','exr_co_5_re','exr_co_5_ou','exr_co_6_id','exr_co_6_re','exr_co_6_ou',
        'exr_co_7_id','exr_co_7_re','exr_co_7_ou','exr_co_8_id','exr_co_8_re','exr_co_8_ou',
        'exr_co_9_id','exr_co_9_re','exr_co_9_ou','exr_co_10_id','exr_co_10_re','exr_co_10_ou',
        'exr_co_11_id','exr_co_11_re','exr_co_11_ou','exr_co_12_id','exr_co_12_re','exr_co_12_ou',
        'exr_co_13_id','exr_co_13_re','exr_co_13_ou','exr_co_14_id','exr_co_14_re','exr_co_14_ou',
        'exr_co_15_id','exr_co_15_re','exr_co_15_ou','exr_co_16_id','exr_co_16_re','exr_co_16_ou',
        'exr_co_17_id','exr_co_17_re','exr_co_17_ou','exr_co_18_id','exr_co_18_re','exr_co_18_ou',
        'exr_co_19_id','exr_co_19_re','exr_co_19_ou','exr_co_20_id','exr_co_20_re','exr_co_20_ou',
    ];

    
    protected $validationRules  = [
        'exr_scm_id'        => ['label' => 'SCM ID',        'rules' => 'required|intval|min_length[1]|max_length[10]'],
        'exr_axdts_id'      => ['label' => 'Exam Date Time','rules' => 'required|intval|min_length[1]|max_length[10]'],
    ];
    
    
    public function getTableName( $withPrefix = false ){
        return $withPrefix ? $this->db->prefixTable($this->table) : $this->table;
    } /* EOM */
    
    public function skipValidation( $skip = true ){
        $this->skipValidation = $skip;
        return $this;
    } /* EOM */
    
    
} /*EOC*/