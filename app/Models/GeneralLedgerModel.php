<?php

namespace App\Models;

use CodeIgniter\Model;

class GeneralLedgerModel extends Model
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

    public function getBeginningBalance($idAccount, $datePeriodStart)
    {	
        $subQuery   =   $this->db->table('t_journaldetails BA')
                        ->select('BA.IDACCOUNT, BA.DEBIT, BA.CREDIT')
                        ->join('t_journalrecap BB', 'BA.IDJOURNALRECAP = BB.IDJOURNALRECAP', 'LEFT')
                        ->where('BA.IDACCOUNT', $idAccount)
                        ->where('BB.DATETRANSACTION < ', $datePeriodStart)
                        ->getCompiledSelect();

        $builder    =   $this->db->table('m_account A')
                        ->select("A.LEVEL, A.DEFAULTDRCR, IFNULL(IF(A.DEFAULTDRCR = 'DR', SUM(B.DEBIT - B.CREDIT), SUM(B.CREDIT - B.DEBIT)), 0) AS BEGINNINGBALANCE")
                        ->join("($subQuery) B", 'A.IDACCOUNT = B.IDACCOUNT', 'LEFT')
                        ->where('A.IDACCOUNT', $idAccount)
                        ->groupBy('A.IDACCOUNT')
                        ->limit(1);
        $query      =   $builder->get();
        $rowData    =   $query->getRowArray();

        if(is_null($rowData)) return ['DEFAULTDRCR' => 'DR', 'BEGINNINGBALANCE' => 0];
        if(!is_null($rowData)) return $rowData;
	}

    public function getTransactionData($idAccount, $datePeriodStart, $datePeriodEnd)
    {	
        $this->select("DATE_FORMAT(B.DATETRANSACTION, '%d %b %Y') AS DATETRANSACTION, B.REFFNUMBER, B.DESCRIPTION AS DESCRIPTIONRECAP, A.DESCRIPTION AS DESCRIPTIONDETAIL,
                    A.DEBIT, A.CREDIT");
        $this->from('t_journaldetails AS A', true);
        $this->join('t_journalrecap AS B', 'A.IDJOURNALRECAP = B.IDJOURNALRECAP', 'LEFT');
        $this->where('A.IDACCOUNT', $idAccount);
        $this->where('B.DATETRANSACTION >= ', $datePeriodStart);
        $this->where('B.DATETRANSACTION <= ', $datePeriodEnd);
        $this->orderBy('B.DATETRANSACTION');
        
        return $this->get()->getResultObject();
	}
}
