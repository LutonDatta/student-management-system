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

class Migration_add_library_items_quantities_table extends \CodeIgniter\Database\Migration {

    
    public function up(){    
        $this->db->disableForeignKeyChecks();
        
        $this->forge->addField([
            'lq_id'             => ['type'=>'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => FALSE, 'auto_increment' => TRUE ],
            'lq_bk_id'          => ['type'=>'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => FALSE ],
            // Allow NULL values as FOREIGN KEY will not accept 0 for UserID.
            'lq_distributed_to' => ['type'=>'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE, 'comment' => 'Who have taken this book?' ],
            'lq_returned_by'    => ['type'=>'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE, 'comment' => 'Who have returned this book after taking/reading it?' ],
            'lq_distributed_at' => ['type'=>'DATETIME','default' => NULL, 'null' => TRUE, 'comment' => 'When this book is distributed to the student.' ], 
            'lq_returned_at'    => ['type'=>'DATETIME','default' => NULL, 'null' => TRUE, 'comment' => 'When this book is distributed to the student.' ], 
            'lq_is_distributed' => ['type'=>'TINYINT','constraint' => 1, 'null' => FALSE, 'default' => '0', 'comment'=>'Is this book distributed.' ],
            'lq_turnover'       => ['type'=>'INT', 'constraint' => 10, 'unsigned' => TRUE, 'null' => FALSE, 'default'=>'0', 'comment' => 'How many times this item has been distributed and returned?' ],
            'lq_serial_number'  => ['type'=>'INT', 'constraint' => 10, 'unsigned' => TRUE, 'null' => FALSE, 'default'=>'0', 'comment' => 'Serial number of quantity of a single book?' ],
            'lq_deleted_at'     => ['type'=>'DATETIME','default' => NULL, 'null' => TRUE ], 
            'lq_updated_at'     => ['type'=>'DATETIME','default' => NULL, 'null' => TRUE ], 
            'lq_inserted_at'    => ['type'=>'DATETIME','default' => NULL, 'null' => TRUE ],
        ]);
        $this->forge->addPrimaryKey('lq_id');
        $this->forge->addKey('lq_bk_id');
        $this->forge->addKey('lq_serial_number');
        $this->forge->addKey('lq_is_distributed');
        $this->forge->addKey('lq_distributed_to');
        $this->forge->addKey('lq_returned_by');
        $this->forge->addKey('lq_deleted_at');
        $this->forge->addKey('lq_updated_at');
        $this->forge->addKey('lq_inserted_at');
        $this->forge->createTable('library_items_quantities', TRUE, ['ENGINE' => 'InnoDB', 'comment'=>'One book may have many quantity in stock.']);

        $this->db->enableForeignKeyChecks();
    }

    public function down(){ 
       
    } // EOM
} // EOC

