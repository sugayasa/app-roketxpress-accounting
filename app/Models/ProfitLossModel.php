<?php

namespace App\Models;
use CodeIgniter\Model;

class ProfitLossModel extends Model
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

    public function getSaldoAccount($idAccount, $dateStartPeriod, $dateEndPeriod)
    {	
        $this->select("IFNULL(IF(C.DEFAULTDRCR = 'DR', SUM(A.DEBIT - A.CREDIT), SUM(A.CREDIT - A.DEBIT)), 0) AS SALDO");
        $this->from('t_journaldetails AS A', true);
        $this->join('t_journalrecap AS B', 'A.IDJOURNALRECAP = B.IDJOURNALRECAP', 'LEFT');
        $this->join('m_account AS C', 'A.IDACCOUNT = C.IDACCOUNT', 'LEFT');
        $this->where('A.IDACCOUNT', $idAccount);
        $this->where('B.DATETRANSACTION >=', $dateStartPeriod);
        $this->where('B.DATETRANSACTION <=', $dateEndPeriod);
        $this->limit(1);

        $result =   $this->first();

        if(is_null($result)) return 0;
        return $result['SALDO'];
    }
}
