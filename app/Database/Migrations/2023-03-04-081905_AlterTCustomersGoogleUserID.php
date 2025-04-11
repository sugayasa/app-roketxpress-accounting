<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterTCustomersGoogleUserID extends Migration
{
    public function up()
    {
        $this->forge->addColumn('t_customers', [
            'GOOGLEUSERID' => [
                'type'              => 'VARCHAR',
                'constraint'        => 32,
                'after'             => 'IDCUSTOMER',
                'null'              => true
            ]
        ]);
        $this->forge->addKey(['GOOGLEUSERID'], false, true, 'GOOGLEUSERID_UNIQUE');
        $this->forge->processIndexes('t_customers');
    }

    public function down()
    {
        $this->forge->dropColumn('t_customers', ['GOOGLEUSERID']);
        $this->forge->dropKey('t_customers', 'GOOGLEUSERID_UNIQUE', false);
    }
}
