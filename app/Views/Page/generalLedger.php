<div class="row justify-content-between align-items-center mb-10">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">General Ledger<span>/ General ledger report per period and account</span></h3>
		</div>
	</div>
	<div class="col-12 col-lg-auto mb-10">
		<div class="page-date-range">
            <input type="text" class="form-control form-control-sm input-date-single mb-10 text-center mr-2" id="datePeriodStart" name="datePeriodStart" value="<?=date('01-m-Y')?>">
            <span class="mr-2 pt-1">To</span>
            <input type="text" class="form-control form-control-sm input-date-single mb-10 text-center" id="datePeriodEnd" name="datePeriodEnd" value="<?=date('t-m-Y')?>">
		</div>
	</div>
</div>
<div class="box">
    <div class="box-body">
        <form class="row" id="formFilter">
            <div class="col-lg-12">
                <div class="form-group">
                    <label for="accountList" class="control-label">
                        Accounts
                        <button class="button button-xs button-warning pull-right" type="button" id="btnResetAccountList"><span><i class="fa fa-refresh"></i>Reset Account List</span></button>
                    </label>
                    <input type="text" class="form-control" data-role="tagsinput" id="accountList" name="accountList">
                    <input type="hidden" id="arrIdAccount" name="arrIdAccount" value="[]">
                </div>
            </div>
        </form>
    </div>
</div>
<div class="row mt-10">
    <div class="col-sm-12">
        <a class="button button-info button-sm d-none mt-1 pull-right" id="excelDataGeneralLedger" target="_blank" href=""><span><i class="fa fa-file-excel-o"></i>Excel Data Ledger</span></a>
    </div>
</div>
<div class="row pl-15 pr-15 responsive-table-container" id="generalLedgerTableContainer">
    <div class="col-sm-12 text-center box mt-10" id="noDataGeneralLedger">
        <div class="box-body"><h6 class="text-warning">No account selected. No data is displayed</h6></div>
    </div>
</div>
<?=$arrElemModule['modalChooseAccount']?>
<script>
	var dataAllAccountJournal   =   JSON.parse('<?=$dataAllAccountJournal?>'),
        url                     =   "<?=BASE_URL_ASSETS_JS?>page-module/generalLedger.js?<?=date("YmdHis")?>";
	$.getScript(url);
</script>