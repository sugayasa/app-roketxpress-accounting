<div class="row justify-content-between align-items-center mb-10">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">Chart of Account<span>/ List of asset, liability, capital and equity accounts</span></h3>
		</div>
	</div>
	<div class="col-12 col-lg-auto mb-10">
		<div class="page-date-range">
			<button class="button button-warning button-sm pull-right d-none btn-block" type="button" id="btnCloseOpeningAccountBalance"><span><i class="fa fa-arrow-circle-left"></i>Back</span></button>
			<button class="button button-primary button-sm pull-right" id="btnOpenOpeningAccountBalance"><span><i class="fa fa-align-right"></i>Opening Account Balance</span></button>
			<button class="button button-success button-sm pull-right" id="btnAddAccount" data-toggle="modal" data-target="#modal-editorAddAccount"><span><i class="fa fa-plus"></i>New Account</span></button>
		</div>
	</div>
</div>
<div class="slideTransition slideContainer slideLeft show" id="slideContainerLeft">
    <div class="box">
        <div class="box-body row">
            <div class="col-lg-3 col-sm-6">
                <div class="form-group">
                    <label for="optionAccountGeneral" class="control-label">General Account</label>
                    <select id="optionAccountGeneral" name="optionAccountGeneral" class="form-control" option-all="All Account"></select>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="form-group">
                    <label for="optionAccountMain" class="control-label">Main Account</label>
                    <select id="optionAccountMain" name="optionAccountMain" class="form-control" option-all="All Account"></select>
                </div>
            </div>
            <div class="col-lg-6 col-sm-12">
                <div class="form-group">
                    <label for="searchKeyword" class="control-label">Search</label>
                    <input type="text" class="form-control mb-10" id="searchKeyword" name="searchKeyword" placeholder="Type something and press ENTER to search">
                </div>
            </div>
        </div>
    </div>
    <div class="box mt-10">
        <div class="box-body row responsive-table-container py-0 px-3">
            <div class="col-12 px-0 py-0">
                <table class="table" id="table-dataChartOfAccount">
                    <thead class="thead-light">
                        <tr>
                            <th colspan="3">Account Code</th>
                            <th >Account Name</th>
                            <th width="100">Default DR/CR</th>
                            <th width="60"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="6" class="text-center">No data found</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="slideTransition slideContainer slideRight hide" id="slideContainerRight">
	<div class="box mb-10 d-none">
		<div class="box-body row">
            <div class="col-lg-2 col-sm-6">
                <div class="form-group mb-0 required">
                    <label for="accountOpeningBalance-reffNumber" class="control-label">Reff. Number</label>
                    <input type="text" class="form-control" id="accountOpeningBalance-reffNumber" name="accountOpeningBalance-reffNumber" readonly>
                </div>
            </div>
            <div class="col-lg-2 col-sm-6">
                <div class="form-group mb-0 required">
                    <label for="accountOpeningBalance-date" class="control-label">Date Of Opening Balance</label>
                    <input type="text" class="form-control input-date-single text-center" id="accountOpeningBalance-date" name="accountOpeningBalance-date" value="<?=date('d-m-Y')?>">
                </div>
            </div>
            <div class="col-lg-6 col-sm-12">
                <div class="form-group mb-0 required">
                    <label for="accountOpeningBalance-description" class="control-label">Description</label>
                    <input type="text" class="form-control" id="accountOpeningBalance-description" name="accountOpeningBalance-description">
                </div>
            </div>
            <div class="col-lg-2 col-sm-6">
                <button class="button button-success button-sm pull-right mt-35" type="button" id="accountOpeningBalance-btnSaveAccountOpeningBalance"><span><i class="fa fa-save"></i> Save Opening Balance</span></button>
            </div>
        </div>
    </div>
	<div class="box mb-10 d-none">
		<div class="box-body row py-0 px-3">
            <div class="col-12 px-0 py-0">
                <table class="table" id="table-dataOpeningAccountBalance">
                    <thead class="thead-light">
                        <tr>
                            <th colspan="3">Account Code</th>
                            <th >Account Name</th>
                            <th width="100">Default DR/CR</th>
                            <th width="160" class="text-right">Debit</th>
                            <th width="160" class="text-right">Credit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="7" class="text-center">No data found</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" id="modal-editorAddAccount" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-lg" role="document">
		<form class="modal-content form-horizontal" id="form-editorAddAccount" autocomplete="off">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-editorAddAccount">Add New Account</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <input type="hidden" id="addAccount-lastNumber" name="addAccount-lastNumber" value="0">
			</div>
			<div class="modal-body">
				<div class="smart-wizard" id="wizardAddAccount">
                    <ul>
                        <li><a href="#step-1">1. Account Type</a></li>
                        <li><a href="#step-2">2. Enter Account Details</a></li>
                    </ul>
                    <div>
                        <div id="step-1">
                            <div class="row mbn-20">
                                <div class="col-12 mb-20"><h6>Select account level and parent account</h6></div>
                                <div class="col-12 mb-20">
                                    <div class="adomx-checkbox-radio-group inline">
                                        <label class="adomx-radio-2"><input type="radio" name="addAccount-radioAccountLevel" value="1" checked> <i class="icon"></i> General Account</label>
                                        <label class="adomx-radio-2"><input type="radio" name="addAccount-radioAccountLevel" value="2"> <i class="icon"></i> Main Account</label>
                                        <label class="adomx-radio-2"><input type="radio" name="addAccount-radioAccountLevel" value="3"> <i class="icon"></i> Sub Account</label>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-12 mb-20">
                                    <div class="form-group">
                                        <label for="addAccount-optionAccountGeneral" class="control-label">General Account</label>
                                        <select id="addAccount-optionAccountGeneral" name="addAccount-optionAccountGeneral" class="form-control" disabled></select>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-12 mb-20">
                                    <div class="form-group">
                                        <label for="addAccount-optionAccountMain" class="control-label">Main Account</label>
                                        <select id="addAccount-optionAccountMain" name="addAccount-optionAccountMain" class="form-control" disabled></select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="step-2">
                            <div class="row mbn-20">
                                <div class="col-12 mb-20"><h6>Complete the form below to create new account</h6></div>
                                <div class="col-lg-2 col-sm-6 mb-20">
                                    <div class="form-group">
                                        <label for="addAccount-optionDefaultDRCR" class="control-label">Default DR/CR</label>
                                        <select id="addAccount-optionDefaultDRCR" name="addAccount-optionDefaultDRCR" class="form-control">
                                            <option value="DR">Debit</option>
                                            <option value="CR">Credit</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-sm-6 mb-20">
                                    <div class="form-group required">
                                        <label for="addAccount-accountCode" class="control-label">Account Code</label>
                                        <input type="text" maxlength="3" class="form-control text-right" name="addAccount-accountCode" id="addAccount-accountCode" onkeypress="maskNumberInput(1, 999, 'addAccount-accountCode')">
                                    </div>
                                </div>
                                <div class="col-lg-8 col-sm-12 mb-20">
                                    <div class="form-group">
                                        <label for="addAccount-optionOrderPosition" class="control-label">Account Order Position</label>
                                        <select id="addAccount-optionOrderPosition" name="addAccount-optionOrderPosition" class="form-control"></select>
                                    </div>
                                </div>
                                <div class="col-sm-12 mb-20">
                                    <div class="form-group required">
                                        <label for="addAccount-accountNameEng" class="control-label">Account Name (Eng)</label>
                                        <input type="text" maxlength="150" class="form-control" name="addAccount-accountNameEng" id="addAccount-accountNameEng">
                                    </div>
                                </div>
                                <div class="col-sm-12 mb-20 required">
                                    <div class="form-group">
                                        <label for="addAccount-accountNameIdn" class="control-label">Account Name (Idn)</label>
                                        <input type="text" maxlength="150" class="form-control" name="addAccount-accountNameIdn" id="addAccount-accountNameIdn">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" tabindex="-1" id="modal-editorEditAccount" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-lg" role="document">
		<form class="modal-content form-horizontal" id="form-editorEditAccount" autocomplete="off">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-editorEditAccount">Edit Account</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body px-10">
                <div class="row mt-10">
                    <div class="col-lg-6 col-sm-12" id="editAccount-elemContainerGeneralAccount">
                        <div class="form-group">
                            <label for="editAccount-optionAccountGeneral" class="control-label">General Account</label>
                            <select id="editAccount-optionAccountGeneral" name="editAccount-optionAccountGeneral" class="form-control" disabled></select>
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-12" id="editAccount-elemContainerMainAccount">
                        <div class="form-group">
                            <label for="editAccount-optionAccountMain" class="control-label">Main Account</label>
                            <select id="editAccount-optionAccountMain" name="addAcceditAccountount-optionAccountMain" class="form-control" disabled></select>
                        </div>
                    </div>
                </div>
                <div class="row mt-10">
                    <div class="col-lg-2 col-sm-6 mb-10">
                        <div class="form-group">
                            <label for="editAccount-optionDefaultDRCR" class="control-label">Default DR/CR</label>
                            <select id="editAccount-optionDefaultDRCR" name="editAccount-optionDefaultDRCR" class="form-control">
                                <option value="DR">Debit</option>
                                <option value="CR">Credit</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 col-sm-6 mb-10">
                        <div class="form-group required">
                            <label for="editAccount-accountCode" class="control-label">Account Code</label>
                            <input type="text" maxlength="3" class="form-control text-right" name="editAccount-accountCode" id="editAccount-accountCode" onkeypress="maskNumberInput(1, 999, 'editAccount-accountCode')">
                        </div>
                    </div>
                    <div class="col-lg-8 col-sm-12 mb-10">
                        <div class="form-group">
                            <label for="editAccount-optionOrderPosition" class="control-label">Account Order Position</label>
                            <select id="editAccount-optionOrderPosition" name="editAccount-optionOrderPosition" class="form-control"></select>
                        </div>
                    </div>
                    <div class="col-sm-12 mb-10">
                        <div class="form-group required">
                            <label for="editAccount-accountNameEng" class="control-label">Account Name (Eng)</label>
                            <input type="text" maxlength="150" class="form-control" name="editAccount-accountNameEng" id="editAccount-accountNameEng">
                        </div>
                    </div>
                    <div class="col-sm-12 mb-10 required">
                        <div class="form-group">
                            <label for="editAccount-accountNameIdn" class="control-label">Account Name (Idn)</label>
                            <input type="text" maxlength="150" class="form-control" name="editAccount-accountNameIdn" id="editAccount-accountNameIdn">
                        </div>
                    </div>
                </div>
			</div>
			<div class="modal-footer">
                <input type="hidden" id="editAccount-idAccount" name="editAccount-idAccount" value="0">
                <input type="hidden" id="editAccount-accountLevel" name="editAccount-accountLevel" value="3">
				<button type="submit" class="button button-primary">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" tabindex="-1" id="modal-sortAccount" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-md" role="document">
		<form class="modal-content form-horizontal" id="form-sortAccount" autocomplete="off">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-sortAccount">Sort Account Order</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body px-10">
                <div class="row mt-10">
                    <div class="col-sm-12 mb-10">
                        <h6 id="sortAccount-containerAccountLevelName">Sort order account level <span id="sortAccount-accountLevelName"></span></h6>
                    </div>
                    <div class="col-sm-12 border-top px-4 pt-10" id="sortAccount-accountOrderList" style="height:400px; overflow-y: scroll;">
                        <div class="list-group" id="sortAccount-accountOrderList"></div>
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
<script>
	var url = "<?=BASE_URL_ASSETS_JS?>page-module/chartOfAccount.js?<?=date("YmdHis")?>";
	$.getScript(url);
</script>