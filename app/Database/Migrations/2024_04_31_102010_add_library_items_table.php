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

class Migration_add_library_items_table extends \CodeIgniter\Database\Migration {

    
    public function up(){    
        $this->db->disableForeignKeyChecks();
        
        
        $this->forge->addField([
            'bk_id'         => ['type'=>'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => FALSE, 'auto_increment' => TRUE ],
            'bk_title'      => ['type'=>'VARCHAR', 'constraint' => 250,'null' => FALSE, 'comment' => 'Book or item title like bangla book'],
            'bk_code'       => ['type'=>'VARCHAR', 'constraint' => 250,'null' => FALSE, 'comment' => 'Code number of the book: ENG-{lq_id}.'],
            'bk_excerpt'    => ['type'=>'TEXT', 'null' => FALSE, 'comment' => 'Simple short description.' ],
            'bk_deleted_at' => ['type'=>'DATETIME','default' => NULL, 'null' => TRUE ], 
            'bk_updated_at' => ['type'=>'DATETIME','default' => NULL, 'null' => TRUE ], 
            'bk_inserted_at'=> ['type'=>'DATETIME','default' => NULL, 'null' => TRUE ],
        ]);
        $this->forge->addPrimaryKey('bk_id');
        $this->forge->addKey('bk_deleted_at');
        $this->forge->addKey('bk_updated_at');
        $this->forge->addKey('bk_inserted_at');
        $this->forge->createTable('library_items', TRUE, ['ENGINE' => 'InnoDB', 'comment'=>'Books or items for the library.']);

        $this->db->enableForeignKeyChecks();
    }

    public function down(){ 
        
    } // EOM
} // EOC

