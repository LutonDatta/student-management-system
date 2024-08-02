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

class Migration_add_academic_exam_date_time_table extends \CodeIgniter\Database\Migration {

    
    public function up(){    
        $this->db->disableForeignKeyChecks();
        
        $this->forge->addField([
            'axdts_id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => FALSE, 'auto_increment' => TRUE ],
            'axdts_class_id'        => ['type' => 'TINYTEXT', /*'constraint' => 255,'unsigned' => TRUE,*/  'null' => FALSE, 'DEFAULT' => serialize([]), 'COMMENT' => 'Serilized list of class ID in array.' ],
            'axdts_exam_routine'    => ['type' => 'TEXT', /*'constraint' => 2000,'unsigned' => TRUE,*/  'null' => FALSE, 'DEFAULT' => serialize([]), 'COMMENT' => 'Serilized list of datetime based on course ids under class ID in array.' ],
            'axdts_session_year'    => ['type' => 'VARCHAR', 'constraint' => 9, 'null' => TRUE, 'comment' => 'Can be 2019 or 2019-11 or 2019-2020 without spaces.' ], 
            'axdts_type'            => ['type' => 'ENUM',  'constraint' => ['1st_mid','2nd_mid','3rd_mid','4th_mid','pretest','test','sem_fin','final','mcq','assign','presen','yr_chan','ses_chan','others'], 'default' => 'others', 'null' => FALSE],
            
            'axdts_exam_starts_at'  => ['type' => 'DATETIME', 'null' => TRUE ],
            'axdts_exam_ends_at'    => ['type' => 'DATETIME', 'null' => TRUE ],
            
            'axdts_deleted_at'  => ['type' => 'DATETIME', 'null' => TRUE ],
            'axdts_updated_at'  => ['type' => 'DATETIME', 'null' => TRUE ],
            'axdts_inserted_at' => ['type' => 'DATETIME', 'null' => TRUE ],
        ]);
        $this->forge->addPrimaryKey('axdts_id');
        $this->forge->addKey('axdts_deleted_at');
        $this->forge->addKey('axdts_updated_at');
        $this->forge->addKey('axdts_inserted_at');
        $this->forge->createTable('exam_date_time', TRUE, ['ENGINE' => 'InnoDB', 'comment'=>'Academic exam date time setup. Setup exam for various classes.']);
        $this->db->enableForeignKeyChecks();
    }

    public function down(){        
        
    } // EOM
} // EOC

