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

class Migration_add_courses_table extends \CodeIgniter\Database\Migration {

    
    public function up(){    
        $this->db->disableForeignKeyChecks();
        
        $this->forge->addField([
            'co_id'         => [ 'type'=>'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => FALSE, 'auto_increment' => TRUE ],
            'co_title'      => ['type' => 'VARCHAR',  'constraint' => 250,'null' => FALSE, 'comment' => 'Course/subject name. Example: Bangla, Business Finance'],
            'co_code'       => ['type' => 'VARCHAR',  'constraint' => 250,'null' => FALSE, 'comment' => 'Course code defined by university. Optional. Example: BBA101'],
            'co_excerpt'    => ['type' => 'TEXT', 'null' => FALSE, 'comment' => 'Simple short description of this course.' ],
            'co_deleted_at' => ['type' => 'DATETIME', 'null' => TRUE ],
            'co_updated_at' => ['type' => 'DATETIME', 'null' => TRUE ],
            'co_inserted_at'=> ['type' => 'DATETIME', 'null' => TRUE ],
        ]);
        $this->forge->addPrimaryKey('co_id');
        $this->forge->addKey('co_deleted_at');
        $this->forge->addKey('co_updated_at');
        $this->forge->addKey('co_inserted_at');
        $this->forge->createTable('courses', TRUE, ['ENGINE' => 'InnoDB', 'comment'=>'Subjects or courses. A student read/study in his session or class.']);
        
        /* Add first support officer and allow him to get access to SOP. */
        (\Config\Database::seeder())->call('AddInitial_Courses');
        
        $this->db->enableForeignKeyChecks();
    }

    public function down(){        
        
    } // EOM
} // EOC

