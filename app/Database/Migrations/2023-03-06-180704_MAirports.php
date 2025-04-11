<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MAirports extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'IDAIRPORT' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'auto_increment'    => true
            ],
            'AIRPORTNAME' => [
                'type'              => 'VARCHAR',
                'constraint'        => 100,
                'null'              => false
            ],
            'PROVINCE' => [
                'type'              => 'VARCHAR',
                'constraint'        => 75,
                'null'              => false
            ],
            'COUNTRY' => [
                'type'              => 'VARCHAR',
                'constraint'        => 75,
                'null'              => false
            ],
            'LATITUDE' => [
                'type'              => 'DECIMAL',
                'constraint'        => '11,8',
                'null'              => false
            ],
            'LONGITUDE' => [
                'type'              => 'DECIMAL',
                'constraint'        => '11,8',
                'null'              => false
            ],
            'RADIUSAREA' => [
                'type'              => 'INT',
                'constraint'        => '11',
                'null'              => false,
                'default'           => 0,
                'comment'           => 'AREA RADIUS IN METER, 0 IS UNLIMITED'
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

        $this->forge->addKey('IDAIRPORT', true);
        $this->forge->addKey(['AIRPORTNAME'], false, true, 'AIRPORTNAME_UNIQUE');
        $this->forge->addKey(['LATITUDE', 'LONGITUDE'], false, true, 'GPS_LOCATION_UNIQUE');
        $this->forge->createTable('m_airports', false, $attributes);
    }

    public function down()
    {
        $this->forge->dropTable('m_airports');
    }
}
