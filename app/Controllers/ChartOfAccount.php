<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use CodeIgniter\I18n\Time;
use App\Models\MainOperation;
use App\Models\ChartOfAccountModel;

class ChartOfAccount extends ResourceController
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

    public function getDataAccount()
    {
        $chartOfAccountModel=   new ChartOfAccountModel();
        $idAccountGeneral   =   $this->request->getVar('idAccountGeneral');
        $idAccountGeneral   =   isset($idAccountGeneral) && $idAccountGeneral != '' ? hashidDecode($idAccountGeneral) : $idAccountGeneral;
        $idAccountMain      =   $this->request->getVar('idAccountMain');
        $idAccountMain      =   isset($idAccountMain) && $idAccountMain != '' ? hashidDecode($idAccountMain) : $idAccountMain;
        $searchKeyword      =   $this->request->getVar('searchKeyword');
        $dataAccountSub     =	$chartOfAccountModel->getDataAccountSub($idAccountGeneral, $idAccountMain, $searchKeyword);
        $result             =   $arrIdAccountGeneral    =   $arrIdAccountMain   =   [];

        if(count($dataAccountSub) > 0){
            foreach($dataAccountSub as $keyAccountSub){
                $arrIdAccountMain[]     =   $keyAccountSub->IDACCOUNTMAIN;
                $arrIdAccountGeneral[]  =   $keyAccountSub->IDACCOUNTGENERAL;
            }
        }

        $dataAccountMain    =	$chartOfAccountModel->getDataAccountMain($idAccountGeneral, $idAccountMain, $searchKeyword, array_unique($arrIdAccountMain), array_unique($arrIdAccountGeneral));

        if(count($dataAccountMain) > 0){
            foreach($dataAccountMain as $keyAccountMain){
                $arrIdAccountGeneral[]  =   $keyAccountMain->IDACCOUNTGENERAL;
            }
        }

        $dataAccountGeneral =	$chartOfAccountModel->getDataAccountGeneral($idAccountGeneral, $searchKeyword, array_unique($arrIdAccountGeneral));

        if(!$dataAccountGeneral) return throwResponseNotFound(lang("CustomSystem.responseMessage.noDataFound"));

        foreach($dataAccountGeneral as $keyAccountGeneral){
            $idAccountGeneral   =   $keyAccountGeneral->IDACCOUNTGENERAL;
            $result[]           =   [
                'IDACCOUNT'         =>  hashidEncode($idAccountGeneral),
                'IDACCOUNTPARENT'   =>  hashidEncode(0),
                'ACCOUNTCODE'       =>  $keyAccountGeneral->ACCOUNTCODE,
                'ACCOUNTNAME'       =>  $keyAccountGeneral->ACCOUNTNAME,
                'DEFAULTDRCR'       =>  $keyAccountGeneral->DEFAULTDRCR,
                'LEVEL'             =>  1
            ];

            if($dataAccountMain){
                foreach($dataAccountMain as $keyAccountMain){
                    $idAccountMain          =   $keyAccountMain->IDACCOUNTMAIN;
                    $idAccountMain_General  =   $keyAccountMain->IDACCOUNTGENERAL;

                    if($idAccountGeneral == $idAccountMain_General){
                        $result[]           =   [
                            'IDACCOUNT'         =>  hashidEncode($idAccountMain),
                            'IDACCOUNTPARENT'   =>  hashidEncode($idAccountGeneral),
                            'ACCOUNTCODE'       =>  $keyAccountMain->ACCOUNTCODE,
                            'ACCOUNTNAME'       =>  $keyAccountMain->ACCOUNTNAME,
                            'DEFAULTDRCR'       =>  $keyAccountMain->DEFAULTDRCR,
                            'LEVEL'             =>  2
                        ];
                    }

                    if($dataAccountSub){
                        foreach($dataAccountSub as $keyAccountSub){
                            $idAccountSub           =   $keyAccountSub->IDACCOUNTSUB;
                            $idAccountSub_Main      =   $keyAccountSub->IDACCOUNTMAIN;
                            $idAccountSub_General   =   $keyAccountSub->IDACCOUNTGENERAL;

                            if($idAccountMain == $idAccountSub_Main && $idAccountGeneral == $idAccountSub_General){
                                $result[]           =   [
                                    'IDACCOUNT'         =>  hashidEncode($idAccountSub),
                                    'IDACCOUNTPARENT'   =>  hashidEncode($idAccountMain),
                                    'ACCOUNTCODE'       =>  $keyAccountSub->ACCOUNTCODE,
                                    'ACCOUNTNAME'       =>  $keyAccountSub->ACCOUNTNAME,
                                    'DEFAULTDRCR'       =>  $keyAccountSub->DEFAULTDRCR,
                                    'LEVEL'             =>  3
                                ];
                            }
                        }
                    }
                }
            }
        }

        $dataAccountOpeningBalance  =   $chartOfAccountModel->getDataAccountOpeningBalance();
        return $this->setResponseFormat('json')
                    ->respond([
                        "result"                    =>  $result,
                        "dataAccountOpeningBalance" =>  $dataAccountOpeningBalance
                     ]);
    }

    public function getNextStepCreateAccount()
    {
        helper(['form']);
        $rules      =   [
            'accountLevel'      => ['label' => 'Account Level', 'rules' => 'required|in_list[1,2,3]'],
            'idAccountGeneral'  => ['label' => 'General Account', 'rules' => 'required|alpha_numeric'],
            'idAccountMain'     => ['label' => 'Main Account', 'rules' => 'required|alpha_numeric'],
        ];

        if(!$this->validate($rules)) return $this->fail($this->validator->getErrors());

        $chartOfAccountModel=   new ChartOfAccountModel();
        $accountLevel       =   $this->request->getVar('accountLevel');
        $idAccountGeneral   =   $this->request->getVar('idAccountGeneral');
        $idAccountGeneral   =   isset($idAccountGeneral) && $idAccountGeneral != '' ? hashidDecode($idAccountGeneral) : $idAccountGeneral;
        $idAccountMain      =   $this->request->getVar('idAccountMain');
        $idAccountMain      =   isset($idAccountMain) && $idAccountMain != '' ? hashidDecode($idAccountMain) : $idAccountMain;
        $detailNextStep     =   false;

        switch($accountLevel){
            case 1  :   $detailNextStep    =   $chartOfAccountModel->getDetailNextStep($accountLevel); break;
            case 2  :   $detailNextStep    =   $chartOfAccountModel->getDetailNextStep($accountLevel, $idAccountGeneral); break;
            case 3  :   $detailNextStep    =   $chartOfAccountModel->getDetailNextStep($accountLevel, $idAccountGeneral, $idAccountMain); break;
            default :   
                break;
        }

        if(!$detailNextStep) return throwResponseNotFound("No next step detail found");

        return $this->setResponseFormat('json')
                    ->respond([
                        "detailNextStep"    =>  $detailNextStep
                     ]);
    }

    public function insertData()
    {
        helper(['form']);
        $rules      =   [
            'accountLevel'      =>  ['label' => 'Account Level', 'rules' => 'required|in_list[1,2,3]'],
            'idAccountGeneral'  =>  ['label' => 'General Account', 'rules' => 'required|alpha_numeric'],
            'idAccountMain'     =>  ['label' => 'Main Account', 'rules' => 'required|alpha_numeric'],
            'defaultDRCR'       =>  ['label' => 'Default DR/CR', 'rules' => 'required|in_list[DR,CR]'],
            'accountCode'       =>  ['label' => 'Account Code', 'rules' => 'required|numeric|max_length[3]'],
            'orderPosition'     =>  ['label' => 'Order Position', 'rules' => 'required|numeric'],
            'accountNameEng'    =>  ['label' => 'Account Name (English)', 'rules' => 'required|alpha_numeric_punct'],
            'accountNameIdn'    =>  ['label' => 'Account Name (Bahasa Indonesia)', 'rules' => 'required|alpha_numeric_punct']
        ];

        $messages   =   [
            'orderPosition' => [
                'required'  => 'Please select valid order position',
                'numeric'   => 'Please select valid order position'
            ]
        ];
        if(!$this->validate($rules, $messages)) return $this->fail($this->validator->getErrors());

        $chartOfAccountModel=   new ChartOfAccountModel();
        $accountLevel       =   $this->request->getVar('accountLevel');
        $idAccountGeneral   =   $this->request->getVar('idAccountGeneral');
        $idAccountGeneral   =   hashidDecode($idAccountGeneral);
        $idAccountMain      =   $this->request->getVar('idAccountMain');
        $idAccountMain      =   hashidDecode($idAccountMain);
        $idAccountParent    =   0;
        $defaultDRCR        =   $this->request->getVar('defaultDRCR');
        $accountCode        =   $this->request->getVar('accountCode');
        $orderPosition      =   $this->request->getVar('orderPosition');
        $accountNameEng     =   $this->request->getVar('accountNameEng');
        $accountNameIdn     =   $this->request->getVar('accountNameIdn');
        $lastNumberPostition=   $this->request->getVar('lastNumberPostition');
        $lastNumberPostition=   isset($lastNumberPostition) && $lastNumberPostition != '' ? $lastNumberPostition : 1;

        switch($accountLevel){
            case 2 : $idAccountParent   =   $idAccountGeneral; break;
            case 3 : $idAccountParent   =   $idAccountMain; break;
            case 1 :
            default : break;
        }

        $checkAccountExist  =   $chartOfAccountModel->where('LEVEL', $accountLevel)->where('IDACCOUNTPARENT', $idAccountParent)->where('ACCOUNTCODE', $accountCode)->first();
        if($checkAccountExist || !is_null($checkAccountExist)) return $this->fail('Please choose another account code', 400);

        $arrInsertData      =   [
            'IDACCOUNTPARENT'   =>  $idAccountParent,
            'LEVEL'             =>  $accountLevel,
            'ACCOUNTCODE'       =>  $accountCode,
            'ACCOUNTNAMEENG'    =>  $accountNameEng,
            'ACCOUNTNAMEID'     =>  $accountNameIdn,
            'DEFAULTDRCR'       =>  $defaultDRCR,
            'ORDERNUMBER'       =>  $orderPosition
        ];

        $mainOperation  =   new MainOperation();
        $procInsertData =   $mainOperation->insertDataTable('m_account', $arrInsertData);

        if(!$procInsertData['status']) return switchMySQLErrorCode($procInsertData['errCode']);
        $idNewAccount   =   $procInsertData['insertID'];

        if($orderPosition != $lastNumberPostition){
            $listAccountOrder   =   $chartOfAccountModel
                                    ->where('LEVEL', $accountLevel)
                                    ->where('IDACCOUNTPARENT', $idAccountParent)
                                    ->where('ORDERNUMBER >=', $orderPosition)
                                    ->where('IDACCOUNT !=', $idNewAccount)
                                    ->orderBy('ORDERNUMBER', 'ASC')
                                    ->findAll();

            if($listAccountOrder || !is_null($listAccountOrder)){
                foreach($listAccountOrder as $keyAccountOrder){
                    $chartOfAccountModel->update($keyAccountOrder['IDACCOUNT'], ['ORDERNUMBER' => ($keyAccountOrder['ORDERNUMBER'] + 1)]);
                }
            }
        }

        return throwResponseOK('New account data has been added');
    }

    public function getDetailAccount()
    {
        helper(['form']);
        $rules      =   [
            'idAccount'  => ['label' => 'Id Account', 'rules' => 'required|alpha_numeric']
        ];

        $messages   =   [
            'idAccount' => [
                'required'      => 'Invalid data sent',
                'alpha_numeric' => 'Invalid data sent'
            ]
        ];

        if(!$this->validate($rules, $messages)) return $this->fail($this->validator->getErrors());

        $chartOfAccountModel=   new ChartOfAccountModel();
        $idAccount          =   $this->request->getVar('idAccount');
        $idAccount          =   hashidDecode($idAccount);
        $detailAccount      =   $chartOfAccountModel->find($idAccount);

        if(!$detailAccount) return throwResponseNotFound("No detail found for account selected");
        $accountLevel       =   $detailAccount['LEVEL'];
        $orderNumber        =   $detailAccount['ORDERNUMBER'];
        $idAccountGeneral   =   $accountLevel == 2 ? $detailAccount['IDACCOUNTPARENT'] : false;
        $idAccountMain      =   $accountLevel == 3 ? $detailAccount['IDACCOUNTPARENT'] : false;
        $listOrderPosition  =   $chartOfAccountModel->getListOrderPosition($accountLevel, $idAccountGeneral, $idAccountMain);
        $arrOrderPosition   =   [];

        if(is_null($listOrderPosition)){
            $arrOrderPosition[] =   [1, 'First', true];
        } else {
            $accountNameBefore  =   '';
            $idAccountBefore    =   0;
            foreach($listOrderPosition as $keyOrderPosition){
                $orderNumberDB  =   $keyOrderPosition->ORDERNUMBER;
                $isSelected     =   $orderNumber == $orderNumberDB ? true : false;

                if($orderNumberDB == 1){
                    $arrOrderPosition[] =   [1, 'First', $isSelected];
                    if($orderNumber != 1) $accountNameBefore  =   $keyOrderPosition->ACCOUNTNAME;
                } else {
                    if($accountNameBefore != ''){
                        if($idAccountBefore != $idAccount) $arrOrderPosition[] =   [$orderNumberDB, 'After '.$accountNameBefore, $isSelected];
                    }
                    $accountNameBefore  =   $keyOrderPosition->ACCOUNTNAME;
                    $idAccountBefore    =   $keyOrderPosition->IDACCOUNT;
                }
            }

            if($idAccountBefore != $idAccount) $arrOrderPosition[] =   [$orderNumberDB, 'After '.$accountNameBefore, $isSelected];
        }

        $detailAccount['IDACCOUNT']         =   hashidEncode($detailAccount['IDACCOUNT']);
        $detailAccount['IDACCOUNTPARENT']   =   hashidEncode($detailAccount['IDACCOUNTPARENT']);
        return $this->setResponseFormat('json')
                    ->respond([
                        "detailAccount"     =>  $detailAccount,
                        "arrOrderPosition"  =>  $arrOrderPosition
                     ]);
    }

    public function updateData()
    {
        helper(['form']);
        $rules      =   [
            'accountLevel'      =>  ['label' => 'Account Level', 'rules' => 'required|in_list[1,2,3]'],
            'idAccount'         =>  ['label' => 'Id Account', 'rules' => 'required|alpha_numeric'],
            'idAccountGeneral'  =>  ['label' => 'General Account', 'rules' => 'required|alpha_numeric'],
            'idAccountMain'     =>  ['label' => 'Main Account', 'rules' => 'required|alpha_numeric'],
            'defaultDRCR'       =>  ['label' => 'Default DR/CR', 'rules' => 'required|in_list[DR,CR]'],
            'accountCode'       =>  ['label' => 'Account Code', 'rules' => 'required|numeric|max_length[3]'],
            'orderPosition'     =>  ['label' => 'Order Position', 'rules' => 'required|numeric'],
            'accountNameEng'    =>  ['label' => 'Account Name (English)', 'rules' => 'required|alpha_numeric_punct'],
            'accountNameIdn'    =>  ['label' => 'Account Name (Bahasa Indonesia)', 'rules' => 'required|alpha_numeric_punct']
        ];

        $messages   =   [
            'accountLevel' => [
                'required'      => 'Invalid data sent',
                'in_list'       => 'Invalid data sent'
            ],
            'idAccount' => [
                'required'      => 'Invalid data sent',
                'alpha_numeric' => 'Invalid data sent'
            ],
            'orderPosition' => [
                'required'      => 'Please select valid order position',
                'numeric'       => 'Please select valid order position'
            ]
        ];
        if(!$this->validate($rules, $messages)) return $this->fail($this->validator->getErrors());

        $chartOfAccountModel=   new ChartOfAccountModel();
        $accountLevel       =   $this->request->getVar('accountLevel');
        $idAccount          =   $this->request->getVar('idAccount');
        $idAccount          =   hashidDecode($idAccount);
        $idAccountGeneral   =   $this->request->getVar('idAccountGeneral');
        $idAccountGeneral   =   hashidDecode($idAccountGeneral);
        $idAccountMain      =   $this->request->getVar('idAccountMain');
        $idAccountMain      =   hashidDecode($idAccountMain);
        $defaultDRCR        =   $this->request->getVar('defaultDRCR');
        $accountCode        =   $this->request->getVar('accountCode');
        $orderPosition      =   $this->request->getVar('orderPosition');
        $accountNameEng     =   $this->request->getVar('accountNameEng');
        $accountNameIdn     =   $this->request->getVar('accountNameIdn');
        $idAccountParent    =   0;

        switch($accountLevel){
            case 2 : $idAccountParent   =   $idAccountGeneral; break;
            case 3 : $idAccountParent   =   $idAccountMain; break;
            case 1 :
            default : break;
        }

        $checkAccountExist  =   $chartOfAccountModel->where('LEVEL', $accountLevel)->where('IDACCOUNTPARENT', $idAccountParent)->where('ACCOUNTCODE', $accountCode)->where('IDACCOUNT !=', $idAccount)->first();
        if($checkAccountExist || !is_null($checkAccountExist)) return $this->fail('Please choose another account code', 400);
        $detailAccount      =   $chartOfAccountModel->find($idAccount);
        $orderNumberDB      =   $detailAccount['ORDERNUMBER'];

        $arrUpdateData      =   [
            'IDACCOUNTPARENT'   =>  $idAccountParent,
            'LEVEL'             =>  $accountLevel,
            'ACCOUNTCODE'       =>  $accountCode,
            'ACCOUNTNAMEENG'    =>  $accountNameEng,
            'ACCOUNTNAMEID'     =>  $accountNameIdn,
            'DEFAULTDRCR'       =>  $defaultDRCR,
            'ORDERNUMBER'       =>  $orderPosition
        ];

        $mainOperation  =   new MainOperation();
        $procUpdateData =   $mainOperation->updateDataTable('m_account', $arrUpdateData, ['IDACCOUNT' => $idAccount]);

        if(!$procUpdateData['status']) return switchMySQLErrorCode($procUpdateData['errCode']);

        if($orderNumberDB != $orderPosition){
            $listAccountOrder   =   $chartOfAccountModel
                                    ->where('LEVEL', $accountLevel)
                                    ->where('IDACCOUNTPARENT', $idAccountParent)
                                    ->where('IDACCOUNT !=', $idAccount)
                                    ->orderBy('ORDERNUMBER', 'ASC')
                                    ->findAll();

            if($listAccountOrder || !is_null($listAccountOrder)){
                $iNumber    =   $orderPosition == 1 ? 2 : 1;
                foreach($listAccountOrder as $keyAccountOrder){
                    if($iNumber != $orderPosition) $chartOfAccountModel->update($keyAccountOrder['IDACCOUNT'], ['ORDERNUMBER' => $iNumber]);
                    $iNumber++;
                }
            }
        }

        return throwResponseOK('Account data has been updated');
    }

    public function getSortOrderAccount()
    {
        helper(['form']);
        $rules      =   [
            'idAccount'     =>  ['label' => 'Id Account', 'rules' => 'required|alpha_numeric'],
            'accountLevel'  =>  ['label' => 'Account Level', 'rules' => 'required|in_list[1,2,3]'],
        ];

        $messages   =   [
            'idAccount'     => [
                'required'      => 'Invalid data sent',
                'alpha_numeric' => 'Invalid data sent'
            ],
            'accountLevel'  => [
                'required'      => 'Invalid data sent',
                'in_list'       => 'Invalid data sent'
            ]
        ];

        if(!$this->validate($rules, $messages)) return $this->fail($this->validator->getErrors());

        $chartOfAccountModel=   new ChartOfAccountModel();
        $idAccount          =   $this->request->getVar('idAccount');
        $idAccount          =   hashidDecode($idAccount);
        $accountLevel       =   $this->request->getVar('accountLevel');
        $detailAccount      =   $chartOfAccountModel->getDetailAccount($accountLevel, $idAccount);

        if(!$detailAccount) return throwResponseNotFound("No detail found for account selected");

        $idAccountParent    =   0;
        $accountLevelName   =   $accountNameGeneral =   $accountNameMain    =   '';
        switch($accountLevel){
            case 1  :
                $accountLevelName   =   'General Account';
                break;
            case 2  :
                $accountLevelName   =   'Main Account';
                $idAccountParent    =   $detailAccount['IDACCOUNTPARENT'];
                $accountNameGeneral =   $detailAccount['ACCOUNTGENERAL'];
                break;
            case 3  :
                $accountLevelName   =   'Sub Account';
                $idAccountParent    =   $detailAccount['IDACCOUNTPARENT'];
                $accountNameGeneral =   $detailAccount['ACCOUNTGENERAL'];
                $accountNameMain    =   $detailAccount['ACCOUNTMAIN'];
                break;
            default :
                break;
        }

        $arrDataSortAccount =   [];
        $accountNameLang    =   lang("CustomSystem.databaseField.m_account.ACCOUNTNAME");
        $dataSortAccount    =   $chartOfAccountModel->where('LEVEL', $accountLevel)->where('IDACCOUNTPARENT', $idAccountParent)->findAll();
        if(!is_null($dataSortAccount)){
            foreach($dataSortAccount as $keySortAccount){
                $arrDataSortAccount[]   =   [
                    'IDACCOUNT'     =>  hashidEncode($keySortAccount['IDACCOUNT']),
                    'ACCOUNTNAME'   =>  $keySortAccount['ACCOUNTCODE'].' - '.$keySortAccount[$accountNameLang]
                ];
            }
        }

        return $this->setResponseFormat('json')
                    ->respond([
                        "accountLevelName"  =>  $accountLevelName,
                        "accountNameGeneral"=>  $accountNameGeneral,
                        "accountNameMain"   =>  $accountNameMain,
                        "arrDataSortAccount"=>  $arrDataSortAccount
                     ]);
    }

    public function saveSortOrderAccount()
    {
        helper(['form']);
        $rules      =   [
            'arrSortAccount'    =>  ['label' => 'Sort Account', 'rules' => 'required|is_array']
        ];

        $messages   =   [
            'arrSortAccount'=> [
                'required'  => 'Invalid data sent',
                'is_array'  => 'Invalid data sent'
            ]
        ];
        if(!$this->validate($rules, $messages)) return $this->fail($this->validator->getErrors());

        $chartOfAccountModel=   new ChartOfAccountModel();
        $arrSortAccount     =   $this->request->getVar('arrSortAccount');
        $arrSortAccountDB   =   [];

        try{
            $numberPosition =   1;
            foreach($arrSortAccount as $idAccountEncoded){
                $idAccount          =   hashidDecode($idAccountEncoded);
                $arrSortAccountDB[] =   [$numberPosition, $idAccount];
                $numberPosition++;
            }
        } catch (\Throwable $th) {
            return throwResponseNotAcceptable('Invalid data sent');
        }

        try{
            foreach($arrSortAccountDB as $arrAccount){
                $chartOfAccountModel->update($arrAccount[1], ['ORDERNUMBER' => $arrAccount[0]]);
            }
        } catch (\Throwable $th) {
            return throwResponseNotAcceptable('Internal database script error');
        }

        return throwResponseOK('Accounts position has been updated');
    }

    public function deleteAccount()
    {
        helper(['form']);
        $rules      =   [
            'accountLevel'      =>  ['label' => 'Account Level', 'rules' => 'required|in_list[1,2,3]'],
            'idAccount'         =>  ['label' => 'Id Account', 'rules' => 'required|alpha_numeric']
        ];

        $messages   =   [
            'accountLevel' => [
                'required'      => 'Invalid data sent',
                'in_list'       => 'Invalid data sent'
            ],
            'idAccount' => [
                'required'      => 'Invalid data sent',
                'alpha_numeric' => 'Invalid data sent'
            ]
        ];
        if(!$this->validate($rules, $messages)) return $this->fail($this->validator->getErrors());

        $chartOfAccountModel=   new ChartOfAccountModel();
        $accountLevel       =   $this->request->getVar('accountLevel');
        $idAccount          =   $this->request->getVar('idAccount');
        $idAccount          =   hashidDecode($idAccount);
        $isChildAccountExist=   $accountLevel == 3 ? false : $chartOfAccountModel->isChildAccountExist($idAccount);

        if($isChildAccountExist) return $this->fail('Can`t delete the account because <b>it has a branch</b>', 400);

        $isTransactionAccountExist      =   $chartOfAccountModel->isTransactionAccountExist($idAccount);
        if($isTransactionAccountExist) return $this->fail('Can`t delete account because account <b>has transaction history</b>', 400);

        $isTemplateJournalAccountExist  =   $chartOfAccountModel->isTemplateJournalAccountExist($idAccount);
        if($isTemplateJournalAccountExist) return $this->fail('Unable to delete the account as it is <b>used in the journal template</b>', 400);

        try{
            $chartOfAccountModel->delete($idAccount);
        } catch (\Throwable $th) {
            return throwResponseNotAcceptable('Internal database script error');
        }

        return throwResponseOK('Accounts has been deleted');
    }

    public function saveAccountOpeningBalance()
    {
        helper(['form']);
        $rules      =   [
            'reffNumberJournalOpeningBalance'   =>  ['label' => 'Reff Number', 'rules' => 'required|exact_length[12]|alpha_numeric'],
            'dateJournalOpeningBalance'         =>  ['label' => 'Opening Balance Date', 'rules' => 'required|exact_length[10]|valid_date[d-m-Y]'],
            'descriptionJournalOpeningBalance'  =>  ['label' => 'Description', 'rules' => 'required|min_length[8]|alpha_numeric_punct'],
            'dataAccountOpeningBalance'         =>  ['label' => 'Opening Balance Account Data', 'rules' => 'required|is_array']
        ];

        $messages   =   [
            'dataAccountOpeningBalance'  => [
                'required'  => 'Invalid data sent',
                'is_array'  => 'Invalid data sent'
            ]
        ];
        if(!$this->validate($rules, $messages)) return $this->fail($this->validator->getErrors());

        $chartOfAccountModel                =   new ChartOfAccountModel();
        $reffNumberJournalOpeningBalance    =   $this->request->getVar('reffNumberJournalOpeningBalance');
        $dateJournalOpeningBalance          =   $this->request->getVar('dateJournalOpeningBalance');
        $dateJournalOpeningBalanceTF        =   Time::createFromFormat('d-m-Y', $dateJournalOpeningBalance);
        $dateJournalOpeningBalance          =   $dateJournalOpeningBalanceTF->toDateString();
        $descriptionJournalOpeningBalance   =   $this->request->getVar('descriptionJournalOpeningBalance');
        $dataAccountOpeningBalance          =   $this->request->getVar('dataAccountOpeningBalance');
        $isJournalRecapExist                =   $chartOfAccountModel->isJournalRecapExist($reffNumberJournalOpeningBalance);
        $totalAccount                       =   count($dataAccountOpeningBalance);
        $idJournalRecap                     =   $totalDebit = $totalCredit = 0;
        $arrTest                            =   [];

        foreach($dataAccountOpeningBalance as $keyAccountOpeningBalance){
            $nominalDebit   =   $keyAccountOpeningBalance[2];
            $nominalCredit  =   $keyAccountOpeningBalance[3];
            $totalDebit     +=  $nominalDebit;
            $totalCredit    +=  $nominalCredit;
        }

        if($totalDebit != $totalCredit) return throwResponseNotAcceptable("The nominal debit and credit are unbalanced. Please review the input data again.");

        if($isJournalRecapExist == 0 || is_null($isJournalRecapExist)){
            $arrInsertDataJournalRecap  =   [
                'REFFNUMBER'        =>  $reffNumberJournalOpeningBalance,
                'DATETRANSACTION'   =>  $dateJournalOpeningBalance,
                'DESCRIPTION'       =>  $descriptionJournalOpeningBalance,
                'TOTALACCOUNT'      =>  $totalAccount,
                'TOTALNOMINAL'      =>  $totalDebit,
                'USERINSERT'        =>  $this->userData->name,
                'DATETIMEINSERT'    =>  $this->currentDateTime
            ];
        } else {
            $idJournalRecap =   $isJournalRecapExist['IDJOURNALRECAP'];
            $arrUpdateDataJournalRecap  =   [
                'REFFNUMBER'        =>  $reffNumberJournalOpeningBalance,
                'DATETRANSACTION'   =>  $dateJournalOpeningBalance,
                'DESCRIPTION'       =>  $descriptionJournalOpeningBalance,
                'TOTALACCOUNT'      =>  $totalAccount,
                'TOTALNOMINAL'      =>  $totalDebit,
                'USERUPDATE'        =>  $this->userData->name,
                'DATETIMEUPDATE'    =>  $this->currentDateTime
            ];
        }

        try {
            $chartOfAccountModel->db->transException(true)->transStart();
            $mainOperation  =   new MainOperation();

            if($idJournalRecap == 0){
                $procInsertData =   $mainOperation->insertDataTable('t_journalrecap', $arrInsertDataJournalRecap);
                if(!$procInsertData['status']) return switchMySQLErrorCode($procInsertData['errCode']);
                $idJournalRecap =   $procInsertData['insertID'];
            } else {
                $mainOperation->updateDataTable('t_journalrecap', $arrUpdateDataJournalRecap, ['IDJOURNALRECAP'=>$idJournalRecap]);
            }

            foreach($dataAccountOpeningBalance as $keyAccountDetail){
                $idAccount              =   hashidDecode($keyAccountDetail[0]);
                $positionDRCR           =   $keyAccountDetail[1];
                $nominalDebit           =   $keyAccountDetail[2];
                $nominalCredit          =   $keyAccountDetail[3];
                $isJournalDetailExist   =   $chartOfAccountModel->isJournalDetailExist($idJournalRecap, $idAccount);
                $arrInsertUpdateJournalDetail   =   [
                    'IDJOURNALRECAP'=>  $idJournalRecap,
                    'IDACCOUNT'     =>  $idAccount,
                    'POSITIONDRCR'  =>  $positionDRCR,
                    'DEBIT'         =>  $nominalDebit,
                    'CREDIT'        =>  $nominalCredit
                ];

                if($isJournalDetailExist){
                    $mainOperation->updateDataTable('t_journaldetails', $arrInsertUpdateJournalDetail, ['IDJOURNALRECAP' => $idJournalRecap, 'IDACCOUNT' => $idAccount]);
                } else {
                    $mainOperation->insertDataTable('t_journaldetails', $arrInsertUpdateJournalDetail);
                }
            }
    		$chartOfAccountModel->db->transComplete();
            return throwResponseOK('Account opening balance has been saved');
        } catch (\Throwable $th) {
            return throwResponseInternalServerError('Internal server error - failed to add data');
        }
    }
}