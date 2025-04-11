<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TCustomers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'IDCUSTOMER' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'auto_increment'    => true
            ],
            'FIRSTNAME' => [
                'type'              => 'VARCHAR',
                'constraint'        => 50
            ],
            'LASTNAME' => [
                'type'              => 'VARCHAR',
                'constraint'        => 50
            ],
            'EMAIL' => [
                'type'              => 'VARCHAR',
                'constraint'        => 100
            ],
            'PASSWORD' => [
                'type'              => 'VARCHAR',
                'constraint'        => 200
            ]
        ]);

        $attributes = ['ENGINE' => 'InnoDB', 'CHARACTER SET' => 'latin1', 'COLLATE' => 'latin1_general_ci'];

        $this->forge->addKey('IDCUSTOMER', true);
        $this->forge->addKey(['EMAIL'], false, true, 'EMAIL_UNIQUE');
        $this->forge->createTable('t_customers', false, $attributes);
    }

    public function down()
    {
        $this->forge->dropTable('t_customers');
    }
}
