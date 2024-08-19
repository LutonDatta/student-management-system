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

class Migration_add_hostel_and_rooms_table extends \CodeIgniter\Database\Migration {

    
    public function up(){    
        $this->db->disableForeignKeyChecks();
        
        $this->forge->addField([
            'hos_id'        => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => FALSE, 'auto_increment' => TRUE ],
            'hos_parent'    => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE, 'default' => NULL, 'comment' => 'Is it a sub class/or department? Add parent id here.' ],
            'hos_capacity'  => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => FALSE, 'default' => '1', 'comment' => '# of seats for room, # of floors for building, # of rooms for floor etc. We can figure our occupied or unoccupied seats using it.' ],
            'hos_title'     => [ 'type' => 'VARCHAR',  'constraint' => 250,'null' => FALSE, 'comment' => 'Example: Class 8 or Faculty of business administration'],
            'hos_excerpt'   => [ 'type' => 'TEXT', 'null' => FALSE, 'comment' => 'Simple short description of this class or faculty or department.' ],
            'hos_del_at'    => [ 'type' => 'DATETIME', 'null' => TRUE ],
            'hos_upd_at'    => [ 'type' => 'DATETIME', 'null' => TRUE ],
            'hos_ins_at'    => [ 'type' => 'DATETIME', 'null' => TRUE ],
        ]);
        $this->forge->addPrimaryKey('hos_id');
        $this->forge->addKey('hos_parent');
        $this->forge->addKey('hos_del_at');
        $this->forge->addKey('hos_upd_at');
        $this->forge->addKey('hos_ins_at');
        $this->forge->createTable('hostel_and_rooms', TRUE, ['ENGINE' => 'InnoDB', 'comment'=>'Buildings, Floors, Rooms and Seat are added to distribute to students from here.']);

        
        (\Config\Database::seeder())->call('AddInitial_HostelAndRooms');
        
        $this->db->enableForeignKeyChecks();
    }

    public function down(){        
        
    } // EOM
} // EOC

