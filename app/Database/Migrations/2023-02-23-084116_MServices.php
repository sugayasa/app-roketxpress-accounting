<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MServices extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'IDSERVICE' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'auto_increment'    => true
            ],
            'SERVICENAME' => [
                'type'              => 'VARCHAR',
                'constraint'        => 50,
                'null'              => false,
                'unique'            => true
            ],
            'DESCRIPTION' => [
                'type'              => 'VARCHAR',
                'constraint'        => 255
            ],
            'STATUS' => [
                'type'              => 'INT',
                'constraint'        => 1,
                'null'              => false,
                'default'           => 1,
                'comment'           => '//1: ACTIVE, 0:INACTIVE'
            ]
        ]);

        $attributes = ['ENGINE' => 'InnoDB', 'CHARACTER SET' => 'latin1', 'COLLATE' => 'latin1_general_ci'];

        $this->forge->addKey('IDSERVICE', true);
        $this->forge->addKey(['SERVICENAME'], false, true, 'SERVICENAME_UNIQUE');
        $this->forge->createTable('m_services', false, $attributes);
    }

    public function down()
    {
        $this->forge->dropTable('m_services');
    }
}
