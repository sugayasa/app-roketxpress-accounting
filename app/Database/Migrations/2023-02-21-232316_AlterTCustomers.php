<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterTCustomers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('t_customers', [
            'PHONENUMBER' => [
                'type'              => 'VARCHAR',
                'constraint'        => 20,
                'after'             => 'EMAIL',
                'null'              => false
            ],
            'HWID' => [
                'type'              => 'VARCHAR',
                'constraint'        => 16,
                'null'              => true
            ],
            'CREATEDAT DATETIME DEFAULT CURRENT_TIMESTAMP',
            'LASTLOGINAT' => [
                'type'              => 'DATETIME',
                'null'              => true
            ]
        ]);

        $this->forge->modifyColumn('t_customers', [
            'FIRSTNAME' => [
                'type'              => 'VARCHAR',
                'constraint'        => 50,
                'null'              => false
            ],
            'LASTNAME' => [
                'type'              => 'VARCHAR',
                'constraint'        => 50,
                'null'              => false
            ],
            'EMAIL' => [
                'type'              => 'VARCHAR',
                'constraint'        => 100,
                'null'              => false
            ],
            'PASSWORD' => [
                'type'              => 'VARCHAR',
                'constraint'        => 200,
                'null'              => false
            ]
        ]);
        $this->forge->addKey(['HWID'], false, true, 'HWID_UNIQUE');
        $this->forge->processIndexes('t_customers');
    }

    public function down()
    {
        $this->forge->dropColumn('t_customers', ['PHONENUMBER', 'HWID', 'CREATEDAT', 'LASTLOGINAT']);
        
        $this->forge->modifyColumn('t_customers', [
            'FIRSTNAME' => [
                'type'              => 'VARCHAR',
                'constraint'        => 50,
                'null'              => true
            ],
            'LASTNAME' => [
                'type'              => 'VARCHAR',
                'constraint'        => 50,
                'null'              => true
            ],
            'EMAIL' => [
                'type'              => 'VARCHAR',
                'constraint'        => 100,
                'null'              => true
            ],
            'PASSWORD' => [
                'type'              => 'VARCHAR',
                'constraint'        => 200,
                'null'              => true
            ]
        ]);

        $this->forge->dropKey('t_customers', 'HWID_UNIQUE', false);

    }
}
