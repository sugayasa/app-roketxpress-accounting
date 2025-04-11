<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use CodeIgniter\I18n\Time;
use App\Models\MainOperation;
use App\Models\TemplateJournalModel;

class TemplateJournal extends ResourceController
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
        helper(['form']);
        $rules      =   [
            'page'              =>  ['label' => 'Page', 'rules' => 'required|numeric']
        ];

        $messages   =   [
            'page'  => [
                'required'=> 'Invalid data sent [1]',
                'numeric' => 'Invalid data sent [2]'
            ]
        ];

        if(!$this->validate($rules, $messages)) return $this->fail($this->validator->getErrors());
        
        $templateJournalModel   =   new TemplateJournalModel();
        $page                   =   $this->request->getVar('page');
        $idAccountMain          =   $this->request->getVar('idAccountMain');
        $idAccountMain          =   isset($idAccountMain) && $idAccountMain != '' ? hashidDecode($idAccountMain) : $idAccountMain;
        $idAccountSub           =   $this->request->getVar('idAccountSub');
        $idAccountSub           =   isset($idAccountSub) && $idAccountSub != '' ? hashidDecode($idAccountSub) : $idAccountSub;
        $searchKeyword          =   $this->request->getVar('searchKeyword');
        $result                 =	$templateJournalModel->getDataTable($page, 20, $idAccountMain, $idAccountSub, $searchKeyword);

        if(count($result['data']) > 0){
            $result['data'] =   encodeDatabaseObjectResultKey($result['data'], 'IDJOURNALTEMPLATERECAP');
        }

        return $this->setResponseFormat('json')
                    ->respond([
                        "result"        =>  $result
                     ]);
    }

    public function insertData()
    {
        helper(['form']);
        $rules      =   [
            'templateName'              =>  ['label' => 'Template Name', 'rules' => 'required|alpha_numeric_punct'],
            'templateDescription'       =>  ['label' => 'Template Description', 'rules' => 'required|alpha_numeric_punct'],
            'arrAccountTemplateDetailDR'=>  ['label' => 'Account Details Debit Position', 'rules' => 'required|is_array'],
            'arrAccountTemplateDetailCR'=>  ['label' => 'Account Details Credit Position', 'rules' => 'required|is_array']
        ];

        if(!$this->validate($rules)) return $this->fail($this->validator->getErrors());

        $templateJournalModel       =   new TemplateJournalModel();
        $templateName               =   $this->request->getVar('templateName');
        $templateDescription        =   $this->request->getVar('templateDescription');
        $arrAccountTemplateDetailDR =   $this->request->getVar('arrAccountTemplateDetailDR');
        $arrAccountTemplateDetailCR =   $this->request->getVar('arrAccountTemplateDetailCR');

        $arrInsertData      =   [
            'TEMPLATENAME'  =>  $templateName,
            'DESCRIPTION'   =>  $templateDescription
        ];

        try {
            $templateJournalModel->db->transException(true)->transStart();
            $mainOperation  =   new MainOperation();
            $procInsertData =   $mainOperation->insertDataTable('t_journaltemplaterecap', $arrInsertData);

            if(!$procInsertData['status']) return switchMySQLErrorCode($procInsertData['errCode']);
            $idJournalTemplateRecap =   $procInsertData['insertID'];

            foreach($arrAccountTemplateDetailDR as $keyAccountTemplateDetailDR){
                $idAccount              =   hashidDecode($keyAccountTemplateDetailDR);
                $arrInsertDataDetail    =   [
                    'IDJOURNALTEMPLATERECAP'    =>  $idJournalTemplateRecap,
                    'IDACCOUNT'                 =>  $idAccount,
                    'DEFAULTDRCR'               =>  'DR'
                ];
                $mainOperation->insertDataTable('t_journaltemplatedetails', $arrInsertDataDetail);
            }

            foreach($arrAccountTemplateDetailCR as $keyAccountTemplateDetailCR){
                $idAccount              =   hashidDecode($keyAccountTemplateDetailCR);
                $arrInsertDataDetail    =   [
                    'IDJOURNALTEMPLATERECAP'    =>  $idJournalTemplateRecap,
                    'IDACCOUNT'                 =>  $idAccount,
                    'DEFAULTDRCR'               =>  'CR'
                ];
                $mainOperation->insertDataTable('t_journaltemplatedetails', $arrInsertDataDetail);
            }
    		$templateJournalModel->db->transComplete();

            return throwResponseOK('New template journal data has been added');
        } catch (\Throwable $th) {
            return throwResponseInternalServerError('Internal server error - failed to add data');
        }
    }

    public function getDetailTemplateJournal()
    {
        helper(['form']);
        $rules      =   [
            'idJournalTemplateRecap'    => ['label' => 'Id Template Journal Recap', 'rules' => 'required|alpha_numeric']
        ];

        $messages   =   [
            'idJournalTemplateRecap'    => [
                'required'      => 'Invalid data sent',
                'alpha_numeric' => 'Invalid data sent'
            ]
        ];

        if(!$this->validate($rules, $messages)) return $this->fail($this->validator->getErrors());

        $templateJournalModel   =   new TemplateJournalModel();
        $idJournalTemplateRecap =   $this->request->getVar('idJournalTemplateRecap');
        $idJournalTemplateRecap =   hashidDecode($idJournalTemplateRecap);
        $detailRecap            =   $templateJournalModel->find($idJournalTemplateRecap);

        if(!$detailRecap) return throwResponseNotFound("No detail found for template journal selected");
        $listDetailTemplate     =   $templateJournalModel->getListDetailTemplate($idJournalTemplateRecap);

        $detailRecap['IDJOURNALTEMPLATERECAP']  =   hashidEncode($detailRecap['IDJOURNALTEMPLATERECAP']);
        $listDetailTemplate                     =   encodeDatabaseObjectResultKey($listDetailTemplate, ['IDJOURNALTEMPLATEDETAIL', 'IDACCOUNT']);

        return $this->setResponseFormat('json')
                    ->respond([
                        "detailRecap"           =>  $detailRecap,
                        "listDetailTemplate"    =>  $listDetailTemplate
                     ]);
    }

    public function updateData()
    {
        helper(['form']);
        $rules      =   [
            'idJournalTemplateRecap'    =>  ['label' => 'Id Journal Template Recap', 'rules' => 'required|alpha_numeric'],
            'templateName'              =>  ['label' => 'Template Name', 'rules' => 'required|alpha_numeric_punct'],
            'templateDescription'       =>  ['label' => 'Template Description', 'rules' => 'required|alpha_numeric_punct'],
            'arrAccountTemplateDetailDR'=>  ['label' => 'Account Details Debit Position', 'rules' => 'required|is_array'],
            'arrAccountTemplateDetailCR'=>  ['label' => 'Account Details Credit Position', 'rules' => 'required|is_array']
        ];

        $messages   =   [
            'idJournalTemplateRecap'        => [
                'required'      => 'Invalid data sent',
                'alpha_numeric' => 'Invalid data sent'
            ]
        ];
        if(!$this->validate($rules, $messages)) return $this->fail($this->validator->getErrors());

        $templateJournalModel       =   new TemplateJournalModel();
        $idJournalTemplateRecap     =   $this->request->getVar('idJournalTemplateRecap');
        $idJournalTemplateRecap     =   hashidDecode($idJournalTemplateRecap);
        $templateName               =   $this->request->getVar('templateName');
        $templateDescription        =   $this->request->getVar('templateDescription');
        $arrAccountTemplateDetailDR =   $this->request->getVar('arrAccountTemplateDetailDR');
        $arrAccountTemplateDetailCR =   $this->request->getVar('arrAccountTemplateDetailCR');
        $arrAccountTemplateDetail   =   [];

        foreach($arrAccountTemplateDetailDR as $idAccount){
            $arrAccountTemplateDetail[] =   [$idAccount, 'DR'];
        }

        foreach($arrAccountTemplateDetailCR as $idAccount){
            $arrAccountTemplateDetail[] =   [$idAccount, 'CR'];
        }

        $arrUpdateData      =   [
            'TEMPLATENAME'  =>  $templateName,
            'DESCRIPTION'   =>  $templateDescription
        ];

        try {
            $templateJournalModel->db->transException(true)->transStart();
            $templateJournalModel->update($idJournalTemplateRecap, $arrUpdateData);
            $listDetailTemplate     =   $templateJournalModel->getListDetailTemplate($idJournalTemplateRecap);
            $arrIdAccountDetailDB   =   [];

            foreach($listDetailTemplate as $keyDetailTemplate){
                $arrIdAccountDetailDB[] =   $keyDetailTemplate->IDACCOUNT;
            }

            $mainOperation  =   new MainOperation();
            foreach($arrAccountTemplateDetail as $keyAccountTemplateDetail){
                $idAccount              =   hashidDecode($keyAccountTemplateDetail[0]);
                $defaultDRCR            =   $keyAccountTemplateDetail[1];
                $arrInsertUpdateDetail  =   [
                    'IDJOURNALTEMPLATERECAP'=>  $idJournalTemplateRecap,
                    'IDACCOUNT'             =>  $idAccount,
                    'DEFAULTDRCR'           =>  $defaultDRCR
                ];

                if(!in_array($idAccount, $arrIdAccountDetailDB)){
                    $mainOperation->insertDataTable('t_journaltemplatedetails', $arrInsertUpdateDetail);
                } else {
                    $mainOperation->updateDataTable('t_journaltemplatedetails', $arrInsertUpdateDetail, ['IDJOURNALTEMPLATERECAP'=>$idJournalTemplateRecap, 'IDACCOUNT' => $idAccount]);
                    unset($arrIdAccountDetailDB[array_search($idAccount, $arrIdAccountDetailDB)]);
                }
            }

            if(count($arrIdAccountDetailDB) > 0){
                foreach($arrIdAccountDetailDB as $idAccount){
                    $mainOperation->deleteDataTable('t_journaltemplatedetails', ['IDJOURNALTEMPLATERECAP'=>$idJournalTemplateRecap, 'IDACCOUNT'=>$idAccount]);
                }
            }
    		$templateJournalModel->db->transComplete();

            return throwResponseOK('Template journal data has been updated');
        } catch (\Throwable $th) {
            return throwResponseInternalServerError('Internal server error - failed to update data');
        }
    }

    public function deleteTemplateJournal()
    {
        helper(['form']);
        $rules      =   [
            'idJournalTemplateRecap'    =>  ['label' => 'Id Journal Template Recap', 'rules' => 'required|alpha_numeric']
        ];

        $messages   =   [
            'idJournalTemplateRecap' => [
                'required'      => 'Invalid data sent',
                'alpha_numeric' => 'Invalid data sent'
            ]
        ];
        if(!$this->validate($rules, $messages)) return $this->fail($this->validator->getErrors());

        $templateJournalModel   =   new TemplateJournalModel();
        $idJournalTemplateRecap =   $this->request->getVar('idJournalTemplateRecap');
        $idJournalTemplateRecap =   hashidDecode($idJournalTemplateRecap);

        try{
            $templateJournalModel->delete($idJournalTemplateRecap);
        } catch (\Throwable $th) {
            return throwResponseNotAcceptable('Internal database script error');
        }

        return throwResponseOK('Template journal has been deleted');
    }
}