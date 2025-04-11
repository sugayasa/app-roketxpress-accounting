<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\MainOperation;
use App\Models\ChartOfAccountModel;

class GeneralJournalModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 't_journalrecap';
    protected $primaryKey       = 'IDJOURNALRECAP';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['REFFNUMBER', 'DATETRANSACTION', 'DESCRIPTION', 'TOTALACCOUNT', 'TOTALNOMINAL', 'USERINSERT', 'USERUPDATE', 'DATETIMEINSERT', 'DATETIMEUPDATE'];

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

    public function getDataTable($page, $dataPerPage, $idAccountGeneral, $idAccountMain, $idAccountSub, $datePeriodStart, $datePeriodEnd, $searchReffNumber, $searchDescription)
    {	
        $mainOperation          =   new MainOperation();
        $accountNameLang        =   lang("CustomSystem.databaseField.m_account.ACCOUNTNAME");
        $con_searchReffNumber   =   isset($searchReffNumber) && $searchReffNumber != "" ? "A.REFFNUMBER LIKE '%".$searchReffNumber."%'" : "1=1";
        $con_searchDescription  =   isset($searchDescription) && $searchDescription != "" ? "A.DESCRIPTION LIKE '%".$searchDescription."%'" : "1=1";
        $con_idRecapByAccount   =   $this->getConditionIdRecapByAccount($idAccountGeneral, $idAccountMain, $idAccountSub, $datePeriodStart, $datePeriodEnd);
        $baseQuery              =   "SELECT A.IDJOURNALRECAP, A.REFFNUMBER, DATE_FORMAT(A.DATETRANSACTION, '%d %b %Y') AS DATETRANSACTION, A.DESCRIPTION,
                                            CONCAT(
                                                '[',
                                                    GROUP_CONCAT(
                                                        JSON_OBJECT(
                                                            'accountCode',
                                                            C.ACCOUNTCODE,
                                                            'accountName',
                                                            C.".$accountNameLang.",
                                                            'description',
                                                            B.DESCRIPTION,
                                                            'debit',
                                                            B.DEBIT,
                                                            'credit',
                                                            B.CREDIT
                                                        )
                                                        ORDER BY B.DEBIT DESC, C.ACCOUNTCODE
                                                    ),
                                                ']'
                                            ) AS OBJACCOUNTDETAILS, A.TOTALNOMINAL
                                    FROM t_journalrecap A
                                    LEFT JOIN t_journaldetails B ON A.IDJOURNALRECAP = B.IDJOURNALRECAP
                                    LEFT JOIN ".accountCodeQueryString($accountNameLang)." AS C ON B.IDACCOUNT = C.IDACCOUNT
                                    WHERE A.DATETRANSACTION BETWEEN '".$datePeriodStart."' AND '".$datePeriodEnd."' AND ".$con_searchDescription." AND ".$con_searchReffNumber." AND ".$con_idRecapByAccount."
                                    GROUP BY A.IDJOURNALRECAP
                                    ORDER BY A.DATETRANSACTION";
        $result                 =   $mainOperation->execQueryWithLimit($baseQuery, $page, $dataPerPage);
        

        if(is_null($result)) return $mainOperation->generateEmptyResult();
        return $mainOperation->generateResultPagination($result, $baseQuery, 'IDJOURNALRECAP', $page, $dataPerPage);
	}

    private function getConditionIdRecapByAccount($idAccountGeneral, $idAccountMain, $idAccountSub, $datePeriodStart, $datePeriodEnd){
        $chartOfAccountModel=   new ChartOfAccountModel();
        $arrIdAccount       =   [];

        if(isset($idAccountSub) && $idAccountSub != ""){
            $arrIdAccount[] =   $idAccountSub;
        } else if(isset($idAccountMain) && $idAccountMain != "") {
            $arrIdAccount[] =   $idAccountMain;
            $dataIdAccount  =   $chartOfAccountModel->where('IDACCOUNTPARENT', $idAccountMain)->findAll();
            if(!is_null($dataIdAccount)){
                foreach($dataIdAccount as $keyIdAccount){
                    $arrIdAccount[] =   $keyIdAccount['IDACCOUNT'];
                }
            }
        } else if(isset($idAccountGeneral) && $idAccountGeneral != "") {
            $dataIdAccountMain  =   $chartOfAccountModel->where('IDACCOUNTPARENT', $idAccountGeneral)->findAll();
            if(!is_null($dataIdAccountMain)){
                foreach($dataIdAccountMain as $keyIdAccountMain){
                    $idAccountMain      =   $keyIdAccountMain['IDACCOUNT'];
                    $arrIdAccount[]     =   $idAccountMain;
                    $dataIdAccountSub   =   $chartOfAccountModel->where('IDACCOUNTPARENT', $idAccountMain)->findAll();
                    if(!is_null($dataIdAccountSub)){
                        foreach($dataIdAccountSub as $keyIdAccountSub){
                            $arrIdAccount[]     =   $keyIdAccountSub['IDACCOUNT'];
                        }
                    }
                }
            }
        } else {
            return "1=1";
        }

        if(count($arrIdAccount) <= 0) return "1=1";

        $this->select("GROUP_CONCAT(DISTINCT(A.IDJOURNALRECAP)) AS STRARRIDJOURNALRECAP");
        $this->from('t_journalrecap A', true);
        $this->join('t_journaldetails AS B', 'A.IDJOURNALRECAP = B.IDJOURNALRECAP', 'LEFT');
        $this->where('A.DATETRANSACTION >=', $datePeriodStart);
        $this->where('A.DATETRANSACTION <=', $datePeriodEnd);
        $this->whereIn('B.IDACCOUNT', $arrIdAccount);
        $rowData    =   $this->get()->getRowArray();

        return 'A.IDJOURNALRECAP IN ('.$rowData['STRARRIDJOURNALRECAP'].')';
    }

    public function getNewReffNumber($prefix, $lastReffNumber = false)
    {	
        $year   =   date('Y');
        if(!$lastReffNumber) $this->select("RIGHT(REFFNUMBER , 6) AS LASTREFNUMBER");
        if($lastReffNumber) $this->select($lastReffNumber." AS LASTREFNUMBER");
        $this->from('t_journalrecap', true);
        $this->where('LEFT(REFFNUMBER, 2)', $prefix);
        $this->where('SUBSTRING(REFFNUMBER, 3,4)', $year);
        $this->orderBy('DATETIMEINSERT DESC, IDJOURNALRECAP DESC');
        $this->limit(1);
        $rowData    =   $this->get()->getRowArray();
        $number     =   $lastReffNumber ? $lastReffNumber : 1;
        if(is_null($rowData)) return $prefix.$year.str_pad($number, 6, '0', STR_PAD_LEFT);
        if(!is_null($rowData)) return $prefix.$year.str_pad(($rowData['LASTREFNUMBER'] * 1 + 1), 6, '0', STR_PAD_LEFT);
	}

    public function getListDetailJournal($idJournalRecap)
    {	
        $accountNameLang    =   lang("CustomSystem.databaseField.m_account.ACCOUNTNAME");
        $this->select("A.IDJOURNALDETAILS, A.IDACCOUNT, B.ACCOUNTCODE, B.".$accountNameLang." AS ACCOUNTNAME, A.DESCRIPTION, A.DEBIT, A.CREDIT");
        $this->from('t_journaldetails A', true);
        $this->join(accountCodeQueryString($accountNameLang).' AS B', 'A.IDACCOUNT = B.IDACCOUNT', 'LEFT');
        $this->where('A.IDJOURNALRECAP', $idJournalRecap);
        $this->orderBy('A.DEBIT DESC');
        return $this->get()->getResultObject();
	}

    public function getDetailAccountByCode($levelAccountCode, $accountCode)
    {	
        $accountCodeField   =   $accountCodeFieldWhere  =   "A.ACCOUNTCODE";

        if($levelAccountCode == 2){
            $accountCodeField       =   "CONCAT(C.ACCOUNTCODE, '-', A.ACCOUNTCODE) AS ACCOUNTCODE";
            $accountCodeFieldWhere  =   "CONCAT(C.ACCOUNTCODE, '-', A.ACCOUNTCODE)";
        } else if ($levelAccountCode == 3){
            $accountCodeField       =   "CONCAT(C.ACCOUNTCODE, '-', B.ACCOUNTCODE, '-', A.ACCOUNTCODE) AS ACCOUNTCODE";
            $accountCodeFieldWhere  =   "CONCAT(C.ACCOUNTCODE, '-', B.ACCOUNTCODE, '-', A.ACCOUNTCODE)";
        }

        $accountNameLang    =   lang("CustomSystem.databaseField.m_account.ACCOUNTNAME");
        $fieldAccountMain   =   $levelAccountCode == 3 ? ", CONCAT('[', B.ACCOUNTCODE, ']', ' - ', B.".$accountNameLang.") AS ACCOUNTMAIN" : "";
        $fieldAccountGeneral=   $levelAccountCode >= 2 ? ", CONCAT('[', C.ACCOUNTCODE, ']', ' - ', C.".$accountNameLang.") AS ACCOUNTGENERAL" : "";
        $tableAliasJoin     =   $levelAccountCode == 3 ? "B" : "A";
        $this->select("A.IDACCOUNT, A.IDACCOUNTPARENT, ".$accountCodeField.", A.".$accountNameLang." AS ACCOUNTNAME".$fieldAccountMain.$fieldAccountGeneral);
        $this->from('m_account AS A', true);
        if($levelAccountCode == 3) $this->join('(SELECT IDACCOUNT, IDACCOUNTPARENT, ACCOUNTCODE, ACCOUNTNAMEENG, ACCOUNTNAMEID FROM m_account WHERE LEVEL = 2) AS B', 'A.IDACCOUNTPARENT = B.IDACCOUNT', 'LEFT');        
        if($levelAccountCode >= 2) $this->join('(SELECT IDACCOUNT, IDACCOUNTPARENT, ACCOUNTCODE, ACCOUNTNAMEENG, ACCOUNTNAMEID FROM m_account WHERE LEVEL = 1) AS C', $tableAliasJoin.'.IDACCOUNTPARENT = C.IDACCOUNT', 'LEFT');
        $this->where($accountCodeFieldWhere, $accountCode);
        $this->limit(1);

        return $this->get()->getRowArray();
	}
}
