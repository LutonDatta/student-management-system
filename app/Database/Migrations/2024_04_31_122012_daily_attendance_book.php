<?php namespace App\Database\Migrations;

/**
 * To track admission/application fee payment we need to track AO id for each student admission application. 
 * We might have rows in students classes courses mapping table if students are upgraded from junior class
 * to upper class.
 */

class Migration_daily_attendance_book extends \CodeIgniter\Database\Migration {

    
    public function up(){
        $this->db->disableForeignKeyChecks();
        
        
        $new_table = 'daily_attendance_book';
        
        $this->forge->addField([
            // We do not need: 'dab_student_u_id', 'dab_c_roll', 'dab_class_id', 'dab_session_year'
            // Because SCM ID can handle all of these
            
            'dab_id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => FALSE, 'auto_increment' => TRUE ],
            'dab_scm_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => FALSE ],
            'dab_course_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => FALSE ],
            // If teacher mark as present then value is 1, if mark as absent then value is 0. Otherwise row should be deleted.
            'dab_is_present'    => ['type'=> 'TINYINT', 'constraint'=> 1,'unsigned' => TRUE,'null' => FALSE, 'comment'   => 'If teacher mark as present then value is 1, if mark as absent then value is 0. Otherwise row should be deleted.' ],
            // NOTE: Column updated below. Default value (CURRENT_TIMESTAMP) does not work in all platform. When work in cloudways, not work in localhost and viseversa.
            'dab_class_date'    => ['type' => 'DATE', 'null' => FALSE, 'default' => '2021-08-28', 'COMMENT' => 'When this class is taken. Teacher may take class advance/due or take attendance of one date in another date. Example: 2021-08-28' ], 
            'dab_del_at'        => ['type' => 'DATETIME', 'null' => TRUE ], 
            'dab_upd_at'        => ['type' => 'DATETIME', 'null' => TRUE ], // Attendance updated
            'dab_ins_at'        => ['type' => 'DATETIME', 'null' => TRUE ], // When teacher take this attendance
        ]);         
        $this->forge->addPrimaryKey('dab_id');
        $this->forge->addKey('dab_scm_id');
        $this->forge->addKey('dab_course_id');
        $this->forge->addKey('dab_is_present');
        $this->forge->addKey('dab_class_date');
        $this->forge->createTable($new_table, TRUE, ['ENGINE' => 'InnoDB', 'comment' => 'Daily class attandance note book of students.']);
        
        // Do not delete these lines. When work in localhost not work in cloudways and viceversa.
        // Explicit Default Handling as of MySQL 8.0.13, CURRENT_TIMESTAMP -> CURRENT_DATE for DATE type column.
        $this->db->simpleQuery(
           "ALTER TABLE ". $this->db->prefixTable($new_table) . " MODIFY dab_class_date DATE DEFAULT (CURRENT_DATE) 
            NOT NULL COMMENT 'When this class is taken. Teacher may take class advance/due or take attendance of one date in another date. Example: 2021-08-28';"
        );

        $this->db->enableForeignKeyChecks();
    }

    public function down(){ 
        
        
    } // EOM
    
} // EOC

