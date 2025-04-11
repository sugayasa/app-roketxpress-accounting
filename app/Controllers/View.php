<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use CodeIgniter\I18n\Time;
use App\Models\ChartOfAccountModel;
use App\Models\MainOperation;

class View extends ResourceController
{
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    use ResponseTrait;
    protected $userData, $currentDateTime, $currentDateDT;
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger) {
        parent::initController($request, $response, $logger);

        try {
            $this->userData         =   $request->userData;
            $this->currentDateTime  =   $request->currentDateTime;
            $this->currentDateDT    =   $request->currentDateDT;
        } catch (\Throwable $th) {
        }
    }

    public function index()
    {
        return $this->failForbidden('[E-AUTH-000] Forbidden Access');
    }
    
    public function dashboard()
    {
        $htmlRes        =   view(
                                'Page/dashboard',
                                ["thisMonth" =>  date('m')],
                                ['debug' => false]
                            );
        return $this->setResponseFormat('json')
        ->respond([
            'htmlRes'   =>  $htmlRes
        ]);
    }
    
    public function chartOfAccount()
    {
        $htmlRes        =   view(
                                'Page/chartOfAccount',
                                [],
                                ['debug' => false]
                            );
        return $this->setResponseFormat('json')
        ->respond([
            'htmlRes'   =>  $htmlRes
        ]);
    }
    
    public function listAssetOwned()
    {
        $mainOperation          =   new MainOperation();
        $initialRecordingDate   =   $mainOperation->getDataSystemSetting(1);
        $htmlRes                =   view(
                                        'Page/listAssetOwned',
                                        [
                                            "initialRecordingDate"  =>  $initialRecordingDate
                                        ],
                                        ['debug' => false]
                                    );
        return $this->setResponseFormat('json')
        ->respond([
            'htmlRes'   =>  $htmlRes
        ]);
    }
    
    public function templateJournal()
    {
        $htmlRes    =   view(
                            'Page/templateJournal',
                            [],
                            ['debug' => false]
                        );
        return $this->setResponseFormat('json')
        ->respond([
            'htmlRes'   =>  $htmlRes
        ]);
    }
    
    public function generalJournal()
    {
        $dataAllAccountJournal  =   $this->getDataAllAccountJournal();
        $htmlRes                =   view(
                                        'Page/generalJournal',
                                        ['dataAllAccountJournal' =>  json_encode($dataAllAccountJournal)],
                                        ['debug' => false]
                                    );
        return $this->setResponseFormat('json')
        ->respond([
            'htmlRes'   =>  $htmlRes
        ]);
    }
    
    public function generalLedger()
    {
        $dataAllAccountJournal  =   $this->getDataAllAccountJournal();
        $arrElemModule          =   [
            "modalChooseAccount"    =>  view('ElemModule/modalChooseAccount', [], ['saveData' => true])
        ];
        $htmlRes                =   view(
                                        'Page/generalLedger',
                                        [
                                            'dataAllAccountJournal' =>  json_encode($dataAllAccountJournal),
                                            'arrElemModule'         =>  $arrElemModule
                                        ],
                                        ['debug' => false]
                                    );
        return $this->setResponseFormat('json')
        ->respond([
            'htmlRes'   =>  $htmlRes
        ]);
    }
    
    public function cashFlow()
    {
        $mainOperation              =   new MainOperation();
        $chartOfAccountModel        =   new ChartOfAccountModel();

        $dataSettingCashFlow        =   $mainOperation->getDataSystemSetting(3);
        $dataSettingCashFlow        =   json_decode($dataSettingCashFlow);
        $arrAccountsCashFlowDefault =   $arrAccountsCashFlow    =   [];

        if(count(get_object_vars($dataSettingCashFlow)) > 0){
            $arrIdAccountsCashFlow  =   $dataSettingCashFlow->idAccountsCashFlow;
            $arrIdAccountGeneral    =   $arrIdAccountMain   =   $arrIdAccountSub    =   [];

            foreach($arrIdAccountsCashFlow as $idAccountsCashFlow){
                $basicDetailAccount             =   $chartOfAccountModel->getBasicDetailAccount($idAccountsCashFlow);
                $idAccountParent                =   $basicDetailAccount['IDACCOUNTPARENT'];
                $levelAccountCashFlow           =   $basicDetailAccount['LEVEL'];
                $detailAccount                  =   $chartOfAccountModel->getDetailAccount($levelAccountCashFlow, $idAccountsCashFlow);
                $arrAccountsCashFlowDefault[]   =   [hashidEncode($idAccountsCashFlow), $detailAccount['ACCOUNTCODE']." ".$detailAccount['ACCOUNTNAME']];

                if($levelAccountCashFlow == 3) {
                    $arrIdAccountSub[]  =   $idAccountsCashFlow;
                    $arrIdAccountMain[] =   $idAccountParent;
                }

                if($levelAccountCashFlow == 2) {
                    $arrIdAccountMain[]     =   $idAccountsCashFlow;
                    $arrIdAccountGeneral[]  =   $idAccountParent;
                }

                if($levelAccountCashFlow == 1) $arrIdAccountGeneral[]  =   $idAccountsCashFlow;
            }
            
            $arrAccountsCashFlow=   $this->getDataAllAccountJournal(true, $arrIdAccountGeneral, $arrIdAccountMain, $arrIdAccountSub);
        }

        $dataDateRange  =   $this->getDataDateRange();
        $arrElemModule  =   [
            "filterDateRange"   =>  view('ElemModule/filterDateRange', [], ['saveData' => true])
        ];
        $htmlRes        =   view(
                                'Page/cashFlow',
                                array_merge(
                                    [
                                        "arrElemModule"             =>  $arrElemModule,
                                        "arrAccountsCashFlowDefault"=>  json_encode($arrAccountsCashFlowDefault),
                                        "arrAccountsCashFlow"       =>  json_encode($arrAccountsCashFlow)
                                    ],
                                    $dataDateRange
                                ),
                                ['debug' => false]
                            );
        return $this->setResponseFormat('json')
        ->respond([
            'htmlRes'   =>  $htmlRes
        ]);
    }
    
    public function profitLoss()
    {
        $arrElemModule      =   [
            "filterDateRange"   =>  view('ElemModule/filterDateRange', [], ['saveData' => true])
        ];
        $dataDateRange  =   $this->getDataDateRange();
        $htmlRes        =   view(
                                'Page/profitLoss',
                                array_merge(
                                    [
                                        "arrElemModule"         =>  $arrElemModule
                                    ],
                                    $dataDateRange
                                ),
                                ['debug' => false]
                            );
        return $this->setResponseFormat('json')
        ->respond(['htmlRes'   =>  $htmlRes]);
    }
    
    public function balanceSheet()
    {
        $htmlRes    =   view(
                            'Page/balanceSheet',
                            ["thisMonth" =>  date('m'), "thisYear" =>  date('Y')],
                            ['debug' => false]
                        );
        return $this->setResponseFormat('json')
        ->respond([
            'htmlRes'   =>  $htmlRes
        ]);
    }
    
    public function settingsUserAdmin()
    {
        $htmlRes    =   view(
                            'Page/setttings/userAdmin',
                            [],
                            ['debug' => false]
                        );
        return $this->setResponseFormat('json')
        ->respond([
            'htmlRes'   =>  $htmlRes
        ]);
    }
    
    public function settingsUserLevelMenu()
    {
        $htmlRes    =   view(
                            'Page/setttings/userLevelMenu',
                            [],
                            ['debug' => false]
                        );
        return $this->setResponseFormat('json')
        ->respond([
            'htmlRes'   =>  $htmlRes
        ]);
    }
    
    public function settingsSystemSetting()
    {
        $arrElemModule          =   [
            "modalChooseAccount"    =>  view('ElemModule/modalChooseAccount', [], ['saveData' => true])
        ];
        $dataAllAccountJournal  =   $this->getDataAllAccountJournal();
        $htmlRes                =   view(
                                        'Page/setttings/systemSetting',
                                        [
                                            'dataAllAccountJournal' =>  json_encode($dataAllAccountJournal),
                                            "arrElemModule"         =>  $arrElemModule,
                                        ],
                                        ['debug' => false]
                                    );
        return $this->setResponseFormat('json')
        ->respond([
            'htmlRes'   =>  $htmlRes
        ]);
    }
    
    public function notification()
    {
        $htmlRes    =   view(
                            'Page/notification',
                            [],
                            ['debug' => false]
                        );
        return $this->setResponseFormat('json')
        ->respond([
            'htmlRes'   =>  $htmlRes
        ]);
    }

    private function getDataDateRange(){
        $firstDateOfLastMonth   =   new \DateTime('first day of last month');
        $firstDateOfLastMonth   =   $firstDateOfLastMonth->format('d-m-Y');
        $lastDateOfLastMonth    =   new \DateTime('last day of last month');
        $lastDateOfLastMonth    =   $lastDateOfLastMonth->format('d-m-Y');
        $firstDateOfLastYear    =   new \DateTime('first day of January last year');
        $firstDateOfLastYear    =   $firstDateOfLastYear->format('d-m-Y');
        $lastDateOfLastYear     =   new \DateTime('first day of December last year');
        $lastDateOfLastYear     =   $lastDateOfLastYear->format('d-m-Y');

        return [
            "firstDateOfLastMonth"  =>  $firstDateOfLastMonth,
            "lastDateOfLastMonth"   =>  $lastDateOfLastMonth,
            "firstDateOfLastYear"   =>  $firstDateOfLastYear,
            "lastDateOfLastYear"    =>  $lastDateOfLastYear
        ];
    }

    private function getDataAllAccountJournal($accountCashFlow = false, $arrIdAccountGeneral = [], $arrIdAccountMain = [], $arrIdAccountSub = [])
    {
        $chartOfAccountModel=   new ChartOfAccountModel();
        $keywordString      =   $accountCashFlow ? null : '';
        $dataAccountSub     =	$chartOfAccountModel->getDataAccountSub(0, 0, '', $arrIdAccountSub);
        $dataAccountSub     =   $accountCashFlow && count($arrIdAccountSub) <= 0 ? [] : $dataAccountSub;

        if(count($dataAccountSub) > 0){
            foreach($dataAccountSub as $keyAccountSub){
                $arrIdAccountMain[]     =   $keyAccountSub->IDACCOUNTMAIN;
                $arrIdAccountGeneral[]  =   $keyAccountSub->IDACCOUNTGENERAL;
            }
        }

        $arrIdAccountGeneralFilter  =   $accountCashFlow ? [] : $arrIdAccountGeneral;
        $dataAccountMain            =	$chartOfAccountModel->getDataAccountMain(0, 0, $keywordString, array_unique($arrIdAccountMain), array_unique($arrIdAccountGeneralFilter));

        if(count($dataAccountMain) > 0){
            foreach($dataAccountMain as $keyAccountMain){
                $arrIdAccountGeneral[]  =   $keyAccountMain->IDACCOUNTGENERAL;
            }
        }

        $dataAccountGeneral =	$chartOfAccountModel->getDataAccountGeneral(0, $keywordString, array_unique($arrIdAccountGeneral));

        if(!$dataAccountGeneral) return [];
        return $this->generateArrAccount($dataAccountGeneral, $dataAccountMain, $dataAccountSub);
    }

    private function generateArrAccount($dataAccountGeneral, $dataAccountMain, $dataAccountSub){
        $chartOfAccountModel=   new ChartOfAccountModel();

        $result =   [];
        foreach($dataAccountGeneral as $keyAccountGeneral){
            $idAccountGeneral   =   $keyAccountGeneral->IDACCOUNTGENERAL;
            $result[]           =   [
                'IDACCOUNT'         =>  hashidEncode($idAccountGeneral),
                'IDACCOUNTPARENT'   =>  hashidEncode(0),
                'ACCOUNTCODE'       =>  $keyAccountGeneral->ACCOUNTCODE,
                'ACCOUNTCODEFULL'   =>  $keyAccountGeneral->ACCOUNTCODE,
                'ACCOUNTNAME'       =>  $keyAccountGeneral->ACCOUNTNAME,
                'DEFAULTDRCR'       =>  $keyAccountGeneral->DEFAULTDRCR,
                'LEVEL'             =>  1
            ];

            if($dataAccountMain){
                foreach($dataAccountMain as $keyAccountMain){
                    $idAccountMain          =   $keyAccountMain->IDACCOUNTMAIN;
                    $idAccountMain_General  =   $keyAccountMain->IDACCOUNTGENERAL;
                    $detailAccountMain      =   $chartOfAccountModel->getDetailAccount(2, $idAccountMain);

                    if($idAccountGeneral == $idAccountMain_General){
                        $result[]           =   [
                            'IDACCOUNT'         =>  hashidEncode($idAccountMain),
                            'IDACCOUNTPARENT'   =>  hashidEncode($idAccountGeneral),
                            'ACCOUNTCODE'       =>  $keyAccountMain->ACCOUNTCODE,
                            'ACCOUNTCODEFULL'   =>  $detailAccountMain['ACCOUNTCODE'],
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
                            $detailAccountSub       =   $chartOfAccountModel->getDetailAccount(3, $idAccountSub);

                            if($idAccountMain == $idAccountSub_Main && $idAccountGeneral == $idAccountSub_General){
                                $result[]           =   [
                                    'IDACCOUNT'         =>  hashidEncode($idAccountSub),
                                    'IDACCOUNTPARENT'   =>  hashidEncode($idAccountMain),
                                    'ACCOUNTCODE'       =>  $keyAccountSub->ACCOUNTCODE,
                                    'ACCOUNTCODEFULL'   =>  $detailAccountSub['ACCOUNTCODE'],
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

        return $result;
    }
}