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
use App\Models\MainOperation;
use App\Models\ChartOfAccountModel;
use App\Models\BalanceSheetModel;
use App\Models\ProfitLossModel;

class BalanceSheet extends ResourceController
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
            'reportFormat'  =>  ['label' => 'Report Format', 'rules' => 'required|in_list[1,2]'],
            'month'         =>  ['label' => 'Month', 'rules' => 'required|in_list[01,02,03,04,05,06,07,08,09,10,11,12]'],
            'year'          =>  ['label' => 'Year', 'rules' => 'required|numeric|exact_length[4]']
        ];

        if(!$this->validate($rules)) return $this->fail($this->validator->getErrors());
        
        $reportFormat           =   $this->request->getVar('reportFormat');
        $month                  =   $this->request->getVar('month');
        $year                   =   $this->request->getVar('year');
        $dateStartPeriod        =   $year."-".$month."-01";
        $dateEndPeriod          =   date("Y-m-t", strtotime($dateStartPeriod));
        $dataAccountAssets      =   $this->getListAccount(1, $dateEndPeriod);
        $dataAccountLiabilities =   $this->getListAccount(2, $dateEndPeriod);
        $dataAccountEquity      =   $this->getListAccount(3, $dateEndPeriod);

        if(!is_null($dataAccountEquity) && count($dataAccountEquity) > 0){
            $mainOperation              =   new MainOperation();
            $idAccountCurrentProfitLoss =   $mainOperation->getDataSystemSetting(2);
            $idAccountCurrentProfitLoss =   $idAccountCurrentProfitLoss != '' && is_numeric($idAccountCurrentProfitLoss) ? filter_var($idAccountCurrentProfitLoss, FILTER_SANITIZE_NUMBER_INT) : 0;
            $totalAccountEquity         =   count($dataAccountEquity);
            $accountTotalNameStr        =   '';
            $saldoCurrentProfitLoss     =   0;
            foreach($dataAccountEquity as $indexAccountEquity => $keyAccountEquity){
                $idAccountEquity    =   hashidDecode($keyAccountEquity['idAccount']);
                $nameAccountEquity  =   $keyAccountEquity['accountName'];
                if($indexAccountEquity == 0) $accountTotalNameStr   =   'Total '.$nameAccountEquity;
                if($idAccountCurrentProfitLoss == $idAccountEquity){
                    $saldoCurrentProfitLoss     =   $this->getSaldoCurrentProfitLoss($dateEndPeriod);
                    $dataAccountEquity[$indexAccountEquity]['saldo']  =   $saldoCurrentProfitLoss;
                }

                if($indexAccountEquity == ($totalAccountEquity - 1) || $nameAccountEquity == $accountTotalNameStr){
                    $totalSaldoEquity   =   $keyAccountEquity['saldo'];
                    $dataAccountEquity[$indexAccountEquity]['saldo']  =   $totalSaldoEquity + $saldoCurrentProfitLoss;
                }
            }
        }

        $result                 =   [
            "dataAccountAssets"         =>  $dataAccountAssets,
            "dataAccountLiabilities"    =>  $dataAccountLiabilities,
            "dataAccountEquity"         =>  $dataAccountEquity
        ];

        $arrParamExcelData  =   [
            'reportFormat'  =>  $reportFormat,
            'year'          =>  $year,
            'month'         =>  $month,
            'dateEndPeriod' =>  $dateEndPeriod
        ];
        $urlExcelData       =   BASE_URL."balanceSheet/excelDataBalanceSheet/".encodeJWTToken($arrParamExcelData);

        return $this->setResponseFormat('json')
                    ->respond([
                        "result"        =>  $result,
                        "urlExcelData"  =>  $urlExcelData
                     ]);
    }

    private function getListAccount($idAccountGeneral, $dateEndPeriod, $isExcelFormat = false)
    {
        $chartOfAccountModel=   new ChartOfAccountModel();
        $balanceSheetModel  =   new BalanceSheetModel();

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
                    $saldoAccountSub    =   $balanceSheetModel->getSaldoAccount($idAccountSub, $dateEndPeriod);
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
                $totalSaldoMain                 =   $balanceSheetModel->getSaldoAccount($idAccountMain, $dateEndPeriod);
                $dataAccount[$iAccount]['saldo']=   $totalSaldoMain;
                $totalSaldoGeneral              +=  $totalSaldoMain;
            }

            $iAccount++;
            $totalAccountMain++;
        }

        if($totalAccountMain > 1){
            $dataAccount[]  =   [
                "idAccount"     =>  $isExcelFormat ? "" : hashidEncode(0),
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

    private function getSaldoCurrentProfitLoss($dateEndPeriod)
    {
        $dateStartPeriod                =   new \DateTime($dateEndPeriod);
        $dateStartPeriod->setDate($dateStartPeriod->format('Y'), 1, 1);
        $dateStartPeriod                =   $dateStartPeriod->format('Y-m-d');
        $saldoAccountOperatingRevenue   =   $this->getSaldoAccountGeneral(4, $dateStartPeriod, $dateEndPeriod);
        $saldoAccountOperatingExpense   =   $this->getSaldoAccountGeneral(5, $dateStartPeriod, $dateEndPeriod);
        $saldoAccountAdminCost          =   $this->getSaldoAccountGeneral(6, $dateStartPeriod, $dateEndPeriod);
        $saldoAccountDepreciationExpense=   $this->getSaldoAccountGeneral(7, $dateStartPeriod, $dateEndPeriod);
        $saldoAccountOtherRevenue       =   $this->getSaldoAccountGeneral(8, $dateStartPeriod, $dateEndPeriod);
        $saldoAccountOtherExpense       =   $this->getSaldoAccountGeneral(9, $dateStartPeriod, $dateEndPeriod);
        $saldoCurrentProfitLoss         =   $saldoAccountOperatingRevenue + $saldoAccountOtherRevenue - $saldoAccountOperatingExpense - $saldoAccountAdminCost - $saldoAccountDepreciationExpense - $saldoAccountOtherExpense;

        return $saldoCurrentProfitLoss;
    }

    private function getSaldoAccountGeneral($idAccountGeneral, $dateStartPeriod, $dateEndPeriod)
    {
        $chartOfAccountModel=   new ChartOfAccountModel();
        $profitLossModel    =   new ProfitLossModel();

        $dataAccountMain    =   $chartOfAccountModel->getDataAccountMain($idAccountGeneral, 0, '');
        $totalSaldoGeneral  =   0;

        foreach($dataAccountMain as $keyAccountMain){
            $idAccountMain  =   $keyAccountMain->IDACCOUNTMAIN;
            $dataAccountSub =   $chartOfAccountModel->getDataAccountSub($idAccountGeneral, $idAccountMain);

            if($dataAccountSub){
                foreach($dataAccountSub as $keyAccountSub){
                    $idAccountMainSub   =   $keyAccountSub->IDACCOUNTMAIN;
                    $idAccountSub       =   $keyAccountSub->IDACCOUNTSUB;
                    $saldoAccountSub    =   $profitLossModel->getSaldoAccount($idAccountSub, $dateStartPeriod, $dateEndPeriod);
                    if($idAccountMain == $idAccountMainSub){
                        $totalSaldoGeneral  +=  $saldoAccountSub;
                    }
                }
            } else {
                $totalSaldoMain     =   $profitLossModel->getSaldoAccount($idAccountMain, $dateStartPeriod, $dateEndPeriod);
                $totalSaldoGeneral  +=  $totalSaldoMain;
            }
        }

        return $totalSaldoGeneral;
    }
    
    public function excelDataBalanceSheet($encryptedParam)
    {
        helper(['firebaseJWT']);

		$arrParam           =	decodeJWTToken($encryptedParam);
		$reportFormat       =	$arrParam->reportFormat;
		$year               =	$arrParam->year;
		$month              =	$arrParam->month;
        $monthYear          =   Time::createFromFormat('Y-m', $year."-".$month);
        $monthYearStr       =   $monthYear->toLocalizedString('MMMM yyyy');
		$dateEndPeriod      =	$arrParam->dateEndPeriod;
        $reportFormatStr    =   '-';

        switch($reportFormat){
            case 1 : $reportFormatStr   =   'Skontro (Horizontal)'; break;
            case 2 : $reportFormatStr   =   'Stafel (Vertical)'; break;
        }
		
		$spreadsheet	    =	new Spreadsheet();
		$sheet			    =	$spreadsheet->getActiveSheet();
		if($reportFormat == 1) $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
		if($reportFormat == 2) $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
		$sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
		$sheet->getPageMargins()->setTop(0.25)->setRight(0.2)->setLeft(0.2)->setBottom(0.25);
		
		$sheet->setCellValue('A1', 'Bali Sun Tours - Accounting');
		$sheet->setCellValue('A2', 'Balance Sheet');
		$sheet->getStyle('A1:A2')->getFont()->setBold(true);
		$sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');
		if($reportFormat == 1) $sheet->mergeCells('A1:I1')->mergeCells('A2:I2');
		if($reportFormat == 2) $sheet->mergeCells('A1:D1')->mergeCells('A2:D2');

		$sheet->setCellValue('A4', 'Period'); $sheet->mergeCells('A4:B4');
        $sheet->setCellValue('C4', ": ".$monthYearStr);
        if($reportFormat == 1) $sheet->mergeCells('C4:I4');
        if($reportFormat == 2) $sheet->mergeCells('C4:D4');
		
        $sheet->setCellValue('A5', 'Format'); $sheet->mergeCells('A5:B5');
        $sheet->setCellValue('C5', ": ".$reportFormatStr);
        if($reportFormat == 1) $sheet->mergeCells('C5:I5');
        if($reportFormat == 2) $sheet->mergeCells('C5:D5');
        $rowNumberLeft  =   $rowNumberRight =   $firstRowNumber =   7;

        $dataAccountAssets      =   $this->getListAccount(1, $dateEndPeriod, true);
        $dataAccountLiabilities =   $this->getListAccount(2, $dateEndPeriod, true);
        $dataAccountEquity      =   $this->getListAccount(3, $dateEndPeriod, true);
        $totalSaldoAssets       =   $totalSaldoLiabilities = $totalSaldoEquity = 0;
            
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

        if(count($dataAccountAssets) > 0){
            foreach($dataAccountAssets as $idxAccountAssets => $accountAssets){
                $this->generateExcelRowValue($sheet, $accountAssets, $rowNumberLeft, 'L', $reportFormat);
                if($idxAccountAssets == count($dataAccountAssets) - 1) $totalSaldoAssets = $accountAssets['saldo'];
                $rowNumberLeft++;
            }
        }

        $positionLiabilitiesEquity  =   'R';
        if($reportFormat == 2) {
            $this->generateExcelTotalSaldoRow($sheet, $rowNumberLeft, 'Saldo Assets', $totalSaldoAssets, 'L', $reportFormat);
            $rowNumberLeft++;
            $this->generateExcelEmptyRow($sheet, $rowNumberLeft, 'L', $reportFormat);
            $rowNumberRight             =   $rowNumberLeft + 1;
            $positionLiabilitiesEquity  =   'L';
            $this->generateExcelEmptyRow($sheet, $rowNumberRight, 'L', $reportFormat);
        } else {
            $this->generateExcelTotalSaldoRow($sheet, $rowNumberLeft, 'Saldo Assets', $totalSaldoAssets, 'L', $reportFormat);
        }

        if(count($dataAccountLiabilities) > 0){
            foreach($dataAccountLiabilities as $idxAccountLiabilities => $accountLiabilities){
                $this->generateExcelRowValue($sheet, $accountLiabilities, $rowNumberRight, $positionLiabilitiesEquity, $reportFormat);
                if($idxAccountLiabilities == count($accountLiabilities) - 1) $totalSaldoLiabilities = $accountLiabilities['saldo'];
                $rowNumberRight++;
            }
        }

        $this->generateExcelEmptyRow($sheet, $rowNumberRight, $positionLiabilitiesEquity, $reportFormat);
        $rowNumberRight++;

        if(count($dataAccountEquity) > 0){
            foreach($dataAccountEquity as $idxAccountEquity => $accountEquity){
                $this->generateExcelRowValue($sheet, $accountEquity, $rowNumberRight, $positionLiabilitiesEquity, $reportFormat);
                if($idxAccountEquity == count($dataAccountEquity) - 1) $totalSaldoEquity = $accountEquity['saldo'];
                $rowNumberRight++;
            }
        }

        $this->generateExcelEmptyRow($sheet, $rowNumberRight, $positionLiabilitiesEquity, $reportFormat);
        $rowNumberRight++;

        $lastRowCell    =   $rowNumberRight > $rowNumberLeft ? $rowNumberRight : $rowNumberLeft;
        $lastRowCell    =   $reportFormat == 2 ? $lastRowCell - 1 : $lastRowCell;
        $this->generateExcelTotalSaldoRow($sheet, $lastRowCell, 'Saldo Liabilities & Equity', ($totalSaldoLiabilities + $totalSaldoEquity), $positionLiabilitiesEquity, $reportFormat);
        
        $sheet->getStyle('A'.$firstRowNumber.':A'.$lastRowCell)->applyFromArray($styleArrayLeft)->getAlignment()->setVertical('top')->setWrapText(true);
        $sheet->getStyle('A'.$firstRowNumber.':C'.$lastRowCell)->applyFromArray($styleArrayTopBottom)->getAlignment()->setVertical('top')->setWrapText(true);
        $sheet->getStyle('D'.$firstRowNumber.':D'.$lastRowCell)->applyFromArray($styleArray)->getAlignment()->setVertical('top')->setWrapText(true);
		
        if($reportFormat == 2){
            $sheet->setBreak('A'.$lastRowCell, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
        } else {
            $sheet->setBreak('I'.$lastRowCell, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
            $sheet->getStyle('F'.$firstRowNumber.':F'.$lastRowCell)->applyFromArray($styleArrayLeft)->getAlignment()->setVertical('top')->setWrapText(true);
            $sheet->getStyle('F'.$firstRowNumber.':H'.$lastRowCell)->applyFromArray($styleArrayTopBottom)->getAlignment()->setVertical('top')->setWrapText(true);
            $sheet->getStyle('I'.$firstRowNumber.':I'.$lastRowCell)->applyFromArray($styleArray)->getAlignment()->setVertical('top')->setWrapText(true);

            if($rowNumberLeft > $rowNumberRight){
                $sheet->mergeCells('F'.($rowNumberRight - 1).':I'.($lastRowCell - 1));
            } else {
                $sheet->mergeCells('A'.($rowNumberRight - 1).':D'.($lastRowCell - 1));
            }
        }
		
		$sheet->getColumnDimension('A')->setWidth(6);
		$sheet->getColumnDimension('B')->setWidth(6);
		$sheet->getColumnDimension('C')->setWidth(60);
		$sheet->getColumnDimension('D')->setWidth(18);

        if($reportFormat == 1){
            $sheet->getColumnDimension('E')->setWidth(6);
            $sheet->getColumnDimension('F')->setWidth(6);
            $sheet->getColumnDimension('G')->setWidth(6);
            $sheet->getColumnDimension('H')->setWidth(60);
            $sheet->getColumnDimension('I')->setWidth(18);
        }

		$sheet->setShowGridLines(false);
		$sheet->getPageSetup()->setFitToWidth(1)->setFitToHeight(0);

		$writer			=	new Xlsx($spreadsheet);
		$filename		=	'ExcelDataBalanceSheet_'.$monthYearStr."_".$reportFormatStr;
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
        die;
	}

    private function generateExcelRowValue($sheet, $objDataValue, $rowNumber, $position, $reportFormat){
        $column1    =   $column2 = $column3 = $column4 = "A";
        switch($position){
            case "L" :  $column1    =   "A";
                        $column2    =   "B";
                        $column3    =   "C";
                        $column4    =   "D";
                        break;
            case "R" :  $column1    =   $reportFormat == 1 ? "F" : "A";
                        $column2    =   $reportFormat == 1 ? "G" : "B";
                        $column3    =   $reportFormat == 1 ? "H" : "C";
                        $column4    =   $reportFormat == 1 ? "I" : "D";
                        break;
        }

        $accountLevel   =   $objDataValue['accountLevel'];
        $accountCode    =   $objDataValue['accountCode'];
        $accountName    =   $objDataValue['accountName'];
        $accountSaldo   =   $objDataValue['saldo'];

        switch($accountLevel){
            case 1  :   $sheet->setCellValue($column1.$rowNumber, $accountName);
                        if($accountSaldo == ""){
                            $sheet->mergeCells($column1.$rowNumber.':'.$column4.$rowNumber);
                        } else {
                            $sheet->mergeCells($column1.$rowNumber.':'.$column3.$rowNumber);
                        }
                        $sheet->getStyle($column1.$rowNumber.':'.$column4.$rowNumber)->getFont()->setBold(true);
                        $sheet->getStyle($column1.$rowNumber.':'.$column4.$rowNumber)->getFont()->getColor()->setARGB('428bfa');
                        break;
            case 2  :   $sheet->setCellValue($column2.$rowNumber, $accountCode." ".$accountName); $sheet->mergeCells($column2.$rowNumber.':'.$column3.$rowNumber);
                        $sheet->getStyle($column1.$rowNumber.':'.$column4.$rowNumber)->getFont()->setBold(true);
                        $sheet->getStyle($column1.$rowNumber.':'.$column4.$rowNumber)->getFont()->getColor()->setARGB('17a2b8');
                        break;
            case 3  :   $sheet->setCellValue($column3.$rowNumber, $accountCode." ".$accountName); $sheet->mergeCells($column1.$rowNumber.':'.$column2.$rowNumber);
                        break;
        }

        $sheet->setCellValue($column4.$rowNumber, $accountSaldo);
        $sheet->getStyle($column4.$rowNumber)->getNumberFormat()->setFormatCode('#,##0');
        return true;
    }

    private function generateExcelTotalSaldoRow($sheet, $rowNumber, $strSaldoText, $saldoValue, $position, $reportFormat){
        $column1    =   $column2 = $column3 = $column4 = "A";
        switch($position){
            case "L" :  $column1    =   "A";
                        $column3    =   "C";
                        $column4    =   "D";
                        break;
            case "R" :  $column1    =   $reportFormat == 1 ? "F" : "A";
                        $column3    =   $reportFormat == 1 ? "H" : "C";
                        $column4    =   $reportFormat == 1 ? "I" : "D";
                        break;
        }

        $sheet->setCellValue($column1.$rowNumber, $strSaldoText);  $sheet->mergeCells($column1.$rowNumber.':'.$column3.$rowNumber);
        $sheet->setCellValue($column4.$rowNumber, $saldoValue);
        $sheet->getStyle($column4.$rowNumber)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle($column1.$rowNumber.':'.$column4.$rowNumber)->getFont()->setBold(true);
        return true;
    }

    private function generateExcelEmptyRow($sheet, $rowNumber, $position, $reportFormat){
        $column1    =   $column2 = $column3 = $column4 = "A";
        switch($position){
            case "L" :  $column1    =   "A";
                        $column4    =   "D";
                        break;
            case "R" :  $column1    =   $reportFormat == 1 ? "F" : "A";
                        $column4    =   $reportFormat == 1 ? "I" : "D";
                        break;
        }
        $sheet->setCellValue($column1.$rowNumber, '');  $sheet->mergeCells($column1.$rowNumber.':'.$column4.$rowNumber);
        return true;
    }
    
}