<?php

namespace App\Models;
use CodeIgniter\Model;

class CashFlowModel extends Model
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
    
    public function getDataCashFlowAccount($arrIdAccountsCashFlow, $arrIdAccount, $dateStartPeriod, $dateEndPeriod)
    {	
        $accountNameLang=   lang("CustomSystem.databaseField.m_account.ACCOUNTNAME");
        $subQuery       =   $this->db->table('t_journaldetails BA')
                            ->select('BA.IDJOURNALRECAP, BA.POSITIONDRCR')
                            ->join('t_journalrecap BB', 'BA.IDJOURNALRECAP = BB.IDJOURNALRECAP', 'LEFT')
                            ->whereIn('BA.IDACCOUNT', $arrIdAccountsCashFlow)
                            ->where("DATE(BB.DATETRANSACTION) BETWEEN '".$dateStartPeriod."' AND '".$dateEndPeriod."'")
                            ->where("BB.REFFNUMBER !=", DEFAULT_REFF_NUMBER_OPENING_BALANCE)
                            ->groupBy('BA.IDJOURNALRECAP')
                            ->getCompiledSelect();

        $builder        =   $this->db->table('t_journaldetails A')
                            ->select('A.IDACCOUNT, C.ACCOUNTCODE, C.'.$accountNameLang.' AS ACCOUNTNAME, IFNULL(IF(B.POSITIONDRCR = "DR", SUM(A.CREDIT), SUM(A.DEBIT) * -1), 0) AS SALDO')
                            ->join("($subQuery) B", 'A.IDJOURNALRECAP = B.IDJOURNALRECAP', 'LEFT', false)
                            ->join('m_account C', 'A.IDACCOUNT = C.IDACCOUNT', 'LEFT')
                            ->whereIn('A.IDACCOUNT', $arrIdAccount)
                            ->whereNotIn('A.IDACCOUNT', $arrIdAccountsCashFlow)
                            ->where('B.IDJOURNALRECAP IS NOT NULL', NULL, false)
                            ->groupBy('A.IDACCOUNT');

        $query          =   $builder->get();
        $result         =   $query->getResult();

        if(is_null($result)) return false;
        return $result;
    }
    
    public function getSaldoAccountCash($arrIdAccountsCashFlow, $dateStartPeriod)
    {	
        $this->select("IFNULL(SUM(A.DEBIT - A.CREDIT), 0) AS SALDO");
        $this->from('t_journaldetails AS A', true);
        $this->join('t_journalrecap AS B', 'A.IDJOURNALRECAP = B.IDJOURNALRECAP', 'LEFT');
        $this->whereIn('A.IDACCOUNT', $arrIdAccountsCashFlow);
        $this->where('B.DATETRANSACTION <', $dateStartPeriod);
        $this->limit(1);

        $result =   $this->first();

        if(is_null($result)) return 0;
        return $result['SALDO'];
    }

    public function getDataDetailCashFlow($idAccount, $arrIdAccountsCashFlow, $dateStart, $dateEnd, $chartOfAccountModel)
    {	
        $this->select("A.IDJOURNALRECAP, DATE_FORMAT(B.DATETRANSACTION, '%d %b %Y') AS DATETRANSACTION, B.REFFNUMBER, '' AS ACCOUNTCASHCODE, '' AS ACCOUNTCASHNAME,
                       B.DESCRIPTION AS DESCRIPTIONRECAP, A.DEBIT, A.CREDIT");
        $this->from('t_journaldetails AS A', true);
        $this->join('t_journalrecap AS B', 'A.IDJOURNALRECAP = B.IDJOURNALRECAP', 'LEFT');
        $this->where('A.IDACCOUNT', $idAccount);
        $this->where('B.DATETRANSACTION >= ', $dateStart);
        $this->where('B.DATETRANSACTION <= ', $dateEnd);
        $this->orderBy('B.DATETRANSACTION');
        $resultJournalDetail        =   $this->get()->getResultObject();
        $resultDataDetailCashFlow   =   [];

        if(!is_null($resultJournalDetail) && count($resultJournalDetail) > 0){
            foreach($resultJournalDetail as $keyJournalDetail){
                $idJournalRecap     =   $keyJournalDetail->IDJOURNALRECAP;
                $queryJournalCashStr=   $this->db->table('t_journaldetails A')
                                    ->select('A.IDACCOUNT, B.LEVEL, A.DESCRIPTION AS DESCRIPTIONDETAIL, A.DEBIT, A.CREDIT')
                                    ->join('m_account B', 'A.IDACCOUNT = B.IDACCOUNT', 'LEFT')
                                    ->where('A.IDJOURNALRECAP', $idJournalRecap)
                                    ->whereIn('A.IDACCOUNT', $arrIdAccountsCashFlow);
                $queryJournalCash   =   $queryJournalCashStr->get();
                $resultJournalCash  =   $queryJournalCash->getResult();

                if(!is_null($resultJournalCash) && count($resultJournalCash) > 0){
                    foreach($resultJournalCash as $keyJournalCash){
                        $detailAccountCash          =   $chartOfAccountModel->getDetailAccount($keyJournalCash->LEVEL, $keyJournalCash->IDACCOUNT);
                        $resultDataDetailCashFlow[] =   [
                            'DATETRANSACTION'   =>  $keyJournalDetail->DATETRANSACTION,
                            'REFFNUMBER'        =>  $keyJournalDetail->REFFNUMBER,
                            'ACCOUNTCASHCODE'   =>  $detailAccountCash['ACCOUNTCODE'],
                            'ACCOUNTCASHNAME'   =>  $detailAccountCash['ACCOUNTNAME'],
                            'DESCRIPTIONRECAP'  =>  $keyJournalDetail->DESCRIPTIONRECAP,
                            'DESCRIPTIONDETAIL' =>  $keyJournalCash->DESCRIPTIONDETAIL,
                            'DEBIT'             =>  $keyJournalCash->DEBIT,
                            'CREDIT'            =>  $keyJournalCash->CREDIT
                        ];
                    }
                }
            }
        }

        return $resultDataDetailCashFlow;
	}
}
