<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\MainOperation;

class ListAssetOwnedModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 't_asset';
    protected $primaryKey       = 'IDASSET';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['IDASSETTYPE', 'IDASSETDEPRECIATIONGROUP', 'ASSETNAME', 'DESCRIPTION', 'PURCHASEDATE', 'PURCHASEPRICE', 'RESIDUALVALUE', 'INSERTDATETIME', 'INSERTUSER'];

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

    public function getDataTable($page, $dataPerPage, $idAssetType, $idDepreciationGroup, $searchKeyword)
    {	
        $mainOperation          =   new MainOperation();
        $con_idAssetType        =   isset($idAssetType) && $idAssetType != '' ? 'A.IDASSETTYPE = '.$idAssetType : '1=1';
        $con_idDepreciationGroup=   isset($idDepreciationGroup) && $idDepreciationGroup != '' ? 'A.IDASSETDEPRECIATIONGROUP = '.$idDepreciationGroup : '1=1';
        $con_searchKeyword      =   isset($searchKeyword) && $searchKeyword != "" ? "(A.ASSETNAME LIKE '%".$searchKeyword."%' OR A.DESCRIPTION LIKE '%".$searchKeyword."%')" : "1=1";
        $baseQuery              =   "SELECT B.ASSETTYPE, A.ASSETNAME, A.DESCRIPTION, C.ASSETDEPRECIATIONGROUPNAME, DATE_FORMAT(A.PURCHASEDATE, '%d %b %Y') AS PURCHASEDATESTR,
                                            A.PURCHASEPRICE, A.RESIDUALVALUE, (A.PURCHASEPRICE - A.RESIDUALVALUE) AS DEPRECIATIONVALUE, A.IDASSET
                                    FROM t_asset A
                                    LEFT JOIN m_assettype B ON A.IDASSETTYPE = B.IDASSETTYPE
                                    LEFT JOIN m_assetdepreciationgroup AS C ON A.IDASSETDEPRECIATIONGROUP = C.IDASSETDEPRECIATIONGROUP
                                    WHERE ".$con_searchKeyword." AND ".$con_idAssetType." AND ".$con_idDepreciationGroup."
                                    ORDER BY B.ASSETTYPE, A.ASSETNAME, A.PURCHASEDATE";
        $result                 =   $mainOperation->execQueryWithLimit($baseQuery, $page, $dataPerPage);
        

        if(is_null($result)) return $mainOperation->generateEmptyResult();
        return $mainOperation->generateResultPagination($result, $baseQuery, 'IDASSET', $page, $dataPerPage);
	}

    public function getDataDepreciationPosting()
    {
        $this->select("A.IDASSETDEPRECIATION, B.IDJOURNALTEMPLATERECAP, B.ASSETNAME, C.ASSETTYPE, D.ASSETDEPRECIATIONGROUPNAME, DATE_FORMAT(B.PURCHASEDATE, '%d %b %Y') AS PURCHASEDATESTR,
                        B.PURCHASEPRICE, B.RESIDUALVALUE, (B.PURCHASEPRICE - B.RESIDUALVALUE) AS DEPRECIATIONVALUE, A.DEPRECIATIONNUMBER, DATE_FORMAT(A.DEPRECIATIONDATE, '%m-%Y') AS DEPRECIATIONPERIOD,
                        DATE_FORMAT(A.DEPRECIATIONDATE, '%b %Y') AS DEPRECIATIONPERIODSTR, DATE_FORMAT(A.DEPRECIATIONDATE, '%d %b %Y') AS DEPRECIATIONDATESTR, A.DEPRECIATIONDATE,
                        A.DEPRECIATIONVALUE AS DEPRECIATIONVALUEJOURNAL, '' AS DETAILTEMPLATEJOURNAL, B.IDASSETTYPE, B.IDASSETDEPRECIATIONGROUP");
        $this->from('t_assetdepreciation A', true);
        $this->join('t_asset AS B', 'A.IDASSET = B.IDASSET', 'LEFT');
        $this->join('m_assettype AS C', 'B.IDASSETTYPE = C.IDASSETTYPE', 'LEFT');
        $this->join('m_assetdepreciationgroup AS D', 'B.IDASSETDEPRECIATIONGROUP = D.IDASSETDEPRECIATIONGROUP', 'LEFT');
        $this->where('A.IDJOURNALRECAP', 0);
        $this->where('A.STATUSJOURNAL', 0);
        $this->where('A.DEPRECIATIONDATE <= ', date('Y-m-d'));
        $this->orderBy('A.DEPRECIATIONDATE ASC, A.DEPRECIATIONNUMBER, B.ASSETNAME');

        $result =   $this->get()->getResultObject();
        if(is_null($result)) return false;
        return $result;
	}

    public function getDetailDepreciationPosting($idAssetDepreciation)
    {
        $this->select("B.IDJOURNALTEMPLATERECAP, DATE_FORMAT(A.DEPRECIATIONDATE, '%d-%m-%Y') AS DEPRECIATIONDATE, A.DEPRECIATIONVALUE,
                    '' AS JOURNALDESCRIPTION, B.ASSETNAME");
        $this->from('t_assetdepreciation A', true);
        $this->join('t_asset AS B', 'A.IDASSET = B.IDASSET', 'LEFT');
        $this->where('A.IDASSETDEPRECIATION', $idAssetDepreciation);
        $this->limit(1);

        $rowData    =   $this->get()->getRowArray();
        if(is_null($rowData)) return false;
        return $rowData;
	}

    public function getDetailDepreciationGroup($idDepreciationGroup)
    {
        $this->select("ASSETDEPRECIATIONGROUPNAME, DESCRIPTION, YEARSBENEFIT");
        $this->from('m_assetdepreciationgroup', true);
        $this->where('IDASSETDEPRECIATIONGROUP', $idDepreciationGroup);
        $this->limit(1);

        $rowData    =   $this->get()->getRowArray();
        if(is_null($rowData)) return false;
        return $rowData;
	}

    public function getDataDepreciation($idAsset, $arrIdJournal = true)
    {
        $fieldArrIdJournal  =   $arrIdJournal ? ", GROUP_CONCAT(IF(C.POSITIONDRCR = 'DR', C.IDJOURNALDETAILS, NULL)) AS ARRIDJOURNALDETAILDEBIT, GROUP_CONCAT(IF(C.POSITIONDRCR = 'CR', C.IDJOURNALDETAILS, NULL)) AS ARRIDJOURNALDETAILCREDIT" : '';
        $this->select("A.IDASSETDEPRECIATION, A.IDJOURNALRECAP, A.DEPRECIATIONNUMBER, DATE_FORMAT(A.DEPRECIATIONDATE, '%d %b %Y') AS DEPRECIATIONDATE,
                    IFNULL(DATE_FORMAT(B.DATETIMEINSERT, '%d %b %Y %H:%i'), '-') AS DATETIMEJOURNAL, A.DEPRECIATIONVALUE, IFNULL(B.TOTALNOMINAL, 0) AS JOURNALVALUE,
                    IFNULL(B.REFFNUMBER, '-') AS REFFNUMBER, IFNULL(B.DESCRIPTION, '-') AS DESCRIPTION, IFNULL(B.USERINSERT, '-') AS USERPOST".$fieldArrIdJournal);
        $this->from('t_assetdepreciation A', true);
        $this->join('t_journalrecap AS B', 'A.IDJOURNALRECAP = B.IDJOURNALRECAP', 'LEFT');
        $this->join('t_journaldetails AS C', 'B.IDJOURNALRECAP = C.IDJOURNALRECAP', 'LEFT');
        $this->where('A.IDASSET', $idAsset);
        $this->groupBy('A.IDASSETDEPRECIATION');
        $this->orderBy('A.DEPRECIATIONNUMBER');

        $rowData    =   $this->get()->getResultObject();
        if(is_null($rowData)) return false;
        return $rowData;
	}

    public function getDetailAsset($idAsset)
    {
        $accountNameLang    =   lang("CustomSystem.databaseField.m_account.ACCOUNTNAME");
        $this->select("A.IDJOURNALRECAPPURCHASE, B.ASSETTYPE, A.ASSETNAME, A.DESCRIPTION, C.ASSETDEPRECIATIONGROUPNAME, C.YEARSBENEFIT, 
                    DATE_FORMAT(A.PURCHASEDATE, '%d %b %Y') AS PURCHASEDATE, A.PURCHASEPRICE, A.RESIDUALVALUE, 0 AS DEPRECIATIONVALUE, 0 AS DEPRECIATIONPERMONTH,
                    D.TEMPLATENAME AS TEMPLATEJOURNALNAME,
                    CONCAT(
                        '[',
                        GROUP_CONCAT(
                            JSON_OBJECT(
                                'POSITIONDRCR',
                                E.DEFAULTDRCR,
                                'ACCOUNTCODE',
                                F.ACCOUNTCODE,
                                'ACCOUNTNAME',
                                F.".$accountNameLang."
                            )
                            ORDER BY E.DEFAULTDRCR DESC
                        ),
                        ']'
                    ) AS DEPRECIATIONACCOUNTJOURNAL");
        $this->from('t_asset AS A', true);
        $this->join('m_assettype AS B', 'A.IDASSETTYPE = B.IDASSETTYPE', 'LEFT');
        $this->join('m_assetdepreciationgroup AS C', 'A.IDASSETDEPRECIATIONGROUP = C.IDASSETDEPRECIATIONGROUP', 'LEFT');
        $this->join('t_journaltemplaterecap AS D', 'A.IDJOURNALTEMPLATERECAP = D.IDJOURNALTEMPLATERECAP', 'LEFT');
        $this->join('t_journaltemplatedetails AS E', 'A.IDJOURNALTEMPLATERECAP = E.IDJOURNALTEMPLATERECAP', 'LEFT');
        $this->join(accountCodeQueryString($accountNameLang).' AS F', 'E.IDACCOUNT = F.IDACCOUNT', 'LEFT');
        $this->where('A.IDASSET', $idAsset);
        $this->groupBy('A.IDASSET');
        $this->limit(1);

        $rowData    =   $this->get()->getRowArray();
        if(is_null($rowData)) return false;
        return $rowData;
	}
}
