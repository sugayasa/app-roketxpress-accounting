<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SeederMasterServices extends Seeder
{
    public function run()
    {
        $serviceList = [
			[
				'SERVICENAME'   => 'Airport Transfer',
				'DESCRIPTION'   => 'Transportation service from airport to destination with limited area',
				'STATUS'        => 1
			],
			[
				'SERVICENAME'   => 'Point to Point Transfer',
				'DESCRIPTION'   => 'Transportation services from one point to a destination within the coverage area',
				'STATUS'        => 0
			],
			[
				'SERVICENAME'   => 'Car Charter',
				'DESCRIPTION'   => 'Car Charter services with various options for car types and duration',
				'STATUS'        => 1
			],
			[
				'SERVICENAME'   => 'Self Drive',
				'DESCRIPTION'   => 'Rental services of various types of cars with various durations for self-driving',
				'STATUS'        => 0
			]
		];

		foreach($serviceList as $dataService){
			$this->db->table('m_services')->insert($dataService);
		}
    }
}
