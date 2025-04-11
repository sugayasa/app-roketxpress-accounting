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
use PhpOffice\PhpSpreadsheet\Style\IOFactory;
use App\Models\MainOperation;
use App\Models\GeneralJournalModel;
use App\Models\ChartOfAccountModel;
use App\Models\TemplateJournalModel;

class GeneralJournal extends ResourceController
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
            'page'              =>  ['label' => 'Page', 'rules' => 'required|numeric'],
            'datePeriodStart'   =>  ['label' => 'Transaction Date', 'rules' => 'required|exact_length[10]|valid_date[d-m-Y]'],
            'datePeriodEnd'     =>  ['label' => 'Transaction Date', 'rules' => 'required|exact_length[10]|valid_date[d-m-Y]']
        ];

        $messages   =   [
            'page'  => [
                'required'=> 'Invalid data sent [1]',
                'numeric' => 'Invalid data sent [2]'
            ]
        ];

        if(!$this->validate($rules, $messages)) return $this->fail($this->validator->getErrors());
        
        $generalJournalModel=   new GeneralJournalModel();
        $page               =   $this->request->getVar('page');
        $idAccountGeneral   =   $this->request->getVar('idAccountGeneral');
        $idAccountGeneral   =   isset($idAccountGeneral) && $idAccountGeneral != '' ? hashidDecode($idAccountGeneral) : $idAccountGeneral;
        $idAccountMain      =   $this->request->getVar('idAccountMain');
        $idAccountMain      =   isset($idAccountMain) && $idAccountMain != '' ? hashidDecode($idAccountMain) : $idAccountMain;
        $idAccountSub       =   $this->request->getVar('idAccountSub');
        $idAccountSub       =   isset($idAccountSub) && $idAccountSub != '' ? hashidDecode($idAccountSub) : $idAccountSub;
        $datePeriodStart    =   $this->request->getVar('datePeriodStart');
        $datePeriodStartTF  =   Time::createFromFormat('d-m-Y', $datePeriodStart);
        $datePeriodStart    =   $datePeriodStartTF->toDateString();
        $datePeriodEnd      =   $this->request->getVar('datePeriodEnd');
        $datePeriodEndTF    =   Time::createFromFormat('d-m-Y', $datePeriodEnd);
        $datePeriodEnd      =   $datePeriodEndTF->toDateString();
        $searchReffNumber   =   $this->request->getVar('searchReffNumber');
        $searchDescription  =   $this->request->getVar('searchDescription');
        $urlExcelData       =   "";
        $result             =	$generalJournalModel->getDataTable($page, 20, $idAccountGeneral, $idAccountMain, $idAccountSub, $datePeriodStart, $datePeriodEnd, $searchReffNumber, $searchDescription);

        if(count($result['data']) > 0){
            $result['data'] =   encodeDatabaseObjectResultKey($result['data'], 'IDJOURNALRECAP');
        }

        $newReffNumber      =   $generalJournalModel->getNewReffNumber('GJ');
        $arrParamExcelData  =   [
            'idAccountGeneral'  =>  $idAccountGeneral,
            'idAccountMain'     =>  $idAccountMain,
            'idAccountSub'      =>  $idAccountSub,
            'datePeriodStart'   =>  $datePeriodStart,
            'datePeriodEnd'     =>  $datePeriodEnd,
            'searchReffNumber'  =>  $searchReffNumber,
            'searchDescription' =>  $searchDescription
        ];
        $urlExcelData           =   BASE_URL."generalJournal/excelDataJournal/".encodeJWTToken($arrParamExcelData);
        return $this->setResponseFormat('json')
                    ->respond([
                        "result"        =>  $result,
                        "newReffNumber" =>  $newReffNumber,
                        "urlExcelData"  =>  $urlExcelData
                     ]);
    }
    
    public function excelDataJournal($encryptedParam)
    {
        helper(['firebaseJWT']);

        $chartOfAccountModel    =   new ChartOfAccountModel();
        $generalJournalModel    =   new GeneralJournalModel();
		$arrParam               =	decodeJWTToken($encryptedParam);
		$idAccountGeneral       =	$arrParam->idAccountGeneral;
		$idAccountMain          =	$arrParam->idAccountMain;
		$idAccountSub           =	$arrParam->idAccountSub;
		$datePeriodStart        =	$arrParam->datePeriodStart;
		$datePeriodEnd          =	$arrParam->datePeriodEnd;
		$searchReffNumber       =	$arrParam->searchReffNumber;
		$searchDescription      =	$arrParam->searchDescription;
        $dataAccountGeneral     =   $chartOfAccountModel->getDetailAccount(1, $idAccountGeneral);
        $strAccountGeneral      =   !is_null($dataAccountGeneral) && $dataAccountGeneral ? $dataAccountGeneral['ACCOUNTNAME'] : 'All General Account';
        $dataAccountMain        =   $chartOfAccountModel->getDetailAccount(1, $idAccountMain);
        $strAccountMain         =   !is_null($dataAccountMain) && $dataAccountMain ? $dataAccountMain['ACCOUNTNAME'] : 'All Main Account';
        $dataAccountSub         =   $chartOfAccountModel->getDetailAccount(1, $idAccountSub);
        $strAccountSub          =   !is_null($dataAccountSub) && $dataAccountSub ? $dataAccountSub['ACCOUNTNAME'] : 'All Sub Account';
        $result                 =	$generalJournalModel->getDataTable(1, 999999, $idAccountGeneral, $idAccountMain, $idAccountSub, $datePeriodStart, $datePeriodEnd, $searchReffNumber, $searchDescription);
		
		if(count($result['data']) <= 0){
            return throwResponseNotFound("No data found for this action");
		}
		
		$spreadsheet	=	new Spreadsheet();
		$sheet			=	$spreadsheet->getActiveSheet();
		
		$sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
		$sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
		$sheet->getPageMargins()->setTop(0.25)->setRight(0.2)->setLeft(0.2)->setBottom(0.25);
		
		$sheet->setCellValue('A1', 'Bali Sun Tours - Accounting');
		$sheet->setCellValue('A2', 'Data General Journal');
		$sheet->getStyle('A1:A2')->getFont()->setBold(true);
		$sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');
		$sheet->mergeCells('A1:H1')->mergeCells('A2:H2');
		
		$sheet->setCellValue('A4', 'General Account');  $sheet->setCellValue('B4', ": ".$strAccountGeneral);    $sheet->mergeCells('B4:H4');
		$sheet->setCellValue('A5', 'Main Account');     $sheet->setCellValue('B5', ": ".$strAccountMain);       $sheet->mergeCells('B5:H5');
		$sheet->setCellValue('A6', 'Sub Account');      $sheet->setCellValue('B6', ": ".$strAccountSub);        $sheet->mergeCells('B6:H6');
		$sheet->setCellValue('A7', 'Date Period');      $sheet->setCellValue('B7', ": ".$datePeriodStart.' - '.$datePeriodEnd); $sheet->mergeCells('B7:H7');
		$sheet->setCellValue('A8', 'Reff Num');         $sheet->setCellValue('B8', ": ".$searchReffNumber);     $sheet->mergeCells('B8:H8');
		$sheet->setCellValue('A9', 'Keyword');          $sheet->setCellValue('B9', ": ".$searchDescription);    $sheet->mergeCells('B9:H9');
				
		$sheet->setCellValue('A11', 'Reff Number');
		$sheet->setCellValue('B11', 'Date Transaction');
		$sheet->setCellValue('C11', 'Description');
		$sheet->setCellValue('D11', 'Account Code');
		$sheet->setCellValue('E11', 'Account Name');
		$sheet->setCellValue('F11', 'Account Description');
		$sheet->setCellValue('G11', 'Debit');
		$sheet->setCellValue('H11', 'Credit');
		
		$sheet->getStyle('A11:H11')->getFont()->setBold(true);
		$sheet->getStyle('A11:H11')->getAlignment()->setHorizontal('center');
		$sheet->getStyle('A11:H11')->getAlignment()->setVertical('center');
		$rowNumber			=	$firstRowNumber	=	12;
		$grandTotalDebit	=	$grandTotalCredit  =   0;
		
		$styleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => Border::BORDER_THIN
				]
			 ]
		];
		
		foreach($result['data'] as $data){
            $objAccountDetails  =   json_decode($data->OBJACCOUNTDETAILS);
            $totalAccountDetails=   count($objAccountDetails) -1;

			$sheet->setCellValue('A'.$rowNumber, $data->REFFNUMBER);        $sheet->mergeCells('A'.$rowNumber.':A'.($rowNumber + $totalAccountDetails));
			$sheet->setCellValue('B'.$rowNumber, $data->DATETRANSACTION);   $sheet->mergeCells('B'.$rowNumber.':B'.($rowNumber + $totalAccountDetails));
			$sheet->setCellValue('C'.$rowNumber, $data->DESCRIPTION);       $sheet->mergeCells('C'.$rowNumber.':C'.($rowNumber + $totalAccountDetails));
			$sheet->setCellValue('D'.$rowNumber, $objAccountDetails[0]->accountCode);
			$sheet->setCellValue('E'.$rowNumber, $objAccountDetails[0]->accountName);
			$sheet->setCellValue('F'.$rowNumber, $objAccountDetails[0]->description);
			$sheet->setCellValue('G'.$rowNumber, $objAccountDetails[0]->debit);
			$sheet->setCellValue('H'.$rowNumber, $objAccountDetails[0]->credit);
			
			$grandTotalDebit    +=	$objAccountDetails[0]->debit;
			$grandTotalCredit   +=	$objAccountDetails[0]->credit;
			$rowNumber++;

            foreach($objAccountDetails as $indexAccountDetails => $keyAccountDetails){
                if($indexAccountDetails > 0){
                    $sheet->setCellValue('D'.$rowNumber, $keyAccountDetails->accountCode);
                    $sheet->setCellValue('E'.$rowNumber, $keyAccountDetails->accountName);
                    $sheet->setCellValue('F'.$rowNumber, $keyAccountDetails->description);
                    $sheet->setCellValue('G'.$rowNumber, $keyAccountDetails->debit);
                    $sheet->setCellValue('H'.$rowNumber, $keyAccountDetails->credit);

                    $grandTotalDebit    +=	$keyAccountDetails->debit;
                    $grandTotalCredit   +=	$keyAccountDetails->credit;
        			$rowNumber++;
                }
            }
		}
				
		$sheet->setCellValue('A'.$rowNumber, 'TOTAL');
		$sheet->mergeCells('A'.$rowNumber.':F'.$rowNumber);
		$sheet->setCellValue('G'.$rowNumber, $grandTotalDebit); $sheet->getStyle('G'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('G'.$rowNumber)->getFont()->setBold(true);
		$sheet->setCellValue('H'.$rowNumber, $grandTotalCredit);$sheet->getStyle('H'.$rowNumber)->getAlignment()->setHorizontal('right');	$sheet->getStyle('H'.$rowNumber)->getFont()->setBold(true);

		$sheet->getStyle('A'.$rowNumber)->getFont()->setBold(true);
		$sheet->getStyle('A'.$rowNumber)->getAlignment()->setHorizontal('center');
		$sheet->getStyle('A'.($firstRowNumber - 1).':H'.$rowNumber)->applyFromArray($styleArray)->getAlignment()->setVertical('top')->setWrapText(true);
		$sheet->setBreak('A'.$rowNumber, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
		
		$sheet->getColumnDimension('A')->setWidth(16);
		$sheet->getColumnDimension('B')->setWidth(16);
		$sheet->getColumnDimension('C')->setWidth(45);
		$sheet->getColumnDimension('D')->setWidth(14);
		$sheet->getColumnDimension('E')->setWidth(35);
		$sheet->getColumnDimension('F')->setWidth(45);
		$sheet->getColumnDimension('G')->setWidth(12);
		$sheet->getColumnDimension('H')->setWidth(12);
		$sheet->setShowGridLines(false);
		
		$sheet->getPageSetup()->setFitToWidth(1)->setFitToHeight(0);

		$writer			=	new Xlsx($spreadsheet);
		$filename		=	'ExcelDataGeneralJournal_'.$datePeriodStart.'_'.$datePeriodEnd;
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
        die;
	}

    public function getDataTemplateJournal()
    {
        $templateJournalModel   =   new TemplateJournalModel();
        $listTemplateJournal    =   $templateJournalModel->getListTemplateJournal();

        if(is_null($listTemplateJournal) || !$listTemplateJournal) return throwResponseNotFound("No template found");

        foreach($listTemplateJournal as $keyTemplateJournal){
            $jsonAccountDetails =   json_decode($keyTemplateJournal->OBJACCOUNTDETAILS);
            $arrAccountDetails  =   [];
            foreach($jsonAccountDetails as $keyAccountDetail){
                $keyAccountDetail->IDACCOUNT    =   hashidEncode($keyAccountDetail->IDACCOUNT);
                $arrAccountDetails[]            =   $keyAccountDetail;
            }
            $keyTemplateJournal->OBJACCOUNTDETAILS  =   json_encode($arrAccountDetails);
        }

        return $this->setResponseFormat('json')
                    ->respond([
                        "listTemplateJournal"   =>  $listTemplateJournal
                     ]);
    }

    public function insertData()
    {
        helper(['form']);
        $rules      =   [
            'date'              =>  ['label' => 'Transaction Date', 'rules' => 'required|exact_length[10]|valid_date[d-m-Y]'],
            'nominal'           =>  ['label' => 'Nominal', 'rules' => 'required|numeric'],
            'description'       =>  ['label' => 'Description', 'rules' => 'required|alpha_numeric_punct'],
            'arrAccountDetail'  =>  ['label' => 'Account Details', 'rules' => 'required|is_array']
        ];

        $messages   =   [
            'arrAccountDetail'  => [
                'required'  => 'Invalid data sent',
                'is_array'  => 'Invalid data sent'
            ]
        ];
        if(!$this->validate($rules, $messages)) return $this->fail($this->validator->getErrors());

        $generalJournalModel=   new GeneralJournalModel();
        $date               =   $this->request->getVar('date');
        $dateTF             =   Time::createFromFormat('d-m-Y', $date);
        $date               =   $dateTF->toDateString();
        $nominal            =   $this->request->getVar('nominal') * 1;
        $description        =   $this->request->getVar('description');
        $arrAccountDetail   =   $this->request->getVar('arrAccountDetail');
        $totalAccount       =   count($arrAccountDetail);
        $newReffNumber      =   $generalJournalModel->getNewReffNumber('GJ');

        if($nominal <= 0) return throwResponseNotAcceptable("The nominal entered is invalid (".$nominal.")");

        $arrInsertData      =   [
            'REFFNUMBER'        =>  $newReffNumber,
            'DATETRANSACTION'   =>  $date,
            'DESCRIPTION'       =>  $description,
            'TOTALACCOUNT'      =>  $totalAccount,
            'TOTALNOMINAL'      =>  $nominal,
            'USERINSERT'        =>  $this->userData->name,
            'DATETIMEINSERT'    =>  $this->currentDateTime
        ];

        try {
            $generalJournalModel->db->transException(true)->transStart();
            $mainOperation  =   new MainOperation();
            $procInsertData =   $mainOperation->insertDataTable('t_journalrecap', $arrInsertData);

            if(!$procInsertData['status']) return switchMySQLErrorCode($procInsertData['errCode']);
            $idJournalRecap =   $procInsertData['insertID'];

            foreach($arrAccountDetail as $keyAccountDetail){
                $idAccount              =   hashidDecode($keyAccountDetail[0]);
                $positionDRCR           =   $keyAccountDetail[2] > 0 ? 'DR' : 'CR';
                $arrInsertDataDetail    =   [
                    'IDJOURNALRECAP'=>  $idJournalRecap,
                    'IDACCOUNT'     =>  $idAccount,
                    'POSITIONDRCR'  =>  $positionDRCR,
                    'DESCRIPTION'   =>  $keyAccountDetail[1],
                    'DEBIT'         =>  $keyAccountDetail[2],
                    'CREDIT'        =>  $keyAccountDetail[3]
                ];
                $mainOperation->insertDataTable('t_journaldetails', $arrInsertDataDetail);
            }
    		$generalJournalModel->db->transComplete();

            return throwResponseOK('New journal data has been added');
        } catch (\Throwable $th) {
            return throwResponseInternalServerError('Internal server error - failed to add data');
        }
    }

    public function getDetailGeneralJournal()
    {
        helper(['form']);
        $rules      =   [
            'idJournalRecap'    => ['label' => 'Id Journal Recap', 'rules' => 'required|alpha_numeric']
        ];

        $messages   =   [
            'idJournalRecap'    => [
                'required'      => 'Invalid data sent',
                'alpha_numeric' => 'Invalid data sent'
            ]
        ];

        if(!$this->validate($rules, $messages)) return $this->fail($this->validator->getErrors());

        $generalJournalModel=   new GeneralJournalModel();
        $idJournalRecap     =   $this->request->getVar('idJournalRecap');
        $idJournalRecap     =   hashidDecode($idJournalRecap);
        $detailRecap        =   $generalJournalModel->find($idJournalRecap);

        if(!$detailRecap) return throwResponseNotFound("No detail found for journal selected");
        $listDetailJournal  =   $generalJournalModel->getListDetailJournal($idJournalRecap);

        $detailRecap['DATETRANSACTION'] =   Time::createFromFormat('Y-m-d', $detailRecap['DATETRANSACTION']);
        $detailRecap['DATETRANSACTION'] =   $detailRecap['DATETRANSACTION']->toLocalizedString('dd-MM-yyyy');
        $detailRecap['IDJOURNALRECAP']  =   hashidEncode($detailRecap['IDJOURNALRECAP']);
        $listDetailJournal              =   encodeDatabaseObjectResultKey($listDetailJournal, ['IDJOURNALDETAILS', 'IDACCOUNT']);

        return $this->setResponseFormat('json')
                    ->respond([
                        "detailRecap"       =>  $detailRecap,
                        "listDetailJournal" =>  $listDetailJournal
                     ]);
    }

    public function updateData()
    {
        helper(['form']);
        $rules      =   [
            'idJournalRecap'        =>  ['label' => 'Id Journal Recap', 'rules' => 'required|alpha_numeric'],
            'arrIdJournalDetails'   =>  ['label' => 'Id Journal Details', 'rules' => 'required|is_array'],
            'date'                  =>  ['label' => 'Transaction Date', 'rules' => 'required|exact_length[10]|valid_date[d-m-Y]'],
            'nominal'               =>  ['label' => 'Nominal', 'rules' => 'required|numeric'],
            'description'           =>  ['label' => 'Description', 'rules' => 'required|alpha_numeric_punct'],
            'arrAccountDetail'      =>  ['label' => 'Account Details', 'rules' => 'required|is_array']
        ];

        $messages   =   [
            'idJournalRecap'        => [
                'required'      => 'Invalid data sent',
                'alpha_numeric' => 'Invalid data sent'
            ],
            'arrIdJournalDetails'   => [
                'required'      => 'Invalid data sent',
                'alpha_numeric' => 'Invalid data sent'
            ],
            'arrAccountDetail'      => [
                'required'  => 'Invalid data sent',
                'is_array'  => 'Invalid data sent'
            ]
        ];
        if(!$this->validate($rules, $messages)) return $this->fail($this->validator->getErrors());

        $generalJournalModel=   new GeneralJournalModel();
        $idJournalRecap     =   $this->request->getVar('idJournalRecap');
        $idJournalRecap     =   hashidDecode($idJournalRecap);
        $arrIdJournalDetails=   $this->request->getVar('arrIdJournalDetails');
        $date               =   $this->request->getVar('date');
        $dateTF             =   Time::createFromFormat('d-m-Y', $date);
        $date               =   $dateTF->toDateString();
        $nominal            =   $this->request->getVar('nominal');
        $description        =   $this->request->getVar('description');
        $arrAccountDetail   =   $this->request->getVar('arrAccountDetail');
        $totalAccount       =   count($arrAccountDetail);

        if($nominal <= 0) return throwResponseNotAcceptable("The nominal entered is invalid");
        if(count($arrIdJournalDetails) <= 0) return throwResponseNotAcceptable("Invalid data sent");

        for($i=0; $i<count($arrIdJournalDetails); $i++){
            $arrIdJournalDetails[$i]    =   hashidDecode($arrIdJournalDetails[$i]);
        }

        $arrUpdateData      =   [
            'DATETRANSACTION'   =>  $date,
            'DESCRIPTION'       =>  $description,
            'TOTALACCOUNT'      =>  $totalAccount,
            'TOTALNOMINAL'      =>  $nominal,
            'USERUPDATE'        =>  $this->userData->name,
            'DATETIMEUPDATE'    =>  $this->currentDateTime
        ];

        try {
            $generalJournalModel->db->transException(true)->transStart();
            $generalJournalModel->update($idJournalRecap, $arrUpdateData);

            $mainOperation  =   new MainOperation();
            foreach($arrAccountDetail as $keyAccountDetail){
                $idAccount          =   hashidDecode($keyAccountDetail[0]);
                $positionDRCR       =   $keyAccountDetail[2] > 0 ? 'DR' : 'CR';
                $idJournalDetail    =   hashidDecode($keyAccountDetail[4]);
                $arrInsertUpdateDetail  =   [
                    'IDJOURNALRECAP'=>  $idJournalRecap,
                    'IDACCOUNT'     =>  $idAccount,
                    'POSITIONDRCR'  =>  $positionDRCR,
                    'DESCRIPTION'   =>  $keyAccountDetail[1],
                    'DEBIT'         =>  $keyAccountDetail[2],
                    'CREDIT'        =>  $keyAccountDetail[3]
                ];
                if(!in_array($idJournalDetail, $arrIdJournalDetails)){
                    $mainOperation->insertDataTable('t_journaldetails', $arrInsertUpdateDetail);
                } else {
                    $mainOperation->updateDataTable('t_journaldetails', $arrInsertUpdateDetail, ['IDJOURNALDETAILS' => $idJournalDetail]);
                    unset($arrIdJournalDetails[array_search($idJournalDetail, $arrIdJournalDetails)]);
                }
            }

            if(count($arrIdJournalDetails) > 0){
                foreach($arrIdJournalDetails as $idJournalDetail){
                    $mainOperation->deleteDataTable('t_journaldetails', ['IDJOURNALDETAILS' => $idJournalDetail]);
                }
            }
    		$generalJournalModel->db->transComplete();

            return throwResponseOK('Journal data has been updated');
        } catch (\Throwable $th) {
            return throwResponseInternalServerError('Internal server error - failed to update data');
        }
    }

    public function deleteGeneralJournal()
    {
        helper(['form']);
        $rules      =   [
            'idJournalRecap'    =>  ['label' => 'Id Journal Recap', 'rules' => 'required|alpha_numeric']
        ];

        $messages   =   [
            'idJournalRecap' => [
                'required'      => 'Invalid data sent',
                'alpha_numeric' => 'Invalid data sent'
            ]
        ];
        if(!$this->validate($rules, $messages)) return $this->fail($this->validator->getErrors());

        $generalJournalModel=   new GeneralJournalModel();
        $idJournalRecap     =   $this->request->getVar('idJournalRecap');
        $idJournalRecap     =   hashidDecode($idJournalRecap);

        try{
            $generalJournalModel->delete($idJournalRecap);
        } catch (\Throwable $th) {
            return throwResponseNotAcceptable('Internal database script error');
        }

        return throwResponseOK('Journal has been deleted');
    }

    public function uploadImportExcelJournal()
    {
        if((($_FILES["file"]["type"] == "application/vnd.ms-excel")
			|| ($_FILES["file"]["type"] == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"))
			&& ($_FILES["file"]["size"] <= 800000)){
			if ($_FILES["file"]["error"] > 0) {
				return throwResponseInternalServerError("Failed to upload this file. File is broken");
			}
			
		} else {
			return throwResponseInternalServerError("Failed to upload this file. This file type is not allowed (".$_FILES["file"]["type"].") or file size is too big (".$_FILES["uploaded_file"]["size"].")");
		}
		
		$dir		=	PATH_STORAGE_TEMP_UPLOAD;
		$extension	=	pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
		$nameFile	=	"importExcelJournal"."_".date('YmdHis').".".$extension;
		$move		=	move_uploaded_file($_FILES["file"]["tmp_name"], $dir.$nameFile);
		
		if($move){
            return $this->setResponseFormat('json')
                        ->respond([
                            "status"                =>  200,
                            "excelJournalFileName"  =>  $nameFile,
                            "extension"             =>  $extension,
                            "message"               =>  "File has been uploaded"
                        ]);
		} else {
			return throwResponseInternalServerError("Failed to upload this file. Please try again later");
		}
    }

    public function scanImportExcelJournal()
    {
        helper(['form']);
        $rules      =   [
            'fileName'  =>  ['label' => 'File Name', 'rules' => 'required|alpha_numeric_punct'],
            'extension' =>  ['label' => 'File Extension', 'rules' => 'required|in_list[xls, xlsx]']
        ];

        $messages   =   [
            'fileName'  => [
                'required'              => 'Invalid file upload',
                'alpha_numeric_punct'   => 'Invalid file upload'
            ],
            'extension'  => [
                'required'  => 'Invalid file upload',
                'in_list'   => 'Invalid file upload'
            ]
        ];
        if(!$this->validate($rules, $messages)) return $this->fail($this->validator->getErrors());

        $fileName   =   $this->request->getVar('fileName');
		$filePath   =	PATH_STORAGE_TEMP_UPLOAD.$fileName;

        try {
            $fileType			=	\PhpOffice\PhpSpreadsheet\IOFactory::identify($filePath);
            $reader				=	\PhpOffice\PhpSpreadsheet\IOFactory::createReader($fileType);
            $spreadsheet		=	$reader->load($filePath);
            $excelData			=	$spreadsheet->getActiveSheet()->toArray();
            $totalDataProcess	=	0;
            $resScan			=	[];
            
            if(count($excelData) > 0){
                $generalJournalModel    =   new GeneralJournalModel();
                try {
                    $iExcelData             =   0;
                    $newReffNumber          =   '';
                    $arrAccountUndetected   =   [];

                    foreach($excelData as $data){
                        $reffNumberImport   =   $data[0];
                        $dateTransaction    =   $data[1];
                        $descriptionRecap   =   $data[2];
                        $accountCode        =   $data[3];
                        $accountName        =   $data[4];
                        $descriptionDetail  =   $data[5];
                        $descriptionDetail  =   is_null($descriptionDetail) ? "" : $descriptionDetail;
                        $nominalDebit       =   preg_replace('/\D/', '', $data[6]);
                        $nominalCredit      =   preg_replace('/\D/', '', $data[7]);
                        
                        if(preg_match('/^[0-9-]+$/', $accountCode)){
                            $lastReffNumber         =   $iExcelData == 0 ? 1 : intval(preg_replace('/\D/', '', substr($newReffNumber, -6))) * 1 + 1;
                            $newReffNumber          =   isset($reffNumberImport) && !is_null($reffNumberImport) ? $generalJournalModel->getNewReffNumber('IJ', $lastReffNumber) : $newReffNumber;
                            $dateTransactionStr     =   'Invalid Date';
                            $dateTransactionDB      =   '0000-00-00';

                            if(isset($dateTransaction) && $dateTransaction != null && $dateTransaction != ''){
                                $dateTransactionCheck   =   detectDateFormat($dateTransaction);
                                if($dateTransactionCheck){
                                    $dateTransactionStr     =   $dateTransactionCheck->format('d M Y');
                                    $dateTransactionDB      =   $dateTransactionCheck->format('Y-m-d');
                                }
                            }


                            $totalDashAccountCode   =   substr_count($accountCode, '-');
                            $levelAccountCode       =   $totalDashAccountCode + 1;
                            $detailAccount          =   $generalJournalModel->getDetailAccountByCode($levelAccountCode, $accountCode);
                            $idAccountDB            =   0;
                            $accountCodeDB          =   $accountNameDB  =   '-';

                            if($detailAccount){
                                $idAccountDB    =   hashidEncode($detailAccount['IDACCOUNT']);
                                $accountCodeDB  =   $detailAccount['ACCOUNTCODE'];
                                $accountNameDB  =   $detailAccount['ACCOUNTNAME'];
                            } else {
                                $arrAccountUndetected[] =   [$accountCode, $accountName];
                            }

                            $resScan[]          =   [
                                'lastReffNumber'        =>  $lastReffNumber,
                                'reffNumber'            =>  $newReffNumber,
                                'reffNumberImport'      =>  $reffNumberImport,
                                'dateTransactionStr'    =>  $dateTransactionStr,
                                'dateTransactionDB'     =>  $dateTransactionDB,
                                'descriptionRecap'      =>  $descriptionRecap,
                                'idAccountDB'           =>  $idAccountDB,
                                'accountCodeDB'         =>  $accountCodeDB,
                                'accountCodeOrigin'     =>  $accountCode,
                                'accountNameDB'         =>  $accountNameDB,
                                'accountNameOrigin'     =>  $accountName,
                                'descriptionDetail'     =>  $descriptionDetail,
                                'nominalDebit'          =>  $nominalDebit,
                                'nominalCredit'         =>  $nominalCredit,
                                'lastReffNumber'        =>  $lastReffNumber
                            ];
                            $lastReffNumber++;
                            $totalDataProcess++;
                        }
                        $iExcelData++;
                    }
                } catch (\Throwable $th) {
                    return throwResponseInternalServerError('Internal server error - failed to read excel data'.$th->getMessage());
                }
            }

            if($totalDataProcess <= 0){
                return throwResponseNotAcceptable('Failed to read excel data');
            } else {
                $arrAccountUndetected   =   array_map('serialize', $arrAccountUndetected); 
                $arrAccountUndetected   =   array_unique($arrAccountUndetected); 
                $arrAccountUndetected   =   array_map('unserialize', $arrAccountUndetected);
                return $this->setResponseFormat('json')
                            ->respond([
                                "resScan"               =>  $resScan,
                                "arrAccountUndetected"  =>  $arrAccountUndetected,
                                "msg"                   =>  "File has been uploaded (".$totalDataProcess." records). Please check scan results"
                            ]);
            }
        } catch (\Throwable $th) {
            return throwResponseInternalServerError('Internal server error - failed to read excel data');
        }
    }

    public function saveImportExcelJournal()
    {
        helper(['form']);
        $rules      =   [
            'arrDataJournalRecap'   =>  ['label' => 'Data Journal', 'rules' => 'required|is_array']
        ];

        $messages   =   [
            'arrDataJournalRecap'   =>  [
                'required'  => 'Invalid data sent',
                'is_array'  => 'Invalid data sent'
            ]
        ];
        if(!$this->validate($rules, $messages)) return $this->fail($this->validator->getErrors());

        $generalJournalModel    =   new GeneralJournalModel();
        $arrDataJournalRecap    =   $this->request->getVar('arrDataJournalRecap');
        $totalDataJournalRecap  =   count($arrDataJournalRecap);

        if($totalDataJournalRecap > 0){
            $totalDataProcess       =   0;
            $reffNumberImportInvalid=   [];
            try {
                $generalJournalModel->db->transException(true)->transStart();
                foreach($arrDataJournalRecap as $indexJournalRecap => $dataJournalRecap){
                    if(!is_null($dataJournalRecap)){
                        $dateTransaction            =   $dataJournalRecap->dateTransaction;
                        $descriptionRecap           =   $dataJournalRecap->descriptionRecap;
                        $reffNumber                 =   $dataJournalRecap->reffNumber;
                        $reffNumberImport           =   $dataJournalRecap->reffNumberImport;
                        $totalNominalJournalRecap   =   preg_replace('/\D/', '', $dataJournalRecap->totalNominalJournalRecap) * 1;
                        $reffNumberImport           =   $dataJournalRecap->reffNumberImport;
                        $arrAccountDetail           =   $dataJournalRecap->arrAccountDetail;
                        $totalAccountDetails        =   count($arrAccountDetail);

                        if($totalNominalJournalRecap > 0 && $dateTransaction != '0000-00-00'){
                            $arrInsertRecap =   [
                                'REFFNUMBER'        =>  $reffNumber,
                                'REFFNUMBERIMPORT'  =>  $reffNumberImport,
                                'DATETRANSACTION'   =>  $dateTransaction,
                                'DESCRIPTION'       =>  $descriptionRecap,
                                'TOTALACCOUNT'      =>  $totalAccountDetails,
                                'TOTALNOMINAL'      =>  $totalNominalJournalRecap,
                                'USERINSERT'        =>  $this->userData->name,
                                'DATETIMEINSERT'    =>  $this->currentDateTime
                            ];

                            try {
                                $mainOperation  =   new MainOperation();
                                $procInsertRecap=   $mainOperation->insertDataTable('t_journalrecap', $arrInsertRecap);

                                if($procInsertRecap['status']) {
                                    $idJournalRecap =   $procInsertRecap['insertID'];

                                    foreach($arrAccountDetail as $keyAccountDetail){
                                        $idAccount              =   hashidDecode($keyAccountDetail[0]);
                                        $positionDRCR           =   $keyAccountDetail[2] > 0 ? 'DR' : 'CR';
                                        $arrInsertDataDetail    =   [
                                            'IDJOURNALRECAP'=>  $idJournalRecap,
                                            'IDACCOUNT'     =>  $idAccount,
                                            'POSITIONDRCR'  =>  $positionDRCR,
                                            'DESCRIPTION'   =>  $keyAccountDetail[1],
                                            'DEBIT'         =>  $keyAccountDetail[2],
                                            'CREDIT'        =>  $keyAccountDetail[3]
                                        ];
                                        $mainOperation->insertDataTable('t_journaldetails', $arrInsertDataDetail);
                                    }
                                    $totalDataProcess++;
                                }
                            } catch (\Throwable $th) {
                                $reffNumberImportInvalid[]  =   $reffNumberImport;
                            }
                        } else {
                            $reffNumberImportInvalid[]  =   $reffNumberImport;
                        }
                    }
                }
                $generalJournalModel->db->transComplete();
            } catch (\Throwable $th) {
                return throwResponseInternalServerError('Failed to submit journal data, please retry the import process later.');
            }

            if($totalDataProcess > 0){
                $reffNumberImportInvalid=   count($reffNumberImportInvalid) <= 0 ? "-" : implode(',', $reffNumberImportInvalid);
                return throwResponseOK('<b>'.$totalDataProcess.'</b> new journal data has been added.<br/>List of invalid journal reff number : '.$reffNumberImportInvalid);
            } else {
                return throwResponseNotModified('No journal data was successfully submitted.');
            }
        } else {
            return throwResponseForbidden('The submitted journal import data is invalid. Please correct the input data');
        }
    }
}