<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Index');
$routes->setDefaultMethod('main');
$routes->setTranslateURIDashes(false);
$routes->set404Override('App\Controllers\Index::response404');
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->post('/', 'Index::index');
$routes->get('/', 'Index::main');
$routes->get('/logoutPage', 'Index::main', ['as' => 'logoutPage']);
$routes->get('/loginPage', 'Index::loginPage');
$routes->post('/mainPage', 'Index::mainPage', ['filter' => 'auth:mustBeLoggedIn']);

$routes->post('access/check', 'Access::check');
$routes->post('access/login', 'Access::login', ['filter' => 'auth:mustNotBeLoggedIn']);
$routes->get('access/logout/(:any)', 'Access::logout/$1');
$routes->get('access/captcha/(:any)', 'Access::captcha/$1');
$routes->post('access/getDataOption', 'Access::getDataOption', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('access/getDataOptionByKey/(:any)/(:any)/(:any)', 'Access::getDataOptionByKey/$1/$2/$3', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('access/getDataDashboard', 'Access::getDataDashboard', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('access/unreadNotificationList', 'Access::unreadNotificationList', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('access/dismissNotification', 'Access::dismissNotification', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('access/dismissAllNotification', 'Access::dismissAllNotification', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('access/detailProfileSetting', 'Access::detailProfileSetting', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('access/saveDetailProfileSetting', 'Access::saveDetailProfileSetting', ['filter' => 'auth:mustBeLoggedIn']);

$routes->post('view/dashboard', 'View::dashboard', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('view/chart-of-account', 'View::chartOfAccount', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('view/list-asset-owned', 'View::listAssetOwned', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('view/template-journal', 'View::templateJournal', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('view/general-journal', 'View::generalJournal', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('view/general-ledger', 'View::generalLedger', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('view/cash-flow', 'View::cashFlow', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('view/profit-and-loss', 'View::profitLoss', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('view/balance-sheet', 'View::balanceSheet', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('view/settings-user-admin', 'View::settingsUserAdmin', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('view/settings-user-level-menu', 'View::settingsUserLevelMenu', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('view/settings-system-setting', 'View::settingsSystemSetting', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('view/notification', 'View::notification', ['filter' => 'auth:mustBeLoggedIn']);

$routes->post('chartOfAccount/getDataAccount', 'ChartOfAccount::getDataAccount', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('chartOfAccount/getNextStepCreateAccount', 'ChartOfAccount::getNextStepCreateAccount', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('chartOfAccount/insertData', 'ChartOfAccount::insertData', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('chartOfAccount/getDetailAccount', 'ChartOfAccount::getDetailAccount', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('chartOfAccount/updateData', 'ChartOfAccount::updateData', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('chartOfAccount/getSortOrderAccount', 'ChartOfAccount::getSortOrderAccount', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('chartOfAccount/saveSortOrderAccount', 'ChartOfAccount::saveSortOrderAccount', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('chartOfAccount/deleteAccount', 'ChartOfAccount::deleteAccount', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('chartOfAccount/saveAccountOpeningBalance', 'ChartOfAccount::saveAccountOpeningBalance', ['filter' => 'auth:mustBeLoggedIn']);

$routes->post('listAssetOwned/getDataTable', 'ListAssetOwned::getDataTable', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('listAssetOwned/getDataDepreciationPosting', 'ListAssetOwned::getDataDepreciationPosting', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('listAssetOwned/getDetailDepreciationPosting', 'ListAssetOwned::getDetailDepreciationPosting', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('listAssetOwned/postAssetDepreciationJournal', 'ListAssetOwned::postAssetDepreciationJournal', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('listAssetOwned/insertData', 'ListAssetOwned::insertData', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('listAssetOwned/getDetailAsset', 'ListAssetOwned::getDetailAsset', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('listAssetOwned/updateData', 'ListAssetOwned::updateData', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('listAssetOwned/deleteDataAsset', 'ListAssetOwned::deleteDataAsset', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('listAssetOwned/getDetailsAssetJournal', 'ListAssetOwned::getDetailsAssetJournal', ['filter' => 'auth:mustBeLoggedIn']);

$routes->post('templateJournal/getDataTable', 'TemplateJournal::getDataTable', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('templateJournal/insertData', 'TemplateJournal::insertData', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('templateJournal/getDetailTemplateJournal', 'TemplateJournal::getDetailTemplateJournal', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('templateJournal/updateData', 'TemplateJournal::updateData', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('templateJournal/deleteTemplateJournal', 'TemplateJournal::deleteTemplateJournal', ['filter' => 'auth:mustBeLoggedIn']);

$routes->post('generalJournal/getDataTable', 'GeneralJournal::getDataTable', ['filter' => 'auth:mustBeLoggedIn']);
$routes->get('generalJournal/excelDataJournal/(:any)', 'GeneralJournal::excelDataJournal/$1');
$routes->post('generalJournal/insertData', 'GeneralJournal::insertData', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('generalJournal/getDataTemplateJournal', 'GeneralJournal::getDataTemplateJournal', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('generalJournal/getDetailGeneralJournal', 'GeneralJournal::getDetailGeneralJournal', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('generalJournal/updateData', 'GeneralJournal::updateData', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('generalJournal/deleteGeneralJournal', 'GeneralJournal::deleteGeneralJournal', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('generalJournal/uploadImportExcelJournal', 'GeneralJournal::uploadImportExcelJournal', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('generalJournal/scanImportExcelJournal', 'GeneralJournal::scanImportExcelJournal', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('generalJournal/saveImportExcelJournal', 'GeneralJournal::saveImportExcelJournal', ['filter' => 'auth:mustBeLoggedIn']);

$routes->post('generalLedger/getDataTable', 'GeneralLedger::getDataTable', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('generalLedger/getDataPerAccountPeriod', 'GeneralLedger::getDataPerAccountPeriod', ['filter' => 'auth:mustBeLoggedIn']);
$routes->get('generalLedger/excelDataLedger/(:any)', 'GeneralLedger::excelDataLedger/$1');

$routes->post('cashFlow/getDataTable', 'CashFlow::getDataTable', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('cashFlow/getDetailCashFlow', 'CashFlow::getDetailCashFlow', ['filter' => 'auth:mustBeLoggedIn']);
$routes->get('cashFlow/excelDataCashFlow/(:any)', 'CashFlow::excelDataCashFlow/$1');

$routes->post('profitLoss/getDataTable', 'ProfitLoss::getDataTable', ['filter' => 'auth:mustBeLoggedIn']);
$routes->get('profitLoss/excelDataProfitLoss/(:any)', 'ProfitLoss::excelDataProfitLoss/$1');

$routes->post('balanceSheet/getDataTable', 'BalanceSheet::getDataTable', ['filter' => 'auth:mustBeLoggedIn']);
$routes->get('balanceSheet/excelDataBalanceSheet/(:any)', 'BalanceSheet::excelDataBalanceSheet/$1');

$routes->post('settings/userAdmin/getDataTable', 'Settings\UserAdmin::getDataTable', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('settings/userAdmin/insertDataUserAdmin', 'Settings\UserAdmin::insertDataUserAdmin', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('settings/userAdmin/updateDataUserAdmin', 'Settings\UserAdmin::updateDataUserAdmin', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('settings/userAdmin/updateStatusUserAdmin', 'Settings\UserAdmin::updateStatusUserAdmin', ['filter' => 'auth:mustBeLoggedIn']);

$routes->post('settings/userLevelMenu/getDataTable', 'Settings\UserLevelMenu::getDataTable', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('settings/userLevelMenu/saveDataUserLevelMenu', 'Settings\UserLevelMenu::saveDataUserLevelMenu', ['filter' => 'auth:mustBeLoggedIn']);

$routes->post('settings/systemSetting/getDataSystemSetting', 'Settings\SystemSetting::getDataSystemSetting', ['filter' => 'auth:mustBeLoggedIn']);
$routes->post('settings/systemSetting/updateSystemSettings', 'Settings\SystemSetting::updateSystemSettings', ['filter' => 'auth:mustBeLoggedIn']);

$routes->post('notification/getDataNotification', 'Notification::getDataNotification', ['filter' => 'auth:mustBeLoggedIn']);

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
