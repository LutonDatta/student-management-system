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

class Migration_add_exam_results_table extends \CodeIgniter\Database\Migration {

    
    public function up(){    
        $this->db->disableForeignKeyChecks();
        
        $this->forge->addField([
            'exr_id'            => ['type'=>'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => FALSE, 'auto_increment' => TRUE ],
            'exr_axdts_id'      => ['type'=>'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => FALSE ],
            'exr_scm_id'        => ['type'=>'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => FALSE ],
            'exr_deleted_at'    => ['type'=>'DATETIME','null' => true ], 
            'exr_updated_at'    => ['type'=>'DATETIME','null' => true ], 
            'exr_inserted_at'   => ['type'=>'DATETIME','null' => true ], 
            
            // co_1_id = Course 1 ID, A max of 15 mandatory and 5 optional courses a student can be admitted in a class based on SCM_ID
            // co_1_re = Course 1 obtained result mark, Course ID can be any of 15+5= 20 admitted course IDs.
            // co_1_ou = mark obtained out of (100), Generally it is 100, or 20 might for assignments
            'exr_co_1_id'   => ['type'=>'INT',      'constraint' => 11,     'unsigned' => TRUE, 'null' => TRUE, 'comment' => 'Any course ID out of 15+5=20 courses a student admitted to.' ],
            'exr_co_1_re'   => ['type'=>'DECIMAL',  'constraint' => [5,2],  'unsigned' => TRUE, 'null' => FALSE, 'default' => '0.00', 'comment' => 'Obtained mark of selected course.' ],
            'exr_co_1_ou'   => ['type'=>'TINYINT',  'constraint' => 3,      'unsigned' => TRUE, 'null' => FALSE, 'default' => '0','comment' => 'Obtained mark of out of 100?'  ],
            'exr_co_2_id'   => ['type'=>'INT',      'constraint' => 11,     'unsigned' => TRUE, 'null' => TRUE ],
            'exr_co_2_re'   => ['type'=>'DECIMAL',  'constraint' => [5,2],  'unsigned' => TRUE, 'null' => FALSE, 'default' => '0.00' ],
            'exr_co_2_ou'   => ['type'=>'TINYINT',  'constraint' => 3,      'unsigned' => TRUE, 'null' => FALSE, 'default' => '0' ],
            'exr_co_3_id'   => ['type'=>'INT',      'constraint' => 11,     'unsigned' => TRUE, 'null' => TRUE ],
            'exr_co_3_re'   => ['type'=>'DECIMAL',  'constraint' => [5,2],  'unsigned' => TRUE, 'null' => FALSE, 'default' => '0.00' ],
            'exr_co_3_ou'   => ['type'=>'TINYINT',  'constraint' => 3,      'unsigned' => TRUE, 'null' => FALSE, 'default' => '0' ],
            'exr_co_4_id'   => ['type'=>'INT',      'constraint' => 11,     'unsigned' => TRUE, 'null' => TRUE ],
            'exr_co_4_re'   => ['type'=>'DECIMAL',  'constraint' => [5,2],  'unsigned' => TRUE, 'null' => FALSE, 'default' => '0.00' ],
            'exr_co_4_ou'   => ['type'=>'TINYINT',  'constraint' => 3,      'unsigned' => TRUE, 'null' => FALSE, 'default' => '0' ],
            'exr_co_5_id'   => ['type'=>'INT',      'constraint' => 11,     'unsigned' => TRUE, 'null' => TRUE ],
            'exr_co_5_re'   => ['type'=>'DECIMAL',  'constraint' => [5,2],  'unsigned' => TRUE, 'null' => FALSE, 'default' => '0.00' ],
            'exr_co_5_ou'   => ['type'=>'TINYINT',  'constraint' => 3,      'unsigned' => TRUE, 'null' => FALSE, 'default' => '0' ],
            'exr_co_6_id'   => ['type'=>'INT',      'constraint' => 11,     'unsigned' => TRUE, 'null' => TRUE ],
            'exr_co_6_re'   => ['type'=>'DECIMAL',  'constraint' => [5,2],  'unsigned' => TRUE, 'null' => FALSE, 'default' => '0.00' ],
            'exr_co_6_ou'   => ['type'=>'TINYINT',  'constraint' => 3,      'unsigned' => TRUE, 'null' => FALSE, 'default' => '0' ],
            'exr_co_7_id'   => ['type'=>'INT',      'constraint' => 11,     'unsigned' => TRUE, 'null' => TRUE ],
            'exr_co_7_re'   => ['type'=>'DECIMAL',  'constraint' => [5,2],  'unsigned' => TRUE, 'null' => FALSE, 'default' => '0.00' ],
            'exr_co_7_ou'   => ['type'=>'TINYINT',  'constraint' => 3,      'unsigned' => TRUE, 'null' => FALSE, 'default' => '0' ],
            'exr_co_8_id'   => ['type'=>'INT',      'constraint' => 11,     'unsigned' => TRUE, 'null' => TRUE ],
            'exr_co_8_re'   => ['type'=>'DECIMAL',  'constraint' => [5,2],  'unsigned' => TRUE, 'null' => FALSE, 'default' => '0.00' ],
            'exr_co_8_ou'   => ['type'=>'TINYINT',  'constraint' => 3,      'unsigned' => TRUE, 'null' => FALSE, 'default' => '0' ],
            'exr_co_9_id'   => ['type'=>'INT',      'constraint' => 11,     'unsigned' => TRUE, 'null' => TRUE ],
            'exr_co_9_re'   => ['type'=>'DECIMAL',  'constraint' => [5,2],  'unsigned' => TRUE, 'null' => FALSE, 'default' => '0.00' ],
            'exr_co_9_ou'   => ['type'=>'TINYINT',  'constraint' => 3,      'unsigned' => TRUE, 'null' => FALSE, 'default' => '0' ],
            'exr_co_10_id'  => ['type'=>'INT',      'constraint' => 11,     'unsigned' => TRUE, 'null' => TRUE ],
            'exr_co_10_re'  => ['type'=>'DECIMAL',  'constraint' => [5,2],  'unsigned' => TRUE, 'null' => FALSE, 'default' => '0.00' ],
            'exr_co_10_ou'  => ['type'=>'TINYINT',  'constraint' => 3,      'unsigned' => TRUE, 'null' => FALSE, 'default' => '0' ],
            'exr_co_11_id'  => ['type'=>'INT',      'constraint' => 11,     'unsigned' => TRUE, 'null' => TRUE ],
            'exr_co_11_re'  => ['type'=>'DECIMAL',  'constraint' => [5,2],  'unsigned' => TRUE, 'null' => FALSE, 'default' => '0.00' ],
            'exr_co_11_ou'  => ['type'=>'TINYINT',  'constraint' => 3,      'unsigned' => TRUE, 'null' => FALSE, 'default' => '0' ],
            'exr_co_12_id'  => ['type'=>'INT',      'constraint' => 11,     'unsigned' => TRUE, 'null' => TRUE ],
            'exr_co_12_re'  => ['type'=>'DECIMAL',  'constraint' => [5,2],  'unsigned' => TRUE, 'null' => FALSE, 'default' => '0.00' ],
            'exr_co_12_ou'  => ['type'=>'TINYINT',  'constraint' => 3,      'unsigned' => TRUE, 'null' => FALSE, 'default' => '0' ],
            'exr_co_13_id'  => ['type'=>'INT',      'constraint' => 11,     'unsigned' => TRUE, 'null' => TRUE ],
            'exr_co_13_re'  => ['type'=>'DECIMAL',  'constraint' => [5,2],  'unsigned' => TRUE, 'null' => FALSE, 'default' => '0.00' ],
            'exr_co_13_ou'  => ['type'=>'TINYINT',  'constraint' => 3,      'unsigned' => TRUE, 'null' => FALSE, 'default' => '0' ],
            'exr_co_14_id'  => ['type'=>'INT',      'constraint' => 11,     'unsigned' => TRUE, 'null' => TRUE ],
            'exr_co_14_re'  => ['type'=>'DECIMAL',  'constraint' => [5,2],  'unsigned' => TRUE, 'null' => FALSE, 'default' => '0.00' ],
            'exr_co_14_ou'  => ['type'=>'TINYINT',  'constraint' => 3,      'unsigned' => TRUE, 'null' => FALSE, 'default' => '0' ],
            'exr_co_15_id'  => ['type'=>'INT',      'constraint' => 11,     'unsigned' => TRUE, 'null' => TRUE ],
            'exr_co_15_re'  => ['type'=>'DECIMAL',  'constraint' => [5,2],  'unsigned' => TRUE, 'null' => FALSE, 'default' => '0.00' ],
            'exr_co_15_ou'  => ['type'=>'TINYINT',  'constraint' => 3,      'unsigned' => TRUE, 'null' => FALSE, 'default' => '0' ],
            'exr_co_16_id'  => ['type'=>'INT',      'constraint' => 11,     'unsigned' => TRUE, 'null' => TRUE ],
            'exr_co_16_re'  => ['type'=>'DECIMAL',  'constraint' => [5,2],  'unsigned' => TRUE, 'null' => FALSE, 'default' => '0.00' ],
            'exr_co_16_ou'  => ['type'=>'TINYINT',  'constraint' => 3,      'unsigned' => TRUE, 'null' => FALSE, 'default' => '0' ],
            'exr_co_17_id'  => ['type'=>'INT',      'constraint' => 11,     'unsigned' => TRUE, 'null' => TRUE ],
            'exr_co_17_re'  => ['type'=>'DECIMAL',  'constraint' => [5,2],  'unsigned' => TRUE, 'null' => FALSE, 'default' => '0.00' ],
            'exr_co_17_ou'  => ['type'=>'TINYINT',  'constraint' => 3,      'unsigned' => TRUE, 'null' => FALSE, 'default' => '0' ],
            'exr_co_18_id'  => ['type'=>'INT',      'constraint' => 11,     'unsigned' => TRUE, 'null' => TRUE ],
            'exr_co_18_re'  => ['type'=>'DECIMAL',  'constraint' => [5,2],  'unsigned' => TRUE, 'null' => FALSE, 'default' => '0.00' ],
            'exr_co_18_ou'  => ['type'=>'TINYINT',  'constraint' => 3,      'unsigned' => TRUE, 'null' => FALSE, 'default' => '0' ],
            'exr_co_19_id'  => ['type'=>'INT',      'constraint' => 11,     'unsigned' => TRUE, 'null' => TRUE ],
            'exr_co_19_re'  => ['type'=>'DECIMAL',  'constraint' => [5,2],  'unsigned' => TRUE, 'null' => FALSE, 'default' => '0.00' ],
            'exr_co_19_ou'  => ['type'=>'TINYINT',  'constraint' => 3,      'unsigned' => TRUE, 'null' => FALSE, 'default' => '0' ],
            'exr_co_20_id'  => ['type'=>'INT',      'constraint' => 11,     'unsigned' => TRUE, 'null' => TRUE ],
            'exr_co_20_re'  => ['type'=>'DECIMAL',  'constraint' => [5,2],  'unsigned' => TRUE, 'null' => FALSE, 'default' => '0.00' ],
            'exr_co_20_ou'  => ['type'=>'TINYINT',  'constraint' => 3,      'unsigned' => TRUE, 'null' => FALSE, 'default' => '0' ],
        ]);
        
        $this->forge->addPrimaryKey('exr_id');
        $this->forge->addKey('exr_scm_id');
        $this->forge->addKey('exr_deleted_at');
        $this->forge->addKey('exr_updated_at');
        $this->forge->addKey('exr_inserted_at');
        $this->forge->createTable('exam_results', TRUE, ['ENGINE' => 'InnoDB', 'comment' => 'Exam results storing and showing for students.']);
        
        
        $foreignKeys = array(
            /* ------ Child Table ---------------- Referenced Parent Table ---------- */
            [ 'exam_results', 'exr_scm_id',     'courses_classes_students_mapping', 'scm_id', 'RESTRICT', 'CASCADE'],
            [ 'exam_results', 'exr_axdts_id',   'exam_date_time',                   'axdts_id','RESTRICT', 'CASCADE'], 
            [ 'exam_results', 'exr_co_1_id',    'courses',                          'co_id',  'RESTRICT', 'CASCADE'], 
            [ 'exam_results', 'exr_co_2_id',    'courses',                          'co_id',  'RESTRICT', 'CASCADE'],
            [ 'exam_results', 'exr_co_3_id',    'courses',                          'co_id',  'RESTRICT', 'CASCADE'],
            [ 'exam_results', 'exr_co_4_id',    'courses',                          'co_id',  'RESTRICT', 'CASCADE'],
            [ 'exam_results', 'exr_co_5_id',    'courses',                          'co_id',  'RESTRICT', 'CASCADE'],
            [ 'exam_results', 'exr_co_6_id',    'courses',                          'co_id',  'RESTRICT', 'CASCADE'],
            [ 'exam_results', 'exr_co_7_id',    'courses',                          'co_id',  'RESTRICT', 'CASCADE'],
            [ 'exam_results', 'exr_co_8_id',    'courses',                          'co_id',  'RESTRICT', 'CASCADE'],
            [ 'exam_results', 'exr_co_9_id',    'courses',                          'co_id',  'RESTRICT', 'CASCADE'],
            [ 'exam_results', 'exr_co_10_id',    'courses',                          'co_id',  'RESTRICT', 'CASCADE'],
            [ 'exam_results', 'exr_co_11_id',    'courses',                          'co_id',  'RESTRICT', 'CASCADE'],
            [ 'exam_results', 'exr_co_12_id',    'courses',                          'co_id',  'RESTRICT', 'CASCADE'],
            [ 'exam_results', 'exr_co_13_id',    'courses',                          'co_id',  'RESTRICT', 'CASCADE'],
            [ 'exam_results', 'exr_co_14_id',    'courses',                          'co_id',  'RESTRICT', 'CASCADE'],
            [ 'exam_results', 'exr_co_15_id',    'courses',                          'co_id',  'RESTRICT', 'CASCADE'],
            [ 'exam_results', 'exr_co_16_id',    'courses',                          'co_id',  'RESTRICT', 'CASCADE'],
            [ 'exam_results', 'exr_co_17_id',    'courses',                          'co_id',  'RESTRICT', 'CASCADE'],
            [ 'exam_results', 'exr_co_18_id',    'courses',                          'co_id',  'RESTRICT', 'CASCADE'],
            [ 'exam_results', 'exr_co_19_id',    'courses',                          'co_id',  'RESTRICT', 'CASCADE'],
            [ 'exam_results', 'exr_co_20_id',    'courses',                          'co_id',  'RESTRICT', 'CASCADE'],
        );
        
                
        foreach( $foreignKeys as $idx => $fk ){
            $this->db->simpleQuery(
                    "ALTER TABLE ". $this->db->prefixTable($fk[0]) . " ADD CONSTRAINT fk_{$fk[0]}_{$fk[1]} FOREIGN KEY({$fk[1]})" .
                    " REFERENCES {$this->db->prefixTable($fk[2])}($fk[3]) ON DELETE {$fk[4]} ON UPDATE {$fk[5]};"
            );
        }
        
        $this->db->enableForeignKeyChecks();
    } /* EOM */

    public function down(){ 
        
    } // EOM
} // EOC

