<?php

namespace App\Controllers\Settings;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\MainOperation;
use App\Models\Settings\UserLevelMenuModel;

class UserLevelMenu extends ResourceController
{
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    use ResponseTrait;
    protected $userData, $currentDateTime;
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger) {
        parent::initController($request, $response, $logger);

        try {
            $this->userData         =   $request->userData;
            $this->currentDateTime  =   $request->currentDateTime;
        } catch (\Throwable $th) {
        }
    }

    public function index()
    {
        return $this->failForbidden('[E-AUTH-000] Forbidden Access');
    }

    public function getDataTable()
    {
        $userLevelMenuModel =   new UserLevelMenuModel();
        $idLevelUserAdmin   =   $this->request->getVar('idLevelUserAdmin');
        $idLevelUserAdmin   =   isset($idLevelUserAdmin) && $idLevelUserAdmin != '' ? hashidDecode($idLevelUserAdmin) : $idLevelUserAdmin;
        $searchKeyword      =   $this->request->getVar('searchKeyword');
        $dataUserLevelMenu  =	$userLevelMenuModel->getDataUserLevelMenu($idLevelUserAdmin, $searchKeyword);

        if($dataUserLevelMenu){
            $result =   encodeDatabaseObjectResultKey($dataUserLevelMenu, 'IDMENUADMIN');
            return $this->setResponseFormat('json')
                        ->respond([
                            "result"    =>  $result
                        ]);
        } else {
            return throwResponseNotFound('No data found based on the applied filter');
        }
    }

    public function saveDataUserLevelMenu()
    {
        helper(['form']);
        $rules      =   [
            'idLevelUserAdmin'  =>  ['label' => 'Id User Admin', 'rules' => 'required|alpha_numeric'],
            'arrIdMenuLevelUser'=>  ['label' => 'Data menu level user', 'rules' => 'required|is_array']
        ];

        $messages   =   [
            'idLevelUserAdmin'  =>  [
                'required'      =>  'Invalid data sent',
                'alpha_numeric' =>  'Invalid data sent'
            ]
        ];
        if(!$this->validate($rules, $messages)) return $this->fail($this->validator->getErrors());

        $userLevelMenuModel     =   new UserLevelMenuModel();
        $idLevelUserAdmin       =   $this->request->getVar('idLevelUserAdmin');
        $idLevelUserAdmin       =   hashidDecode($idLevelUserAdmin);
        $arrIdMenuLevelUser     =   $this->request->getVar('arrIdMenuLevelUser');

        try {
            foreach($arrIdMenuLevelUser as $idMenuAdmin=>$openPermission){
                $idMenuAdmin        =   hashidDecode($idMenuAdmin);
                $idMenuLevelAdmin   =   $userLevelMenuModel->getIdMenuLevelAdmin($idLevelUserAdmin, $idMenuAdmin);

                if($idMenuLevelAdmin){
                    if(!$openPermission) $userLevelMenuModel->delete($idMenuLevelAdmin);
                } else {
                    if($openPermission) $userLevelMenuModel->insert(['IDUSERADMINLEVEL'=>$idLevelUserAdmin, 'IDMENUADMIN'=>$idMenuAdmin]);
                }
            }
        } catch (\Throwable $th) {
            return throwResponseInternalServerError('Failed to save user level menu.'.$th->getMessage());
        }

        return throwResponseOK('Menu user level has been saved');
    }
}