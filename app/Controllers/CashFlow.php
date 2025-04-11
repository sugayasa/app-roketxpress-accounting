<?php
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use CodeIgniter\I18n\Time;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Models\ChartOfAccountModel;
use App\Models\CashFlowModel;
use App\Models\MainOperation;

class CashFlow extends ResourceController
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
        $mainOperation      =   new MainOperation();

        $rules      =   [
            'dateRangeType'         =>  ['label' => 'Date Range Type', 'rules' => 'required|in_list[1,2,3,4,5,6]'],
            'dateStart'             =>  ['label' => 'Date Range Start', 'rules' => 'required|exact_length[10]|valid_date[d-m-Y]'],
            'dateEnd'               =>  ['label' => 'Date Range End', 'rules' => 'required|exact_length[10]|valid_date[d-m-Y]'],
            'arrAccountCashFlowName'=>  ['label' => 'Cash FLow Account', 'rules' => 'required|is_array'],
            'arrIdAccountCashFlow'  =>  ['label' => 'Cash FLow Account', 'rules' => 'required|is_array']
        ];

        if(!$this->validate($rules)) return $this->fail($this->validator->getErrors());
        
        $dateRangeType          =   $this->request->getVar('dateRangeType');
        $dateStartPeriodStr     =   $this->request->getVar('dateStart');
        $dateStartPeriodTF      =   Time::createFromFormat('d-m-Y', $dateStartPeriodStr);
        $dateStartPeriod        =   $dateStartPeriodTF->toDateString();
        $dateEndPeriodStr       =   $this->request->getVar('dateEnd');
        $dateEndPeriodTF        =   Time::createFromFormat('d-m-Y', $dateEndPeriodStr);
        $dateEndPeriod          =   $dateEndPeriodTF->toDateString();
        $arrAccountCashFlowName =   $this->request->getVar('arrAccountCashFlowName');
        $arrIdAccountCashFlow   =   $this->request->getVar('arrIdAccountCashFlow');
        $dataSettingCashFlow    =   $mainOperation->getDataSystemSetting(3);
        $dataSettingCashFlow    =   json_decode($dataSettingCashFlow);
        $arrIdAccountsCashFlow  =   [];

        if(is_array($arrIdAccountCashFlow) && count($arrIdAccountCashFlow) > 0){
            foreach($arrIdAccountCashFlow as $idAccountCashFlow){
                $arrIdAccountsCashFlow[]    =   hashidDecode($idAccountCashFlow);
            }
        }

        if(count(get_object_vars($dataSettingCashFlow)) > 0){
            $result             =   $this->generateCashFlowResult(false, $dataSettingCashFlow, $arrIdAccountsCashFlow, $dateStartPeriod, $dateEndPeriod);
            $arrParamExcelData  =   [
                'dateRangeType'         =>  $dateRangeType,
                'dateStartPeriod'       =>  $dateStartPeriod,
                'dateEndPeriod'         =>  $dateEndPeriod,
                'dateStartPeriodStr'    =>  $dateStartPeriodStr,
                'dateEndPeriodStr'      =>  $dateEndPeriodStr,
                'arrAccountCashFlowName'=>  $arrAccountCashFlowName,
                'arrIdAccountsCashFlow' =>  $arrIdAccountsCashFlow
            ];
            $urlExcelData       =   BASE_URL."cashFlow/excelDataCashFlow/".encodeJWTToken($arrParamExcelData);

            return $this->setResponseFormat('json')
                        ->respond([
                            "result"        =>  $result,
                            "urlExcelData"  =>  $urlExcelData
                        ]);
        } else {
            return throwResponseNotFound('No cash flow data found in selected date range');
        }
    }

    private function generateCashFlowResult($excelFormat = false, $dataSettingCashFlow, $arrIdAccountsCashFlow, $dateStartPeriod, $dateEndPeriod) {
        $chartOfAccountModel=   new ChartOfAccountModel();
        $cashFlowModel      =   new CashFlowModel();

        $beginningBalanceCashFlow   =   $cashFlowModel->getSaldoAccountCash($arrIdAccountsCashFlow, $dateStartPeriod);
        $dataCashFlowSection        =   $dataSettingCashFlow->dataCashFlowSection;
        $result                     =   [];
        
        if(count($dataCashFlowSection) > 0){
            $totalCashMutation      =   0;
            foreach($dataCashFlowSection as $keyCashFlowSection){
                $sectionName        =   $keyCashFlowSection->sectionName;
                $generalAccountList =   $keyCashFlowSection->generalAccountList;
                $totalSaldoSection  =   0;
                $result[]           =   [
                    "idAccount"     =>  "",
                    "accountLevel"  =>  1,
                    "accountCode"   =>  "",
                    "accountName"   =>  $sectionName,
                    "textBoldClass" =>  "text-primary",
                    "saldo"         =>  ""
                ];

                if(count($generalAccountList) > 0){
                    foreach($generalAccountList as $idAccountGeneral){
                        $detailAccountGeneral   =   $chartOfAccountModel->getDetailAccount(1, $idAccountGeneral);
                        $dataAccountMain        =   $chartOfAccountModel->getDataAccountMain($idAccountGeneral, 0, '');

                        foreach($dataAccountMain as $keyAccountMain){
                            $idAccountMain          =   $keyAccountMain->IDACCOUNTMAIN;
                            $dataAccountSub         =   $chartOfAccountModel->getDataAccountSub($idAccountGeneral, $idAccountMain);
                            $arrIdAccountSubSearch  =   [];

                            if($dataAccountSub){
                                foreach($dataAccountSub as $keyAccountSub){
                                    $idAccountSub           =   $keyAccountSub->IDACCOUNTSUB;
                                    $arrIdAccountSubSearch[]=   $idAccountSub;
                                }
                            }

                            $dataCashFlowAccountMain    =   $cashFlowModel->getDataCashFlowAccount($arrIdAccountsCashFlow, [$idAccountMain], $dateStartPeriod, $dateEndPeriod);
                            $dataCashFlowAccountSub     =   false;
                            if(count($arrIdAccountSubSearch) > 0){
                                $dataCashFlowAccountSub =   $cashFlowModel->getDataCashFlowAccount($arrIdAccountsCashFlow, $arrIdAccountSubSearch, $dateStartPeriod, $dateEndPeriod);
                            }

                            if($dataCashFlowAccountMain || $dataCashFlowAccountSub){
                                $accountCodeMain    =   $detailAccountGeneral['ACCOUNTCODE']."-".$keyAccountMain->ACCOUNTCODE;
                                $totalResultSub     =   0;
                                
                                if($dataCashFlowAccountSub){
                                    foreach($dataCashFlowAccountSub as $keyCashFlowAccountSub){
                                        $saldoCashFlowAccountSub    =   $keyCashFlowAccountSub->SALDO * 1;
                                        if($saldoCashFlowAccountSub != 0){
                                            $totalResultSub++;
                                        }
                                    }
                                }

                                $saldoCashFlowAccountMain       =   $dataCashFlowAccountMain ? $dataCashFlowAccountMain[0]->SALDO * 1 : "";
                                $totalSaldoCashFlowAccountMain  =   $saldoCashFlowAccountMain == "" ? 0 : $saldoCashFlowAccountMain;
                                $totalSaldoSection              +=  $totalSaldoCashFlowAccountMain;
                                if(($saldoCashFlowAccountMain != 0 && $saldoCashFlowAccountMain != "") || $totalResultSub > 0){
                                    $result[]       =   [
                                        "idAccount"     =>  $excelFormat ? $idAccountMain : hashidEncode($idAccountMain),
                                        "accountLevel"  =>  2,
                                        "accountCode"   =>  $accountCodeMain,
                                        "accountName"   =>  $keyAccountMain->ACCOUNTNAME,
                                        "textBoldClass" =>  "text-info",
                                        "saldo"         =>  $saldoCashFlowAccountMain
                                    ];

                                    if($totalResultSub > 0) {
                                        foreach($dataCashFlowAccountSub as $keyCashFlowAccountSub){
                                            $saldoCashFlowAccountSub    =   $keyCashFlowAccountSub->SALDO * 1;
                                            if($saldoCashFlowAccountSub != 0){
                                                $result[]   =   [
                                                    "idAccount"     =>  $excelFormat ? $idAccountMain : hashidEncode($keyCashFlowAccountSub->IDACCOUNT),
                                                    "accountLevel"  =>  3,
                                                    "accountCode"   =>  $accountCodeMain."-".$keyCashFlowAccountSub->ACCOUNTCODE,
                                                    "accountName"   =>  $keyCashFlowAccountSub->ACCOUNTNAME,
                                                    "textBoldClass" =>  "",
                                                    "saldo"         =>  $keyCashFlowAccountSub->SALDO
                                                ];
                                            }
                                            $totalSaldoCashFlowAccountMain  +=  $saldoCashFlowAccountSub;
                                            $totalSaldoSection              +=  $saldoCashFlowAccountSub;
                                        }

                                        $result[]       =   [
                                            "idAccount"     =>  "",
                                            "accountLevel"  =>  2,
                                            "accountCode"   =>  $accountCodeMain,
                                            "accountName"   =>  "Total ".$keyAccountMain->ACCOUNTNAME,
                                            "textBoldClass" =>  "text-info",
                                            "saldo"         =>  $totalSaldoCashFlowAccountMain
                                        ];
                                    }
                                }
                            }
                        }
                    }
                }

                $result[]           =   [
                    "idAccount"     =>  "",
                    "accountLevel"  =>  1,
                    "accountCode"   =>  "",
                    "accountName"   =>  "Total ".$sectionName,
                    "textBoldClass" =>  "text-primary",
                    "saldo"         =>  $totalSaldoSection
                ];

                $result[]           =   [
                    "idAccount"     =>  "",
                    "accountLevel"  =>  0,
                    "accountCode"   =>  "",
                    "accountName"   =>  "",
                    "textBoldClass" =>  "",
                    "saldo"         =>  ""
                ];

                $totalCashMutation  +=  $totalSaldoSection;
            }

            $result[]           =   [
                "idAccount"     =>  "",
                "accountLevel"  =>  1,
                "accountCode"   =>  "",
                "accountName"   =>  "Beginning Balance",
                "textBoldClass" =>  "text-primary",
                "saldo"         =>  $beginningBalanceCashFlow
            ];

            $result[]           =   [
                "idAccount"     =>  "",
                "accountLevel"  =>  1,
                "accountCode"   =>  "",
                "accountName"   =>  "Cash Mutation",
                "textBoldClass" =>  "text-primary",
                "saldo"         =>  $totalCashMutation
            ];

            $result[]           =   [
                "idAccount"     =>  "",
                "accountLevel"  =>  1,
                "accountCode"   =>  "",
                "accountName"   =>  "Ending Balance",
                "textBoldClass" =>  "text-primary",
                "saldo"         =>  ($beginningBalanceCashFlow + $totalCashMutation)
            ];
        }

        return $result;
    }

    public function getDetailCashFlow()
    {
        helper(['form']);
        $rules      =   [
            'idAccount'             =>  ['label' => 'Account Code', 'rules' => 'required|alpha_numeric'],
            'arrIdAccountCashFlow'  =>  ['label' => 'Cash FLow Account', 'rules' => 'required|is_array'],
            'dateStart'             =>  ['label' => 'Transaction Date', 'rules' => 'required|exact_length[10]|valid_date[d-m-Y]'],
            'dateEnd'               =>  ['label' => 'Transaction Date', 'rules' => 'required|exact_length[10]|valid_date[d-m-Y]']
        ];

        $messages   =   [
            'idAccount' => [
                'required'      => 'Invalid data sent',
                'alpha_numeric' => 'Invalid data sent'
            ]
        ];

        if(!$this->validate($rules, $messages)) return $this->fail($this->validator->getErrors());
        
        $cashFlowModel          =   new CashFlowModel();
        $chartOfAccountModel    =   new ChartOfAccountModel();
        $idAccount              =   $this->request->getVar('idAccount');
        $idAccount              =   hashidDecode($idAccount);
        $arrIdAccountCashFlow   =   $this->request->getVar('arrIdAccountCashFlow');
        $dateStart              =   $this->request->getVar('dateStart');
        $dateStartTF            =   Time::createFromFormat('d-m-Y', $dateStart);
        $dateStart              =   $dateStartTF->toDateString();
        $dateEnd                =   $this->request->getVar('dateEnd');
        $dateEnddTF             =   Time::createFromFormat('d-m-Y', $dateEnd);
        $dateEnd                =   $dateEnddTF->toDateString();
        $arrIdAccountsCashFlow  =   [];

        if(is_array($arrIdAccountCashFlow) && count($arrIdAccountCashFlow) > 0){
            foreach($arrIdAccountCashFlow as $idAccountCashFlow){
                $arrIdAccountsCashFlow[]    =   hashidDecode($idAccountCashFlow);
            }
        }

        $dataDetailsCashFlow    =   $cashFlowModel->getDataDetailCashFlow($idAccount, $arrIdAccountsCashFlow, $dateStart, $dateEnd, $chartOfAccountModel);
        $detailAccountBasic     =   $chartOfAccountModel->getBasicDetailAccount($idAccount);
        $detailAccount          =   $chartOfAccountModel->getDetailAccount($detailAccountBasic['LEVEL'], $idAccount);

        unset($detailAccount['IDACCOUNT']);
        unset($detailAccount['IDACCOUNTPARENT']);

        return $this->setResponseFormat('json')
                    ->respond([
                        "detailAccount"         =>  $detailAccount,
                        "dataDetailsCashFlow"   =>  $dataDetailsCashFlow
                     ]);
    }
    
    public function excelDataCashFlow($encryptedParam)
    {
        helper(['firebaseJWT']);
        $mainOperation          =   new MainOperation();

		$arrParam               =	decodeJWTToken($encryptedParam);
		$dateRangeType          =	$arrParam->dateRangeType;
		$dateStartPeriod        =	$arrParam->dateStartPeriod;
		$dateEndPeriod          =	$arrParam->dateEndPeriod;
		$dateStartPeriodStr     =	$arrParam->dateStartPeriodStr;
		$dateEndPeriodStr       =	$arrParam->dateEndPeriodStr;
		$arrAccountCashFlowName =	$arrParam->arrAccountCashFlowName;
		$arrIdAccountsCashFlow  =	$arrParam->arrIdAccountsCashFlow;
        $periodType             =   '-';

        switch($dateRangeType){
            case 1 : $periodType    =   'This Month'; break;
            case 2 : $periodType    =   'Last Month'; break;
            case 3 : $periodType    =   'Year To Date'; break;
            case 4 : $periodType    =   'Year To Last Month'; break;
            case 5 : $periodType    =   'Last Year'; break;
            case 6 : $periodType    =   'Custom Date Range'; break;
        }
		
		$spreadsheet	    =	new Spreadsheet();
		$sheet			    =	$spreadsheet->getActiveSheet();
		$sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
		$sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
		$sheet->getPageMargins()->setTop(0.25)->setRight(0.2)->setLeft(0.2)->setBottom(0.25);
		
		$sheet->setCellValue('A1', 'Bali Sun Tours - Accounting');
		$sheet->setCellValue('A2', 'Data Cash Flow');
		$sheet->getStyle('A1:A2')->getFont()->setBold(true);
		$sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');
		$sheet->mergeCells('A1:D1')->mergeCells('A2:D2');
		$sheet->setCellValue('A4', 'Period'); $sheet->mergeCells('A4:B4');
        $sheet->setCellValue('C4', ": ".$periodType); $sheet->mergeCells('C4:D4');
		$sheet->setCellValue('A5', 'Date Range'); $sheet->mergeCells('A5:B5');
        $sheet->setCellValue('C5', ": ".$dateStartPeriodStr.' - '.$dateEndPeriodStr); $sheet->mergeCells('C5:D5');
		$sheet->setCellValue('A6', 'Account Cash'); $sheet->mergeCells('A6:B6');
        $rowNumberAccountCashList   =   6;

        foreach($arrAccountCashFlowName as $accountCashFlowName){
            $strAdditional  =   $rowNumberAccountCashList == 6 ? ": " : "  ";
            $sheet->setCellValue('C'.$rowNumberAccountCashList, $strAdditional.$accountCashFlowName);
            $sheet->mergeCells('C'.$rowNumberAccountCashList.':D'.$rowNumberAccountCashList);
            $rowNumberAccountCashList++;
        }

        $rowNumber              =   $firstRowNumber =   $rowNumberAccountCashList + 1;
        $dataSettingCashFlow    =   $mainOperation->getDataSystemSetting(3);
        $dataSettingCashFlow    =   json_decode($dataSettingCashFlow);
        $resultData             =  [];

        if(count(get_object_vars($dataSettingCashFlow)) > 0){
            $resultData =   $this->generateCashFlowResult(true, $dataSettingCashFlow, $arrIdAccountsCashFlow, $dateStartPeriod, $dateEndPeriod);
        }
            
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ];

        $styleArrayTopBottom    = [
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
                'horizontal' => [
                    'borderStyle' => Border::BORDER_THIN,
                ]
            ]
        ];

         $styleArrayLeft    = [
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ];

        $sheet->setCellValue('A'.$rowNumber, 'Account / Description'); $sheet->mergeCells('A'.$rowNumber.':C'.$rowNumber);
        $sheet->setCellValue('D'.$rowNumber, 'Nominal');
        $sheet->getStyle('D'.$rowNumber)->getAlignment()->setHorizontal('right');
        $sheet->getStyle('A'.$rowNumber.':D'.$rowNumber)->getFont()->setBold(true);
        $rowNumber++;

        if(count($resultData) > 0){
            foreach($resultData as $idxResult => $data){
                $this->generateExcelRowValue($sheet, $data, $rowNumber);
                $rowNumber++;
            }
        }
        
        $rowNumber  =   $rowNumber-1;
        $sheet->getStyle('A'.$firstRowNumber.':A'.$rowNumber)->applyFromArray($styleArrayLeft)->getAlignment()->setVertical('top')->setWrapText(true);
        $sheet->getStyle('A'.$firstRowNumber.':C'.$rowNumber)->applyFromArray($styleArrayTopBottom)->getAlignment()->setVertical('top')->setWrapText(true);
        $sheet->getStyle('D'.$firstRowNumber.':D'.$rowNumber)->applyFromArray($styleArray)->getAlignment()->setVertical('top')->setWrapText(true);
		$sheet->setBreak('A'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
		
		$sheet->getColumnDimension('A')->setWidth(6);
		$sheet->getColumnDimension('B')->setWidth(6);
		$sheet->getColumnDimension('C')->setWidth(60);
		$sheet->getColumnDimension('D')->setWidth(18);
		$sheet->setShowGridLines(false);
		
		$sheet->getPageSetup()->setFitToWidth(1)->setFitToHeight(0);

		$writer			=	new Xlsx($spreadsheet);
		$filename		=	'ExcelDataCashFlow_'.$periodType."_".$dateStartPeriodStr.'_'.$dateEndPeriodStr;
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
        die;
	}

    private function generateExcelRowValue($sheet, $objDataValue, $rowNumber){
        $idAccount      =   $objDataValue['idAccount'];
        $accountLevel   =   $objDataValue['accountLevel'];
        $accountCode    =   $objDataValue['accountCode'];
        $accountName    =   $objDataValue['accountName'];

        switch($accountLevel){
            case 1  :   $sheet->setCellValue('A'.$rowNumber, $accountName); $sheet->mergeCells('A'.$rowNumber.':C'.$rowNumber);
                        $sheet->getStyle('A'.$rowNumber.':D'.$rowNumber)->getFont()->setBold(true);
                        $sheet->getStyle('A'.$rowNumber.':D'.$rowNumber)->getFont()->getColor()->setARGB('428bfa');
                        break;
            case 2  :   $accountCodeName    =   $idAccount == "" ? $accountName : $accountCode." ".$accountName;
                        $sheet->setCellValue('B'.$rowNumber, $accountCodeName); $sheet->mergeCells('B'.$rowNumber.':C'.$rowNumber);
                        $sheet->getStyle('A'.$rowNumber.':D'.$rowNumber)->getFont()->setBold(true);
                        $sheet->getStyle('A'.$rowNumber.':D'.$rowNumber)->getFont()->getColor()->setARGB('17a2b8');
                        break;
            case 3  :   $sheet->setCellValue('C'.$rowNumber, $accountCode." ".$accountName); $sheet->mergeCells('A'.$rowNumber.':B'.$rowNumber);
                        break;
        }

        $sheet->setCellValue('D'.$rowNumber, $objDataValue['saldo']);
        return true;
    }
}