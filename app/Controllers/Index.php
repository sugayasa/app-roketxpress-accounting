<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;
use App\Models\AccessModel;

class Index extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        return $this->failForbidden('[E-AUTH-000] Forbidden Access');
    }

    public function response404()
    {
        return $this->failNotFound('[E-AUTH-404] Not Found');
    }

    public function main()
    {
        return view('main');
    }

    public function loginPage()
    {
        return view('login');
    }

    public function mainPage()
    {
        helper(['form']);

        $hardwareID     =   strtoupper($this->request->getVar('hardwareID'));
        $lastPageAlias  =   strtoupper($this->request->getVar('lastPageAlias'));
        $header         =   $this->request->getServer('HTTP_AUTHORIZATION');
        $explodeHeader  =   $header != "" ? explode(' ', $header) : [];
        $token          =   is_array($explodeHeader) && isset($explodeHeader[1]) && $explodeHeader[1] != "" ? $explodeHeader[1] : "";

        if(isset($token) && $token != ""){
            try {
                $dataDecode         =   decodeJWTToken($token);
                $idUserAdmin        =   intval($dataDecode->idUserAdmin);
                $idUserAdminLevel   =   intval($dataDecode->idUserAdminLevel);
                $hardwareIDToken    =   $dataDecode->hardwareID;

                if($idUserAdmin != 0){
                    if(isset($idUserAdminLevel) && $idUserAdminLevel != "" && $idUserAdminLevel != 0){
                        $accessModel    =   new AccessModel();
                        $userAdminDataDB=   $accessModel->getUserAdminDetail($idUserAdmin);

                        if(!$userAdminDataDB || is_null($userAdminDataDB)) return $this->failUnauthorized('[E-AUTH-001.1.0] Invalid token - Not registered');

                        $hardwareIDDB       =   $userAdminDataDB['HARDWAREID'];
                        $idUserAdminLevel   =   $userAdminDataDB['IDUSERADMINLEVEL'];

                        if($hardwareID == $hardwareIDDB && $hardwareID == $hardwareIDToken){
                            $userAdminData  =   array(
                                "name"      =>   $userAdminDataDB['NAME'],
                                "email"     =>   $userAdminDataDB['EMAIL'],
                                "levelName" =>   $userAdminDataDB['LEVELNAME']
                            );

                            try {
                                $listMenuDB         =   $accessModel->getUserAdminMenu($idUserAdminLevel);
                                $listMenuGroupDB    =   $accessModel->getUserAdminGroupMenu($idUserAdminLevel);
                                $menuElement	    =	$this->menuBuilder($listMenuDB, $lastPageAlias, $listMenuGroupDB);
                                $arrElemModule      =   [
                                    "modalGeneralLedger"    =>  view('ElemModule/modalGeneralLedger', [], ['saveData' => true])
                                ];
                                $htmlRes            =   view(
                                                            'mainPage',
                                                            array(
                                                                "userAdminData"         => $userAdminData,
                                                                "menuElement"           => $menuElement,
                                                                "arrElemModule"         => $arrElemModule,
                                                                "allowNotifList"        => [],
                                                                "optionHour"	        => OPTION_HOUR,
                                                                "optionMinuteInterval"	=> OPTION_MINUTEINTERVAL,
                                                                "optionMonth"	        => OPTION_MONTH,
                                                                "optionYear"	        => OPTION_YEAR
                                                            ),
                                                            ['debug' => false]
                                                        );
                                return $this->setResponseFormat('json')
                                ->respond([
                                    'htmlRes'   =>  $htmlRes
                                ]);
                            } catch (\Throwable $th) {
                                var_dump($th); die();
                                return $this->failUnauthorized('[E-AUTH-001.1.1] Internal error. Failed to respond');
                            }
                        } else {
                            return $this->failUnauthorized('[E-AUTH-001.1.2] Invalid token - Hardware ID');
                        }
                    } else {
                        return $this->failUnauthorized('[E-AUTH-001.1.3] Invalid token - Level');
                    }
                } else {
                    return $this->failUnauthorized('[E-AUTH-001.1.4] Invalid token - User ID');
                }
            } catch (\Throwable $th) {
                return $this->failUnauthorized('[E-AUTH-001.2.0] Invalid token');
            }
        } else {
            return $this->failUnauthorized('[E-AUTH-001.2.0] Invalid token');
        }
    }

    public function menuBuilder($listMenuDB, $lastPageAlias, $listMenuGroupDB)
    {
        if($listMenuDB == "" || !is_array($listMenuDB) || empty($listMenuDB)){
			return "<li><center>No Menu</center></li>";
		} else {			
			$groupActive	=	0;
			$arrGroupCheck	=	array();
			$i				=	0;
			$menuElement	=	$groupActiveName	=	"";
				
			foreach($listMenuGroupDB as $keyMenuGroup){
				$arrGroupCheck[]    =	$keyMenuGroup->GROUPNAME;
			}
			
			foreach($listMenuDB as $keyMenu){
				
				if(!in_array($keyMenu->GROUPNAME, $arrGroupCheck)){
					
					if($groupActive == 1){
						$groupActive	=	0;
						$menuElement	.=	"</ul></li>";
					}

					$active			=	$lastPageAlias == $keyMenu->MENUALIAS ? "active" : "";
					$menuElement	.=	"<li id='menu".$keyMenu->MENUALIAS."' class='menu-item ".$active."' data-alias='".$keyMenu->MENUALIAS."' data-url='".$keyMenu->URL."'>
											<a href='#'><i class='fa ".$keyMenu->ICON."'></i> <span>".$keyMenu->MENUNAME."</span></a>";
					
				} else {
					
					if($groupActiveName != $keyMenu->GROUPNAME && $groupActiveName != "" && $groupActive == 1){
						$menuElement	.=	"</ul></li>";
					}
					
					if($groupActive == 0 || $groupActiveName != $keyMenu->GROUPNAME){
						$menuElement	.=	"<li class='has-sub-menu'><a href='#'><i class='fa ".$keyMenu->ICON."'></i> <span id='groupMenu".str_replace(" ", "", $keyMenu->GROUPNAME)."'>".$keyMenu->GROUPNAME."</span><span class='menu-expand'><i class='fa fa-chevron-down'></i></span></a><ul class='side-header-sub-menu' style='display: block;'>";
						$groupActive	=	1;
					}
					
					$menuElement	.=	"<li id='menu".$keyMenu->MENUALIAS."' class='menu-item' data-alias='".$keyMenu->MENUALIAS."' data-url='".$keyMenu->URL."'><a href='#'><span>".$keyMenu->MENUNAME."</span></a></li>";
					$groupActiveName=	$keyMenu->GROUPNAME;
				}
				
				$i++;
				
			}
			
			return $menuElement."</ul>";
		}
    }
}
