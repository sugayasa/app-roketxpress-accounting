<?php

namespace App\Models\Settings;
use CodeIgniter\Model;

class UserAdminModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'm_useradmin';
    protected $primaryKey       = 'IDUSERADMIN';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['IDUSERADMINLEVEL', 'NAME', 'EMAIL', 'USERNAME', 'PASSWORD', 'STATUS'];

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
    
    public function getDataUserAdmin($idLevelUserAdmin, $searchKeyword)
    {	
        $this->select("A.IDUSERADMIN, A.IDUSERADMINLEVEL, B.LEVELNAME, A.NAME, A.EMAIL, A.USERNAME, A.STATUS");
        $this->from('m_useradmin AS A', true);
        $this->join('m_useradminlevel AS B', 'A.IDUSERADMINLEVEL = B.IDUSERADMINLEVEL', 'LEFT');
        if(isset($idLevelUserAdmin) && $idLevelUserAdmin != 0 && $idLevelUserAdmin != '') $this->where('A.IDUSERADMINLEVEL', $idLevelUserAdmin);
        if(isset($searchKeyword) && !is_null($searchKeyword)){
            $this->groupStart();
            $this->like('B.LEVELNAME', $searchKeyword, 'both')
            ->orLike('A.NAME', $searchKeyword, 'both')
            ->orLike('A.EMAIL', $searchKeyword, 'both')
            ->orLike('A.USERNAME', $searchKeyword, 'both');
            $this->groupEnd();
        }
        $this->orderBy('B.LEVELNAME, A.NAME');

        $result =   $this->get()->getResultObject();

        if(is_null($result)) return false;
        return $result;
	}
}