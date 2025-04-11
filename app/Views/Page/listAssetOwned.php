<div class="row justify-content-between align-items-center mb-10">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">List of Assets Owned<span>/ List of assets such as buildings, vehicles, office equipment and others</span></h3>
		</div>
	</div>
	<div class="col-12 col-lg-auto mb-10">
		<div class="page-date-range">
			<button class="button button-success button-sm pull-right" id="btnAddAssetData" data-toggle="modal" data-target="#modal-editorAssetData"><span><i class="fa fa-plus"></i>New Asset Data</span></button>
            <button class="button button-warning button-sm pull-right d-none btn-block" type="button" id="btnCloseSetDetailAsset"><span><i class="fa fa-arrow-circle-left"></i>Back</span></button>
        </div>
	</div>
</div>
<div class="slideTransition slideContainer slideLeft show" id="slideContainerLeft">
    <div class="box mb-10">
        <div class="box-body">
            <ul class="nav nav-tabs" id="tabsPanel">
				<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#assetsListTab"><i class="fa fa-list"></i> Assets List</a></li>
				<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#depreciationPostingTab"><i class="fa fa-clipboard"></i> Depreciation Posting</a></li>
			</ul>
        </div>
    </div>
    <div class="box">
        <div class="box-body">
            <div class="tab-content">
                <div class="tab-pane fade show active" id="assetsListTab">
                    <div class="row">
                        <div class="col-lg-3 col-sm-6">
                            <div class="form-group">
                                <label for="assetsList-optionAssetType" class="control-label">Asset Type</label>
                                <select id="assetsList-optionAssetType" name="assetsList-optionAssetType" class="form-control" option-all="All Asset Type"></select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="form-group">
                                <label for="assetsList-optionDepreciationGroup" class="control-label">Depreciation Group</label>
                                <select id="assetsList-optionDepreciationGroup" name="assetsList-optionDepreciationGroup" class="form-control" option-all="All Depreciation Group"></select>
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="assetsList-searchKeyword" class="control-label">Search</label>
                                <input type="text" class="form-control mb-10" id="assetsList-searchKeyword" name="assetsList-searchKeyword" placeholder="Type something & press ENTER">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 pt-15 pr-20 pb-10">
                            <span class="pl-15" id="tableDataCount-dataAsset">0 data found</span>
                        </div>
                        <div class="col-12">
                            <table class="table" id="table-dataAsset">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="160">Asset Type</th>
                                        <th width="250">Asset Name</th>
                                        <th >Description</th>
                                        <th width="180">Depreciation Group</th>
                                        <th width="120" class="text-center">Purchase Date</th>
                                        <th width="100" class="text-right">Purchase Price</th>
                                        <th width="100" class="text-right">Residual Value</th>
                                        <th width="100" class="text-right">Depreciation Value</th>
                                        <th width="90"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="9" class="text-center">No data found</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-sm-12 mb-10 pl-30 pr-30 pb-10">
                            <hr/>
                            <ul class="pagination" id="tablePagination-dataAsset"></ul>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="depreciationPostingTab">
                    <div class="row">
                        <div class="col-lg-2 col-sm-8">
                            <div class="form-group">
                                <label for="depreciationPosting-optionDepreciationPeriod" class="control-label">Depreciation Period</label>
                                <select id="depreciationPosting-optionDepreciationPeriod" name="depreciationPosting-optionDepreciationPeriod" class="form-control"></select>
                            </div>
                        </div>
                        <div class="col-lg-2 col-sm-4">
                            <div class="form-group">
                                <label for="depreciationPosting-optionAssetType" class="control-label">Asset Type</label>
                                <select id="depreciationPosting-optionAssetType" name="depreciationPosting-optionAssetType" class="form-control" option-all="All Asset Type"></select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-5">
                            <div class="form-group">
                                <label for="depreciationPosting-optionDepreciationGroup" class="control-label">Depreciation Group</label>
                                <select id="depreciationPosting-optionDepreciationGroup" name="depreciationPosting-optionDepreciationGroup" class="form-control" option-all="All Depreciation Group"></select>
                            </div>
                        </div>
                        <div class="col-lg-5 col-sm-7">
                            <div class="form-group">
                                <label for="depreciationPosting-searchKeyword" class="control-label">Search</label>
                                <input type="text" class="form-control mb-10" id="depreciationPosting-searchKeyword" name="depreciationPosting-searchKeyword" placeholder="Type something & press ENTER">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <table class="table" id="table-dataDepreciationPosting">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="220">Asset Details</th>
                                        <th width="160">Purchase Detail</th>
                                        <th width="50" class="text-right">#</th>
                                        <th width="100">Journal Date</th>
                                        <th >Journal Description</th>
                                        <th >Journal Account</th>
                                        <th width="100" class="text-right">Value</th>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="slideTransition slideContainer slideRight hide" id="slideContainerRight">
	<div class="box mb-10 d-none">
		<div class="box-body">
            <div class="row">
                <div class="col-sm-12 border-bottom"><h5>Detail Asset</h5></div>
            </div>
            <div class="row">
				<div class="col-lg-3 col-sm-12 mt-15">
					<div class="row">
						<div class="col-sm-12 mb-10">
							<h6 class="mb-0">Asset Type</h6>
							<p id="detailAsset-assetType">-</p>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12 mb-10">
							<h6 class="mb-0">Asset Name</h6>
							<p id="detailAsset-assetName">-</p>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12 mb-10">
							<h6 class="mb-0">Description</h6>
							<p id="detailAsset-description">-</p>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12 mb-10">
							<h6 class="mb-0">Depreciation Group</h6>
							<p id="detailAsset-depreciationGroup">-</p>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12 mb-10">
							<h6 class="mb-0">Benefit Time</h6>
							<p id="detailAsset-benefitTime">-</p>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12 mb-10">
							<h6 class="mb-0">Purchase Date</h6>
							<p id="detailAsset-purchaseDate">-</p>
						</div>
					</div>
				</div>
				<div class="col-lg-4 col-sm-12 mt-15">
					<h6 class="mb-0">Price & Value</h6>
					<div class="order-details-customer-info mb-10">
						<ul class="ml-5">
							<li> <span>Purchase Price</span> <span id="detailAsset-purchasePrice">0</span> </li>
							<li> <span>Residual Value</span> <span id="detailAsset-residualValue">0</span> </li>
							<li> <span>Depreciation Value</span> <span id="detailAsset-depreciationValue">0</span> </li>
							<li> <span>Depreciation /M</span> <span id="detailAsset-depreciationPerMonth">0</span> </li>
						</ul>
					</div>
					<div class="mt-15 row">
						<div class="col-sm-12">
							<h6 class="mb-0">Template Journal</h6>
							<p id="detailAsset-templateJournalName">-</p>
						</div>
					</div>
					<h6 class="mt-15 mb-0">Depreciation Account Journal</h6>
					<div class="order-details-customer-info mb-10">
						<ul class="ml-5" id="detailAsset-accountJournal"></ul>
					</div>
				</div>
                <div class="col-lg-5 col-sm-12 mt-15">
					<h6 class="mb-0">Purchase Journal</h6>
					<div class="order-details-customer-info mb-20">
						<ul class="ml-5">
							<li> <span>Date</span> <span id="detailAsset-purchaseJournalDate">-</span> </li>
							<li> <span>Reff. Number</span> <span id="detailAsset-purchaseJournalReffNumber">-</span> </li>
							<li> <span>Description</span> <span id="detailAsset-purchaseJournalDescription">-</span> </li>
							<li> <span>Nominal Total</span> <span id="detailAsset-purchaseJournalNominalTotal">0</span> </li>
						</ul>
					</div>
                    <table class="table" id="detailAsset-tablePurchaseJournalAccountDetails">
                        <thead class="thead-light">
                            <tr>
                                <th >Account</th>
                                <th width="120" class="text-right">Debit</th>
                                <th width="120" class="text-right">Credit</th>
                                <th width="10"></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
				</div>
			</div>
        </div>
    </div>
	<div class="box d-none">
		<div class="box-body">
            <div class="row">
                <div class="col-sm-12"><h5>Detail Depreciation</h5></div>
            </div>
            <div class="row">
                <div class="col-sm-12 tableFixHead" style="max-height: 450px">
                    <table class="table" id="detailAsset-tableListDetailDepreciation">
                        <thead class="thead-light">
                            <tr>
                                <th width="60" class="text-right">Number</th>
                                <th width="120">Date Depreciation</th>
                                <th width="140">Date Posting</th>
                                <th width="140" class="text-right">Depreciation Value</th>
                                <th width="140" class="text-right">Journal Value</th>
                                <th width="200">Journal Reff Num</th>
                                <th>Journal Description</th>
                                <th width="180">User Post</th>
                            </tr>
                        </thead>
                        <tbody><tr><td colspan="8" class="text-center">No data found</td></tr></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" id="modal-editorAssetData" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-lg" role="document">
		<form class="modal-content form-horizontal" id="form-editorAssetData" autocomplete="off">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-editorAssetData">Editor Asset Data</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
                <ul class="nav nav-tabs border-bottom pb-20 mb-20" id="tabsPanel">
                    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#editorAssetData-inputDataAsset"><i class="fa fa-list-ul"></i> Data Asset</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#editorAssetData-inputPurchaseJournal"><i class="fa fa-list-alt"></i> Purchase Journal</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade active show" id="editorAssetData-inputDataAsset">
                        <div class="row">
                            <div class="col-lg-6 col-sm-12 mb-10">
                                <div class="form-group required">
                                    <label for="editorAssetData-optionDepreciationGroup" class="control-label">Depreciation Group</label>
                                    <select id="editorAssetData-optionDepreciationGroup" name="editorAssetData-optionDepreciationGroup" class="form-control"></select>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12 mb-10">
                                <div class="form-group required">
                                    <label for="editorAssetData-optionTemplateJournal" class="control-label">Template Journal</label>
                                    <select id="editorAssetData-optionTemplateJournal" name="editorAssetData-optionTemplateJournal" class="form-control"></select>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-12 mb-10">
                                <div class="form-group required">
                                    <label for="editorAssetData-optionAssetType" class="control-label">Asset Type</label>
                                    <select id="editorAssetData-optionAssetType" name="editorAssetData-optionAssetType" class="form-control"></select>
                                </div>
                            </div>
                            <div class="col-lg-8 col-sm-12 mb-10">
                                <div class="form-group required">
                                    <label for="editorAssetData-assetName" class="control-label">Asset Name</label>
                                    <input type="text" class="form-control mb-10" id="editorAssetData-assetName" name="editorAssetData-assetName">
                                </div>
                            </div>
                            <div class="col-sm-12 mb-10">
                                <div class="form-group">
                                    <label for="editorAssetData-assetDescription" class="control-label">Description</label>
                                    <input type="text" class="form-control mb-10" id="editorAssetData-assetDescription" name="editorAssetData-assetDescription">
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-6 mb-10">
                                <div class="form-group required">
                                    <label for="editorAssetData-purchaseDate" class="control-label">Purchase Date</label>
                                    <input type="text" class="form-control input-date-single mb-10 text-center" id="editorAssetData-purchaseDate" name="editorAssetData-purchaseDate" value="<?=date('d-m-Y')?>">
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-6 mb-10">
                                <div class="form-group required">
                                    <label for="editorAssetData-purchasePrice" class="control-label">Purchase Price</label>
                                    <input type="text" class="form-control text-right" id="editorAssetData-purchasePrice" name="editorAssetData-purchasePrice" value="0" onkeypress="maskNumberInput(0, 99999999999, 'editorAssetData-purchasePrice')">
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-12 mb-10">
                                <div class="form-group required">
                                    <label for="editorAssetData-residualValue" class="control-label">Residual Value</label>
                                    <input type="text" class="form-control text-right" id="editorAssetData-residualValue" name="editorAssetData-residualValue" value="0" onkeypress="maskNumberInput(0, 99999999999, 'editorAssetData-residualValue')">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="editorAssetData-inputPurchaseJournal">
                        <div class="row">
                            <div class="col-sm-12 border-bottom pb-20 mb-20 d-none" id="editorAssetData-warningDataJournal">
                                <div class="alert alert-warning" role="alert">
                                    <i class="zmdi zmdi-info"></i> <span id="editorAssetData-warningDataJournalStr"></span>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-12">
                                <div class="form-group required">
                                    <label for="editorAssetData-reffNumber" class="control-label">Reff. Number</label>
                                    <input type="text" class="form-control mb-10" id="editorAssetData-reffNumber" name="editorAssetData-reffNumber" readonly="">
                                </div>
                            </div>
                            <div class="col-lg-8 col-sm-12">
                                <div class="form-group required">
                                    <label for="editorAssetData-descriptionTransaction" class="control-label">Description</label>
                                    <input type="text" class="form-control mb-10" id="editorAssetData-descriptionTransaction" name="editorAssetData-descriptionTransaction">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <button id="editorAssetData-btnAddAccountJournal" class="button button-success button-sm pull-right" type="button" data-toggle="modal" data-target="#modal-addAccountJournal">
                                    <span><i class="fa fa-plus"></i> Account</span>
                                </button>
                            </div>
                            <div class="col-sm-12 tableFixHead" style="height: 250px">
                                <table class="table" id="editorAssetData-tableAccountDetails">
                                    <thead class="thead-light">
                                        <tr>
                                            <th >Account</th>
                                            <th width="120" class="text-right">Debit</th>
                                            <th width="120" class="text-right">Credit</th>
                                            <th width="60"></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <div class="col-sm-12">
                                <table class="table">
                                    <tbody>
                                        <tr class="text-bold">
                                            <td width="530"><b>Total</b></td>
                                            <td width="140" class="text-right"><b id="editorAssetData-totalNominalDebit">0</b></td>
                                            <td width="140" class="text-right"><b id="editorAssetData-totalNominalCredit">0</b></td>
                                            <td width="60" class="text-center" id="editorAssetData-statusBalanceDebitCredit"><i class="fa fa-check text-success"></i></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
			</div>
			<div class="modal-footer">
                <input type="hidden" id="editorAssetData-idPurchaseJournalRecap" name="editorAssetData-idPurchaseJournalRecap" value="0">
                <input type="hidden" id="editorAssetData-idAsset" name="editorAssetData-idAsset" value="0">
				<button type="submit" class="button button-primary">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
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
<div class="modal fade" tabindex="-1" id="modal-editorAssetDepreciation" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<form class="modal-content form-horizontal" id="form-editorAssetDepreciation" autocomplete="off">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-editorAssetDepreciation">Posting Asset Depreciation</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
                <div class="row mb-15 border-bottom">
                    <div class="col-lg-3 col-sm-6 mb-5">
                        <div class="form-group required">
                            <label for="editorAssetDepreciation-reffNumber" class="control-label">Reff. Number</label>
                            <input type="text" class="form-control mb-10" id="editorAssetDepreciation-reffNumber" name="editorAssetDepreciation-reffNumber" readonly="">
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 mb-5">
                        <div class="form-group required">
                            <label for="editorAssetDepreciation-journalDate" class="control-label">Journal Date</label>
                            <input type="text" class="form-control input-date-single mb-10 text-center"" id="editorAssetDepreciation-journalDate" name="editorAssetDepreciation-journalDate">
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-12 mb-5">
                        <div class="form-group required">
                            <label for="editorAssetDepreciation-journalDescription" class="control-label">Description</label>
                            <input type="text" class="form-control mb-10" id="editorAssetDepreciation-journalDescription" name="editorAssetDepreciation-journalDescription">
                        </div>
                    </div>
                    <div class="col-sm-12 mb-15">
                        <h6>Debit Account</h6>
                        <div class="row mb-15" id="editorAssetDepreciation-rowDebitAccount">
                            <div class="col-sm-12 mb-10 pl-30" id="editorAssetDepreciation-colNoDataDebitAccount"><b>No data account found</b></div>
                        </div>
                        <h6>Credit Account</h6>
                        <div class="row" id="editorAssetDepreciation-rowCreditAccount">
                            <div class="col-sm-12 mb-10 pl-30" id="editorAssetDepreciation-colNoDataCreditAccount"><b>No data account found</b></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-2 col-sm-12 text-left">
						<button type="button" class="button button-box button-primary btn-disabled" id="editorAssetDepreciation-previousData" disabled="disabled">
							<i class="fa fa-arrow-left"></i>
						</button>
					</div>
                    <div class="col-lg-8 col-sm-12 text-center">
                        <input type="hidden" id="editorAssetDepreciation-idAssetDepreciation" name="editorAssetDepreciation-idAssetDepreciation" value="0">
                        <button type="submit" class="button button-success">Save Depreciation Journal</button>
					</div>
                    <div class="col-lg-2 col-sm-12 text-right">
						<button type="button" class="button button-box button-primary" id="editorAssetDepreciation-nextData">
							<i class="fa fa-arrow-right"></i>
						</button>
					</div>
                </div>
			</div>
		</form>
	</div>
</div>
<script>
	var url = "<?=BASE_URL_ASSETS_JS?>page-module/listAssetOwned.js?<?=date("YmdHis")?>",
        initialRecordingDate = "<?=$initialRecordingDate?>",
        newReffNumber = '';
	$.getScript(url);
</script>