<?php

namespace App\Models;
use CodeIgniter\Model;

class ChartOfAccountModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'm_account';
    protected $primaryKey       = 'IDACCOUNT';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['IDACCOUNT', 'IDACCOUNTPARENT', 'LEVEL', 'ACCOUNTCODE', 'ACCOUNTNAMEENG', 'ACCOUNTNAMEID', 'DEFAULTDRCR', 'ORDERNUMBER'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
    
    public function getDataAccountSub($idAccountGeneral = 0, $idAccountMain = 0, $searchKeyword = null, $arrIdAccountSub = [])
    {	
        $accountNameLang    =   lang("CustomSystem.databaseField.m_account.ACCOUNTNAME");
        $this->select("A.IDACCOUNT AS IDACCOUNTSUB, B.IDACCOUNT AS IDACCOUNTMAIN, C.IDACCOUNT AS IDACCOUNTGENERAL, A.ACCOUNTCODE, A.".$accountNameLang." AS ACCOUNTNAME, A.DEFAULTDRCR");
        $this->from('m_account AS A', true);
        $this->join('(SELECT IDACCOUNT, IDACCOUNTPARENT, ORDERNUMBER, ACCOUNTNAMEENG, ACCOUNTNAMEID FROM m_account WHERE LEVEL = 2) AS B', 'A.IDACCOUNTPARENT = B.IDACCOUNT', 'LEFT');
        $this->join('(SELECT IDACCOUNT, ORDERNUMBER, ACCOUNTNAMEENG, ACCOUNTNAMEID FROM m_account WHERE LEVEL = 1) AS C', 'B.IDACCOUNTPARENT = C.IDACCOUNT', 'LEFT');
        $this->where('A.LEVEL', 3);
        if(isset($idAccountGeneral) && $idAccountGeneral != 0 && $idAccountGeneral != '') $this->where('C.IDACCOUNT', $idAccountGeneral);
        if(isset($idAccountMain) && $idAccountMain != 0 && $idAccountMain != '') $this->where('B.IDACCOUNT', $idAccountMain);
        if(isset($searchKeyword) && !is_null($searchKeyword)){
            $this->groupStart();
            $this->like('A.'.$accountNameLang, $searchKeyword, 'both')
            ->orLike('B.'.$accountNameLang, $searchKeyword, 'both')
            ->orLike('C.'.$accountNameLang, $searchKeyword, 'both');
            $this->groupEnd();
        }
        if(isset($arrIdAccountSub) && $arrIdAccountSub != 0 && count($arrIdAccountSub) > 0) $this->whereIn('A.IDACCOUNT', $arrIdAccountSub);
        $this->orderBy('C.ORDERNUMBER, B.ORDERNUMBER, A.ORDERNUMBER');

        $result =   $this->get()->getResultObject();

        if(is_null($result)) return false;
        return $result;
	}
    
    public function getDataAccountMain($idAccountGeneral = 0, $idAccountMain = 0, $searchKeyword = null, $arrIdAccountMain = [], $arrIdAccountGeneral = [])
    {	
        $accountNameLang    =   lang("CustomSystem.databaseField.m_account.ACCOUNTNAME");
        $isKeywordSearch    =   isset($searchKeyword) && !is_null($searchKeyword) ? true : false;
        $this->select("A.IDACCOUNT AS IDACCOUNTMAIN, B.IDACCOUNT AS IDACCOUNTGENERAL, A.ACCOUNTCODE, A.".$accountNameLang." AS ACCOUNTNAME, A.DEFAULTDRCR");
        $this->from('m_account AS A', true);
        $this->join('(SELECT IDACCOUNT, IDACCOUNTPARENT, ORDERNUMBER, ACCOUNTNAMEENG, ACCOUNTNAMEID FROM m_account WHERE LEVEL = 1) AS B', 'A.IDACCOUNTPARENT = B.IDACCOUNT', 'LEFT');
        $this->where('A.LEVEL', 2);
        if(isset($idAccountGeneral) && $idAccountGeneral != 0 && $idAccountGeneral != '') $this->where('B.IDACCOUNT', $idAccountGeneral);
        if(isset($idAccountMain) && $idAccountMain != 0 && $idAccountMain != '') $this->where('A.IDACCOUNT', $idAccountMain);
        $this->groupStart();
            if(isset($arrIdAccountMain) && is_array($arrIdAccountMain) && count($arrIdAccountMain) > 0) $this->whereIn('A.IDACCOUNT', $arrIdAccountMain);
            if(isset($arrIdAccountGeneral) && is_array($arrIdAccountGeneral) && count($arrIdAccountGeneral) > 0 && !$isKeywordSearch) $this->orWhereIn('B.IDACCOUNT', $arrIdAccountGeneral);
            if($isKeywordSearch){
                $this->orLike('A.'.$accountNameLang, $searchKeyword, 'both')
                ->orLike('B.'.$accountNameLang, $searchKeyword, 'both');
            }
        $this->groupEnd();
        $this->orderBy('B.ORDERNUMBER, A.ORDERNUMBER');

        $result =   $this->get()->getResultObject();

        if(is_null($result)) return false;
        return $result;
	}
    
    public function getDataAccountGeneral($idAccountGeneral = 0, $searchKeyword = null, $arrIdAccountGeneral = [])
    {	
        $accountNameLang    =   lang("CustomSystem.databaseField.m_account.ACCOUNTNAME");
        $isKeywordSearch    =   isset($searchKeyword) && !is_null($searchKeyword) && $searchKeyword != '' ? true : false;
        $this->select("IDACCOUNT AS IDACCOUNTGENERAL, ACCOUNTCODE, ".$accountNameLang." AS ACCOUNTNAME, DEFAULTDRCR");
        $this->from('m_account', true);
        $this->where('LEVEL', 1);
        if(isset($idAccountGeneral) && $idAccountGeneral != 0 && $idAccountGeneral != '') $this->where('IDACCOUNT', $idAccountGeneral);
        $this->groupStart();
            if(isset($arrIdAccountGeneral) && is_array($arrIdAccountGeneral) && count($arrIdAccountGeneral) > 0) $this->whereIn('IDACCOUNT', $arrIdAccountGeneral);
            if($isKeywordSearch) $this->orLike($accountNameLang, $searchKeyword, 'both');
        $this->groupEnd();
        $this->orderBy('ORDERNUMBER');

        $result =   $this->get()->getResultObject();

        if(is_null($result)) return false;
        return $result;
	}

    public function getDataAccountOpeningBalance()
    {	
        $this->select("IDJOURNALRECAP, REFFNUMBER, DATE_FORMAT(DATETRANSACTION, '%d-%m-%Y') AS DATETRANSACTION, DESCRIPTION");
        $this->from('t_journalrecap', true);
        $this->where('REFFNUMBER', DEFAULT_REFF_NUMBER_OPENING_BALANCE);
        $this->limit(1);
        $detailJournalRecap =   $this->get()->getRowArray();

        if($detailJournalRecap === null){
            return [
                "detailJournalRecap"    =>  [
                    "REFFNUMBER"        =>  DEFAULT_REFF_NUMBER_OPENING_BALANCE,
                    "DATETRANSACTION"   =>  date('01-01-Y'),
                    "DESCRIPTION"       =>  "Saldo Awal Akun Neraca"
                ],
                "dataOpeningBalance"    =>  []
            ];
        }

        $idJournalRecap     =   $detailJournalRecap['IDJOURNALRECAP'];
        unset($detailJournalRecap['IDJOURNALRECAP']);

        $this->select("IDACCOUNT, POSITIONDRCR, DEBIT, CREDIT");
        $this->from('t_journaldetails', true);
        $this->where('IDJOURNALRECAP', $idJournalRecap);
        $dataOpeningBalance =   $this->get()->getResultObject();

        return [
            "detailJournalRecap"    =>  $detailJournalRecap,
            "dataOpeningBalance"    =>  $dataOpeningBalance === null ? [] : encodeDatabaseObjectResultKey($dataOpeningBalance, 'IDACCOUNT')
        ];
	}

    public function isJournalRecapExist($reffNumber)
    {	
        $this->select("IDJOURNALRECAP");
        $this->from('t_journalrecap', true);
        $this->where('REFFNUMBER', $reffNumber);
        $this->limit(1);
        $detailJournalRecap =   $this->get()->getRowArray();

        if($detailJournalRecap === null){
            return false;
        } else {
            return $detailJournalRecap;
        }
	}

    public function isJournalDetailExist($idJournalRecap, $idAccount)
    {	
        $this->select("IDJOURNALDETAILS");
        $this->from('t_journaldetails', true);
        $this->where('IDJOURNALRECAP', $idJournalRecap);
        $this->where('IDACCOUNT', $idAccount);
        $this->limit(1);
        $detailJournalRecap =   $this->get()->getRowArray();

        if($detailJournalRecap === null) return false;
        return true;
	}

    public function getDetailNextStep($accountLevel, $idAccountGeneral = false, $idAccountMain = false)
    {	
        $result =   $this->getListOrderPosition($accountLevel, $idAccountGeneral, $idAccountMain);

        if(is_null($result)) return false;

        $lastNumber =   1;
        $dataResult =   [
            'defaultDRCR'       =>  $accountLevel == 1 ? 'DR' : '',
            'listOrderPosition' =>  [
                ['First', 1]
            ]
        ];

        foreach($result as $key){
            if($dataResult['defaultDRCR'] == '') $dataResult['defaultDRCR'] =   $key->DEFAULTDRCR;
            $lastNumber =  $key->ORDERNUMBER * 1 + 1; 
            $dataResult['listOrderPosition'][]  =   [
                'After '.$key->ACCOUNTNAME,
                $lastNumber
            ];
        }

        if($dataResult['defaultDRCR'] == '') $dataResult['defaultDRCR'] = $this->getDefaultDRCRAccount($accountLevel, $idAccountGeneral, $idAccountMain);
        $dataResult['lastNumber']   =   $lastNumber;

        return $dataResult;
	}

    public function getListOrderPosition($accountLevel, $idAccountGeneral = false, $idAccountMain = false)
    {	
        $accountNameLang    =   lang("CustomSystem.databaseField.m_account.ACCOUNTNAME");
        $this->select("IDACCOUNT, CONCAT('[', ACCOUNTCODE, ']', ' - ', ".$accountNameLang.") AS ACCOUNTNAME, ORDERNUMBER, DEFAULTDRCR");
        $this->from('m_account', true);
        $this->where('LEVEL', $accountLevel);
        if($idAccountGeneral && $accountLevel == 1) $this->where('IDACCOUNTPARENT', 0);
        if($idAccountGeneral && $accountLevel == 2) $this->where('IDACCOUNTPARENT', $idAccountGeneral);
        if($idAccountMain && $accountLevel == 3) $this->where('IDACCOUNTPARENT', $idAccountMain);
        $this->orderBy('ORDERNUMBER');

        return $this->get()->getResultObject();
	}

    public function getDefaultDRCRAccount($accountLevel, $idAccountGeneral, $idAccountMain)
    {	
        $this->select("DEFAULTDRCR");
        $this->from('m_account', true);
        if($idAccountGeneral && $accountLevel == 2) $this->where('IDACCOUNT', $idAccountGeneral);
        if($idAccountMain && $accountLevel == 3) $this->where('IDACCOUNT', $idAccountMain);
        $this->limit(1);

        $result =   $this->first();

        if(is_null($result)) return 'DR';
        return $result['DEFAULTDRCR'];
    }

    public function getBasicDetailAccount($idAccount)
    {	
        $accountNameLang    =   lang("CustomSystem.databaseField.m_account.ACCOUNTNAME");
        $this->select("IDACCOUNT, IDACCOUNTPARENT, ACCOUNTCODE, ".$accountNameLang." AS ACCOUNTNAME, LEVEL");
        $this->from('m_account', true);
        $this->where('IDACCOUNT', $idAccount);
        $this->limit(1);

        return $this->get()->getRowArray();
	}

    public function getDetailAccount($accountLevel, $idAccount)
    {	
        $accountCodeField   =   "A.ACCOUNTCODE";

        if($accountLevel == 2){
            $accountCodeField   =   "CONCAT(C.ACCOUNTCODE, '-', A.ACCOUNTCODE) AS ACCOUNTCODE";
        } else if ($accountLevel == 3){
            $accountCodeField   =   "CONCAT(C.ACCOUNTCODE, '-', B.ACCOUNTCODE, '-', A.ACCOUNTCODE) AS ACCOUNTCODE";
        }

        $accountNameLang    =   lang("CustomSystem.databaseField.m_account.ACCOUNTNAME");
        $fieldAccountMain   =   $accountLevel == 3 ? ", CONCAT('[', B.ACCOUNTCODE, ']', ' - ', B.".$accountNameLang.") AS ACCOUNTMAIN" : "";
        $fieldAccountGeneral=   $accountLevel >= 2 ? ", CONCAT('[', C.ACCOUNTCODE, ']', ' - ', C.".$accountNameLang.") AS ACCOUNTGENERAL" : "";
        $tableAliasJoin     =   $accountLevel == 3 ? "B" : "A";
        $this->select("A.IDACCOUNT, A.IDACCOUNTPARENT, ".$accountCodeField.", A.".$accountNameLang." AS ACCOUNTNAME".$fieldAccountMain.$fieldAccountGeneral);
        $this->from('m_account AS A', true);
        if($accountLevel == 3) $this->join('(SELECT IDACCOUNT, IDACCOUNTPARENT, ACCOUNTCODE, ACCOUNTNAMEENG, ACCOUNTNAMEID FROM m_account WHERE LEVEL = 2) AS B', 'A.IDACCOUNTPARENT = B.IDACCOUNT', 'LEFT');        
        if($accountLevel >= 2) $this->join('(SELECT IDACCOUNT, IDACCOUNTPARENT, ACCOUNTCODE, ACCOUNTNAMEENG, ACCOUNTNAMEID FROM m_account WHERE LEVEL = 1) AS C', $tableAliasJoin.'.IDACCOUNTPARENT = C.IDACCOUNT', 'LEFT');
        $this->where('A.IDACCOUNT', $idAccount);
        $this->limit(1);

        return $this->get()->getRowArray();
	}

    public function isChildAccountExist($idAccount)
    {	
        $this->select("IDACCOUNT");
        $this->from('m_account', true);
        $this->where('IDACCOUNTPARENT', $idAccount);
        $this->limit(1);

        $rowData    =   $this->get()->getRowArray();
        if(is_null($rowData)) return false;
        return true;
	}

    public function isTransactionAccountExist($idAccount)
    {	
        $this->select("IDJOURNALDETAILS");
        $this->from('t_journaldetails', true);
        $this->where('IDACCOUNT', $idAccount);
        $this->limit(1);

        $rowData    =   $this->get()->getRowArray();
        if(is_null($rowData)) return false;
        return true;
	}

    public function isTemplateJournalAccountExist($idAccount)
    {	
        $this->select("IDJOURNALTEMPLATEDETAIL");
        $this->from('t_journaltemplatedetails', true);
        $this->where('IDACCOUNT', $idAccount);
        $this->limit(1);

        $rowData    =   $this->get()->getRowArray();
        if(is_null($rowData)) return false;
        return true;
	}
}
