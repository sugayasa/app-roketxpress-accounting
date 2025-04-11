<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MCartypes extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'IDCARTYPE' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'auto_increment'    => true
            ],
            'CARTYPENAME' => [
                'type'              => 'VARCHAR',
                'constraint'        => 100,
                'null'              => false
            ],
            'DESCRIPTION' => [
                'type'              => 'VARCHAR',
                'constraint'        => 150,
                'null'              => false,
                'default'           => ''
            ],
            'MAXCAPACITY' => [
                'type'              => 'INT',
                'constraint'        => 2,
                'null'              => false,
                'default'           => 1
            ],
            'IMAGEFILENAME' => [
                'type'              => 'VARCHAR',
                'constraint'        => '255',
                'null'              => false,
                'default'           => ''
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

        $this->forge->addKey('IDCARTYPE', true);
        $this->forge->addKey(['CARTYPENAME'], false, true, 'CARTYPENAME_UNIQUE');
        $this->forge->createTable('m_cartypes', false, $attributes);
    }

    public function down()
    {
        $this->forge->dropTable('m_cartypes');
    }
}
