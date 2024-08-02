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

class Migration_add_hand_cash_collections_table extends \CodeIgniter\Database\Migration {

    
    public function up(){    
        $this->db->disableForeignKeyChecks();
        
        $this->forge->addField([
            'hc_id'             => ['type'=>'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => FALSE, 'auto_increment' => TRUE ],
            'hc_scm_id'         => ['type'=>'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => FALSE, 'default' => '0' ],
            
            'hc_salary_months_txt'      => ['type'=>'VARCHAR', 'constraint' => 255, 'comment' => 'A comment taken from teacher. Name of the months. Ex. January, February etc.' ],
            'hc_amt_salary'             => ['type'=>'DECIMAL', 'constraint' => [6,2], 'unsigned' => true, 'default' => '0.00' ],
            'hc_amt_electricity_fee'    => ['type'=>'DECIMAL', 'constraint' => [6,2], 'unsigned' => true, 'default' => '0.00' ],
            'hc_amt_ict_fee'            => ['type'=>'DECIMAL', 'constraint' => [6,2], 'unsigned' => true, 'default' => '0.00' ],
            'hc_amt_welcome_fee'        => ['type'=>'DECIMAL', 'constraint' => [6,2], 'unsigned' => true, 'default' => '0.00', 'comment' => 'Naveen Baran fee' ],
            'hc_amt_farewell_fee'       => ['type'=>'DECIMAL', 'constraint' => [6,2], 'unsigned' => true, 'default' => '0.00', 'comment' => 'Bidai Onostan, farewell ceremony Fee' ],
            'hc_amt_girls_guides_fee'   => ['type'=>'DECIMAL', 'constraint' => [6,2], 'unsigned' => true, 'default' => '0.00' ],
            'hc_amt_printing_fee'       => ['type'=>'DECIMAL', 'constraint' => [6,2], 'unsigned' => true, 'default' => '0.00', 'comment' => 'Syllabus, Hajira potrao etc' ],
            'hc_amt_sports_fee'         => ['type'=>'DECIMAL', 'constraint' => [6,2], 'unsigned' => true, 'default' => '0.00' ],
            'hc_amt_lab_fee'            => ['type'=>'DECIMAL', 'constraint' => [6,2], 'unsigned' => true, 'default' => '0.00' ],
            'hc_amt_teacher_welfare_fee'=> ['type'=>'DECIMAL', 'constraint' => [6,2], 'unsigned' => true, 'default' => '0.00' ],
            'hc_amt_milad_puja_fee'     => ['type'=>'DECIMAL', 'constraint' => [6,2], 'unsigned' => true, 'default' => '0.00' ],
            'hc_amt_development_fee'    => ['type'=>'DECIMAL', 'constraint' => [6,2], 'unsigned' => true, 'default' => '0.00' ],
            'hc_amt_poverty_fund_fee'   => ['type'=>'DECIMAL', 'constraint' => [6,2], 'unsigned' => true, 'default' => '0.00' ],
            'hc_amt_reading_room_fee'   => ['type'=>'DECIMAL', 'constraint' => [6,2], 'unsigned' => true, 'default' => '0.00' ],
            'hc_amt_cultural_program'   => ['type'=>'DECIMAL', 'constraint' => [6,2], 'unsigned' => true, 'default' => '0.00' ],
            'hc_amt_garden_fee'         => ['type'=>'DECIMAL', 'constraint' => [6,2], 'unsigned' => true, 'default' => '0.00' ],
            'hc_amt_common_room_fee'    => ['type'=>'DECIMAL', 'constraint' => [6,2], 'unsigned' => true, 'default' => '0.00' ],
            'hc_amt_session_fee'        => ['type'=>'DECIMAL', 'constraint' => [6,2], 'unsigned' => true, 'default' => '0.00' ],
            'hc_amt_id_fee'             => ['type'=>'DECIMAL', 'constraint' => [6,2], 'unsigned' => true, 'default' => '0.00' ],
            'hc_amt_total'              => ['type'=>'DECIMAL', 'constraint' => [6,2], 'unsigned' => true, 'default' => '0.00' ],
            
            'hc_is_paid'        => ['type'=>'TINYINT', 'constraint' => 1, 'unsigned' => TRUE, 'default' => '0', 'COMMENT' => 'Confirmation from teacher if it is paid.' ],
            'hc_deleted_at'     => ['type'=>'DATETIME','null' => true ], 
            'hc_updated_at'     => ['type'=>'DATETIME','null' => true ], 
            'hc_inserted_at'    => ['type'=>'DATETIME','null' => true ], 
        ]);         
        $this->forge->addPrimaryKey('hc_id');
        $this->forge->addKey('hc_scm_id');
        $this->forge->addKey('hc_amt_total');
        $this->forge->addKey('hc_is_paid');
        $this->forge->addKey('hc_deleted_at');
        $this->forge->addKey('hc_updated_at');
        $this->forge->addKey('hc_inserted_at');
        $this->forge->createTable('hand_cash_collections', TRUE, ['ENGINE' => 'InnoDB', 'comment' => 'Cash collections from student by teachers.']);
        
        
        $this->db->enableForeignKeyChecks();
    }

    public function down(){ 
        
    } // EOM
} // EOC

