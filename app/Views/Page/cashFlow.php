<div class="row justify-content-between align-items-center mb-10">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">Cash Flow<span>/ Cash flow report per date range</span></h3>
		</div>
	</div>
</div>
<div class="box mt-10">
    <div class="box-body responsive-table-container">
        <div class="row border-bottom pb-10 mb-10"><?=$arrElemModule['filterDateRange']?></div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group mb-0">
                    <label for="accountCashFlowList" class="control-label">Accounts</label>
                    <input type="text" class="form-control" data-role="tagsinput" id="accountCashFlowList" name="accountCashFlowList">
                    <input type="hidden" id="arrIdAccountCashFlow" name="arrIdAccountCashFlow" value="[]">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="box mt-10">
    <div class="box-body px-0 py-0 responsive-table-container">
        <div class="row">
            <div class="col-12 pt-20 pb-5">
                <a class="button button-info button-sm pull-right" id="excelDataCashFlow" target="_blank" href="#"><span><i class="fa fa-file-excel-o"></i>Excel Data Cash Flow</span></a>
            </div>
            <div class="col-12">
                <table class="table" id="table-dataCashFlow">
                    <thead class="thead-light">
                        <tr>
                            <th colspan="3">Account / Description</th>
                            <th width="200" class="text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4" class="text-center">No data found</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" id="modal-chooseAccount" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-lg" role="document">
		<form class="modal-content form-horizontal" id="form-chooseAccount" autocomplete="off">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-chooseAccount">Cash Flow Account</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <table class="table">
                            <thead class="thead-light">
                                <tr>
                                    <th width="120">Account Code</th>
                                    <th>Account Name</th>
                                    <th width="40"><label class="adomx-checkbox"><input type="checkbox" id="checkBoxAllAccount" checked/> <i class="icon"></i></label></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 tableFixHead" style="max-height: 400px">
                        <table class="table" id="chooseAccount-listAccountJournal">
                            <tbody><tr><td colspan="5" class="text-center">No data found</td></tr></tbody>
                        </table>
                    </div>
                </div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="button button-primary">Choose Account</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" tabindex="-1" id="modal-cashFLowDetails" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content form-horizontal" id="modal-cashFLowDetails-content" autocomplete="off">
			<div class="modal-header">
				<h4 class="modal-title" id="modal-cashFLowDetails-title">Cash Flow Details</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 mb-10">
                        <h6 id="modal-cashFLowDetails-detailAccountDatePeriod"></h6>
                    </div>
                    <div class="col-sm-12 pl-0 pr-0 tableFixHead" style="height: 500px">
                        <table class="table" id="modal-cashFLowDetails-table">
                            <thead class="thead-light">
                                <tr>
                                    <th width="120">Date</th>
                                    <th width="140">Reff. Number</th>
                                    <th width="240">Account Cash</th>
                                    <th> Description</th>
                                    <th width="130" class="text-right">Debit</th>
                                    <th width="130" class="text-right">Credit</th>
                                </tr> 
                            </thead>
                            <tbody></tbody>
                        </table> 
                    </div>
                </div>
			</div>
			<div class="modal-footer pt-0 pl-0 pr-0">
                <table class="table">
                    <thead class="thead-light">
                        <tr>
                            <th class="border-right">Total Mutation</th>
                            <th width="130" class="text-right" id="modal-cashFLowDetails-totalDebit">0</th>
                            <th width="130" class="text-right" id="modal-cashFLowDetails-totalCredit">0</th>
                        </tr> 
                    </thead>
                </table> 
			</div>
        </div>
	</div>
</div>
<script>
	var firstDateOfYear = '<?=date('01-01-Y')?>',
        firstDateOfMonth = '<?=date('01-m-Y')?>',
        currentDate = '<?=date('d-m-Y')?>',
        lastDateOfMonth = '<?=date('t-m-Y')?>',
        firstDateOfLastMonth = '<?=$firstDateOfLastMonth?>',
        lastDateOfLastMonth = '<?=$lastDateOfLastMonth?>',
        firstDateOfLastYear = '<?=$firstDateOfLastYear?>',
        lastDateOfLastYear = '<?=$lastDateOfLastYear?>',
        arrAccountsCashFlowDefault = JSON.parse('<?=$arrAccountsCashFlowDefault?>'),
        arrAccountsCashFlow = JSON.parse('<?=$arrAccountsCashFlow?>'),
        url = "<?=BASE_URL_ASSETS_JS?>page-module/cashFlow.js?<?=date("YmdHis")?>";
	$.getScript(url);
</script>