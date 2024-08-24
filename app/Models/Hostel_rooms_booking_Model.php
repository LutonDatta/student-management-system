<?php namespace App\Models;

use CodeIgniter\Model;

class Hostel_rooms_booking_Model extends Model{
    protected $table      = 'hostel_rooms_booking';
    protected $primaryKey = 'hrb_id';
    protected $returnType = 'object';
    
    protected $allowedFields    = [
        'hrb_hos_id', 'hrb_seat_no', 'hrb_student_id', 'hrb_del_at'
    ];

    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;
    protected $skipValidation   = false;
    protected $createdField     = 'hrb_ins_at';
    protected $updatedField     = 'hrb_upd_at';
    protected $deletedField     = 'hrb_del_at';
    
    protected $validationRules  = [
        'hrb_hos_id'    => ['label' => 'Hostel Room ID','rules' => 'greater_than_equal_to[1]|max_length[11]'],
        'hrb_student_id'=> ['label' => 'Student ID',    'rules' => 'greater_than_equal_to[1]|max_length[11]'],
        'hrb_seat_no'   => ['label' => 'Seat Number',   'rules' => 'greater_than_equal_to[1]|max_length[11]'],
    ];
    
    public function delete_permanently( int $id = NULL ){
        return $this->delete( $id, TRUE );
    }
    
    public function getTableName( $withPrefix = false ){
        return $withPrefix ? $this->db->prefixTable($this->table) : $this->table;
    }
    
} // End class  