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

class Migration_add_foreign_keys extends \CodeIgniter\Database\Migration {

    
    public function up(){
        $this->db->disableForeignKeyChecks();
        
        $foreignKeys = array(
            /* ------ Child Table ---------------- Referenced Parent Table ---------- */
            [ 'classes_and_semesters',  'fcs_parent',       'classes_and_semesters',    'fcs_id',   'CASCADE', 'CASCADE'],
            
            [ 'courses_classes_mapping',    'ccm_class_id',         'classes_and_semesters','fcs_id',   'RESTRICT', 'CASCADE'],
            [ 'courses_classes_mapping',    'ccm_course_id',        'courses',              'co_id',    'RESTRICT', 'CASCADE'],
            
            [ 'courses_classes_students_mapping',   'scm_u_id',         'user_students',                'student_u_id',     'RESTRICT', 'CASCADE'],
            [ 'courses_classes_students_mapping',   'scm_class_id',     'classes_and_semesters', 'fcs_id',  'RESTRICT', 'CASCADE'],
            [ 'courses_classes_students_mapping',   'scm_course_1',     'courses',      'co_id',  'RESTRICT', 'CASCADE'],
            [ 'courses_classes_students_mapping',   'scm_course_2',     'courses',      'co_id',  'RESTRICT', 'CASCADE'],
            [ 'courses_classes_students_mapping',   'scm_course_3',     'courses',      'co_id',  'RESTRICT', 'CASCADE'],
            [ 'courses_classes_students_mapping',   'scm_course_4',     'courses',      'co_id',  'RESTRICT', 'CASCADE'],
            [ 'courses_classes_students_mapping',   'scm_course_5',     'courses',      'co_id',  'RESTRICT', 'CASCADE'],
            [ 'courses_classes_students_mapping',   'scm_course_6',     'courses',      'co_id',  'RESTRICT', 'CASCADE'],
            [ 'courses_classes_students_mapping',   'scm_course_7',     'courses',      'co_id',  'RESTRICT', 'CASCADE'],
            [ 'courses_classes_students_mapping',   'scm_course_8',     'courses',      'co_id',  'RESTRICT', 'CASCADE'],
            [ 'courses_classes_students_mapping',   'scm_course_9',     'courses',      'co_id',  'RESTRICT', 'CASCADE'],
            [ 'courses_classes_students_mapping',   'scm_course_10',     'courses',      'co_id',  'RESTRICT', 'CASCADE'],
            [ 'courses_classes_students_mapping',   'scm_course_11',     'courses',      'co_id',  'RESTRICT', 'CASCADE'],
            [ 'courses_classes_students_mapping',   'scm_course_12',     'courses',      'co_id',  'RESTRICT', 'CASCADE'],
            [ 'courses_classes_students_mapping',   'scm_course_13',     'courses',      'co_id',  'RESTRICT', 'CASCADE'],
            [ 'courses_classes_students_mapping',   'scm_course_14',     'courses',      'co_id',  'RESTRICT', 'CASCADE'],
            [ 'courses_classes_students_mapping',   'scm_course_15',     'courses',      'co_id',  'RESTRICT', 'CASCADE'],
            [ 'courses_classes_students_mapping',   'scm_course_op_1',     'courses',      'co_id',  'RESTRICT', 'CASCADE'],
            [ 'courses_classes_students_mapping',   'scm_course_op_2',     'courses',      'co_id',  'RESTRICT', 'CASCADE'],
            [ 'courses_classes_students_mapping',   'scm_course_op_3',     'courses',      'co_id',  'RESTRICT', 'CASCADE'],
            [ 'courses_classes_students_mapping',   'scm_course_op_4',     'courses',      'co_id',  'RESTRICT', 'CASCADE'],
            [ 'courses_classes_students_mapping',   'scm_course_op_5',     'courses',      'co_id',  'RESTRICT', 'CASCADE'],
                        
            [ 'library_items_quantities',   'lq_bk_id',             'library_items',    'bk_id', 'CASCADE', 'CASCADE'], /* Remove quantities along with libray books */
            [ 'library_items_quantities',   'lq_distributed_to',    'user_students',    'student_u_id', 'RESTRICT', 'CASCADE'],
            [ 'library_items_quantities',   'lq_returned_by',       'user_students',    'student_u_id', 'RESTRICT', 'CASCADE'],
                                                
            [ 'daily_attendance_book', 'dab_scm_id',     'courses_classes_students_mapping', 'scm_id', 'CASCADE', 'CASCADE'], // Delete/update with parent row
            [ 'daily_attendance_book', 'dab_course_id',  'courses', 'co_id', 'CASCADE', 'CASCADE'], // Delete/update with parent row
            
            [ 'hand_cash_collections',      'hc_scm_id',        'courses_classes_students_mapping', 'scm_id', 'RESTRICT', 'CASCADE'],
        );
        
        foreach( $foreignKeys as $idx => $fk ){
            $this->db->simpleQuery(
                    "ALTER TABLE ". $this->db->prefixTable($fk[0]) . " ADD CONSTRAINT fk_{$fk[0]}_{$fk[1]} FOREIGN KEY({$fk[1]})" .
                    " REFERENCES {$this->db->prefixTable($fk[2])}($fk[3]) ON DELETE {$fk[4]} ON UPDATE {$fk[5]};"
            );
        }
        
        $this->db->enableForeignKeyChecks();
    }

    public function down(){ 
        
        
    } // EOM
    
} // EOC

