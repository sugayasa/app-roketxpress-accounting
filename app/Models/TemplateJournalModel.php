<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\MainOperation;
use App\Models\ChartOfAccountModel;

class TemplateJournalModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 't_journaltemplaterecap';
    protected $primaryKey       = 'IDJOURNALTEMPLATERECAP';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['TEMPLATENAME', 'DESCRIPTION'];

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

    public function getDataTable($page, $dataPerPage, $idAccountMain, $idAccountSub, $searchKeyword)
    {	
        $mainOperation          =   new MainOperation();
        $accountNameLang        =   lang("CustomSystem.databaseField.m_account.ACCOUNTNAME");
        $con_searchKeyword      =   isset($searchKeyword) && $searchKeyword != "" ? "(A.TEMPLATENAME LIKE '%".$searchKeyword."%' OR A.DESCRIPTION LIKE '%".$searchKeyword."%')" : "1=1";
        $con_idRecapByAccount   =   $this->getConditionIdRecapByAccount($idAccountMain, $idAccountSub);
        $baseQuery              =   "SELECT A.IDJOURNALTEMPLATERECAP, A.TEMPLATENAME, A.DESCRIPTION, 
                                            CONCAT(
                                                '[',
                                                    GROUP_CONCAT(
                                                        JSON_OBJECT(
                                                            'accountName',
                                                            CONCAT(C.ACCOUNTCODE, ' - ', C.".$accountNameLang."),
                                                            'drCrPosition',
                                                            B.DEFAULTDRCR
                                                        )
                                                        ORDER BY C.ACCOUNTCODE
                                                    ),
                                                ']'
                                            ) AS OBJTEMPLATEJOURNALDETAILS
                                    FROM t_journaltemplaterecap A
                                    LEFT JOIN t_journaltemplatedetails B ON A.IDJOURNALTEMPLATERECAP = B.IDJOURNALTEMPLATERECAP
                                    LEFT JOIN ".accountCodeQueryString($accountNameLang)." AS C ON B.IDACCOUNT = C.IDACCOUNT
                                    WHERE ".$con_searchKeyword." AND ".$con_idRecapByAccount."
                                    GROUP BY A.IDJOURNALTEMPLATERECAP
                                    ORDER BY A.TEMPLATENAME";
        $result                 =   $mainOperation->execQueryWithLimit($baseQuery, $page, $dataPerPage);
        

        if(is_null($result)) return $mainOperation->generateEmptyResult();
        return $mainOperation->generateResultPagination($result, $baseQuery, 'IDJOURNALTEMPLATERECAP', $page, $dataPerPage);
	}

    private function getConditionIdRecapByAccount($idAccountMain, $idAccountSub){
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
        } else {
            return "1=1";
        }

        if(count($arrIdAccount) <= 0) return "1=1";

        $this->select("GROUP_CONCAT(DISTINCT(A.IDJOURNALTEMPLATERECAP)) AS STRARRIDJOURNALTEMPLATERECAP");
        $this->from('t_journaltemplaterecap A', true);
        $this->join('t_journaltemplatedetails AS B', 'A.IDJOURNALTEMPLATERECAP = B.IDJOURNALTEMPLATERECAP', 'LEFT');
        $this->whereIn('B.IDACCOUNT', $arrIdAccount);
        $rowData    =   $this->get()->getRowArray();

        return 'A.IDJOURNALTEMPLATERECAP IN ('.$rowData['STRARRIDJOURNALTEMPLATERECAP'].')';
    }

    public function getListDetailTemplate($idJournalTemplateRecap)
    {	
        $accountNameLang    =   lang("CustomSystem.databaseField.m_account.ACCOUNTNAME");
        $this->select("A.IDJOURNALTEMPLATEDETAIL, A.IDACCOUNT, C.DESCRIPTION, B.ACCOUNTCODE, B.".$accountNameLang." AS ACCOUNTNAME,
                    CONCAT(B.ACCOUNTCODE, ' ', B.".$accountNameLang.") AS ACCOUNTCODENAME, A.DEFAULTDRCR");
        $this->from('t_journaltemplatedetails A', true);
        $this->join(accountCodeQueryString($accountNameLang).' AS B', 'A.IDACCOUNT = B.IDACCOUNT', 'LEFT');
        $this->join('t_journaltemplaterecap AS C', 'A.IDJOURNALTEMPLATERECAP = C.IDJOURNALTEMPLATERECAP', 'LEFT');
        $this->where('A.IDJOURNALTEMPLATERECAP', $idJournalTemplateRecap);
        $this->orderBy('A.DEFAULTDRCR DESC');
        return $this->get()->getResultObject();
	}

    public function getListTemplateJournal()
    {	
        $accountNameLang    =   lang("CustomSystem.databaseField.m_account.ACCOUNTNAME");
        $this->select("A.TEMPLATENAME, A.DESCRIPTION,
                        CONCAT('[',
                            GROUP_CONCAT(JSON_OBJECT('IDACCOUNT', B.IDACCOUNT, 'DEFAULTDRCR', B.DEFAULTDRCR, 'ACCOUNTCODE', C.ACCOUNTCODE, 'ACCOUNTNAME', C.".$accountNameLang.")
                            ORDER BY B.DEFAULTDRCR DESC, C.ACCOUNTCODE),
                        ']') AS OBJACCOUNTDETAILS");
        $this->from('t_journaltemplaterecap A', true);
        $this->join('t_journaltemplatedetails AS B', 'A.IDJOURNALTEMPLATERECAP = B.IDJOURNALTEMPLATERECAP', 'LEFT');
        $this->join(accountCodeQueryString($accountNameLang).' AS C', 'B.IDACCOUNT = C.IDACCOUNT', 'LEFT');
        $this->groupBy('A.IDJOURNALTEMPLATERECAP');
        $this->orderBy('A.TEMPLATENAME');
        return $this->get()->getResultObject();
	}
}
