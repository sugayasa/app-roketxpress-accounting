<div class="row justify-content-between align-items-center mb-10">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">System Setting <span> / List of system settings related to journal accounts, profit and loss, cash flow, balance sheet, etc</span></h3>
		</div>
	</div>
</div>
<div class="box mb-10">
    <div class="box-body">
        <ul class="nav nav-tabs" id="tabsPanel">
            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#basicSettingTab"><i class="fa fa-folder"></i> Basic Setting</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#cashFlowTab"><i class="fa fa-stumbleupon"></i> Cash Flow</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#profitLossReportTab"><i class="fa fa-balance-scale"></i> Profit & Loss Report</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#balanceSheetReportTab"><i class="fa fa-table"></i> Balance Sheet Report</a></li>
        </ul>
    </div>
</div>
<div class="box">
    <div class="box-body">
        <div class="tab-content">
            <div class="tab-pane fade show active" id="basicSettingTab">
                <div class="row">
                    <div class="col-sm-12 border-bottom pb-30 mb-30">
                        <div class="alert alert-primary py-2" role="alert">
                            <i class="zmdi zmdi-info font18px"></i> Changes to the basic settings will significantly impact all application system calculations and reports.<br/>Please proceed with caution before modifying the settings data.
                        </div>
                    </div>
                    <div class="col-sm-12 pl-30 pr-30 border-bottom pb-30 mb-30">
                        <div class="row pt-10 pb-10 mb-20 rounded-lg systemSettingRowElement">
                            <div class="col-lg-8 col-sm-12 border-right">
                                <h6 class='font-weight-bold basicSettingTab-settingName' data-idSystemSettings='1'>Initial Recording Date</h6>
                                <p class='basicSettingTab-settingDescription' data-idSystemSettings='1'>The date when financial record-keeping first commenced.<br/>This typically depends on the initial account/balance sheet balance or the date when the company was established, and the initial capital contribution was made.</p>
                            </div>
                            <div class="col-lg-4 col-sm-12">
                                <input type="text" class="form-control basicSettingTab-inputValue input-date-single text-center mt-15" id="basicSettingTab-initialDate" name="basicSettingTab-initialDate" data-idSystemSettings='1' value="<?=date('01-m-Y')?>">
                            </div>
                        </div>
                        <div class="row pt-10 pb-10 rounded-lg systemSettingRowElement">
                            <div class="col-lg-8 col-sm-12 border-right">
                                <h6 class='font-weight-bold basicSettingTab-settingName' data-idSystemSettings='2'>Current Year Profit/Loss Account</h6>
                                <p class='basicSettingTab-settingDescription' data-idSystemSettings='2'>The account used to record and display the calculation results of the current year's profit/loss, according to the selected date period.<br/>This account is primarily shown in the financial balance sheet report.</p>
                            </div>
                            <div class="col-lg-4 col-sm-12">
                                <input type="text" class="form-control basicSettingTab-inputValue mt-15" id="basicSettingTab-profitLossAccount" name="basicSettingTab-profitLossAccount" data-idSystemSettings='2'>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 text-center">
                        <button class="button button-primary button-sm" id="basicSettingTab-btnSaveSetting"><span>Save Setting</span></button>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="cashFlowTab">
                <div class="row">
                </div>
            </div>
            <div class="tab-pane fade" id="profitLossReportTab">
                <div class="row">
                </div>
            </div>
            <div class="tab-pane fade" id="balanceSheetReportTab">
                <div class="row">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" id="modalSettingBasic-chooseAccount" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-md" role="document">
        <form class="modal-content form-horizontal" id="form-modalSettingBasic-chooseAccount" autocomplete="off">
            <div class="modal-header">
                <h4 class="modal-title">Choose Account</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <div class="row mbn-20">
                    <div class="col-sm-12 mb-10">
                        <div class="form-group">
                            <label for="modalSettingBasic-optionAccountMain" class="control-label">Main Account</label>
                            <select id="modalSettingBasic-optionAccountMain" name="modalSettingBasic-optionAccountMain" class="form-control"></select>
                        </div>
                    </div>
                    <div class="col-sm-12 mb-10">
                        <div class="form-group">
                            <label for="modalSettingBasic-optionAccountSub" class="control-label">Sub Account</label>
                            <select id="modalSettingBasic-optionAccountSub" name="modalSettingBasic-optionAccountSub" class="form-control"></select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="button button-primary">Choose Account</button>
                <button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<style>
.systemSettingRowElement{
	border: 1px solid #e0e0e0;
	background-color: #fff;
}
</style>
<script>
	var dataAllAccountJournal   =   JSON.parse('<?=$dataAllAccountJournal?>'),
        url                     = "<?=BASE_URL_ASSETS_JS?>page-module/settings/systemSetting.js?<?=date("YmdHis")?>";
	$.getScript(url);
</script>