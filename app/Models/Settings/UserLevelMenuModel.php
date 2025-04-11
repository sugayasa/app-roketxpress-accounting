<?php

namespace App\Models\Settings;
use CodeIgniter\Model;

class UserLevelMenuModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'm_menuleveladmin';
    protected $primaryKey       = 'IDMENULEVELADMIN';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['IDUSERADMINLEVEL', 'IDMENUADMIN'];

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
    
    public function getDataUserLevelMenu($idLevelUserAdmin, $searchKeyword)
    {	
        $this->select("A.GROUPNAME, A.MENUNAME, A.IDMENUADMIN, IFNULL(B.IDMENUADMIN, 0) AS OPEN");
        $this->from('m_menuadmin AS A', true);
        $this->join('m_menuleveladmin AS B', 'A.IDMENUADMIN = B.IDMENUADMIN AND B.IDUSERADMINLEVEL = '.$idLevelUserAdmin, 'LEFT');
        $this->where('A.SUPERADMIN', 0);
        if(isset($searchKeyword) && !is_null($searchKeyword)){
            $this->groupStart();
            $this->like('A.GROUPNAME', $searchKeyword, 'both')
            ->orLike('A.MENUNAME', $searchKeyword, 'both');
            $this->groupEnd();
        }
        $this->orderBy('A.ORDERGROUP, A.ORDERMENU');

        $result =   $this->get()->getResultObject();

        if(is_null($result)) return false;
        return $result;
	}

    public function getIdMenuLevelAdmin($idLevelUserAdmin, $idMenuAdmin)
    {	
        $this->select("IDMENULEVELADMIN");
        $this->from('m_menuleveladmin', true);
        $this->where('IDUSERADMINLEVEL', $idLevelUserAdmin);
        $this->where('IDMENUADMIN', $idMenuAdmin);
        $this->limit(1);
        $rowData    =   $this->get()->getRowArray();

        if(is_null($rowData)) return false;
        if(!is_null($rowData)) return $rowData['IDMENULEVELADMIN'];
	}
}