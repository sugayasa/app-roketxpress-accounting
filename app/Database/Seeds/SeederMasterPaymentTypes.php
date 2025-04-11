<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SeederMasterPaymentTypes extends Seeder
{
    public function run()
    {
        $paymentTypeList = [
			[
				'PAYMENTTYPENAME'   => 'Cash',
				'DESCRIPTION'       => 'Cash payment when with driver',
				'ALLOWBOOKNOW'      => 1,
				'ALLOWSCHEDULED'    => 0,
				'STATUS'            => 1
            ],
			[
				'PAYMENTTYPENAME'   => 'MasterCard',
				'DESCRIPTION'       => 'Credit Card',
				'ALLOWBOOKNOW'      => 1,
				'ALLOWSCHEDULED'    => 1,
				'STATUS'            => 1
            ],
			[
				'PAYMENTTYPENAME'   => 'Visa',
				'DESCRIPTION'       => 'Credit Card',
				'ALLOWBOOKNOW'      => 1,
				'ALLOWSCHEDULED'    => 1,
				'STATUS'            => 1
            ],
			[
				'PAYMENTTYPENAME'   => 'Paypal',
				'DESCRIPTION'       => 'Digital Wallet',
				'ALLOWBOOKNOW'      => 1,
				'ALLOWSCHEDULED'    => 1,
				'STATUS'            => 1
            ],
		];

		foreach($paymentTypeList as $dataPaymentType){
			$this->db->table('m_paymenttypes')->insert($dataPaymentType);
		}
    }
}
