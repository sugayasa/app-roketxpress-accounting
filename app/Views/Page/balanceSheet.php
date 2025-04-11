<div class="row justify-content-between align-items-center mb-10">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">Balance Sheet<span>/ Balance Sheet per monthly period</span></h3>
		</div>
	</div>
	<div class="col-12 col-lg-auto mb-10">
		<div class="page-date-range">
            <select id="optionMonth" name="optionMonth" class="form-control mr-2"></select>
            <select id="optionYear" name="optionYear" class="form-control"></select>
		</div>
	</div>
</div>
<div class="box mt-10">
    <div class="box-body pt-15 pb-15">
        <div class="row">
            <div class="col-lg-8 col-sm-12">
                <div class="adomx-checkbox-radio-group inline mt-1">
                    <label class="adomx-radio-2"><input type="radio" id="reportFormat" value="1" name="reportFormat" checked><i class="icon"></i> Skontro (Horizontal)</label>
                    <label class="adomx-radio-2"><input type="radio" id="reportFormat" value="2" name="reportFormat"><i class="icon"></i> Stafel (Vertical)</label>
                </div>
            </div>
            <div class="col-lg-4 col-sm-12">
                <a class="button button-info button-sm pull-right mt-1 d-none" id="excelDataBalanceSheet" target="_blank" href="#"><span><i class="fa fa-file-excel-o"></i>Excel Data Balance Sheet</span></a>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-6 col-md-12" id="containerTable-dataBalanceSheetAssets">
        <div class="box mt-10">
            <div class="box-body px-0 py-0 responsive-table-container">
                <table class="table" id="table-dataBalanceSheetAssets">
                    <tbody>
                        <tr>
                            <td class="text-center">No data found</td>
                        </tr>
                    </tbody>
                </table>
                <table class="table border-top-2px" id="table-footerBalanceSheetAssets">
                    <thead>
                        <tr>
                            <th>Saldo Assets</th>
                            <th  width="160" class="text-right" id="th-footerBalanceSheetAssets">0</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-md-12" id="containerTable-dataBalanceSheetLiabilitiesEquity">
        <div class="box mt-10">
            <div class="box-body px-0 py-0 responsive-table-container">
                <table class="table" id="table-dataBalanceSheetLiabilitiesEquity">
                    <tbody>
                        <tr>
                            <td class="text-center">No data found</td>
                        </tr>
                    </tbody>
                </table>
                <table class="table border-top-2px" id="table-footerBalanceSheetLiabilitiesEquity">
                    <thead>
                        <tr>
                            <th>Saldo Liabilities & Equity</th>
                            <th  width="160" class="text-right" id="th-footerBalanceSheetLiabilitiesEquity">0</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
	var thisMonth = '<?=$thisMonth?>',
        thisYear = '<?=$thisYear?>',
        url = "<?=BASE_URL_ASSETS_JS?>page-module/balanceSheet.js?<?=date("YmdHis")?>";
	$.getScript(url);
</script>