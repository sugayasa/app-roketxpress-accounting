<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\MainOperation;

class NotificationUserAdminModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 't_notificationuseradmin';
    protected $primaryKey       = 'IDNOTIFICATIONUSERADMIN';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['IDUSERADMINNOTIFICATIONTYPE', 'IDUSERADMIN', 'IDPRIMARY', 'TITLE', 'MESSAGE', 'DATETIMECREATE', 'DATETIMEREAD'];

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
    
    public function getDataUnreadNotification($idUserAdmin)
    {	
        $this->select("A.IDNOTIFICATIONUSERADMIN, A.IDUSERADMINNOTIFICATIONTYPE, B.NOTIFICATIONTYPE, B.ICON, B.COLOR, A.MESSAGE, A.IDPRIMARY,
                    DATE_FORMAT(A.DATETIMECREATE, '%d %b %Y %H:%i') AS DATETIMECREATE");
        $this->from('t_notificationuseradmin AS A', true);
        $this->join('m_useradminnotificationtype AS B', 'A.IDUSERADMINNOTIFICATIONTYPE = B.IDUSERADMINNOTIFICATIONTYPE', 'LEFT');
        $this->where('A.IDUSERADMIN', $idUserAdmin);
        $this->where('A.DATETIMEREAD', '0000-00-00 00:00:00');
        $this->orderBy('A.DATETIMECREATE DESC');

        $result =   $this->get()->getResultObject();

        if(is_null($result)) return false;
        return $result;
	}
    
    public function getDataNotification($page, $dataPerPage, $status, $idUserAdminNotificationType, $keywordSearch, $idUserAdmin)
    {	
        $mainOperation          =   new MainOperation();
		$con_idNotificationType	=	isset($idUserAdminNotificationType) && $idUserAdminNotificationType != "" ? "A.IDUSERADMINNOTIFICATIONTYPE  = ".$idUserAdminNotificationType : "1=1";
		$con_keywordSearch	    =	isset($keywordSearch) && $keywordSearch != "" ? "(A.TITLE LIKE '%".$keywordSearch."%' OR A.MESSAGE LIKE '%".$keywordSearch."%')" : "1=1";
		$con_status             =	isset($status) && $status != 1 ? "A.DATETIMEREAD != '0000-00-00 00:00:00'" : "A.DATETIMEREAD = '0000-00-00 00:00:00'";
		$baseQuery			    =	"SELECT A.IDNOTIFICATIONUSERADMIN, A.IDUSERADMINNOTIFICATIONTYPE , B.NOTIFICATIONTYPE, B.ICON, A.TITLE, A.MESSAGE, A.IDPRIMARY,
                                            DATE_FORMAT(A.DATETIMECREATE, '%d %b %Y %H:%i') AS DATETIMECREATE
                                    FROM t_notificationuseradmin A
                                    LEFT JOIN m_useradminnotificationtype B ON A.IDUSERADMINNOTIFICATIONTYPE  = B.IDUSERADMINNOTIFICATIONTYPE 
                                    WHERE ".$con_idNotificationType." AND ".$con_keywordSearch." AND A.IDUSERADMIN = ".$idUserAdmin." AND ".$con_status."
                                    ORDER BY A.DATETIMECREATE DESC";
        $result                 =   $mainOperation->execQueryWithLimit($baseQuery, $page, $dataPerPage);

        if(is_null($result)) return $mainOperation->generateEmptyResult();
        return $mainOperation->generateResultPagination($result, $baseQuery, 'IDNOTIFICATIONUSERADMIN', $page, $dataPerPage);
	}
}
