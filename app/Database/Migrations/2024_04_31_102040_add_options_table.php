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

class Migration_add_options_table extends \CodeIgniter\Database\Migration {

    
    public function up(){    
        $this->db->disableForeignKeyChecks();
        
        $this->forge->addField([
            'option_id'         => ['type'=>'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => FALSE, 'auto_increment' => TRUE ],
            'option_key'        => ['type'=>'VARCHAR', 'constraint' => 250,'null' => FALSE, 'comment' => 'Setting key of the school'],
            'option_value'      => ['type'=>'TEXT', 'null' => FALSE],
            'option_deleted_at' => ['type'=>'DATETIME', 'null' => TRUE ],
            'option_updated_at' => ['type'=>'DATETIME', 'null' => TRUE ],
            'option_inserted_at'=> ['type'=>'DATETIME', 'null' => TRUE ],
        ]);                 
        $this->forge->addPrimaryKey('option_id');
        $this->forge->addUniqueKey('option_key');
        $this->forge->addKey('option_deleted_at');
        $this->forge->addKey('option_updated_at');
        $this->forge->addKey('option_inserted_at');
        $this->forge->createTable('options', TRUE, ['ENGINE' => 'InnoDB', 'comment' => 'We need some system specific settings like wordpress.']);
                
        $this->db->enableForeignKeyChecks();
    } // EOM

    public function down(){      
        
    } // EOM
} // EOC

