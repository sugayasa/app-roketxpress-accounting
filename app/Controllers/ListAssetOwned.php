<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use CodeIgniter\I18n\Time;
use App\Models\MainOperation;
use App\Models\ListAssetOwnedModel;
use App\Models\GeneralJournalModel;
use App\Models\TemplateJournalModel;
use PhpParser\Node\Stmt\Foreach_;

class ListAssetOwned extends ResourceController
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

    //OK
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
        
        $listAssetOwnedModel    =   new ListAssetOwnedModel();
        $page                   =   $this->request->getVar('page');
        $idAssetType            =   $this->request->getVar('idAssetType');
        $idAssetType            =   isset($idAssetType) && $idAssetType != '' ? hashidDecode($idAssetType) : $idAssetType;
        $idDepreciationGroup    =   $this->request->getVar('idDepreciationGroup');
        $idDepreciationGroup    =   isset($idDepreciationGroup) && $idDepreciationGroup != '' ? hashidDecode($idDepreciationGroup) : $idDepreciationGroup;
        $searchKeyword          =   $this->request->getVar('searchKeyword');
        $result                 =	$listAssetOwnedModel->getDataTable($page, 20, $idAssetType, $idDepreciationGroup, $searchKeyword);

        if(count($result['data']) > 0){
            $result['data'] =   encodeDatabaseObjectResultKey($result['data'], 'IDASSET');
        }

        $generalJournalModel=   new GeneralJournalModel();
        $newReffNumber      =   $generalJournalModel->getNewReffNumber('PA');
        return $this->setResponseFormat('json')
                    ->respond([
                        "result"        =>  $result,
                        "newReffNumber" =>  $newReffNumber
                     ]);
    }

    public function getDataDepreciationPosting()
    {
        $listAssetOwnedModel=   new ListAssetOwnedModel();
        $result             =	$listAssetOwnedModel->getDataDepreciationPosting();

        if(!$result) return throwResponseNotFound("No data journal post asset ");

        $result                 =   encodeDatabaseObjectResultKey($result, ['IDASSETDEPRECIATION', 'IDASSETTYPE', 'IDASSETDEPRECIATIONGROUP']);
        $templateJournalModel   =   new TemplateJournalModel();
        foreach($result as $key){
            $idJournalTemplateRecap =   $key->IDJOURNALTEMPLATERECAP;
            $assetName              =   $key->ASSETNAME;
            $dateDepreciation       =   $key->DEPRECIATIONDATE;
            $dateDepreciationTF     =   Time::createFromFormat('Y-m-d', $dateDepreciation);
            $monthDepreciation      =   $dateDepreciationTF->toLocalizedString('MMM');
            $yearDepreciation       =   $dateDepreciationTF->toLocalizedString('yyyy');
            $detailTemplateJournal  =   $templateJournalModel->getListDetailTemplate($idJournalTemplateRecap);
            
            foreach($detailTemplateJournal as $keyTemplateJournal){
                $description                    =   $keyTemplateJournal->DESCRIPTION;
                $keyTemplateJournal->DESCRIPTION=   str_replace(['!detail aset!', '!bulan!', '!tahun!'], [$assetName, $monthDepreciation, $yearDepreciation], $description);
                unset($keyTemplateJournal->IDJOURNALTEMPLATEDETAIL);
            }

            $detailTemplateJournal      =   encodeDatabaseObjectResultKey($detailTemplateJournal, 'IDACCOUNT');
            $key->DETAILTEMPLATEJOURNAL =   $detailTemplateJournal;
            unset($key->IDJOURNALTEMPLATERECAP);
        }

        return $this->setResponseFormat('json')
                    ->respond([
                        "result"        =>  $result
                        ]);
    }

    public function getDetailDepreciationPosting()
    {
        helper(['form']);
        $rules      =   [
            'idAssetDepreciation'    => ['label' => 'Asset Depreciation ID', 'rules' => 'required|alpha_numeric']
        ];

        $messages   =   [
            'idAssetDepreciation'    => [
                'required'      => 'Invalid submission data',
                'alpha_numeric' => 'Invalid submission data'
            ]
        ];

        if(!$this->validate($rules, $messages)) return $this->fail($this->validator->getErrors());

        $listAssetOwnedModel    =   new ListAssetOwnedModel();
        $generalJournalModel    =   new GeneralJournalModel();
        $newReffNumber          =   $generalJournalModel->getNewReffNumber('DJ');
        $idAssetDepreciation    =   $this->request->getVar('idAssetDepreciation');
        $idAssetDepreciation    =   hashidDecode($idAssetDepreciation);
        $detailDepreciation     =   $listAssetOwnedModel->getDetailDepreciationPosting($idAssetDepreciation);

        if(!$detailDepreciation) return throwResponseNotFound("No detail found for data depreciation selected");
        
        $templateJournalModel   =   new TemplateJournalModel();
        $idJournalTemplateRecap =   $detailDepreciation['IDJOURNALTEMPLATERECAP'];
        $assetName              =   $detailDepreciation['ASSETNAME'];
        $dateDepreciation       =   $detailDepreciation['DEPRECIATIONDATE'];
        $dateDepreciationTF     =   Time::createFromFormat('d-m-Y', $dateDepreciation);
        $monthDepreciation      =   $dateDepreciationTF->toLocalizedString('MMM');
        $yearDepreciation       =   $dateDepreciationTF->toLocalizedString('yyyy');
        $detailTemplateJournal  =   $templateJournalModel->getListDetailTemplate($idJournalTemplateRecap);
        $journalDescription     =   '';
        
        foreach($detailTemplateJournal as $keyTemplateJournal){
            $journalDescription =   $keyTemplateJournal->DESCRIPTION;
            $journalDescription =   str_replace(['!detail aset!', '!bulan!', '!tahun!'], [$assetName, $monthDepreciation, $yearDepreciation], $journalDescription);
            unset($keyTemplateJournal->IDJOURNALTEMPLATEDETAIL);
        }

        $detailTemplateJournal                      =   encodeDatabaseObjectResultKey($detailTemplateJournal, 'IDACCOUNT');
        $detailDepreciation['JOURNALDESCRIPTION']   =   $journalDescription;
        unset($detailDepreciation['IDJOURNALTEMPLATERECAP']);

        return $this->setResponseFormat('json')
                    ->respond([
                        "newReffNumber"         =>  $newReffNumber,
                        "detailDepreciation"    =>  $detailDepreciation,
                        "detailTemplateJournal" =>  $detailTemplateJournal
                     ]);
    }

    public function postAssetDepreciationJournal()
    {
        helper(['form']);
        $rules      =   [
            'idAssetDepreciation'   => ['label' => 'Asset Depreciation ID', 'rules' => 'required|alpha_numeric'],
            'reffNumber'            => ['label' => 'Journal Reff Number', 'rules' => 'required|alpha_numeric|exact_length[12]'],
            'journalDate'           => ['label' => 'Journal Date', 'rules' => 'required|exact_length[10]|valid_date[d-m-Y]'],
            'journalDescription'    => ['label' => 'Journal Description', 'rules' => 'required|alpha_numeric_punct'],
            'arrJournalAccountDR'   => ['label' => 'Data Debit Account', 'rules' => 'required|is_array'],
            'arrJournalAccountCR'   => ['label' => 'Data Credit Account', 'rules' => 'required|is_array']
        ];

        $messages   =   [
            'idAssetDepreciation'    => [
                'required'      => 'Invalid submission data',
                'alpha_numeric' => 'Invalid submission data'
            ],
            'arrJournalAccountDR'   => [
                'required'  => 'Please complete the journal debit account data',
                'is_array'  => 'Invalid data sent debit account data'
            ],
            'arrJournalAccountCR'   => [
                'required'  => 'Please complete the journal credit account data',
                'is_array'  => 'Invalid data sent credit account data'
            ]
        ];

        if(!$this->validate($rules, $messages)) return $this->fail($this->validator->getErrors());

        $generalJournalModel    =   new GeneralJournalModel();
        $newReffNumber          =   $generalJournalModel->getNewReffNumber('DJ');
        $idAssetDepreciation    =   $this->request->getVar('idAssetDepreciation');
        $idAssetDepreciation    =   hashidDecode($idAssetDepreciation);
        $journalDate            =   $this->request->getVar('journalDate');
        $journalDateTF          =   Time::createFromFormat('d-m-Y', $journalDate);
        $journalDate            =   $journalDateTF->toDateString();
        $journalNominal         =   $this->request->getVar('journalNominal');
        $journalDescription     =   $this->request->getVar('journalDescription');
        $arrJournalAccountDR    =   $this->request->getVar('arrJournalAccountDR');
        $arrJournalAccountCR    =   $this->request->getVar('arrJournalAccountCR');
        $arrJournalAccount      =   array_merge($arrJournalAccountDR, $arrJournalAccountCR);
        $totalAccount           =   count($arrJournalAccount);

        $arrInsertRecap =   [
            'REFFNUMBER'        =>  $newReffNumber,
            'DATETRANSACTION'   =>  $journalDate,
            'DESCRIPTION'       =>  $journalDescription,
            'TOTALACCOUNT'      =>  $totalAccount,
            'TOTALNOMINAL'      =>  $journalNominal,
            'USERINSERT'        =>  $this->userData->name,
            'DATETIMEINSERT'    =>  $this->currentDateTime
        ];

        try {
            $generalJournalModel->db->transException(true)->transStart();
            $mainOperation  =   new MainOperation();
            $procInsertRecap=   $mainOperation->insertDataTable('t_journalrecap', $arrInsertRecap);

            if(!$procInsertRecap['status']) return switchMySQLErrorCode($procInsertRecap['errCode']);
            $idJournalRecap =   $procInsertRecap['insertID'];

            foreach($arrJournalAccount as $keyAccountJournal){
                $idAccount          =   $keyAccountJournal[0];
                $idAccount          =   hashidDecode($idAccount);
                $positionDRCR       =   $keyAccountJournal[1];
                $nominalDRCR        =   $keyAccountJournal[2];
                $descriptionDRCR    =   $keyAccountJournal[3];
                $debitValue         =   $positionDRCR == 'DR' ? $nominalDRCR : 0;
                $creditValue        =   $positionDRCR == 'CR' ? $nominalDRCR : 0;
                $arrInsertDataDetail    =   [
                    'IDJOURNALRECAP'=>  $idJournalRecap,
                    'IDACCOUNT'     =>  $idAccount,
                    'POSITIONDRCR'  =>  $positionDRCR,
                    'DESCRIPTION'   =>  $descriptionDRCR,
                    'DEBIT'         =>  $debitValue,
                    'CREDIT'        =>  $creditValue
                ];
                $mainOperation->insertDataTable('t_journaldetails', $arrInsertDataDetail);
            }

            $mainOperation->updateDataTable(
                't_assetdepreciation',
                [
                    "IDJOURNALRECAP"=>  $idJournalRecap,
                    "STATUSJOURNAL" =>  1
                ],
                ['IDASSETDEPRECIATION'=>$idAssetDepreciation]
            );

            $generalJournalModel->db->transComplete();
            return $this->setResponseFormat('json')
                    ->respond([
                        "newReffNumber" =>  $newReffNumber,
                        "messages"      =>  [
                            'message'   =>  'New depreciation journal has been added successfully'
                        ]
                     ]);
        } catch (\Throwable $th) {
            return throwResponseInternalServerError('Internal server error - failed to add data');
        }
    }

    //OK
    public function insertData()
    {
        helper(['form']);
        $rules      =   [
            'idDepreciationGroup'   =>  ['label' => 'Depreciation Group', 'rules' => 'required|alpha_numeric'],
            'idTemplateJournal'     =>  ['label' => 'Template Journal', 'rules' => 'required|alpha_numeric'],
            'idAssetType'           =>  ['label' => 'Asset Type', 'rules' => 'required|alpha_numeric'],
            'assetName'             =>  ['label' => 'Asset Name', 'rules' => 'required|alpha_numeric_punct'],
            'purchaseDate'          =>  ['label' => 'Purchase Date', 'rules' => 'required|exact_length[10]|valid_date[d-m-Y]'],
            'purchasePrice'         =>  ['label' => 'Purchase Price', 'rules' => 'required|numeric'],
            'residualValue'         =>  ['label' => 'Residual Value', 'rules' => 'required|numeric'],
            'arrDataJournal'        =>  ['label' => 'Purchase Journal', 'rules' => 'required|is_array']
        ];

        if(!$this->validate($rules)) return $this->fail($this->validator->getErrors());

        $mainOperation          =   new MainOperation();
        $listAssetOwnedModel    =   new ListAssetOwnedModel();
        $generalJournalModel    =   new GeneralJournalModel();
        $idDepreciationGroup    =   $this->request->getVar('idDepreciationGroup');
        $idDepreciationGroup    =   hashidDecode($idDepreciationGroup);
        $idTemplateJournal      =   $this->request->getVar('idTemplateJournal');
        $idTemplateJournal      =   hashidDecode($idTemplateJournal);
        $idAssetType            =   $this->request->getVar('idAssetType');
        $idAssetType            =   hashidDecode($idAssetType);
        $assetName              =   $this->request->getVar('assetName');
        $assetDescription       =   $this->request->getVar('assetDescription');
        $purchaseDate           =   $this->request->getVar('purchaseDate');
        $purchaseDateTF         =   Time::createFromFormat('d-m-Y', $purchaseDate);
        $purchaseDate           =   $purchaseDateTF->toDateString();
        $purchasePrice          =   $this->request->getVar('purchasePrice');
        $residualValue          =   $this->request->getVar('residualValue');
        $initialRecordingDate   =   $mainOperation->getDataSystemSetting(1);
        $initialRecordingDateTF =   Time::createFromFormat('Y-m-d', $initialRecordingDate);
        $idJournalRecapPurchase =   null;

        if ($initialRecordingDateTF->isBefore($purchaseDateTF)) {
            $arrDataJournal     =   $this->request->getVar('arrDataJournal');
            $journalDescription =   $arrDataJournal->journalDescription;
            $journalArrDetail   =   $arrDataJournal->arrAccountDetail;
            $totalAccountJournal=   $arrDataJournal->totalAccountJournal;
            $totalNominalJournal=   $arrDataJournal->totalNominalJournal;

            if($purchasePrice <= $residualValue) return throwResponseNotAcceptable("The Purchase Price field must contain a number greater than Residual Value.");
            if($journalDescription == '' || strlen(str_replace(" ", "", $journalDescription)) <= 8) return throwResponseNotAcceptable("Please enter a valid purchase journal description");
            if(count($journalArrDetail) <= 0) return throwResponseNotAcceptable("Please enter the transaction account details first");

            $newReffNumber                  =   $generalJournalModel->getNewReffNumber('PA');
            $arrInsertDataPurchaseJournal   =   [
                'REFFNUMBER'        =>  $newReffNumber,
                'DATETRANSACTION'   =>  $purchaseDate,
                'DESCRIPTION'       =>  $journalDescription,
                'TOTALACCOUNT'      =>  $totalAccountJournal,
                'TOTALNOMINAL'      =>  $totalNominalJournal,
                'USERINSERT'        =>  $this->userData->name,
                'DATETIMEINSERT'    =>  $this->currentDateTime
            ];
        }

        try {
            $listAssetOwnedModel->db->transException(true)->transStart();

            try {
                $procInsertDataPurchaseJournal  =   $mainOperation->insertDataTable('t_journalrecap', $arrInsertDataPurchaseJournal);
                if(!$procInsertDataPurchaseJournal['status']) return switchMySQLErrorCode($procInsertDataPurchaseJournal['errCode']);
                $idJournalRecapPurchase         =   $procInsertDataPurchaseJournal['insertID'];

                foreach($journalArrDetail as $keyAccountDetail){
                    $idAccount              =   hashidDecode($keyAccountDetail[0]);
                    $positionDRCR           =   $keyAccountDetail[2] > 0 ? 'DR' : 'CR';
                    $arrInsertDataDetail    =   [
                        'IDJOURNALRECAP'=>  $idJournalRecapPurchase,
                        'IDACCOUNT'     =>  $idAccount,
                        'POSITIONDRCR'  =>  $positionDRCR,
                        'DESCRIPTION'   =>  $keyAccountDetail[1],
                        'DEBIT'         =>  $keyAccountDetail[2],
                        'CREDIT'        =>  $keyAccountDetail[3]
                    ];
                    $mainOperation->insertDataTable('t_journaldetails', $arrInsertDataDetail);
                }
            } catch (\Throwable $th) {
                return throwResponseInternalServerError('Internal server error - failed to add data purchase journal', $th);
            }

            $arrInsertData      =   [
                'IDASSETTYPE'               =>  $idAssetType,
                'IDASSETDEPRECIATIONGROUP'  =>  $idDepreciationGroup,
                'IDJOURNALTEMPLATERECAP'    =>  $idTemplateJournal,
                'IDJOURNALRECAPPURCHASE'    =>  $idJournalRecapPurchase,
                'ASSETNAME'                 =>  $assetName,
                'DESCRIPTION'               =>  $assetDescription,
                'PURCHASEDATE'              =>  $purchaseDate,
                'PURCHASEPRICE'             =>  $purchasePrice,
                'RESIDUALVALUE'             =>  $residualValue,
                'INSERTDATETIME'            =>  $this->currentDateTime,
                'INSERTUSER'                =>  $this->userData->name
            ];

            $procInsertData =   $mainOperation->insertDataTable('t_asset', $arrInsertData);

            if(!$procInsertData['status']) return switchMySQLErrorCode($procInsertData['errCode']);
            $idAsset                =   $procInsertData['insertID'];
            $detailDepreciationGroup=   $listAssetOwnedModel->getDetailDepreciationGroup($idDepreciationGroup);
    
            if(!$detailDepreciationGroup) throw new \Exception('Detail depreciation group not found');

            $templateJournalModel   =   new TemplateJournalModel();
            $detailTemplateJournal  =   $templateJournalModel->getListDetailTemplate($idTemplateJournal);
            $yearsBenefit           =   $detailDepreciationGroup['YEARSBENEFIT'];
            $totalNumberDepreciation=   $yearsBenefit * 12;
            $depreciationValue      =   number_format(($purchasePrice - $residualValue) / $totalNumberDepreciation, 0, '', '');
            $currentDateDepreciation=   $purchaseDate;
            $arrInsertDepreciation  =   [];

            for($depreciationNumber = 0; $depreciationNumber < $totalNumberDepreciation; $depreciationNumber++){
                $currentDateDepreciationTF  =   Time::createFromFormat('Y-m-d', $currentDateDepreciation);
                $dateDepreciationTF         =   $currentDateDepreciationTF->addMonths($depreciationNumber);
                $dateDepreciation           =   $dateDepreciationTF->toDateString();
                $dateDepreciation           =   date("Y-m-t", strtotime($dateDepreciation));
                $idJournalRecap             =   null;
                $statusJournal              =   0;

                if($dateDepreciation <= date('Y-m-d')){
                    if(!is_null($detailTemplateJournal)) $idJournalRecap =   $this->insertJournalDepreciation($depreciationNumber, $detailTemplateJournal, $arrInsertData, $dateDepreciation, $depreciationValue);
                    $statusJournal  =   1;
                }

                $arrInsertDepreciation[]    =   [
                    'IDASSET'           =>  $idAsset,
                    'IDJOURNALRECAP'    =>  $idJournalRecap,
                    'DEPRECIATIONNUMBER'=>  $depreciationNumber + 1,
                    'DEPRECIATIONDATE'  =>  $dateDepreciation,
                    'DEPRECIATIONVALUE' =>  $depreciationValue,
                    'STATUSJOURNAL'     =>  $statusJournal
                ];
            }
    
            $procInsertDepreciation =   $mainOperation->insertDataBatchTable('t_assetdepreciation', $arrInsertDepreciation);
            if(count($arrInsertDepreciation) <= 0 || !$procInsertDepreciation['status']) throw new \Exception('Failed to insert depreciation list');
    		$listAssetOwnedModel->db->transComplete();

            return throwResponseOK('New asset data has been added');
        } catch (\Throwable $th) {
            return throwResponseInternalServerError('Internal server error - failed to add data<br/>'.$th->getMessage(), $th);
        }
    }

    private function insertJournalDepreciation($depreciationNumber, $detailTemplateJournal, $detailAsset, $dateTransaction, $depreciationValue)
    {
        $generalJournalModel=   new GeneralJournalModel();
        $newReffNumber      =   $generalJournalModel->getNewReffNumber('DJ');
        $dateTransactionTF  =   Time::createFromFormat('Y-m-d', $dateTransaction);
        $monthTransaction   =   $dateTransactionTF->toLocalizedString('MMM');
        $yearTransaction    =   $dateTransactionTF->toLocalizedString('yyyy');
        $assetName          =   $detailAsset['ASSETNAME'];
        $description        =   $detailTemplateJournal[0]->DESCRIPTION;
        $description        =   str_replace(['!detail aset!', '!bulan!', '!tahun!'], [$assetName, $monthTransaction, $yearTransaction], $description);
        $totalAccount       =   count($detailTemplateJournal);

        if($depreciationNumber != 1){
            $reffNumber     =   substr($newReffNumber, -6) * 1;
            $reffNumber     =   $reffNumber + $depreciationNumber;
            $newReffNumber  =   substr($newReffNumber, 0, 6).str_pad($reffNumber, 6, '0', STR_PAD_LEFT);
        }

        $arrInsertData      =   [
            'REFFNUMBER'        =>  $newReffNumber,
            'DATETRANSACTION'   =>  $dateTransaction,
            'DESCRIPTION'       =>  $description,
            'TOTALACCOUNT'      =>  $totalAccount,
            'TOTALNOMINAL'      =>  $depreciationValue,
            'USERINSERT'        =>  $this->userData->name,
            'DATETIMEINSERT'    =>  $this->currentDateTime
        ];

        try {
            $generalJournalModel->db->transException(true)->transStart();
            $mainOperation  =   new MainOperation();
            $procInsertData =   $mainOperation->insertDataTable('t_journalrecap', $arrInsertData);

            if(!$procInsertData['status'])  return switchMySQLErrorCode($procInsertData['errCode'], false);
            $idJournalRecap =   $procInsertData['insertID'];

            foreach($detailTemplateJournal as $keyTemplateJournal){
                $positionDRCR   =   $keyTemplateJournal->DEFAULTDRCR;
                $debitValue     =   $positionDRCR == 'DR' ? $depreciationValue : 0;
                $creditValue    =   $positionDRCR == 'CR' ? $depreciationValue : 0;
                $arrInsertDataDetail    =   [
                    'IDJOURNALRECAP'=>  $idJournalRecap,
                    'IDACCOUNT'     =>  $keyTemplateJournal->IDACCOUNT,
                    'POSITIONDRCR'  =>  $positionDRCR,
                    'DESCRIPTION'   =>  '',
                    'DEBIT'         =>  $debitValue,
                    'CREDIT'        =>  $creditValue
                ];
                $mainOperation->insertDataTable('t_journaldetails', $arrInsertDataDetail);
            }
            $generalJournalModel->db->transComplete();

            return $idJournalRecap;
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    //OK
    public function getDetailAsset()
    {
        helper(['form']);
        $rules      =   [
            'idAsset'   => ['label' => 'Id Asset', 'rules' => 'required|alpha_numeric']
        ];

        $messages   =   [
            'idAsset'   => [
                'required'      => 'Invalid data sent',
                'alpha_numeric' => 'Invalid data sent'
            ]
        ];

        if(!$this->validate($rules, $messages)) return $this->fail($this->validator->getErrors());

        $listAssetOwnedModel=   new ListAssetOwnedModel();
        $idAsset            =   $this->request->getVar('idAsset');
        $idAsset            =   hashidDecode($idAsset);
        $detailAsset        =   $listAssetOwnedModel->find($idAsset);

        if(!$detailAsset) return throwResponseNotFound("No detail found for asset selected");

        $detailPurchaseJournal                  =   $this->getDetailPurchaseAssetJournal($detailAsset['IDJOURNALRECAPPURCHASE']);
        $detailAsset['IDASSET']                 =   hashidEncode($detailAsset['IDASSET']);
        $detailAsset['IDASSETTYPE']             =   hashidEncode($detailAsset['IDASSETTYPE']);
        $detailAsset['IDASSETDEPRECIATIONGROUP']=   hashidEncode($detailAsset['IDASSETDEPRECIATIONGROUP']);
        $detailAsset['IDJOURNALTEMPLATERECAP']  =   hashidEncode($detailAsset['IDJOURNALTEMPLATERECAP']);
        $detailAsset['IDJOURNALRECAPPURCHASE']  =   hashidEncode($detailAsset['IDJOURNALRECAPPURCHASE']);
        $detailAsset['PURCHASEDATE']            =   Time::createFromFormat('Y-m-d', $detailAsset['PURCHASEDATE']);
        $detailAsset['PURCHASEDATE']            =   $detailAsset['PURCHASEDATE']->toLocalizedString('dd-MM-yyyy');

        return $this->setResponseFormat('json')
                    ->respond([
                        "detailAsset"           =>  $detailAsset,
                        "detailPurchaseJournal" =>  $detailPurchaseJournal
                     ]);
    }

    private function getDetailPurchaseAssetJournal($idJournalRecapPurchase){
        $generalJournalModel=   new GeneralJournalModel();
        $detailRecap        =   $generalJournalModel->find($idJournalRecapPurchase);

        if(!$detailRecap || is_null($idJournalRecapPurchase) || $idJournalRecapPurchase == '' || $idJournalRecapPurchase == 0) return ["detailRecap" => "", "listDetailJournal" => ""];
        $listDetailJournal  =   $generalJournalModel->getListDetailJournal($idJournalRecapPurchase);

        $detailRecap['DATETRANSACTION'] =   Time::createFromFormat('Y-m-d', $detailRecap['DATETRANSACTION']);
        $detailRecap['DATETRANSACTION'] =   $detailRecap['DATETRANSACTION']->toLocalizedString('d M Y');
        $detailRecap['IDJOURNALRECAP']  =   hashidEncode($detailRecap['IDJOURNALRECAP']);
        $listDetailJournal              =   encodeDatabaseObjectResultKey($listDetailJournal, ['IDJOURNALDETAILS', 'IDACCOUNT']);

        return [
            "detailRecap"       =>  $detailRecap,
            "listDetailJournal" =>  $listDetailJournal
        ];
    }



    //OK
    public function updateData()
    {
        helper(['form']);
        $rules      =   [
            'idAsset'               =>  ['label' => 'Id Asset', 'rules' => 'required|alpha_numeric'],
            'idDepreciationGroup'   =>  ['label' => 'Depreciation Group', 'rules' => 'required|alpha_numeric'],
            'idTemplateJournal'     =>  ['label' => 'Template Journal', 'rules' => 'required|alpha_numeric'],
            'idAssetType'           =>  ['label' => 'Asset Type', 'rules' => 'required|alpha_numeric'],
            'assetName'             =>  ['label' => 'Asset Name', 'rules' => 'required|alpha_numeric_punct'],
            'purchaseDate'          =>  ['label' => 'Purchase Date', 'rules' => 'required|exact_length[10]|valid_date[d-m-Y]'],
            'purchasePrice'         =>  ['label' => 'Purchase Price', 'rules' => 'required|numeric'],
            'residualValue'         =>  ['label' => 'Residual Value', 'rules' => 'required|numeric'],
        ];

        $messages   =   [
            'idAsset'        => [
                'required'      => 'Invalid data sent',
                'alpha_numeric' => 'Invalid data sent'
            ]
        ];
        if(!$this->validate($rules, $messages)) return $this->fail($this->validator->getErrors());

        $mainOperation          =   new MainOperation();
        $listAssetOwnedModel    =   new ListAssetOwnedModel();
        $templateJournalModel   =   new TemplateJournalModel();
        $generalJournalModel    =   new GeneralJournalModel();
        $idAsset                =   $this->request->getVar('idAsset');
        $idAsset                =   hashidDecode($idAsset);
        $idDepreciationGroup    =   $this->request->getVar('idDepreciationGroup');
        $idDepreciationGroup    =   hashidDecode($idDepreciationGroup);
        $idTemplateJournal      =   $this->request->getVar('idTemplateJournal');
        $idTemplateJournal      =   hashidDecode($idTemplateJournal);
        $idAssetType            =   $this->request->getVar('idAssetType');
        $idAssetType            =   hashidDecode($idAssetType);
        $assetName              =   $this->request->getVar('assetName');
        $assetDescription       =   $this->request->getVar('assetDescription');
        $purchaseDate           =   $this->request->getVar('purchaseDate');
        $purchaseDateTF         =   Time::createFromFormat('d-m-Y', $purchaseDate);
        $purchaseDate           =   $purchaseDateTF->toDateString();
        $purchasePrice          =   $this->request->getVar('purchasePrice');
        $residualValue          =   $this->request->getVar('residualValue');
        $detailAsset            =   $listAssetOwnedModel->find($idAsset);
        $detailTemplateJournal  =   $templateJournalModel->getListDetailTemplate($idTemplateJournal);

        if($purchasePrice <= $residualValue) return throwResponseNotAcceptable("The Purchase Price field must contain a number greater than Residual Value.");
        if(!$detailAsset) return throwResponseNotFound("No detail found for asset selected");

        $dataDepreciation       =   $listAssetOwnedModel->getDataDepreciation($idAsset);
        $detailDepreciationGroup=   $listAssetOwnedModel->getDetailDepreciationGroup($idDepreciationGroup);
        $yearsBenefit           =   $detailDepreciationGroup['YEARSBENEFIT'];
        $totalNumberDepreciation=   $yearsBenefit * 12;
        $DBIdDepreciationGroup  =   $detailAsset['IDASSETDEPRECIATIONGROUP'];

        $arrUpdateData      =   [
            'IDASSETTYPE'               =>  $idAssetType,
            'IDASSETDEPRECIATIONGROUP'  =>  $idDepreciationGroup,
            'IDJOURNALTEMPLATERECAP'    =>  $idTemplateJournal,
            'ASSETNAME'                 =>  $assetName,
            'DESCRIPTION'               =>  $assetDescription,
            'PURCHASEDATE'              =>  $purchaseDate,
            'PURCHASEPRICE'             =>  $purchasePrice,
            'RESIDUALVALUE'             =>  $residualValue
        ];

        try {
            $listAssetOwnedModel->db->transException(true)->transStart();
            $mainOperation->updateDataTable('t_asset', $arrUpdateData, ['IDASSET' => $idAsset]);
            $depreciationValue  =   number_format(($purchasePrice - $residualValue) / $totalNumberDepreciation, 0, '', '');
    
            //Update data (detail depresiasi & jurnal) jika dengan kondisi ada perubahan atau tidak :
            // 1. Nilai residu / harga beli berubah
            // 2. Tanggal beli berubah
            // 3. Template jurnal yang dipilih berubah
            $currentDateDepreciation=   $purchaseDate;
            for($depreciationNumber = 0; $depreciationNumber < $totalNumberDepreciation; $depreciationNumber++){
                $currentDateDepreciationTF  =   Time::createFromFormat('Y-m-d', $currentDateDepreciation);
                $dateDepreciationTF         =   $currentDateDepreciationTF->addMonths($depreciationNumber);
                $dateDepreciation           =   $dateDepreciationTF->toDateString();
                $dateDepreciation           =   date("Y-m-t", strtotime($dateDepreciation));
                $monthTransaction           =   $dateDepreciationTF->toLocalizedString('MMM');
                $yearTransaction            =   $dateDepreciationTF->toLocalizedString('yyyy');
                $descriptionJournal         =   $detailTemplateJournal[0]->DESCRIPTION;
                $descriptionJournal         =   str_replace(['!detail aset!', '!bulan!', '!tahun!'], [$assetName, $monthTransaction, $yearTransaction], $descriptionJournal);
                $idAccountDebitJournal      =   $detailTemplateJournal[0]->DEFAULTDRCR == 'DR' ? $detailTemplateJournal[0]->IDACCOUNT : $detailTemplateJournal[1]->IDACCOUNT;
                $idAccountCreditJournal     =   $detailTemplateJournal[0]->DEFAULTDRCR == 'CR' ? $detailTemplateJournal[0]->IDACCOUNT : $detailTemplateJournal[1]->IDACCOUNT;
                $idAssetDepreciation        =   isset($dataDepreciation[$depreciationNumber]) ? $dataDepreciation[$depreciationNumber]->IDASSETDEPRECIATION : 0;
                $idJournalRecap             =   isset($dataDepreciation[$depreciationNumber]) ? $dataDepreciation[$depreciationNumber]->IDJOURNALRECAP : 0;
                $arrIdJournalDetailDebit    =   isset($dataDepreciation[$depreciationNumber]) && !is_null($dataDepreciation[$depreciationNumber]->ARRIDJOURNALDETAILDEBIT) ? explode(',', $dataDepreciation[$depreciationNumber]->ARRIDJOURNALDETAILDEBIT) : [];
                $arrIdJournalDetailCredit   =   isset($dataDepreciation[$depreciationNumber]) && !is_null($dataDepreciation[$depreciationNumber]->ARRIDJOURNALDETAILCREDIT) ? explode(',', $dataDepreciation[$depreciationNumber]->ARRIDJOURNALDETAILCREDIT) : [];
                $arrUpdateDepreciation      =   [
                    'DEPRECIATIONDATE' => $dateDepreciation,
                    'DEPRECIATIONVALUE'=> $depreciationValue
                ];

                if($idAssetDepreciation != 0) $mainOperation->updateDataTable('t_assetdepreciation', $arrUpdateDepreciation, ['IDASSETDEPRECIATION' => $idAssetDepreciation]);
                if($idJournalRecap != 0) {
                    $arrUpdateJournalRecap  =   [
                        'DESCRIPTION'       => $descriptionJournal,
                        'TOTALNOMINAL'      => $depreciationValue,
                        'DATETRANSACTION'   => $dateDepreciation,
                        'USERUPDATE'        => $this->userData->name,
                        'DATETIMEUPDATE'    => $this->currentDateTime
                    ];

                    $mainOperation->updateDataTable('t_journalrecap', $arrUpdateJournalRecap, ['IDJOURNALRECAP' => $idJournalRecap]);

                    if(count($arrIdJournalDetailDebit) > 0){
                        foreach($arrIdJournalDetailDebit as $idJournalDetailDebit){
                            $mainOperation->updateDataTable('t_journaldetails', ['IDACCOUNT' => $idAccountDebitJournal, 'DEBIT' => $depreciationValue, 'CREDIT' => 0], ['IDJOURNALDETAILS' => $idJournalDetailDebit]);
                        }
                    }

                    if(count($arrIdJournalDetailCredit) > 0){
                        foreach($arrIdJournalDetailCredit as $idJournalDetailCredit){
                            $mainOperation->updateDataTable('t_journaldetails', ['IDACCOUNT' => $idAccountCreditJournal, 'DEBIT' => 0, 'CREDIT' => $depreciationValue], ['IDJOURNALDETAILS' => $idJournalDetailCredit]);
                        }
                    }
                }
            }

            //Insert - Delete data (detail depresiasi & jurnal) jika jumlah periode penyusutan tidak sama
            if($DBIdDepreciationGroup != $idDepreciationGroup){
                $DBDetailDepreciationGroup  =   $listAssetOwnedModel->getDetailDepreciationGroup($DBIdDepreciationGroup);
                $DBYearsBenefit             =   $DBDetailDepreciationGroup['YEARSBENEFIT'];
                $DBTotalNumberDepreciation  =   $DBYearsBenefit * 12;

                if($DBTotalNumberDepreciation > $totalNumberDepreciation){
                    foreach($dataDepreciation as $keyDepreciation){
                        $idAssetDepreciation=   $keyDepreciation->IDASSETDEPRECIATION;
                        $idJournalRecap     =   $keyDepreciation->IDJOURNALRECAP;
                        $depreciationNumber =   $keyDepreciation->DEPRECIATIONNUMBER;
                        if($depreciationNumber > $totalNumberDepreciation){
                            if($idJournalRecap != 0) $generalJournalModel->delete($idJournalRecap);
                            $mainOperation->deleteDataTable('t_assetdepreciation', ['IDASSETDEPRECIATION' => $idAssetDepreciation]);
                        }
                    }
                } else if($DBTotalNumberDepreciation < $totalNumberDepreciation) {
                    $arrInsertDepreciation  =   [];
                    for($depreciationNumber = $DBTotalNumberDepreciation; $depreciationNumber < $totalNumberDepreciation; $depreciationNumber++){
                        $currentDateDepreciationTF  =   Time::createFromFormat('Y-m-d', $currentDateDepreciation);
                        $dateDepreciationTF         =   $currentDateDepreciationTF->addMonths($depreciationNumber);
                        $dateDepreciation           =   $dateDepreciationTF->toDateString();
                        $dateDepreciation           =   date("Y-m-t", strtotime($dateDepreciation));
                        $idJournalRecap             =   null;
                        $statusJournal              =   0;

                        if($dateDepreciation <= date('Y-m-d')){
                            if(!is_null($detailTemplateJournal)) $idJournalRecap =   $this->insertJournalDepreciation($depreciationNumber, $detailTemplateJournal, $arrUpdateData, $dateDepreciation, $depreciationValue);
                            $statusJournal  =   1;
                        }

                        $arrInsertDepreciation[]    =   [
                            'IDASSET'           =>  $idAsset,
                            'IDJOURNALRECAP'    =>  $idJournalRecap,
                            'DEPRECIATIONNUMBER'=>  $depreciationNumber + 1,
                            'DEPRECIATIONDATE'  =>  $dateDepreciation,
                            'DEPRECIATIONVALUE' =>  $depreciationValue,
                            'STATUSJOURNAL'     =>  $statusJournal
                        ];
                    }

                    $procInsertDepreciation =   $mainOperation->insertDataBatchTable('t_assetdepreciation', $arrInsertDepreciation);
                    if(count($arrInsertDepreciation) <= 0 || !$procInsertDepreciation['status']) throw new \Exception('Failed to insert depreciation list');
                }
            }

    		$listAssetOwnedModel->db->transComplete();
            return throwResponseOK('Asset data has been updated');
        } catch (\Throwable $th) {
            return throwResponseInternalServerError('Internal server error - failed to update data');
        }
    }

    //OK
    public function deleteDataAsset()
    {
        helper(['form']);
        $rules      =   [
            'idAsset'   =>  ['label' => 'Id Asset', 'rules' => 'required|alpha_numeric']
        ];

        $messages   =   [
            'idAsset'   => [
                'required'      => 'Invalid data sent',
                'alpha_numeric' => 'Invalid data sent'
            ]
        ];
        if(!$this->validate($rules, $messages)) return $this->fail($this->validator->getErrors());

        $mainOperation      =   new MainOperation();
        $listAssetOwnedModel=   new ListAssetOwnedModel();
        $generalJournalModel=   new GeneralJournalModel();
        $idAsset            =   $this->request->getVar('idAsset');
        $idAsset            =   hashidDecode($idAsset);
        $dataDepreciation   =   $listAssetOwnedModel->getDataDepreciation($idAsset);

        try{
            $listAssetOwnedModel->db->transException(true)->transStart();
            if($dataDepreciation && count($dataDepreciation) > 0){
                foreach($dataDepreciation as $keyDepreciation){
                    $idJournalRecap =   $keyDepreciation->IDJOURNALRECAP;
                    if($idJournalRecap != 0) $generalJournalModel->delete($idJournalRecap);
                }
            }

            $listAssetOwnedModel->delete($idAsset);
            $mainOperation->deleteDataTable('t_asset', ['IDASSET' => $idAsset]);
            $listAssetOwnedModel->db->transComplete();
        } catch (\Throwable $th) {
            return throwResponseNotAcceptable('Internal database script error');
        }

        return throwResponseOK('Asset data has been deleted');
    }

    public function getDetailsAssetJournal()
    {
        helper(['form']);
        $rules      =   [
            'idAsset'   => ['label' => 'Id Asset', 'rules' => 'required|alpha_numeric']
        ];

        $messages   =   [
            'idAsset'   => [
                'required'      => 'Invalid data sent',
                'alpha_numeric' => 'Invalid data sent'
            ]
        ];

        if(!$this->validate($rules, $messages)) return $this->fail($this->validator->getErrors());

        $listAssetOwnedModel=   new ListAssetOwnedModel();
        $idAsset            =   $this->request->getVar('idAsset');
        $idAsset            =   hashidDecode($idAsset);
        $detailAsset        =   $listAssetOwnedModel->getDetailAsset($idAsset);

        if(!$detailAsset) return throwResponseNotFound("No detail found for asset selected");

        $dataAssetDepreciation              =   $listAssetOwnedModel->getDataDepreciation($idAsset, false);
        $dataAssetDepreciation              =   $dataAssetDepreciation ? encodeDatabaseObjectResultKey($dataAssetDepreciation, ['IDASSETDEPRECIATION', 'IDJOURNALRECAP']) : [];
        $totalNumberDepreciation            =   $detailAsset['YEARSBENEFIT'] * 12;
        $detailAsset['DEPRECIATIONVALUE']   =   $detailAsset['PURCHASEPRICE'] - $detailAsset['RESIDUALVALUE'];
        $detailAsset['DEPRECIATIONPERMONTH']=   number_format($detailAsset['DEPRECIATIONVALUE'] / $totalNumberDepreciation, 0, '', '');
        $detailPurchaseJournal              =   $this->getDetailPurchaseAssetJournal($detailAsset['IDJOURNALRECAPPURCHASE']);
        unset($detailAsset['IDJOURNALRECAPPURCHASE']);

        return $this->setResponseFormat('json')
                    ->respond([
                        "detailAsset"           =>  $detailAsset,
                        "dataAssetDepreciation" =>  $dataAssetDepreciation,
                        "detailPurchaseJournal" =>  $detailPurchaseJournal
                     ]);
    }
}