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

class Migration_add_courses_classes_students_mapping_table extends \CodeIgniter\Database\Migration {

    
    public function up(){    
        $this->db->disableForeignKeyChecks();
        
        $this->forge->addField([
            'scm_id'            => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => FALSE, 'auto_increment' => TRUE ],
            'scm_u_id'          => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => FALSE, 'default' => '0', 'comment' => 'Student ID. This is user ID actually.' ],
            'scm_session_year'  => [ 'type' => 'VARCHAR', 'constraint' => 9, 'null' => TRUE, 'comment' => 'This student is admitted to this class for which year/session? It can be 2019 or 2019-11 or 2019-2020.' ], 
            'scm_class_id'      => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'default' => '0', 'null' => FALSE, 'comment' => 'In which class this student is admitted?' ], 
            // requested = Student requested to get admitted to this class/semester (APPLIED)
            // whitelisted = Admin whitelisted this student for admission consideration
            // exam_phase = This student is in exam phase, need to sit in examination like Written/MCQ
            // viva_phase = Selected for Viva, need to sit for viva
            // rejected = authority rejected admission requests of the students 
            // cancelled = student/user cancelled his own admission requests to that institution
            // admitted = authority made this user a student of this class/semester
            // passed = this student passed this course
            // dropped = student is dropped from the class or session, or failed
            'scm_status'        => [ 'type' => 'ENUM',  'constraint' => ['requested','whitelisted','exam_phase','viva_phase','rejected','canceled','admitted','passed','dropped'], 'default' => 'requested', 'null' => FALSE],
            'scm_c_roll'    => ['type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'null' => FALSE, 'default' => '0', 'after' => 'scm_status', 'comment' => 'Class roll of the student - after admission we need to assign it' ], 
        
            // A student can be admitted to 15 compulsory and 5 optional courses in a class/session maximum to our site. 
            // Normally 10 -12 is ideal. This is course Id of courses table.
            // Allow NULL values, as foreign key will not accept 0. because 0 is not a valid row (primary key value)
            'scm_course_1'      => [ 'type'=>'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE ],
            'scm_course_2'      => [ 'type'=>'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE ],
            'scm_course_3'      => [ 'type'=>'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE ],
            'scm_course_4'      => [ 'type'=>'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE ],
            'scm_course_5'      => [ 'type'=>'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE ],
            'scm_course_6'      => [ 'type'=>'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE ],
            'scm_course_7'      => [ 'type'=>'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE ],
            'scm_course_8'      => [ 'type'=>'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE ],
            'scm_course_9'      => [ 'type'=>'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE ],
            'scm_course_10'     => [ 'type'=>'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE ],
            'scm_course_11'     => [ 'type'=>'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE ],
            'scm_course_12'     => [ 'type'=>'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE ],
            'scm_course_13'     => [ 'type'=>'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE ],
            'scm_course_14'     => [ 'type'=>'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE ],
            'scm_course_15'     => [ 'type'=>'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE ],
            'scm_course_op_1'   => [ 'type'=>'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE ], // Optional
            'scm_course_op_2'   => [ 'type'=>'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE ],
            'scm_course_op_3'   => [ 'type'=>'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE ],
            'scm_course_op_4'   => [ 'type'=>'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE ],
            'scm_course_op_5'   => [ 'type'=>'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE ],
            'scm_deleted_at'    => ['type' => 'DATETIME', 'null' => TRUE ],
            'scm_updated_at'    => ['type' => 'DATETIME', 'null' => TRUE ],
            'scm_inserted_at'   => ['type' => 'DATETIME', 'null' => TRUE ],
        ]);
        $this->forge->addPrimaryKey('scm_id');
        $this->forge->addKey('scm_u_id');
        $this->forge->addKey('scm_status');
        $this->forge->addKey('scm_c_roll');
        $this->forge->addKey('scm_class_id');
        $this->forge->addKey('scm_session_year');
        $this->forge->addKey('scm_deleted_at');
        $this->forge->addKey('scm_updated_at');
        $this->forge->addKey('scm_inserted_at');
        $this->forge->createTable('courses_classes_students_mapping', TRUE, ['ENGINE' => 'InnoDB', 'comment'=>'Which student is admitted to which class, semester? and which copurses includes to that class?']);
        
        $this->db->enableForeignKeyChecks();
    }

    public function down(){   
        
    } // EOM
} // EOC

