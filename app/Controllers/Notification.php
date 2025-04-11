<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\NotificationUserAdminModel;

class Notification extends ResourceController
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

    public function getDataNotification()
    {
        helper(['form']);
        $rules      =   [
            'page'  => ['label' => 'Page', 'rules' => 'required|numeric'],
            'status'=> ['label' => 'Status', 'rules' => 'required|numeric']
        ];

        $messages   =   [
            'page'  => [
                'required'=> 'The data sent is invalid [1]',
                'numeric' => 'The data sent is invalid [2]'
            ],
            'status'  => [
                'required'=> 'The data sent is invalid [3]',
                'numeric' => 'The data sent is invalid [4]'
            ]
        ];

        if(!$this->validate($rules, $messages)) return $this->fail($this->validator->getErrors());
        
        $notificationUserAdminModel =   new NotificationUserAdminModel();
        $page                       =   $this->request->getVar('page');
        $status                     =   $this->request->getVar('status');
        $idNotificationUserAdminType=   $this->request->getVar('idNotificationUserAdminType');
        $idNotificationUserAdminType=   isset($idNotificationUserAdminType) && $idNotificationUserAdminType != "" ? hashidDecode($idNotificationUserAdminType) : "";
        $keywordSearch              =   $this->request->getVar('keywordSearch');
        $idUserAdmin                =   $this->userData->idUserAdmin;
        $result                     =	$notificationUserAdminModel->getDataNotification($page, 25, $status, $idNotificationUserAdminType, $keywordSearch, $idUserAdmin);

        return $this->setResponseFormat('json')
                    ->respond([
                        "result"    =>  $result
                     ]);
    }
}