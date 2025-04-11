<?php

if(!function_exists('switchMySQLErrorCode')){
    function switchMySQLErrorCode($errorCode, $httpResponse = true){
       switch($errorCode){
            case 0		:	$msgError   =   "No data changes";
                            return $httpResponse ? throwResponseNotModified($msgError) : $msgError;
                            break;
            case 1062	:	$msgError   =   "There is duplication in the input data";
                            return $httpResponse ? throwResponseConlflict($msgError) : $msgError;
                            break;
            case 1054	:	$msgError   =   "Database internal script error";
                            return $httpResponse ? throwResponseInternalServerError($msgError) : $msgError;
                            break;
            case 1329	:	$msgError   =   "No data - zero rows fetched, selected, or processed";
                            return $httpResponse ? throwResponseInternalServerError($msgError) : $msgError;
                            break;
            default		:	$msgError   =   "Unknown database internal error";
                            return $httpResponse ? throwResponseInternalServerError($msgError) : $msgError;
                            break;
        }
    }
}

if(!function_exists('accountCodeQueryString')){
    function accountCodeQueryString($accountNameLang){
       return "(SELECT A.IDACCOUNT, CONCAT(C.ACCOUNTCODE, '-', B.ACCOUNTCODE, '-', A.ACCOUNTCODE) AS ACCOUNTCODE, A.".$accountNameLang." FROM m_account A
            LEFT JOIN (SELECT IDACCOUNT, IDACCOUNTPARENT, ACCOUNTCODE FROM m_account WHERE LEVEL = 2) B ON A.IDACCOUNTPARENT = B.IDACCOUNT
            LEFT JOIN (SELECT IDACCOUNT, ACCOUNTCODE FROM m_account WHERE LEVEL = 1) C ON B.IDACCOUNTPARENT = C.IDACCOUNT
            WHERE A.LEVEL = 3
            UNION ALL
            SELECT A.IDACCOUNT, CONCAT(B.ACCOUNTCODE, '-', A.ACCOUNTCODE) AS ACCOUNTCODE, A.".$accountNameLang." FROM m_account A
            LEFT JOIN (SELECT IDACCOUNT, ACCOUNTCODE FROM m_account WHERE LEVEL = 1) B ON A.IDACCOUNTPARENT = B.IDACCOUNT
            WHERE A.LEVEL = 2)";
    }
}