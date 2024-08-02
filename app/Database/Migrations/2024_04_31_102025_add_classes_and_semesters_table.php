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

class Migration_add_classes_and_semesters_table extends \CodeIgniter\Database\Migration {

    
    public function up(){    
        $this->db->disableForeignKeyChecks();
        
        $this->forge->addField([
            'fcs_id'        => [ 'type'=>'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => FALSE, 'auto_increment' => TRUE ],
            'fcs_parent'    => [ 'type'=>'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE, 'default' => NULL, 'comment' => 'Is it a sub class/or department? Add parent id here.' ],
            'fcs_title'     => ['type' => 'VARCHAR',  'constraint' => 250,'null' => FALSE, 'comment' => 'Example: Class 8 or Faculty of business administration'],
            'fcs_excerpt'   => ['type' => 'TEXT', 'null' => FALSE, 'comment' => 'Simple short description of this class or faculty or department.' ],
            'fcs_session_starts' => ['type' => 'VARCHAR',  'constraint' => 250,'null' => FALSE, 'comment' => 'Month 3 character like jan, feb. Ii it is a parent faculty then it do not need this value. It is for class. Maximum limit is 12 months. Such as from jan to dec a class runs, a semester runs from july to december etc'],
            'fcs_session_ends' => ['type' => 'VARCHAR',  'constraint' => 25,'null' => FALSE],
            'fcs_del_at'  => ['type' => 'DATETIME', 'null' => TRUE ],
            'fcs_upd_at'  => ['type' => 'DATETIME', 'null' => TRUE ],
            'fcs_ins_at' => ['type' => 'DATETIME', 'null' => TRUE ],
        ]);
        $this->forge->addPrimaryKey('fcs_id');
        $this->forge->addKey('fcs_parent');
        $this->forge->addKey('fcs_del_at');
        $this->forge->addKey('fcs_upd_at');
        $this->forge->addKey('fcs_ins_at');
        $this->forge->createTable('classes_and_semesters', TRUE, ['ENGINE' => 'InnoDB', 'comment'=>'Class, semester, department and faculty are distributed here.']);

        
        /* Add first support officer and allow him to get access to SOP. */
        (\Config\Database::seeder())->call('AddInitial_ClassesAndSemesters');
        
        $this->db->enableForeignKeyChecks();
    }

    public function down(){        
        
    } // EOM
} // EOC

