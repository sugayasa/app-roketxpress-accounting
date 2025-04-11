<div class="row justify-content-between align-items-center mb-10">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">General Journal<span>/ General journal data per period and account</span></h3>
		</div>
	</div>
	<div class="col-12 col-lg-auto mb-10">
		<div class="page-date-range">
			<button class="button button-success button-sm pull-right" id="btnOpenGeneralJournalEditor" onclick="openGeneralJournalEditor(false)"><span><i class="fa fa-plus"></i>New Journal</span></button>
            <button class="button button-primary button-sm pull-right" id="btnImportExcelJournal" onclick="openFormImportExcelJournal()"><span><i class="fa fa-cloud-upload"></i>Import Journal</span></button></div>
			<button class="button button-warning button-sm pull-right d-none btn-block" type="button" id="btnCloseGeneralJournalEditor"><span><i class="fa fa-arrow-circle-left"></i>Back</span></button>
			<button class="button button-warning button-sm pull-right d-none btn-block" type="button" id="btnCloseFormImportExcelJournal"><span><i class="fa fa-arrow-circle-left"></i>Back</span></button>
	</div>
</div>
<div class="slideTransition slideContainer slideLeft show" id="slideContainerLeft">
    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-lg-4 col-sm-12">
                    <div class="form-group">
                        <label for="optionAccountGeneral" class="control-label">General Account</label>
                        <select id="optionAccountGeneral" name="optionAccountGeneral" class="form-control" option-all="All Account"></select>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="form-group">
                        <label for="optionAccountMain" class="control-label">Main Account</label>
                        <select id="optionAccountMain" name="optionAccountMain" class="form-control" option-all="All Account"></select>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="form-group">
                        <label for="optionAccountSub" class="control-label">Sub Account</label>
                        <select id="optionAccountSub" name="optionAccountSub" class="form-control" option-all="All Account"></select>
                    </div>
                </div>
                <div class="col-lg-2 col-sm-6">
                    <div class="form-group">
                        <label for="datePeriodStart" class="control-label">Date Period</label>
                        <input type="text" class="form-control input-date-single mb-10 text-center" id="datePeriodStart" name="datePeriodStart" value="<?=date('01-m-Y')?>">
                    </div>
                </div>
                <div class="col-lg-2 col-sm-6">
                    <div class="form-group">
                        <label for="datePeriodEnd" class="control-label">.</label>
                        <input type="text" class="form-control input-date-single mb-10 text-center" id="datePeriodEnd" name="datePeriodEnd" value="<?=date('t-m-Y')?>">
                    </div>
                </div>
                <div class="col-lg-2 col-sm-4">
                    <div class="form-group">
                        <label for="searchReffNumber" class="control-label">Reff. Number</label>
                        <input type="text" class="form-control mb-10" id="searchReffNumber" name="searchReffNumber" placeholder="Type something & press ENTER">
                    </div>
                </div>
                <div class="col-lg-6 col-sm-8">
                    <div class="form-group">
                        <label for="searchDescription" class="control-label">Description</label>
                        <input type="text" class="form-control mb-10" id="searchDescription" name="searchDescription" placeholder="Type something & press ENTER">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="box mt-10">
        <div class="box-body px-0 py-0 responsive-table-container">
            <div class="row">
                <div class="col-12 pt-15 pr-20 pb-10">
                    <span class="pl-15" id="tableDataCount-dataGeneralJournal">0 data found</span>
                    <a class="button button-info button-sm pull-right mr-20 d-none" id="excelDataGeneralJournal" target="_blank" href=""><span><i class="fa fa-file-excel-o"></i>Excel Journal</span></a>
                </div>
                <div class="col-12">
                    <table class="table" id="table-dataGeneralJournal">
                        <thead class="thead-light">
                            <tr>
                                <th width="140">Reff. Number</th>
                                <th width="120" class="text-center">Date</th>
                                <th >Description</th>
                                <th width="130">Account Code</th>
                                <th width="350">Account Name</th>
                                <th width="130" class="text-right">Debit</th>
                                <th width="130" class="text-right">Credit</th>
                                <th width="60"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="8" class="text-center">No data found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-sm-12 mb-10 pl-30 pr-30 pb-10">
                    <hr/>
                    <ul class="pagination" id="tablePagination-dataGeneralJournal"></ul>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="slideTransition slideContainer slideRight hide" id="slideContainerRight">
	<div class="box d-none">
		<div class="box-body">
			<form class="row" id="form-generalJournalEditor">
				<div class="col-sm-12 mb-20">
                    <div class="alert alert-warning" role="alert">
                        <i class="zmdi zmdi-info"></i> <span>Use this form to enter transactions that do not normally occur in system</span>
                    </div>
                </div>
				<div class="col-sm-12 mb-10">
                    <div class="row">
                        <div class="col-sm-12 mb-20 border-bottom">
                            <h6>Transaction Details<button class="button button-info button-sm pull-right" type="button" data-toggle="modal" data-target="#modal-chooseTemplateJournal"><span><i class="fa fa-pencil-square-o"></i> Template Journal</span></button></h6>
                        </div>
                        <div class="col-lg-2 col-sm-6">
                            <div class="form-group required">
                                <label for="generalJournalEditor-reffNumber" class="control-label">Reff. Number</label>
        						<input type="text" class="form-control mb-10" id="generalJournalEditor-reffNumber" name="generalJournalEditor-reffNumber" readonly>
                            </div>
                        </div>
                        <div class="col-lg-2 col-sm-6">
                            <div class="form-group required">
                                <label for="generalJournalEditor-dateTransaction" class="control-label">Date</label>
                                <input type="text" class="form-control input-date-single mb-10 text-center" id="generalJournalEditor-dateTransaction" name="dateTransaction" value="<?=date('d-m-Y')?>">
                            </div>
                        </div>
                        <div class="col-lg-2 col-sm-12">
                            <div class="form-group required">
                                <label for="generalJournalEditor-nominalTransaction" class="control-label">Nominal</label>
        						<input type="text" class="form-control mb-10 text-right" id="generalJournalEditor-nominalTransaction" name="generalJournalEditor-nominalTransaction" value="0" readonly>
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group required">
                                <label for="generalJournalEditor-descriptionTransaction" class="control-label">Description</label>
        						<input type="text" class="form-control mb-10" id="generalJournalEditor-descriptionTransaction" name="generalJournalEditor-descriptionTransaction">
                            </div>
                        </div>
                    </div>
                </div>
				<div class="col-sm-12 mb-10 d-none" id="generalJournalEditor-descriptionVariableFormContainer">
                    <div class="row" id="generalJournalEditor-descriptionVariableForm">
                        <div class="col-sm-12 mb-20 border-bottom">
                            <h6>Description Variable</h6>
                        </div>
                        <div class="col-sm-12 mb-20">
                            <div class="alert alert-primary" role="alert">
                                <i class="zmdi zmdi-info"></i> <span>The Description Variable below <b>will modify the journal description</b> at the top</span>
                            </div>
                        </div>
                    </div>
                </div>
				<div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-12 mb-20 border-bottom">
                            <h6>Accounts<button class="button button-success button-sm pull-right" type="button" data-toggle="modal" data-target="#modal-addAccountJournal"><span><i class="fa fa-plus"></i> Account</span></button></h6>
                        </div>
                        <div class="col-sm-12 mb-20 d-none" id="alertTemplateJournal">
                            <div class="alert alert-danger" role="alert">
                                <i class="zmdi zmdi-info"></i> <span>Enter a <b>description and debit/credit nominal</b> for each account displayed</span>
                            </div>
                        </div>
                        <div class="col-sm-12 mb-10 tableFixHead" style="height: 300px;">
                            <table class="table" id="generalJournalEditor-tableAccountDetails">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="180">Account Code</th>
                                        <th width="350">Account Name</th>
                                        <th width="140" class="text-right">Debit</th>
                                        <th width="140" class="text-right">Credit</th>
                                        <th width="60"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="generalJournalEditor-noDataTableAccountDetails">
                                        <td colspan="5" class="text-center">No data</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-sm-12">
                            <table class="table">
                                <tbody>
                                    <tr class="text-bold">
                                        <td width="530"><b>Total</b></td>
                                        <td width="140" class="text-right"><b id="generalJournalEditor-totalNominalDebit">0</b></td>
                                        <td width="140" class="text-right"><b id="generalJournalEditor-totalNominalCredit">0</b></td>
                                        <td width="60" class="text-center" id="generalJournalEditor-statusBalanceDebitCredit"><i class="fa fa-check text-success"></i></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
				<div class="col-sm-12 border-top text-center">
                    <input type="hidden" id="generalJournalEditor-newReffNumber" name="generalJournalEditor-newReffNumber" value="">
                    <input type="hidden" id="generalJournalEditor-arrIdJournalDetails" name="generalJournalEditor-arrIdJournalDetails" value="">
                    <input type="hidden" id="generalJournalEditor-idJournalRecap" name="generalJournalEditor-idJournalRecap" value="0">
                    <input type="hidden" id="generalJournalEditor-defaultDescription" name="generalJournalEditor-defaultDescription" value="">
                    <button class="button button-primary button-sm mt-20" id="generalJournalEditor-btnSubmit"><span>Save Transaction</span></button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="slideTransition slideContainer slideRight hide" id="slideContainerRightImportJournal">
	<div class="box d-none">
		<div class="box-body">
			<div class="row mb-10">
				<div class="col-lg-8 col-sm-12">
					<div class="alert alert-primary py-2" role="alert">
						<i class="zmdi zmdi-info"></i> The allowed documents are <b>xls and xlsx</b> with a maximum size of <b>800 kolibytes</b>.<br/>Please follow the journal Excel file format instructions as shown in the image below.
					</div>
                    <img src="<?=BASE_URL_ASSETS_IMG?>excelFormatImportJournal.png">
				</div>
				<div class="col-lg-4 col-sm-12 text-center">
                    <div class="row mb-10">
        				<div class="col-sm-12 text-center border-bottom pb-10 mb-10">
                            <p>Click upload button or drag and drop your file below</p>
                        </div>
        				<div class="col-sm-12 text-center">
                            <i class="fa fa-cloud-upload display-3" style="font-size: 48px;"></i><br/>
                            <a href="#" id="uploaderImportExcelJournal">Upload Excel</a>
                        </div>
                    </div>
				</div>
			</div>
        </div>
    </div>
	<div class="box d-none mt-10" id="importExcelJournalScanningResult">
		<div class="box-head py-2">
			<div class="row mt-5">
				<div class="col-sm-12">
                    <h5>
                        Scanning Results
                        <button class="button button-sm button-primary pull-right d-none" type="button" id="importExcelJournalScanningResult-btnSaveScanningResult"><span><i class="fa fa-save"></i>Save Result</span></button>
                    </h5>
                </div>
            </div>
        </div>
		<div class="box-body">
			<div class="row mt-5 responsive-table-container tableFixHead" style="height: 600px">
                <div class="col-sm-12 px-0">
                    <table class="table" id="table-importExcelJournalScanningResult">
                        <thead class="thead-light">
                            <tr>
                                <th width="140">Reff. Number</th>
                                <th width="120">Date</th>
                                <th >Description</th>
                                <th width="130">Account Code</th>
                                <th width="350">Account Name</th>
                                <th >Account Description</th>
                                <th width="130" class="text-right">Debit</th>
                                <th width="130" class="text-right">Credit</th>
                                <th width="80">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="9" class="text-center"><b>No Data</b></td>
                            </tr>
                        </tbody>
                    </table>
			    </div>
            </div>
			<div class="row mt-5">
                <div class="col-sm-12 px-0">
                    <table class="table" id="table-importExcelJournalScanningResult">
                        <thead class="thead-light">
                            <tr>
                                <th>Total Nominal</th>
                                <th width="130" class="text-right" id="importExcelJournalScanningResult-totalNominalDebit">0</th>
                                <th width="130" class="text-right" id="importExcelJournalScanningResult-totalNominalCredit">0</th>
                                <th width="80"></th>
                            </tr>
                        </thead>
                    </table>
				</div>
			</div>
		</div>
    </div>
