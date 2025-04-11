<?php

namespace App\Controllers;

use App\Models\ChartOfAccountModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use CodeIgniter\I18n\Time;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\RichText\Run;
use App\Models\GeneralLedgerModel;

class GeneralLedger extends ResourceController
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
            'datePeriodStart'   =>  ['label' => 'Transaction Date', 'rules' => 'required|exact_length[10]|valid_date[d-m-Y]'],
            'datePeriodEnd'     =>  ['label' => 'Transaction Date', 'rules' => 'required|exact_length[10]|valid_date[d-m-Y]']
        ];

        if(!$this->validate($rules)) return $this->fail($this->validator->getErrors());
        
        $generalLedgerModel =   new GeneralLedgerModel();
        $arrIdAccount       =   $this->request->getVar('arrIdAccount');
        $arrNameAccount     =   $this->request->getVar('arrNameAccount');
        $datePeriodStart    =   $this->request->getVar('datePeriodStart');
        $datePeriodStartTF  =   Time::createFromFormat('d-m-Y', $datePeriodStart);
        $datePeriodStart    =   $datePeriodStartTF->toDateString();
        $datePeriodEnd      =   $this->request->getVar('datePeriodEnd');
        $datePeriodEndTF    =   Time::createFromFormat('d-m-Y', $datePeriodEnd);
        $datePeriodEnd      =   $datePeriodEndTF->toDateString();
        $iAccount           =   0;
        $result             =   $arrIdAccountDecode =   [];
        $urlExcelData       =   "";

        if(!is_array($arrIdAccount) || count($arrIdAccount) <= 0) return throwResponseNotAcceptable("Please select at least 1 account");
        foreach($arrIdAccount as $idAccount){
            $idAccount              =   hashidDecode($idAccount);
            $arrIdAccountDecode[]   =   $idAccount;
            $beginningBalanceData   =   $generalLedgerModel->getBeginningBalance($idAccount, $datePeriodStart);
            $transactionData        =   $generalLedgerModel->getTransactionData($idAccount, $datePeriodStart, $datePeriodEnd);
            $result[]               =   [
                'ACCOUNTNAME'           =>  $arrNameAccount[$iAccount],
                'BEGINNINGBALANCEDATA'  =>  $beginningBalanceData,
                'TRANSACTIONDATA'       =>  $transactionData
            ];
            $iAccount++;
        }

        $arrParamExcelData  =   [
            'arrIdAccount'      =>  $arrIdAccountDecode,
            'arrNameAccount'    =>  $arrNameAccount,
            'datePeriodStart'   =>  $datePeriodStart,
            'datePeriodEnd'     =>  $datePeriodEnd
        ];
        $urlExcelData           =   BASE_URL."generalLedger/excelDataLedger/".encodeJWTToken($arrParamExcelData);

        return $this->setResponseFormat('json')
                    ->respond([
                        "result"        =>  $result,
                        "urlExcelData"  =>  $urlExcelData
                     ]);
    }

    public function getDataPerAccountPeriod()
    {
        helper(['form']);
        $rules      =   [
            'idAccount' =>  ['label' => 'Account Code', 'rules' => 'required|alpha_numeric'],
            'dateStart' =>  ['label' => 'Transaction Date', 'rules' => 'required|exact_length[10]|valid_date[d-m-Y]'],
            'dateEnd'   =>  ['label' => 'Transaction Date', 'rules' => 'required|exact_length[10]|valid_date[d-m-Y]']
        ];

        $messages   =   [
            'idAccount' => [
                'required'      => 'Invalid data sent',
                'alpha_numeric' => 'Invalid data sent'
            ]
        ];

        if(!$this->validate($rules, $messages)) return $this->fail($this->validator->getErrors());
        
        $generalLedgerModel =   new GeneralLedgerModel();
        $chartOfAccountModel=   new ChartOfAccountModel();
        $idAccount          =   $this->request->getVar('idAccount');
        $idAccount          =   hashidDecode($idAccount);
        $dateStart          =   $this->request->getVar('dateStart');
        $dateStartTF        =   Time::createFromFormat('d-m-Y', $dateStart);
        $dateStart          =   $dateStartTF->toDateString();
        $dateEnd            =   $this->request->getVar('dateEnd');
        $dateEnddTF         =   Time::createFromFormat('d-m-Y', $dateEnd);
        $dateEnd            =   $dateEnddTF->toDateString();

        $beginningBalanceData   =   $generalLedgerModel->getBeginningBalance($idAccount, $dateStart);
        $transactionData        =   $generalLedgerModel->getTransactionData($idAccount, $dateStart, $dateEnd);
        $detailAccount          =   $chartOfAccountModel->getDetailAccount($beginningBalanceData['LEVEL'], $idAccount);
        unset($detailAccount['IDACCOUNT']);
        unset($detailAccount['IDACCOUNTPARENT']);

        return $this->setResponseFormat('json')
                    ->respond([
                        "detailAccount"         =>  $detailAccount,
                        "beginningBalanceData"  =>  $beginningBalanceData,
                        "transactionData"       =>  $transactionData
                     ]);
    }
    
    public function excelDataLedger($encryptedParam)
    {
        helper(['firebaseJWT']);

        $generalLedgerModel =   new GeneralLedgerModel();
		$arrParam           =	decodeJWTToken($encryptedParam);
		$arrIdAccount       =	$arrParam->arrIdAccount;
		$arrNameAccount     =	$arrParam->arrNameAccount;
		$datePeriodStart    =	$arrParam->datePeriodStart;
		$datePeriodEnd      =	$arrParam->datePeriodEnd;
		
		$spreadsheet	    =	new Spreadsheet();
		$sheet			    =	$spreadsheet->getActiveSheet();
		$sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
		$sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
		$sheet->getPageMargins()->setTop(0.25)->setRight(0.2)->setLeft(0.2)->setBottom(0.25);
		
		$sheet->setCellValue('A1', 'Bali Sun Tours - Accounting');
		$sheet->setCellValue('A2', 'Data General Ledger');
		$sheet->getStyle('A1:A2')->getFont()->setBold(true);
		$sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');
		$sheet->mergeCells('A1:F1')->mergeCells('A2:F2');
		$sheet->setCellValue('A4', 'Date Period');  $sheet->setCellValue('B4', ": ".$datePeriodStart.' - '.$datePeriodEnd); $sheet->mergeCells('B4:F4');
        $rowNumber  =   6;
        $iAccount   =   0;
        $grandTotalDebit    =   $grandTotalCredit   =   0;
            
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ];

        foreach($arrIdAccount as $idAccount){
            $firstRowNumber =   $rowNumber;
    		$sheet->setCellValue('A'.$rowNumber, $arrNameAccount[$iAccount]);  $sheet->getStyle('A'.$rowNumber)->getFont()->setBold(true);  $sheet->mergeCells('A'.$rowNumber.':F'.$rowNumber);
            $iAccount++;
            $rowNumber++;

            $sheet->setCellValue('A'.$rowNumber, 'Date Transaction');
            $sheet->setCellValue('B'.$rowNumber, 'Reff Number');
            $sheet->setCellValue('C'.$rowNumber, 'Description');  $sheet->mergeCells('C'.$rowNumber.':D'.$rowNumber);
            $sheet->setCellValue('E'.$rowNumber, 'Debit');
            $sheet->setCellValue('F'.$rowNumber, 'Credit');
            
            $sheet->getStyle('A'.$rowNumber.':F'.$rowNumber)->getFont()->setBold(true);
            $sheet->getStyle('A'.$rowNumber.':F'.$rowNumber)->getAlignment()->setVertical('center');
            $sheet->getStyle('B'.$rowNumber.':D'.$rowNumber)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('E'.$rowNumber.':F'.$rowNumber)->getAlignment()->setHorizontal('right');
            $rowNumber++;

            $idAccount              =   $idAccount;
            $beginningBalanceData   =   $generalLedgerModel->getBeginningBalance($idAccount, $datePeriodStart);
            $transactionData        =   $generalLedgerModel->getTransactionData($idAccount, $datePeriodStart, $datePeriodEnd);
            $defaultDRCR            =   $beginningBalanceData['DEFAULTDRCR'];
            $beginningBalance       =   $beginningBalanceData['BEGINNINGBALANCE'];
            $beginningBalanceDR     =   $defaultDRCR == 'DR' && $beginningBalance > 0 ? $beginningBalance : '';
            $beginningBalanceCR     =   $defaultDRCR == 'CR' && $beginningBalance <= 0 ? $beginningBalance : '';

            $sheet->setCellValue('A'.$rowNumber, 'Beginning Balance');  $sheet->mergeCells('A'.$rowNumber.':D'.$rowNumber);
            $sheet->setCellValue('E'.$rowNumber, $beginningBalanceDR);
            $sheet->setCellValue('F'.$rowNumber, $beginningBalanceCR);
            $sheet->getStyle('A'.$rowNumber.':F'.$rowNumber)->getFont()->setBold(true);
            $totalDebit =	$totalCredit    =   $totalMutation   =   0;
            $rowNumber++;
            
            foreach($transactionData as $data){
                $descriptionJournal =   new RichText();
                $descriptionRecap   =   $descriptionJournal->createTextRun($data->DESCRIPTIONRECAP);
                $descriptionRecap->getFont()->setSize(12);
                $descriptionDetail  =   $descriptionJournal->createTextRun("\n".$data->DESCRIPTIONDETAIL);
                $descriptionDetail->getFont()->setSize(10)->setItalic(true);

                $sheet->setCellValue('A'.$rowNumber, $data->DATETRANSACTION);
                $sheet->setCellValue('B'.$rowNumber, $data->REFFNUMBER);
                $sheet->setCellValue('C'.$rowNumber, $descriptionJournal);  $sheet->mergeCells('C'.$rowNumber.':D'.$rowNumber);
                $sheet->setCellValue('E'.$rowNumber, $data->DEBIT);
                $sheet->setCellValue('F'.$rowNumber, $data->CREDIT);
                
                $totalDebit         +=	$data->DEBIT;
                $totalCredit        +=	$data->CREDIT;
                $totalMutation      +=  $data->DEBIT - $data->CREDIT;
                $grandTotalDebit    +=  $data->DEBIT;
                $grandTotalCredit   +=  $data->CREDIT;
                $sheet->getRowDimension($rowNumber)->setRowHeight(-1);
                $rowNumber++;
            }
                    
            $sheet->setCellValue('A'.$rowNumber, 'TOTAL');  $sheet->mergeCells('A'.$rowNumber.':D'.$rowNumber);
            $sheet->setCellValue('E'.$rowNumber, $totalDebit);
            $sheet->setCellValue('F'.$rowNumber, $totalCredit);
            $sheet->getStyle('A'.$rowNumber.':F'.$rowNumber)->getFont()->setBold(true);
            $rowNumber++;

            $sheet->setCellValue('A'.$rowNumber, 'Beginning Balance');
            $sheet->setCellValue('B'.$rowNumber, $beginningBalance);    $sheet->getStyle('B'.$rowNumber)->getAlignment()->setHorizontal('right');
            $sheet->setCellValue('C'.$rowNumber, 'Total Mutation');
            $sheet->setCellValue('D'.$rowNumber, $totalMutation);   $sheet->getStyle('D'.$rowNumber)->getAlignment()->setHorizontal('right');
            $sheet->setCellValue('E'.$rowNumber, 'Ending Balance');
            $sheet->setCellValue('F'.$rowNumber, ($beginningBalance + $totalMutation)); $sheet->getStyle('F'.$rowNumber)->getAlignment()->setHorizontal('right');
            $sheet->getStyle('A'.$rowNumber.':F'.$rowNumber)->getFont()->setBold(true);
    		$sheet->getStyle('A'.$firstRowNumber.':F'.$rowNumber)->applyFromArray($styleArray)->getAlignment()->setVertical('top')->setWrapText(true);
            $rowNumber  +=  2;
        }

        $sheet->setCellValue('A'.$rowNumber, 'GRAND TOTAL'); $sheet->mergeCells('A'.$rowNumber.':D'.$rowNumber);
        $sheet->setCellValue('E'.$rowNumber, $grandTotalDebit);
        $sheet->setCellValue('F'.$rowNumber, $grandTotalCredit);
		$sheet->setBreak('A'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
		
		$sheet->getColumnDimension('A')->setWidth(18);
		$sheet->getColumnDimension('B')->setWidth(18);
		$sheet->getColumnDimension('C')->setWidth(18);
		$sheet->getColumnDimension('D')->setWidth(18);
		$sheet->getColumnDimension('E')->setWidth(18);
		$sheet->getColumnDimension('F')->setWidth(18);
		$sheet->setShowGridLines(false);
		
		$sheet->getPageSetup()->setFitToWidth(1)->setFitToHeight(0);

		$writer			=	new Xlsx($spreadsheet);
		$filename		=	'ExcelDataGeneralLedger_'.$datePeriodStart.'_'.$datePeriodEnd;
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
        die;
	}
}