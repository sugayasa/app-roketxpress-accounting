<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MPaymenttypes extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'IDPAYMENTTYPE' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'auto_increment'    => true
            ],
            'PAYMENTTYPENAME' => [
                'type'              => 'VARCHAR',
                'constraint'        => 50,
                'null'              => false
            ],
            'DESCRIPTION' => [
                'type'              => 'VARCHAR',
                'constraint'        => 150,
                'null'              => false,
                'default'           => ''
            ],
            'ALLOWBOOKNOW' => [
                'type'              => 'INT',
                'constraint'        => 1,
                'null'              => false,
                'default'           => 1,
                'comment'           => '1:ALLOWED, 0:NOT ALLOWED'
            ],
            'ALLOWSCHEDULED' => [
                'type'              => 'INT',
                'constraint'        => 1,
                'null'              => false,
                'default'           => 1,
                'comment'           => '1:ALLOWED, 0:NOT ALLOWED'
            ],
            'STATUS' => [
                'type'              => 'INT',
                'constraint'        => 1,
                'null'              => false,
                'default'           => 1,
                'comment'           => '1:ACTIVE, -1:INACTIVE'
            ]
        ]);

        $attributes = ['ENGINE' => 'InnoDB', 'CHARACTER SET' => 'latin1', 'COLLATE' => 'latin1_general_ci'];

        $this->forge->addKey('IDPAYMENTTYPE', true);
        $this->forge->addKey(['PAYMENTTYPENAME'], false, true, 'PAYMENTTYPENAME_UNIQUE');
        $this->forge->createTable('m_paymenttypes', false, $attributes);
    }

    public function down()
    {
        $this->forge->dropTable('m_paymenttypes');
    }
}