</div>
<div class="modal fade" tabindex="-1" id="modal-addAccountJournal" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-md" role="document">
		<form class="modal-content form-horizontal" id="form-addAccountJournal" autocomplete="off">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-addAccountJournal">Editor Account Transaction</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
                <div class="row mbn-20">
                    <div class="col-sm-12 mb-10">
                        <div class="form-group">
                            <label for="addAccountJournal-optionAccountMain" class="control-label">Main Account</label>
                            <select id="addAccountJournal-optionAccountMain" name="addAccountJournal-optionAccountMain" class="form-control"></select>
                        </div>
                    </div>
                    <div class="col-sm-12 mb-10">
                        <div class="form-group">
                            <label for="addAccountJournal-optionAccountSub" class="control-label">Sub Account</label>
                            <select id="addAccountJournal-optionAccountSub" name="addAccountJournal-optionAccountSub" class="form-control"></select>
                        </div>
                    </div>
                    <div class="col-sm-12 mb-20">
                        <div class="alert alert-primary" role="alert">
                            <i class="zmdi zmdi-info"></i> <span>Default position for selected account :<br/>[ + ] <b id="addAccountJournal-textDefaultPositionPlus"></b><br/>[ - ] <b id="addAccountJournal-textDefaultPositionMinus"></b></span>
                        </div>
                    </div>
                    <div class="col-12 mb-10">
                        <div class="form-group required">
                            <label for="addAccountJournal-debitCredit" class="control-label">Position</label>
                            <div class="adomx-checkbox-radio-group inline">
                                <label class="adomx-radio-2"><input type="radio" name="addAccountJournal-debitCredit" value="DR" checked> <i class="icon"></i> Debit</label>
                                <label class="adomx-radio-2"><input type="radio" name="addAccountJournal-debitCredit" value="CR"> <i class="icon"></i> Credit</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 mb-10">
                        <div class="form-group">
                            <label for="addAccountJournal-description" class="control-label">Description</label>
                            <input type="text" class="form-control mb-10" id="addAccountJournal-description" name="addAccountJournal-description">
                        </div>
                    </div>
                    <div class="col-sm-12 mb-10">
                        <div class="form-group required">
                            <label for="addAccountJournal-nominalTransaction" class="control-label">Nominal</label>
                            <input type="text" class="form-control mb-10 text-right" id="addAccountJournal-nominalTransaction" name="addAccountJournal-nominalTransaction" value="0" onkeypress="maskNumberInput(0, 999999999, 'addAccountJournal-nominalTransaction')">
                        </div>
                    </div>
                </div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="button button-primary">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" tabindex="-1" id="modal-chooseTemplateJournal" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-md" role="document">
		<form class="modal-content form-horizontal" id="form-chooseTemplateJournal" autocomplete="off">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-chooseTemplateJournal">Choose Template Journal</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
                <div class="row mbn-20">
                    <div class="col-sm-12 mb-10">
                        <div class="form-group">
                            <label for="chooseTemplateJournal-searchKeyword" class="control-label">Search Template</label>
                            <input type="text" class="form-control mb-10" id="chooseTemplateJournal-searchKeyword" name="chooseTemplateJournal-searchKeyword" value="">
                        </div>
                    </div>
                    <div class="col-sm-12 mb-10 tableFixHead" style="height:500px">
                        <table class="table" id="chooseTemplateJournal-tableTemplate">
                            <tbody>
                                <tr>
                                    <td class="text-center">No template found</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" tabindex="-1" id="modal-fixUndetectedAccount" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-xl" role="document">
		<form class="modal-content form-horizontal" id="form-fixUndetectedAccount" autocomplete="off">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-fixUndetectedAccount">Fix Undetected Account</h4>
			</div>
			<div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 border-bottom mb-10 pb-10">
                        <div class="alert alert-warning py-2" role="alert">
                            <i class="zmdi zmdi-info"></i> Please match the journal account data in the import file with the journal accounts in the system.
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-12 border-right tableFixHead" style="max-height: 400px">
                        <table class="table" id="fixUndetectedAccount-listAccountUndetected">
                            <thead class="thead-light">
                                <tr>
                                    <th width="45%">Account Code - File</th>
                                    <th width="45%">Account Code - Convert System</th>
                                    <th width="10%"></th>
                                </tr>
                            </thead>
                            <tbody><tr><td colspan="2" class="text-center">No data found</td></tr></tbody>
                        </table>
                    </div>
                    <div class="col-lg-6 col-sm-12 tableFixHead" style="max-height: 400px">
                        <table class="table" id="fixUndetectedAccount-tableListAccountChoose">
                            <thead class="thead-light">
                                <tr>
                                    <th width="120" colspan="3">Account Code</th>
                                    <th>Account Name</th>
                                    <th width="40"></th>
                                </tr>
                            </thead>
                            <tbody><tr><td colspan="5" class="text-center">No data found</td></tr></tbody>
                        </table>
                    </div>
                </div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="button button-primary">Save</button>
			</div>
		</form>
	</div>
</div>
<script>
	var dataAllAccountJournal   =   JSON.parse('<?=$dataAllAccountJournal?>'),
        url                     =   "<?=BASE_URL_ASSETS_JS?>page-module/generalJournal.js?<?=date("YmdHis")?>";
	$.getScript(url);
</script>