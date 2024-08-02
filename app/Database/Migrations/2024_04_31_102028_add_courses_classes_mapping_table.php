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

class Migration_add_courses_classes_mapping_table extends \CodeIgniter\Database\Migration {

    
    public function up(){    
        $this->db->disableForeignKeyChecks();
       
        $this->forge->addField([
            'ccm_id'            => [ 'type' => 'INT',    'constraint' => 11, 'unsigned' => TRUE, 'null' => FALSE, 'auto_increment' => TRUE ],
            'ccm_class_id'      => [ 'type' => 'INT',    'constraint' => 11, 'unsigned' => TRUE, 'null' => FALSE, 'comment' => 'Class ID or semester ID' ],
            'ccm_year_session'  => [ 'type' => 'VARCHAR','constraint' => 250,'null'=> FALSE, 'default'=> '','comment' => 'Year or session. Ex: 2020, 2020-21, 2020-2021 etc.'],
            'ccm_course_id'     => [ 'type' => 'INT',    'constraint' => 11, 'unsigned' => TRUE, 'null' => FALSE, 'comment' => 'Course ID' ],
            'ccm_is_compulsory' => [ 'type' => 'TINYINT','constraint' => 1,  'default' => '0', 'null' => FALSE, 'comment' => 'Is this course mandatory for students? Some subjects might be optional.' ],
            
            'ccm_deleted_at'  => ['type' => 'DATETIME', 'null' => TRUE ],
            'ccm_updated_at'  => ['type' => 'DATETIME', 'null' => TRUE ],
            'ccm_inserted_at' => ['type' => 'DATETIME', 'null' => TRUE ], 
        ]);
        $this->forge->addPrimaryKey('ccm_id');
        $this->forge->addKey('ccm_course_id');
        $this->forge->addKey('ccm_class_id');
        $this->forge->addKey('ccm_year_session');
        $this->forge->addKey('ccm_is_compulsory');
        $this->forge->addKey('ccm_deleted_at');
        $this->forge->addKey('ccm_updated_at');
        $this->forge->addKey('ccm_inserted_at');
        $this->forge->createTable('courses_classes_mapping', TRUE, ['ENGINE' => 'InnoDB', 'comment'=>'Which courses are read in which class/semesters?.']);

        $this->db->enableForeignKeyChecks();
        
    }

    public function down(){     
        
    } // EOM
} // EOC

