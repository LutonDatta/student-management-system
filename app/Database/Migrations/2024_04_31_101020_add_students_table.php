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

class Migration_add_students_table extends \CodeIgniter\Database\Migration {

    
    public function up(){    
        $this->db->disableForeignKeyChecks();
        
        $this->forge->addField([
            'student_u_id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => FALSE, 'auto_increment' => TRUE ],
            'student_u_email_own'       => ['type' => 'VARCHAR',  'constraint' => 250,'null' => FALSE, 'comment'=>'mobile number of student'],
            'student_u_mobile_own'      => ['type' => 'VARCHAR',  'constraint' => 250,'null' => FALSE, 'comment'=>'mobile number of student'],
            'student_u_mobile_father'   => ['type' => 'VARCHAR',  'constraint' => 250,'null' => FALSE, 'comment'=>'mobile number of father'],
            'student_u_mobile_mother'   => ['type' => 'VARCHAR',  'constraint' => 250,'null' => FALSE, 'comment'=>'mobile number of mother'],
            'student_u_name_initial'    => ['type' => 'VARCHAR',  'constraint' => 250,'null' => FALSE ],
            'student_u_name_first'      => ['type' => 'VARCHAR',  'constraint' => 250,'null' => FALSE ],
            'student_u_name_middle'     => ['type' => 'VARCHAR',  'constraint' => 250,'null' => FALSE ],
            'student_u_name_last'       => ['type' => 'VARCHAR',  'constraint' => 250,'null' => FALSE ],
            'student_u_father_name'     => ['type' => 'VARCHAR',  'constraint' => 250,'null' => FALSE ],
            'student_u_mother_name'     => ['type' => 'VARCHAR',  'constraint' => 250,'null' => FALSE ],
            'student_u_nid_no'          => ['type' => 'VARCHAR',  'constraint' => 250,'null' => FALSE ],
            'student_u_birth_reg_no'    => ['type' => 'VARCHAR',  'constraint' => 250,'null' => FALSE ],
            'student_u_date_of_birth'   => ['type' => 'DATETIME','default' => '1000-01-01 00:00:00', 'null' => FALSE, 'comment' => 'Real birth date of user.' ],
            'student_u_gender'          => ['type' => 'ENUM',  'constraint' => ['male','female','3rd','others'],'null' => FALSE, 'default' => 'male' ],  
            'student_u_religion'        => ['type' => 'ENUM', 'null' => FALSE, 'default' => 'others', 'constraint' => ['christianity','islam','hinduism','nonreligious','buddhism','primal-indigenous','diasporic','sikhism','juche','others','judaism', 'unaffiliated'] ],  
            'student_u_addr_country'    => ['type' => 'VARCHAR',  'constraint' => 250,'null' => FALSE ],            
            'student_u_addr_state'      => ['type' => 'VARCHAR',  'constraint' => 250,'null' => FALSE ],            
            'student_u_addr_district'   => ['type' => 'VARCHAR',  'constraint' => 250,'null' => FALSE ],            
            'student_u_addr_thana'      => ['type' => 'VARCHAR',  'constraint' => 250, 'null' => FALSE, 'comment' => 'Thana or Subdistrict' ],
            'student_u_addr_post_office'=> ['type' => 'VARCHAR',  'constraint' => 250,'null' => FALSE ],            
            'student_u_addr_zip_code'   => ['type' => 'VARCHAR',  'constraint' => 250,'null' => FALSE ],            
            'student_u_addr_village'        => ['type' => 'VARCHAR',  'constraint' => 250,'null' => FALSE ],            
            'student_u_addr_road_house_no'  => ['type' => 'VARCHAR',  'constraint' => 250,'null' => FALSE ],            
                        
            'student_u_deleted_at'  => ['type' => 'DATETIME', 'null' => TRUE ],
            'student_u_updated_at'  => ['type' => 'DATETIME', 'null' => TRUE ],
            'student_u_inserted_at' => ['type' => 'DATETIME', 'null' => TRUE ],
        ]);         
        $this->forge->addPrimaryKey('student_u_id');
        $this->forge->addKey('student_u_gender');   
        $this->forge->addKey('student_u_deleted_at');
        $this->forge->addKey('student_u_updated_at');
        $this->forge->addKey('student_u_inserted_at');
        $this->forge->createTable('students', TRUE, ['ENGINE' => 'InnoDB', 'COMMENT' => 'Teachers edit these students records from students admission page.']);
        
        
        $this->db->enableForeignKeyChecks();
    }

    public function down(){
        
    } // EOM
} // EOC

