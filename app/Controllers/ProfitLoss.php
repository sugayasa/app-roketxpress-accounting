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
use App\Models\ProfitLossModel;

class ProfitLoss extends ResourceController
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
            'dateRangeType' =>  ['label' => 'Date Range Type', 'rules' => 'required|in_list[1,2,3,4,5,6]'],
            'dateStart'     =>  ['label' => 'Date Range Start', 'rules' => 'required|exact_length[10]|valid_date[d-m-Y]'],
            'dateEnd'       =>  ['label' => 'Date Range End', 'rules' => 'required|exact_length[10]|valid_date[d-m-Y]']
        ];

        if(!$this->validate($rules)) return $this->fail($this->validator->getErrors());
        
        $dateRangeType                  =   $this->request->getVar('dateRangeType');
        $dateStartPeriodStr             =   $this->request->getVar('dateStart');
        $dateStartPeriodTF              =   Time::createFromFormat('d-m-Y', $dateStartPeriodStr);
        $dateStartPeriod                =   $dateStartPeriodTF->toDateString();
        $dateEndPeriodStr               =   $this->request->getVar('dateEnd');
        $dateEndPeriodTF                =   Time::createFromFormat('d-m-Y', $dateEndPeriodStr);
        $dateEndPeriod                  =   $dateEndPeriodTF->toDateString();
        $dataAccountOperatingRevenue    =   $this->getListAccount(4, $dateStartPeriod, $dateEndPeriod);
        $dataAccountOtherRevenue        =   $this->getListAccount(8, $dateStartPeriod, $dateEndPeriod);
        $dataAccountOperatingExpense    =   $this->getListAccount(5, $dateStartPeriod, $dateEndPeriod);
        $dataAccountAdminCost           =   $this->getListAccount(6, $dateStartPeriod, $dateEndPeriod);
        $dataAccountDepreciationExpense =   $this->getListAccount(7, $dateStartPeriod, $dateEndPeriod);
        $dataAccountOtherExpense        =   $this->getListAccount(9, $dateStartPeriod, $dateEndPeriod);

        $result =   [
            "dataAccountOperatingRevenue"   =>  $dataAccountOperatingRevenue,
            "dataAccountOtherRevenue"       =>  $dataAccountOtherRevenue,
            "dataAccountOperatingExpense"   =>  $dataAccountOperatingExpense,
            "dataAccountAdminCost"          =>  $dataAccountAdminCost,
            "dataAccountDepreciationExpense"=>  $dataAccountDepreciationExpense,
            "dataAccountOtherExpense"       =>  $dataAccountOtherExpense
        ];

        $arrParamExcelData  =   [
            'dateRangeType'     =>  $dateRangeType,
            'dateStartPeriod'   =>  $dateStartPeriod,
            'dateEndPeriod'     =>  $dateEndPeriod,
            'dateStartPeriodStr'=>  $dateStartPeriodStr,
            'dateEndPeriodStr'  =>  $dateEndPeriodStr,
        ];
        $urlExcelData   =   BASE_URL."profitLoss/excelDataProfitLoss/".encodeJWTToken($arrParamExcelData);

        return $this->setResponseFormat('json')
                    ->respond([
                        "result"        =>  $result,
                        "urlExcelData"  =>  $urlExcelData
                     ]);
    }

    private function getListAccount($idAccountGeneral, $dateStartPeriod, $dateEndPeriod, $isExcelFormat = false)
    {
        $chartOfAccountModel=   new ChartOfAccountModel();
        $profitLossModel    =   new ProfitLossModel();

        $dataAccountMain    =   $chartOfAccountModel->getDataAccountMain($idAccountGeneral, 0, '');
        $dataAccountGeneral =   $chartOfAccountModel->getDataAccountGeneral($idAccountGeneral, '', [$idAccountGeneral]);
        $dataAccount        =   [];
        $accountCodeGeneral =   $accountNameGeneral =   "";
        $totalAccountMain   =   0;
        $totalSaldoGeneral  =   0;
        $iAccount           =   0;

        foreach($dataAccountGeneral as $keyAccountGeneral){
            $dataAccount[]  =   [
                "idAccount"     =>  $isExcelFormat ? "" : hashidEncode($keyAccountGeneral->IDACCOUNTGENERAL),
                "accountLevel"  =>  1,
                "accountCode"   =>  $keyAccountGeneral->ACCOUNTCODE,
                "accountName"   =>  $keyAccountGeneral->ACCOUNTNAME,
                "textBoldClass" =>  "text-primary",
                "saldo"         =>  ""
            ];
            $accountCodeGeneral =   $keyAccountGeneral->ACCOUNTCODE;
            $accountNameGeneral =   $keyAccountGeneral->ACCOUNTNAME;
            $iAccount++;
        }

        foreach($dataAccountMain as $keyAccountMain){
            $idAccountMain  =   $keyAccountMain->IDACCOUNTMAIN;
            $accountCodeMain=   $keyAccountMain->ACCOUNTCODE;
            $totalAccountSub=   0;
            $totalSaldoMain =   0;
            $dataAccountSub =   $chartOfAccountModel->getDataAccountSub($idAccountGeneral, $idAccountMain);
            $dataAccount[]  =   [
                "idAccount"     =>  $isExcelFormat ? "" : hashidEncode($idAccountMain),
                "accountLevel"  =>  2,
                "accountCode"   =>  $accountCodeGeneral."-".$keyAccountMain->ACCOUNTCODE,
                "accountName"   =>  $keyAccountMain->ACCOUNTNAME,
                "textBoldClass" =>  "text-info",
                "saldo"         =>  ""
            ];

            if($dataAccountSub){
                foreach($dataAccountSub as $keyAccountSub){
                    $idAccountMainSub   =   $keyAccountSub->IDACCOUNTMAIN;
                    $idAccountSub       =   $keyAccountSub->IDACCOUNTSUB;
                    $saldoAccountSub    =   $profitLossModel->getSaldoAccount($idAccountSub, $dateStartPeriod, $dateEndPeriod);
                    if($idAccountMain == $idAccountMainSub){
                        $dataAccount[]  =   [
                            "idAccount"     =>  $isExcelFormat ? "" : hashidEncode($idAccountSub),
                            "accountLevel"  =>  3,
                            "accountCode"   =>  $accountCodeGeneral."-".$accountCodeMain."-".$keyAccountSub->ACCOUNTCODE,
                            "accountName"   =>  $keyAccountSub->ACCOUNTNAME,
                            "textBoldClass" =>  "",
                            "saldo"         =>  $saldoAccountSub
                        ];
                        $totalSaldoMain     +=  $saldoAccountSub;
                        $totalSaldoGeneral  +=  $saldoAccountSub;
                        $totalAccountSub++;
                        $iAccount++;
                    }
                }
                
                if($totalAccountSub > 1){
                    $dataAccount[]  =   [
                        "idAccount"     =>  $isExcelFormat ? "" : hashidEncode(0),
                        "accountLevel"  =>  2,
                        "accountCode"   =>  "",
                        "accountName"   =>  "Total ".$keyAccountMain->ACCOUNTNAME,
                        "textBoldClass" =>  "text-info",
                        "saldo"         =>  $totalSaldoMain
                    ];

                    $iAccount++;
                }
            } else {
                $totalSaldoMain                 =   $profitLossModel->getSaldoAccount($idAccountMain, $dateStartPeriod, $dateEndPeriod);
                $dataAccount[$iAccount]['saldo']=   $totalSaldoMain;
                $totalSaldoGeneral              +=  $totalSaldoMain;
            }

            $iAccount++;
            $totalAccountMain++;
        }

        if($totalAccountMain > 1){
            $dataAccount[]  =   [
                "idAccount"     =>  0,
                "accountLevel"  =>  1,
                "accountCode"   =>  "",
                "accountName"   =>  "Total ".$accountNameGeneral,
                "textBoldClass" =>  "text-primary",
                "saldo"         =>  $totalSaldoGeneral
            ];
            $iAccount++;
        }

        return $dataAccount;
    }
    
    public function excelDataProfitLoss($encryptedParam)
    {
        helper(['firebaseJWT']);

		$arrParam           =	decodeJWTToken($encryptedParam);
		$dateRangeType      =	$arrParam->dateRangeType;
		$dateStartPeriod    =	$arrParam->dateStartPeriod;
		$dateEndPeriod      =	$arrParam->dateEndPeriod;
		$dateStartPeriodStr =	$arrParam->dateStartPeriodStr;
		$dateEndPeriodStr   =	$arrParam->dateEndPeriodStr;
        $periodType         =   '-';

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
		$sheet->setCellValue('A2', 'Data Profit Loss');
		$sheet->getStyle('A1:A2')->getFont()->setBold(true);
		$sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');
		$sheet->mergeCells('A1:D1')->mergeCells('A2:D2');
		$sheet->setCellValue('A4', 'Period'); $sheet->mergeCells('A4:B4');
        $sheet->setCellValue('C4', ": ".$periodType); $sheet->mergeCells('C4:D4');
		$sheet->setCellValue('A5', 'Date Range'); $sheet->mergeCells('A5:B5');
        $sheet->setCellValue('C5', ": ".$dateStartPeriodStr.' - '.$dateEndPeriodStr); $sheet->mergeCells('C5:D5');
        $rowNumber  =   $firstRowNumber =   7;

        $dataAccountOperatingRevenue    =   $this->getListAccount(4, $dateStartPeriod, $dateEndPeriod, true);
        $dataAccountOtherRevenue        =   $this->getListAccount(8, $dateStartPeriod, $dateEndPeriod, true);
        $dataAccountOperatingExpense    =   $this->getListAccount(5, $dateStartPeriod, $dateEndPeriod, true);
        $dataAccountAdminCost           =   $this->getListAccount(6, $dateStartPeriod, $dateEndPeriod, true);
        $dataAccountDepreciationExpense =   $this->getListAccount(7, $dateStartPeriod, $dateEndPeriod, true);
        $dataAccountOtherExpense        =   $this->getListAccount(9, $dateStartPeriod, $dateEndPeriod, true);
        $totalSaldoOperatingRevenue = $totalSaldoOtherRevenue = $totalSaldoOperatingExpense = $totalSaldoAdminCost = $totalSaldoDepreciationExpense = $totalSaldoOtherExpense = 0;
            
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
        $sheet->setCellValue('D'.$rowNumber, 'Saldo');
        $sheet->getStyle('D'.$rowNumber)->getAlignment()->setHorizontal('right');
        $sheet->getStyle('A'.$rowNumber.':D'.$rowNumber)->getFont()->setBold(true);
        $rowNumber++;

        if(count($dataAccountOperatingRevenue) > 0){
            foreach($dataAccountOperatingRevenue as $idxAccountOperatingRevenue => $accountOperatingRevenue){
                $this->generateExcelRowValue($sheet, $accountOperatingRevenue, $rowNumber);
                if($idxAccountOperatingRevenue == count($dataAccountOperatingRevenue) - 1) $totalSaldoOperatingRevenue = $accountOperatingRevenue['saldo'];
                $rowNumber++;
            }
        }

        $this->generateExcelEmptyRow($sheet, $rowNumber);
        $rowNumber++;

        if(count($dataAccountOperatingExpense) > 0){
            foreach($dataAccountOperatingExpense as $idxAccountOperatingExpense => $accountOperatingExpense){
                $this->generateExcelRowValue($sheet, $accountOperatingExpense, $rowNumber);
                if($idxAccountOperatingExpense == count($dataAccountOperatingExpense) - 1) $totalSaldoOperatingExpense = $accountOperatingExpense['saldo'];
                $rowNumber++;
            }
        }

        $this->generateExcelTotalSaldoRow($sheet, $rowNumber, 'Gross Profit/Loss', ($totalSaldoOperatingRevenue - $totalSaldoOperatingExpense));
        $rowNumber++;
        $this->generateExcelEmptyRow($sheet, $rowNumber);
        $rowNumber++;

        if(count($dataAccountAdminCost) > 0){
            foreach($dataAccountAdminCost as $idxAccountAdminCost => $accountAdminCost){
                $this->generateExcelRowValue($sheet, $accountAdminCost, $rowNumber);
                if($idxAccountAdminCost == count($dataAccountAdminCost) - 1) $totalSaldoAdminCost = $accountAdminCost['saldo'];
                $rowNumber++;
            }
        }

        $this->generateExcelEmptyRow($sheet, $rowNumber);
        $rowNumber++;

        if(count($dataAccountDepreciationExpense) > 0){
            foreach($dataAccountDepreciationExpense as $idxAccountDepreciationExpense => $accountDepreciationExpense){
                $this->generateExcelRowValue($sheet, $accountDepreciationExpense, $rowNumber);
                if($idxAccountDepreciationExpense == count($dataAccountDepreciationExpense) - 1) $totalSaldoDepreciationExpense = $accountDepreciationExpense['saldo'];
                $rowNumber++;
            }
        }

        $this->generateExcelEmptyRow($sheet, $rowNumber);
        $rowNumber++;

        if(count($dataAccountOtherExpense) > 0){
            foreach($dataAccountOtherExpense as $idxAccountOtherExpense => $accountOtherExpense){
                $this->generateExcelRowValue($sheet, $accountOtherExpense, $rowNumber);
                if($idxAccountOtherExpense == count($dataAccountOtherExpense) - 1) $totalSaldoOtherExpense = $accountOtherExpense['saldo'];
                $rowNumber++;
            }
        }

        $this->generateExcelTotalSaldoRow($sheet, $rowNumber, 'Operational Profit/Loss', ($totalSaldoOperatingRevenue - $totalSaldoOperatingExpense - $totalSaldoAdminCost - $totalSaldoDepreciationExpense - $totalSaldoOtherExpense));
        $rowNumber++;
        $this->generateExcelEmptyRow($sheet, $rowNumber);
        $rowNumber++;

        if(count($dataAccountOtherRevenue) > 0){
            foreach($dataAccountOtherRevenue as $idxAccountOtherRevenue => $accountOtherRevenue){
                $this->generateExcelRowValue($sheet, $accountOtherRevenue, $rowNumber);
                if($idxAccountOtherRevenue == count($dataAccountOtherRevenue) - 1) $totalSaldoOtherRevenue = $accountOtherRevenue['saldo'];
                $rowNumber++;
            }
        }

        $this->generateExcelTotalSaldoRow($sheet, $rowNumber, 'Net Profit/Loss', ($totalSaldoOperatingRevenue + $totalSaldoOtherRevenue - $totalSaldoOperatingExpense - $totalSaldoAdminCost - $totalSaldoDepreciationExpense - $totalSaldoOtherExpense));
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
		$filename		=	'ExcelDataProfitLoss_'.$periodType."_".$dateStartPeriodStr.'_'.$dateEndPeriodStr;
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
        die;
	}

    private function generateExcelRowValue($sheet, $objDataValue, $rowNumber){
        $accountLevel   =   $objDataValue['accountLevel'];
        $accountCode    =   $objDataValue['accountCode'];
        $accountName    =   $objDataValue['accountName'];

        switch($accountLevel){
            case 1  :   $sheet->setCellValue('A'.$rowNumber, $accountName); $sheet->mergeCells('A'.$rowNumber.':C'.$rowNumber);
                        $sheet->getStyle('A'.$rowNumber.':D'.$rowNumber)->getFont()->setBold(true);
                        $sheet->getStyle('A'.$rowNumber.':D'.$rowNumber)->getFont()->getColor()->setARGB('428bfa');
                        break;
            case 2  :   $sheet->setCellValue('B'.$rowNumber, $accountCode." ".$accountName); $sheet->mergeCells('B'.$rowNumber.':C'.$rowNumber);
                        $sheet->getStyle('A'.$rowNumber.':D'.$rowNumber)->getFont()->setBold(true);
                        $sheet->getStyle('A'.$rowNumber.':D'.$rowNumber)->getFont()->getColor()->setARGB('17a2b8');
                        break;
            case 3  :   $sheet->setCellValue('C'.$rowNumber, $accountCode." ".$accountName); $sheet->mergeCells('A'.$rowNumber.':B'.$rowNumber);
                        break;
        }

        $sheet->setCellValue('D'.$rowNumber, $objDataValue['saldo']);
        return true;
    }

    private function generateExcelTotalSaldoRow($sheet, $rowNumber, $strSaldoText, $saldoValue){
        $sheet->setCellValue('A'.$rowNumber, $strSaldoText);  $sheet->mergeCells('A'.$rowNumber.':C'.$rowNumber);
        $sheet->setCellValue('D'.$rowNumber, $saldoValue);
        $sheet->getStyle('A'.$rowNumber.':D'.$rowNumber)->getFont()->setBold(true);
        return true;
    }

    private function generateExcelEmptyRow($sheet, $rowNumber){
        $sheet->setCellValue('A'.$rowNumber, '');  $sheet->mergeCells('A'.$rowNumber.':C'.$rowNumber);
        return true;
    }
}