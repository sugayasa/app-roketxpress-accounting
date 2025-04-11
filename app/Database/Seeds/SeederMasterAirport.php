<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SeederMasterAirport extends Seeder
{
    public function run()
    {
        $airportList = [
			[
				'AIRPORTNAME'   => 'I Gusti Ngurah Rai',
				'PROVINCE'      => 'Bali',
				'COUNTRY'       => 'Indonesia',
				'LATITUDE'      => '-8.74605974',
				'LONGITUDE'     => '115.16665825',
				'RADIUSAREA'    => 800,
				'STATUS'        => 1
			]
		];

		foreach($airportList as $dataAirport){
			$this->db->table('m_airports')->insert($dataAirport);
		}
    }
}
