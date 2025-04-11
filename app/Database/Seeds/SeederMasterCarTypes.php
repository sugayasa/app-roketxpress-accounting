<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SeederMasterCarTypes extends Seeder
{
    public function run()
    {
        $cartypeList = [
			[
				'CARTYPENAME'   => 'Small',
				'DESCRIPTION'   => 'Small cars with a maximum load of 3 passengers such as Toyota Agya and Honda Brio',
				'MAXCAPACITY'   => 3,
				'IMAGEFILENAME' => 'small_car.png',
				'STATUS'        => 1
            ],
			[
				'CARTYPENAME'   => 'Medium',
				'DESCRIPTION'   => 'Medium cars with a maximum load of 5 passengers such as Toyota Avanza, Suzuki Ertiga and Mitsubishi Xpander',
				'MAXCAPACITY'   => 5,
				'IMAGEFILENAME' => 'medium_car.png',
				'STATUS'        => 1
            ],
			[
				'CARTYPENAME'   => 'Minivan',
				'DESCRIPTION'   => 'Minivan with a maximum load of 6 passengers such as Suzuki APV and Daihatsu Luxio',
				'MAXCAPACITY'   => 6,
				'IMAGEFILENAME' => 'minivan_car.png',
				'STATUS'        => 1
            ]
		];

		foreach($cartypeList as $dataCarType){
			$this->db->table('m_cartypes')->insert($dataCarType);
		}
    }
}
