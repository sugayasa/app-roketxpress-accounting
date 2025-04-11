<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\AccessModel;
use App\Models\NotificationUserAdminModel;
use CodeIgniter\I18n\Time;

class Access extends ResourceController
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

    public function check()
    {
        helper(['form', 'firebaseJWT', 'hashid']);

        $rules  =   [
            'hardwareID'    =>  ['label' => 'Hardware ID', 'rules' => 'required|alpha_numeric_punct|min_length[10]'],
        ];

        if(!$this->validate($rules)) return $this->fail($this->validator->getErrors());

        $hardwareID     =   strtoupper($this->request->getVar('hardwareID'));
        $header         =   $this->request->getServer('HTTP_AUTHORIZATION');
        $explodeHeader  =   $header != "" ? explode(' ', $header) : [];
        $token          =   is_array($explodeHeader) && isset($explodeHeader[1]) && $explodeHeader[1] != "" ? $explodeHeader[1] : "";
        $timeCreate     =   Time::now(APP_TIMEZONE)->toDateTimeString();
        $statusCode     =   401;
        $responseMsg    =   'Please enter your username and password';
        $captchaCode    =   generateRandomCharacter(4, 3);

        $userAdminData  =   array(
            "name"  =>   "",
            "email" =>   ""
        );

        $tokenPayload   =   array(
            "idUserAdmin"       =>  0,
            "idUserAdminLevel"  =>  0,
            "username"          =>  "",
            "name"              =>  "",
            "email"             =>  "",
            "captchaCode"       =>  $captchaCode,
            "hardwareID"        =>  $hardwareID,
            "timeCreate"        =>  $timeCreate
        );

        $defaultToken           =   encodeJWTToken($tokenPayload);

        if(isset($token) && $token != ""){
            try {
                $dataDecode     =   decodeJWTToken($token);
                $idUserAdmin    =   intval($dataDecode->idUserAdmin);
                $hardwareIDToken=   $dataDecode->hardwareID;
                $timeCreateToken=   $dataDecode->timeCreate;

                if($idUserAdmin != 0){
                    $accessModel    =   new AccessModel(); 
                    $userAdminDataDB=   $accessModel
                                        ->where("IDUSERADMIN", $idUserAdmin)
                                        ->first();

                    if(!$userAdminDataDB || is_null($userAdminDataDB)) return throwResponseUnauthorized('[E-AUTH-001.1.0] Your user is not registered. Please log in to continue', ['token'=>$defaultToken]);

                    $hardwareIDDB   =   $userAdminDataDB['HARDWAREID'];

                    if($hardwareID == $hardwareIDDB && $hardwareID == $hardwareIDToken){
                        $timeCreateToken    =   Time::parse($timeCreateToken, APP_TIMEZONE);
                        $minutesDifference  =   $timeCreateToken->difference(Time::now(APP_TIMEZONE))->getMinutes();

                        if($minutesDifference > MAX_INACTIVE_SESSION_MINUTES){
                            return throwResponseForbidden('Session ends, please log in first');
                        }
            
                        $accessModel->update($idUserAdmin, ['DATETIMELOGIN' => $timeCreate]);

                        $userAdminData  =   [
                            "name"  =>   $userAdminDataDB['NAME'],
                            "email" =>   $userAdminDataDB['EMAIL']
                        ];

                        $tokenPayload['idUserAdmin']        =   $idUserAdmin;
                        $tokenPayload['idUserAdminLevel']   =   $userAdminDataDB['IDUSERADMINLEVEL'];
                        $tokenPayload['username']           =   $userAdminDataDB['USERNAME'];
                        $tokenPayload['name']               =   $userAdminDataDB['NAME'];
                        $tokenPayload['email']              =   $userAdminDataDB['EMAIL'];
                        $statusCode                         =   200;
                        $responseMsg                        =   'Login successfully, continue';
                    } else {
                        return throwResponseUnauthorized('[E-AUTH-001.1.2] Hardware ID changed, please login to continue', ['token'=>$defaultToken]);
                    }
                }
            } catch (\Throwable $th) {
                return throwResponseUnauthorized('[E-AUTH-001.2.0] Invalid Token', ['token'=>$defaultToken]);
            }
        }

        $newToken       =   encodeJWTToken($tokenPayload);
        return $this->setResponseFormat('json')
                    ->respond([
                        'token'         =>  $newToken,
                        'userAdminData' =>  $userAdminData,
                        'messages'      =>  [
                            "accessMessage" =>  $responseMsg
                        ]
                    ])
                    ->setStatusCode($statusCode);

    }

    public function login()
    {
        helper(['form']);
        $rules  =   [
            'username'  =>  'required|min_length[5]',
            'password'  =>  'required|min_length[5]',
            'captcha'   =>  'required|alpha_numeric|exact_length[4]'
        ];

        if(!$this->validate($rules)) return $this->fail($this->validator->getErrors());

        $accessModel    =   new AccessModel();
        $username       =   $this->request->getVar('username');
        $password       =   $this->request->getVar('password');
        $captcha        =   $this->request->getVar('captcha');
        $captchaToken   =   $this->userData->captchaCode;

        if($captcha != $captchaToken) return $this->fail('The captcha code you entered does not match');

        $dataUserAdmin  =   $accessModel->where("USERNAME", $username)->where("STATUS", 1)->first();

        if(!$dataUserAdmin) return $this->failNotFound('There are no matching usernames, enter another username');
 
        $passwordVerify =   password_verify($password, $dataUserAdmin['PASSWORD']);
        if(!$passwordVerify) return $this->fail('The password you entered is incorrect');

        $idUserAdmin        =   $dataUserAdmin['IDUSERADMIN'];
        $idUserAdminLevel   =   $dataUserAdmin['IDUSERADMINLEVEL'];
        $name               =   $dataUserAdmin['NAME'];
        $email              =   $dataUserAdmin['EMAIL'];
        $currentDateTime    =   $this->currentDateTime;
        $hardwareID         =   $this->userData->hardwareID;
        
        $dataUpdateUserAdmin    =   [
            'HARDWAREID'    =>   $hardwareID,
            'DATETIMELOGIN' =>   $currentDateTime    
        ];

        $accessModel        =   new AccessModel();
        $accessModel->where('HARDWAREID', $hardwareID)->set('HARDWAREID', 'null', false)->update();
        $accessModel->update($idUserAdmin, $dataUpdateUserAdmin);

        $tokenUpdate        =   array(
            "idUserAdmin"       =>  $idUserAdmin,
            "idUserAdminLevel"  =>  $idUserAdminLevel,
            "username"          =>  $username,
            "name"              =>  $name,
            "email"             =>  $email
        );
        
        return $this->setResponseFormat('json')
                    ->respond([
                        'tokenUpdate'   =>  $tokenUpdate,
                        'message'       =>  "Login successfully"
                    ]);		
    }

    public function logout($token = false)
    {
        if(!$token || $token == "") return $this->failUnauthorized('[E-AUTH-001.1] Token Required');
        helper(['firebaseJWT']);

        try {
            $dataDecode         =   decodeJWTToken($token);
            $idUserAdmin        =   $dataDecode->idUserAdmin;
            $hardwareID         =   $dataDecode->hardwareID;
            $accessModel        =   new AccessModel();
            $userAdminDataDB    =   $accessModel
                                    ->where("IDUSERADMIN", $idUserAdmin)
                                    ->first();

            if(!$userAdminDataDB || is_null($userAdminDataDB)) return $this->failUnauthorized('[E-AUTH-001.3] Invalid token - Not registered');

            $hardwareIDDB       =   $userAdminDataDB['HARDWAREID'];

            if($hardwareID == $hardwareIDDB){
                $accessModel->where('HARDWAREID', $hardwareID)->set('HARDWAREID', 'null', false)->update();
            }

            return redirect()->to(BASE_URL.'logoutPage');
        } catch (\Throwable $th) {
            return $this->failUnauthorized('[E-AUTH-001.2] Token tidak valid - '.$th->getMessage());
        }
    }

    public function captcha($token = '')
    {
        if(!$token || $token == "") $this->returnBlankCaptcha();
        helper(['firebaseJWT']);

        try {
            $dataDecode     =   decodeJWTToken($token);
            $captchaCode    =   $dataDecode->captchaCode;
            $codeLength     =   strlen($captchaCode);

            generateCaptchaImage($captchaCode, $codeLength);
        } catch (\Throwable $th) {
            $this->returnBlankCaptcha();
        }
    }

    private function returnBlankCaptcha()
    {
        $img    =   imagecreatetruecolor(120, 20);
        $bg     =   imagecolorallocate ( $img, 255, 255, 255 );
        imagefilledrectangle($img, 0, 0, 120, 20, $bg);
        
        ob_start();
        imagejpeg($img, "blank.jpg", 100);
        $contents = ob_get_contents();
        ob_end_clean();

        $dataUri = "data:image/jpeg;base64," . base64_encode($contents);
        echo $dataUri;
    }

    public function getDataOption()
    {
        $accessModel                    =   new AccessModel();
        $dataUserAdminLevel             =   encodeDatabaseObjectResultKey($accessModel->getDataUserAdminLevel(), 'ID');
        $dataUserAdminLevelMenu         =   encodeDatabaseObjectResultKey($accessModel->getDataUserAdminLevelMenu(), 'ID');
        $dataNotificationUserAdminType  =   encodeDatabaseObjectResultKey($accessModel->getDataNotificationUserAdminType(), 'ID');
        $dataAccountGeneral             =   encodeDatabaseObjectResultKey($accessModel->getDataAccountGeneral(), 'ID');
        $dataAccountMain                =   encodeDatabaseObjectResultKey($accessModel->getDataAccountMain(), ['ID', 'IDGROUP', 'PARENTVALUE']);
        $dataAccountSub                 =   encodeDatabaseObjectResultKey($accessModel->getDataAccountSub(), ['ID', 'IDGROUP', 'PARENTVALUE', 'PARENTVALUE2']);
        $dataAssetType                  =   encodeDatabaseObjectResultKey($accessModel->getDataAssetType(), 'ID');
        $dataDepreciationGroup          =   encodeDatabaseObjectResultKey($accessModel->getDataDepreciationGroup(), 'ID');

        return $this->setResponseFormat('json')
                    ->respond([
                        "data"  =>  [
                            "dataUserAdminLevelMenu"        => $dataUserAdminLevelMenu,
                            "dataUserAdminLevel"            => $dataUserAdminLevel,
                            "dataNotificationUserAdminType" => $dataNotificationUserAdminType,
                            "dataAccountGeneral"            => $dataAccountGeneral,
                            "dataAccountMain"               => $dataAccountMain,
                            "dataAccountSub"                => $dataAccountSub,
                            "dataAssetType"                 => $dataAssetType,
                            "dataDepreciationGroup"         => $dataDepreciationGroup
                        ]
                    ]);
    }

    public function getDataOptionByKey($keyName, $optionName = false, $keyword = false)
    {
        $accessModel    =   new AccessModel();
        $optionName     =   $optionName != false ? $optionName : 'randomOption';
        $dataOption     =   [];
        $arrEncodeKey   =   ['ID'];

        switch($keyName){
            case 'templateJournalData' :
                $dataOption =   $accessModel->getDataSettingTemplateJournal($keyword);
                break;
            default :
                break;
        }

        $dataOption     =   encodeDatabaseObjectResultKey($dataOption, $arrEncodeKey);
        return $this->setResponseFormat('json')
                ->respond([
                    "dataOption"    =>  $dataOption,
                    "optionName"    =>  $optionName
                ]);
    }
	
    public function unreadNotificationList()
    {
        $notificationUserAdminModel =   new NotificationUserAdminModel();
        $idUserAdmin                =   $this->userData->idUserAdmin;
        $unreadNotificationList	    =	$notificationUserAdminModel->getDataUnreadNotification($idUserAdmin);
		$totalUnreadNotification    =	0;
		$unreadNotificationArray    =	array();
		
		if($unreadNotificationList){
			foreach($unreadNotificationList as $unreadNotificationData){
				if(count($unreadNotificationArray) < 10){
					$unreadNotificationArray[]	=	$unreadNotificationData;
				}
                $unreadNotificationData->IDNOTIFICATIONUSERADMIN    =   hashidEncode($unreadNotificationData->IDNOTIFICATIONUSERADMIN);
                $unreadNotificationData->IDPRIMARY                  =   hashidEncode($unreadNotificationData->IDPRIMARY);
				$totalUnreadNotification++;
			}		
		}

        return $this->setResponseFormat('json')
                    ->respond([
                        "totalUnreadNotification"   =>  $totalUnreadNotification,
                        "unreadNotificationArray"   =>  $unreadNotificationArray
                     ]);
    }

    public function dismissNotification()
    {
        helper(['form']);
        $rules      =   [
            'idNotificationUserAdmin'  => ['label' => 'ID Notifikasi', 'rules' => 'required|alpha_numeric']
        ];

        $messages   =   [
            'idNotificationUserAdmin'  => [
                'required'      => 'The data sent is invalid',
                'alpha_numeric' => 'The data sent is invalid'
            ]
        ];

        if(!$this->validate($rules, $messages)) return $this->fail($this->validator->getErrors());

        $notificationUserAdminModel    =   new NotificationUserAdminModel();
        $idNotificationUserAdmin       =   $this->request->getVar('idNotificationUserAdmin');
        $idNotificationUserAdmin       =   hashidDecode($idNotificationUserAdmin);

        $notificationUserAdminModel->where('IDNOTIFICATIONUSERADMIN', $idNotificationUserAdmin)->set('DATETIMEREAD', $this->currentDateTime)->update();

        return $this->setResponseFormat('json')
                    ->respond([
                        "message"   =>  "The notification has been ignored"
                     ]);
    }

    public function dismissAllNotification()
    {
        $notificationUserAdminModel =   new NotificationUserAdminModel();
        $idUserAdmin                =   $this->userData->idUserAdmin;

        $notificationUserAdminModel
        ->where('IDUSERADMIN', $idUserAdmin)
        ->where('DATETIMEREAD', '0000-00-00 00:00:00')
        ->set('DATETIMEREAD', $this->currentDateTime)
        ->update();

        return $this->setResponseFormat('json')
                    ->respond([
                        "notification"  =>  "All unread notifications have been ignored"
                     ]);
    }

    public function detailProfileSetting()
    {
        $username   =   $this->userData->username;
        $name       =   $this->userData->name;
        $email      =   $this->userData->email;

        return $this->setResponseFormat('json')
                    ->respond([
                        "username"  =>  $username,
                        "name"      =>  $name,
                        "email"     =>  $email
                     ]);
    }

    public function saveDetailProfileSetting()
    {
        helper(['form']);
        $idUserAdmin  =   $this->userData->idUserAdmin;
        $rules          =   [
            'username'  => ['label' => 'Username', 'rules' => 'required|alpha_numeric|min_length[4]'],
            'name'      => ['label' => 'Nama', 'rules' => 'required|alpha_numeric_space|min_length[4]'],
            'email'     => ['label' => 'Email', 'rules' => 'required|valid_email|is_unique[m_useradmin.EMAIL, IDUSERADMIN, '.$idUserAdmin.']']
        ];

        $notifikasi =   [
            'email' => ['is_unique' => 'This email address is already registered, please enter another email address'],
        ];

        if(!$this->validate($rules, $notifikasi)) return $this->fail($this->validator->getErrors());

        $accessModel            =   new AccessModel();
        $username               =   $this->request->getVar('username');
        $name                   =   $this->request->getVar('name');
        $email                  =   $this->request->getVar('email');
        $oldPassword            =   $this->request->getVar('oldPassword');
        $newPassword            =   $this->request->getVar('newPassword');
        $repeatPassword         =   $this->request->getVar('repeatPassword');
        $relogin                =   false;

        $arrUpdateUserPartner   =   [
            'NAME'      =>  $name,
            'EMAIL'     =>  $email,
            'USERNAME'  =>  $username
        ];

        if($oldPassword != "" || $newPassword != "" || $repeatPassword != ""){
			if($oldPassword == "") return throwResponseNotAcceptable("Please enter your old password (your current password)");
			if($newPassword == "") return throwResponseNotAcceptable("Please enter a new password");
            if($repeatPassword == "") return throwResponseNotAcceptable("Please enter a new password repeat");
			if($newPassword != $repeatPassword) return throwResponseNotAcceptable("The repetition of the password you entered is not match");
			
            $dataUserAdmin  =   $accessModel->where("IDUSERADMIN", $idUserAdmin)->first();
            if(!$dataUserAdmin) return $this->failNotFound('Your user data was not found, please try again later');
            $passwordVerify =   password_verify($oldPassword, $dataUserAdmin['PASSWORD']);
            if(!$passwordVerify) return $this->fail('The old password you entered is incorrect');
			
			$arrUpdateUserPartner['PASSWORD']   =	password_hash($newPassword, PASSWORD_DEFAULT);
            $relogin                            =   true;
		}

        $accessModel->update($idUserAdmin, $arrUpdateUserPartner);
        $tokenUpdate            =   [
            "username"  =>  $username,
            "name"      =>  $name,
            "email"     =>  $email
        ];

        return $this->setResponseFormat('json')
                    ->respond([
                        "message"       =>  "Your user data has been updated",
                        "name"          =>  $name,
                        "email"         =>  $email,
                        "relogin"       =>  $relogin,
                        "tokenUpdate"   =>  $tokenUpdate
                     ]);
    }

    //need improvement
    public function getDataDashboard()
    {
        helper(['form']);
        $rules  =   [
            'month' =>  'required|exact_length[2]|numeric',
            'year'  =>  'required|exact_length[4]|numeric'
        ];

        if(!$this->validate($rules)) return $this->fail($this->validator->getErrors());

        $accessModel        =   new AccessModel();
        $idPartnerType      =   $this->userData->idPartnerType;
        $idVendor           =   $this->userData->idVendor;
        $idUserAdmin           =   $this->userData->idUserAdmin;
        $month              =   $this->request->getVar('month');
        $year               =   $this->request->getVar('year');
		$yearMonth			=	$year."-".$month;
		$firstDateYearMonth	=	$year."-".$month."-01";
		$lastDateYearMonth	=	date('Y-m-t', strtotime($firstDateYearMonth));
		$lastYearMonth		=	date("Y-m", strtotime('-1 month', strtotime($firstDateYearMonth)));
		$minReservationDate	=	"2022-04-01";
		$dataReservation	=	$idPartnerType == 1 ?
                                $accessModel->getDataTotalReservationVendor($yearMonth, $lastYearMonth, $idVendor) :
                                $accessModel->getDataTotalReservationUserAdmin($yearMonth, $lastYearMonth, $idUserAdmin);
		
		if($dataReservation){
			$dataReservation['PERCENTAGETHISMONTH']		=	0;
			$dataReservation['PERCENTAGETHISMONTHSTYLE']=	0;
			$dataReservation['PERCENTAGETODAY']			=	0;
			$dataReservation['PERCENTAGETOMORROW']		=	0;
			$totalReservationThisMonth					=	$dataReservation['TOTALRESERVATIONTHISMONTH'];
		}
		
		if($totalReservationThisMonth > 0){
			$dataReservation['PERCENTAGETHISMONTH']		=	$dataReservation['TOTALRESERVATIONLASTMONTH'] == 0 ? 0 : number_format($totalReservationThisMonth / $dataReservation['TOTALRESERVATIONLASTMONTH'] * 100, 0, '.', ',');
			$dataReservation['PERCENTAGETHISMONTHSTYLE']=	$dataReservation['PERCENTAGETHISMONTH'] > 100 ? 100 : $dataReservation['PERCENTAGETHISMONTH'];
			$dataReservation['PERCENTAGETODAY']			=	$totalReservationThisMonth == 0 ? 0 : number_format($dataReservation['TOTALRESERVATIONTODAY'] / $totalReservationThisMonth * 100, 0, '.', ',');
			$dataReservation['PERCENTAGETOMORROW']		=	$totalReservationThisMonth == 0 ? 0 : number_format($dataReservation['TOTALRESERVATIONTOMORROW'] / $totalReservationThisMonth * 100, 0, '.', ',');
			$minReservationDate							=	$dataReservation['MINRESERVATIONDATE'];
		}

		$year1				=	date('Y', strtotime($minReservationDate));
		$year2				=	date('Y', strtotime($firstDateYearMonth));
		$month1				=	date('m', strtotime($minReservationDate));
		$month2				=	date('m', strtotime($firstDateYearMonth));
		$totalMonth			=	(($year2 - $year1) * 12) + ($month2 - $month1);
		$dataTopProduct     =	$idPartnerType == 1 ?
                                $accessModel->getDataTopProductVendor($yearMonth, $totalMonth, $lastDateYearMonth, $idVendor) :
                                $accessModel->getDataTopProductUserAdmin($yearMonth, $totalMonth, $lastDateYearMonth, $idUserAdmin);
		$dataStatistic		=	$this->getDataStatistic($yearMonth, $firstDateYearMonth, $idPartnerType, $idVendor, $idUserAdmin);
		
		return $this->setResponseFormat('json')
                    ->respond([
                        "lastYearMonth"		=>	$lastYearMonth,
                        "dataReservation"	=>	$dataReservation,
                        "dataTopProduct"    =>	$dataTopProduct,
                        "dataStatistic"		=>	$dataStatistic,
                        "minReservationDate"=>	$minReservationDate,
                        "totalMonth"		=>	$totalMonth
                    ]);
	}
	
    //need improvement
	private function getDataStatistic($yearMonth, $firstDate, $idPartnerType, $idVendor, $idUserAdmin)
    {	
        $accessModel            =   new AccessModel();
		$totalDays			    =	date("t", strtotime($firstDate));
		$dataGraphReservation	=	$idPartnerType == 1 ?
                                    $accessModel->getDataGraphReservationVendor($yearMonth, $idVendor) :
                                    $accessModel->getDataGraphReservationUserAdmin($yearMonth, $idUserAdmin);
		$arrDates			    =	$arrDetailData	=	$arrDatesCheck   =   $arrTotalReservationDate   =   array();
		
		for($i=0; $i<$totalDays; $i++){
			$dateCheck		    =	date('Y-m-d', strtotime('+'.$i.' day', strtotime($firstDate)));
			$dateStr		    =	date('d', strtotime('+'.$i.' day', strtotime($firstDate)));
			
			$arrDates[]		    =	$dateStr;
			$arrDatesCheck[]    =	$dateCheck;
            $arrTotalReservationDate[]	=	0;			
		}
		
		if($dataGraphReservation){
			foreach($dataGraphReservation as $keyGraphReservation){
				$dateCheckDB=	$keyGraphReservation->SCHEDULEDATE;
				$index		=	array_search($dateCheckDB, $arrDatesCheck);
				
				$arrTotalReservationDate[$index]	=	$keyGraphReservation->TOTALRESERVATION;
			}
		}
		
        $arrDetailData[]	=	array(
                                        "label"			=>	"Total Reservartion",
                                        "data"			=>	$arrTotalReservationDate,
                                        "borderColor"	=>	"#4dc9f6",
                                        "borderWidth"	=>	3,
                                        "fill"			=>	false,
                                        "lineTension"	=>	0.3
                                    );
		
		return array(
						"arrDates"		=>	$arrDates,
						"arrDetailData"	=>	$arrDetailData
        );	
	}
}