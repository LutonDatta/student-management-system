<?php namespace App\Database\Migrations;

/**
 * @package    Ultra-School
 * @author     Ultra-School Dev Team
 * @copyright  2019-2020 Ultra Data Safety Solutions Pvt. Ltd.
 * @license    Private License
 * @link       https://ultra-school.com
 * @since      Version 1.0.0
 * @filesource
 */

class Migration_add_hostel_rooms_booking_table extends \CodeIgniter\Database\Migration {

    
    public function up(){    
        $this->db->disableForeignKeyChecks();
        
        $this->forge->addField([
            'hrb_id'        => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => FALSE, 'auto_increment' => TRUE ],
            'hrb_hos_id'    => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE, 'default' => NULL, 'comment' => 'Hostel room number.' ],
            'hrb_seat_no'   => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE, 'default' => NULL, 'comment' => 'Seat number in a hostel room.' ],
            'hrb_student_id'=> [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE, 'default' => NULL, 'comment' => 'Student ID to whome the seat is occupied to.' ],
            'hrb_del_at'    => [ 'type' => 'DATETIME', 'null' => TRUE ],
            'hrb_upd_at'    => [ 'type' => 'DATETIME', 'null' => TRUE ],
            'hrb_ins_at'    => [ 'type' => 'DATETIME', 'null' => TRUE ],
        ]);
        $this->forge->addPrimaryKey('hrb_id');
        $this->forge->addKey('hrb_seat_no');
        $this->forge->addKey('hrb_del_at');
        $this->forge->addKey('hrb_upd_at');
        $this->forge->addKey('hrb_ins_at');
        $this->forge->createTable('hostel_rooms_booking', TRUE, ['ENGINE' => 'InnoDB', 'comment'=>'Seat booking of hostel rooms.']);
        
        $this->db->enableForeignKeyChecks();
    }

    public function down(){        
        
    } // EOM
} // EOC

