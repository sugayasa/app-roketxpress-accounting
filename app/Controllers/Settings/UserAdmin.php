<?php

namespace App\Controllers\Settings;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\MainOperation;
use App\Models\Settings\UserAdminModel;

class UserAdmin extends ResourceController
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
        $userAdminModel     =   new UserAdminModel();
        $idLevelUserAdmin   =   $this->request->getVar('idLevelUserAdmin');
        $idLevelUserAdmin   =   isset($idLevelUserAdmin) && $idLevelUserAdmin != '' ? hashidDecode($idLevelUserAdmin) : $idLevelUserAdmin;
        $searchKeyword      =   $this->request->getVar('searchKeyword');
        $dataUserAdmin      =	$userAdminModel->getDataUserAdmin($idLevelUserAdmin, $searchKeyword);

        if($dataUserAdmin){
            $result =   encodeDatabaseObjectResultKey($dataUserAdmin, 'IDUSERADMIN');
            $result =   encodeDatabaseObjectResultKey($dataUserAdmin, 'IDUSERADMINLEVEL');
            return $this->setResponseFormat('json')
                        ->respond([
                            "result"    =>  $result
                        ]);
        } else {
            return throwResponseNotFound('No data found based on the applied filter');
        }
    }

    public function insertDataUserAdmin()
    {
        helper(['form']);
        $idUserAdmin    =   $this->userData->idUserAdmin;
        $rules          =   [
            'idUserAdminLevel'  =>  ['label' => 'User Admin Level', 'rules' => 'required|alpha_numeric'],
            'nameUser'          =>  ['label' => 'Name', 'rules' => 'required|alpha_numeric_space'],
            'userEmail'         =>  ['label' => 'Email', 'rules' => 'required|valid_email|is_unique[m_useradmin.EMAIL, IDUSERADMIN, '.$idUserAdmin.']'],
            'username'          =>  ['label' => 'username', 'rules' => 'required|alpha_numeric|min_length[5]'],
            'newPassword'       =>  ['label' => 'New Password', 'rules' => 'required|alpha_numeric'],
            'repeatPassword'    =>  ['label' => 'Repeat Password', 'rules' => 'required|alpha_numeric']
        ];

        $messages   =   [
            'email' => ['is_unique' => 'This email address is already registered, please enter another email address'],
        ];
        if(!$this->validate($rules, $messages)) return $this->fail($this->validator->getErrors());

        $idUserAdminLevel   =   $this->request->getVar('idUserAdminLevel');
        $idUserAdminLevel   =   hashidDecode($idUserAdminLevel);
        $nameUser           =   $this->request->getVar('nameUser');
        $userEmail          =   $this->request->getVar('userEmail');
        $username           =   $this->request->getVar('username');
        $newPassword        =   $this->request->getVar('newPassword');
        $repeatPassword     =   $this->request->getVar('repeatPassword');

        if($newPassword == "") return throwResponseNotAcceptable("Please enter a new password");
        if($repeatPassword == "") return throwResponseNotAcceptable("Please enter a new password repeat");
        if($newPassword != $repeatPassword) return throwResponseNotAcceptable("The repetition of the password you entered is not match");

        $arrInsertData      =   [
            'IDUSERADMINLEVEL'  =>  $idUserAdminLevel,
            'NAME'              =>  $nameUser,
            'EMAIL'             =>  $userEmail,
            'USERNAME'          =>  $username,
            'PASSWORD'          =>  password_hash($newPassword, PASSWORD_DEFAULT),
            'STATUS'            =>  1
        ];

        $mainOperation  =   new MainOperation();
        $procInsertData =   $mainOperation->insertDataTable('m_useradmin', $arrInsertData);

        if(!$procInsertData['status']) return switchMySQLErrorCode($procInsertData['errCode']);
        return throwResponseOK('New user admin data has been added');
    }

    public function updateDataUserAdmin()
    {
        helper(['form']);
        $idUserAdmin    =   $this->userData->idUserAdmin;
        $rules          =   [
            'idUserAdmin'       =>  ['label' => 'ID User Admin', 'rules' => 'required|alpha_numeric'],
            'idUserAdminLevel'  =>  ['label' => 'User Admin Level', 'rules' => 'required|alpha_numeric'],
            'nameUser'          =>  ['label' => 'Name', 'rules' => 'required|alpha_numeric_space'],
            'userEmail'         =>  ['label' => 'Email', 'rules' => 'required|valid_email|is_unique[m_useradmin.EMAIL, IDUSERADMIN, '.$idUserAdmin.']'],
            'username'          =>  ['label' => 'username', 'rules' => 'required|alpha_numeric|min_length[5]']
        ];

        $messages   =   [
            'idUserAdmin'   => [
                'required'      => 'Invalid data sent',
                'alpha_numeric' => 'Invalid data sent'
            ],
            'email'         => ['is_unique' => 'This email address is already registered, please enter another email address']
        ];
        if(!$this->validate($rules, $messages)) return $this->fail($this->validator->getErrors());

        $idUserAdmin        =   $this->request->getVar('idUserAdmin');
        $idUserAdmin        =   hashidDecode($idUserAdmin);
        $idUserAdminLevel   =   $this->request->getVar('idUserAdminLevel');
        $idUserAdminLevel   =   hashidDecode($idUserAdminLevel);
        $nameUser           =   $this->request->getVar('nameUser');
        $userEmail          =   $this->request->getVar('userEmail');
        $username           =   $this->request->getVar('username');
        $oldPassword        =   $this->request->getVar('oldPassword');
        $newPassword        =   $this->request->getVar('newPassword');
        $repeatPassword     =   $this->request->getVar('repeatPassword');

        $arrUpdateUserAdmin =   [
            'IDUSERADMINLEVEL'  =>  $idUserAdminLevel,
            'NAME'              =>  $nameUser,
            'EMAIL'             =>  $userEmail,
            'USERNAME'          =>  $username
        ];

        if($oldPassword != "" || $newPassword != "" || $repeatPassword != ""){
			if($oldPassword == "") return throwResponseNotAcceptable("Please enter your old password (your current password)");
            if($newPassword == "") return throwResponseNotAcceptable("Please enter a new password");
            if($repeatPassword == "") return throwResponseNotAcceptable("Please enter a new password repeat");
            if($newPassword != $repeatPassword) return throwResponseNotAcceptable("The repetition of the password you entered is not match");

            $userAdminModel =   new UserAdminModel();
            $dataUserAdmin  =   $userAdminModel->where("IDUSERADMIN", $idUserAdmin)->first();
            if(!$dataUserAdmin) return $this->failNotFound('User admin data was not found, please try again later');
            $passwordVerify =   password_verify($oldPassword, $dataUserAdmin['PASSWORD']);
            if(!$passwordVerify) return $this->fail('The old password you entered is incorrect');
			
			$arrUpdateUserAdmin['PASSWORD'] =   password_hash($newPassword, PASSWORD_DEFAULT);
        }
        $mainOperation  =   new MainOperation();
        $procUpdateData =   $mainOperation->updateDataTable('m_useradmin', $arrUpdateUserAdmin, ['IDUSERADMIN' => $idUserAdmin]);

        if(!$procUpdateData['status']) return switchMySQLErrorCode($procUpdateData['errCode']);
        return throwResponseOK('User admin data has been updated');
    }

    public function updateStatusUserAdmin()
    {
        helper(['form']);
        $rules      =   [
            'idUserAdmin'   =>  ['label' => 'Id User Admin', 'rules' => 'required|alpha_numeric'],
            'status'        =>  ['label' => 'status', 'rules' => 'required|in_list[1, -1]']
        ];

        $messages   =   [
            'idUserAdmin' => [
                'required'      => 'Invalid data sent',
                'alpha_numeric' => 'Invalid data sent'
            ]
        ];
        if(!$this->validate($rules, $messages)) return $this->fail($this->validator->getErrors());

        $userAdminModel =   new UserAdminModel();
        $idUserAdmin    =   $this->request->getVar('idUserAdmin');
        $idUserAdmin    =   hashidDecode($idUserAdmin);
        $status         =   $this->request->getVar('status') * -1;
        $statusStr      =   $status == 1 ? 'activevated' : 'deactivevated';

        try{
            $userAdminModel->update($idUserAdmin, ['STATUS' => $status]);
        } catch (\Throwable $th) {
            return throwResponseNotAcceptable('Internal database script error');
        }

        return throwResponseOK('User admin data has been '.$statusStr);
    }
}