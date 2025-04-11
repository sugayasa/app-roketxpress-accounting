<?php

namespace App\Models;

use CodeIgniter\Model;
use PHPUnit\Framework\Constraint\IsNull;

class AccessModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'm_useradmin';
    protected $primaryKey       = 'IDUSERADMIN';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['IDUSERADMINLEVEL', 'NAME', 'EMAIL', 'USERNAME', 'PASSWORD', 'HARDWAREID', 'DATETIMELOGIN', 'DATETIMEEXPIRED', 'STATUS'];

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

    public function checkHardwareIDUserAdmin($idUserAdmin, $hardwareID)
    {
        $this->select('IDUSERADMIN');
        $this->from('m_useradmin', true);
        $this->where('IDUSERADMIN', $idUserAdmin);
        $this->where('HARDWAREID', $hardwareID);

        if(is_null($this->get()->getRowArray())) return false;
        return true;
    }

    public function getUserAdminDetail($idUserAdmin)
    {
        $this->select('A.HARDWAREID, A.IDUSERADMINLEVEL, A.NAME, A.EMAIL, B.LEVELNAME');
        $this->from('m_useradmin AS A', true);
        $this->join('m_useradminlevel AS B', 'A.IDUSERADMINLEVEL = B.IDUSERADMINLEVEL', 'LEFT');
        $this->where('A.IDUSERADMIN', $idUserAdmin);

        return $this->get()->getRowArray();
    }

    public function getUserAdminMenu($idUserAdminLevel)
    {
        $this->select('B.GROUPNAME, B.MENUNAME, B.MENUALIAS, B.URL, B.ICON');
        $this->from('m_menuleveladmin AS A', true);
        $this->join('m_menuadmin AS B', 'A.IDMENUADMIN = B.IDMENUADMIN', 'LEFT');
        $this->where('A.IDUSERADMINLEVEL', $idUserAdminLevel);
        $this->orderBy('B.ORDERGROUP, B.ORDERMENU');

        return $this->get()->getResultObject();
    }

    public function getUserAdminGroupMenu($idUserAdminLevel)
    {
        $this->select('B.GROUPNAME');
        $this->from('m_menuleveladmin AS A', true);
        $this->join('m_menuadmin AS B', 'A.IDMENUADMIN = B.IDMENUADMIN', 'LEFT');
        $this->where('A.IDUSERADMINLEVEL', $idUserAdminLevel);
        $this->groupBy('B.GROUPNAME');
        $this->having('COUNT(B.IDMENUADMIN) > ', 1);
        $this->orderBy('B.ORDERGROUP');

        return $this->get()->getResultObject();
    }

    //temporary unused
    public function getListNotificationTypeUserLevelAdmin($idUserAdminLevel)
    {
        $this->select('NOTIFSCHEDULE, NOTIFFINANCE');
        $this->from('m_useradminlevel', true);
        $this->where('IDUSERADMINLEVEL', $idUserAdminLevel);
        $result =   $this->get()->getRowArray();

        if(is_null($result)){
            return array(
                "NOTIFSCHEDULE" =>	0,
                "NOTIFFINANCE"  =>	0
            );
        }

        return $result;
    }

    public function getDataNotificationUserAdminType()
    {
        $this->select('IDUSERADMINNOTIFICATIONTYPE AS ID, NOTIFICATIONTYPE AS VALUE');
        $this->from('m_useradminnotificationtype', true);
        $this->orderBy('NOTIFICATIONTYPE');

        return $this->get()->getResultObject();
    }

    public function getDataUserAdminLevel()
    {
        $this->select('IDUSERADMINLEVEL AS ID, LEVELNAME AS VALUE');
        $this->from('m_useradminlevel', true);
        $this->orderBy('LEVELNAME');

        return $this->get()->getResultObject();
    }

    public function getDataUserAdminLevelMenu()
    {
        $this->select('A.IDUSERADMINLEVEL AS ID, C.MENUNAME AS VALUE');
        $this->from('m_menuleveladmin AS A', true);
        $this->join('m_useradminlevel AS B', 'A.IDUSERADMINLEVEL = B.IDUSERADMINLEVEL', 'LEFT');
        $this->join('m_menuadmin AS C', 'A.IDMENUADMIN = C.IDMENUADMIN', 'LEFT');
        $this->orderBy('A.IDUSERADMINLEVEL, C.ORDERGROUP, C.ORDERMENU');

        return $this->get()->getResultObject();
    }

    public function getDataAccountGeneral()
    {
        $this->select("IDACCOUNT AS ID, CONCAT(ACCOUNTCODE, ' ', ".lang("CustomSystem.databaseField.m_account.ACCOUNTNAME").") AS VALUE");
        $this->from("m_account", true);
        $this->where("LEVEL", 1);
        $this->orderBy("ORDERNUMBER");

        return $this->get()->getResultObject();
    }

    public function getDataAccountMain()
    {
        $accountNameLang    =   lang("CustomSystem.databaseField.m_account.ACCOUNTNAME");
        $this->select("A.IDACCOUNT AS ID, A.IDACCOUNTPARENT AS PARENTVALUE, B.IDACCOUNT AS IDGROUP, CONCAT(B.ACCOUNTCODE, '-', A.ACCOUNTCODE, ' ', A.".$accountNameLang.") AS VALUE,
                        CONCAT(B.ACCOUNTCODE, ' ', B.".$accountNameLang.") AS VALUEGROUP, A.DEFAULTDRCR");
        $this->from("m_account AS A", true);
        $this->join("(SELECT IDACCOUNT, ACCOUNTCODE, ".$accountNameLang.", ORDERNUMBER  FROM m_account WHERE LEVEL = 1) AS B", "A.IDACCOUNTPARENT = B.IDACCOUNT", "LEFT");
        $this->where("LEVEL", 2);
        $this->orderBy("B.ORDERNUMBER, A.ORDERNUMBER");

        return $this->get()->getResultObject();
    }

    public function getDataAccountSub()
    {
        $accountNameLang    =   lang("CustomSystem.databaseField.m_account.ACCOUNTNAME");
        $this->select("A.IDACCOUNT AS ID, A.IDACCOUNTPARENT AS PARENTVALUE, B.IDACCOUNTPARENT AS PARENTVALUE2, B.IDACCOUNT AS IDGROUP,
                        CONCAT(C.ACCOUNTCODE, '-', B.ACCOUNTCODE, '-', A.ACCOUNTCODE, ' ', A.".$accountNameLang.") AS VALUE,
                        CONCAT(C.ACCOUNTCODE, '-', B.ACCOUNTCODE, ' ', B.".$accountNameLang.") AS VALUEGROUP, A.DEFAULTDRCR");
        $this->from("m_account AS A", true);
        $this->join("(SELECT IDACCOUNT, IDACCOUNTPARENT, ACCOUNTCODE, ".$accountNameLang.", ORDERNUMBER  FROM m_account WHERE LEVEL = 2) AS B", "A.IDACCOUNTPARENT = B.IDACCOUNT", "LEFT");
        $this->join("(SELECT IDACCOUNT, ACCOUNTCODE, ".$accountNameLang.", ORDERNUMBER  FROM m_account WHERE LEVEL = 1) AS C", "B.IDACCOUNTPARENT = C.IDACCOUNT", "LEFT");
        $this->where("LEVEL", 3);
        $this->orderBy("C.ORDERNUMBER, B.ORDERNUMBER, A.ORDERNUMBER");

        return $this->get()->getResultObject();
    }

    public function getDataAssetType()
    {
        $this->select("IDASSETTYPE AS ID, ASSETTYPE AS VALUE");
        $this->from("m_assettype", true);
        $this->orderBy("ASSETTYPE");

        return $this->get()->getResultObject();
    }

    public function getDataDepreciationGroup()
    {
        $this->select("IDASSETDEPRECIATIONGROUP AS ID, CONCAT(ASSETDEPRECIATIONGROUPNAME, ' - ', YEARSBENEFIT, ' Tahun') AS VALUE");
        $this->from("m_assetdepreciationgroup", true);
        $this->orderBy("ASSETDEPRECIATIONGROUPNAME");

        return $this->get()->getResultObject();
    }

    public function getDataSettingTemplateJournal($keyword)
    {
        $this->select("A.IDJOURNALTEMPLATERECAP AS ID, B.TEMPLATENAME AS VALUE");
        $this->from("t_journaltemplatesetting AS A", true);
        $this->join('t_journaltemplaterecap AS B', 'A.IDJOURNALTEMPLATERECAP = B.IDJOURNALTEMPLATERECAP', 'LEFT');
        $this->where('A.TEMPLATESETTINGNAME', $keyword);
        $this->orderBy("B.TEMPLATENAME");

        return $this->get()->getResultObject();
    }
}
