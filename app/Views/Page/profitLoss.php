<div class="row justify-content-between align-items-center mb-10">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">Profit and Loss<span>/ Profit and loss per date range</span></h3>
		</div>
	</div>
</div>
<div class="box mt-10">
    <div class="box-body responsive-table-container">
        <div class="row"><?=$arrElemModule['filterDateRange']?></div>
    </div>
</div>
<div class="box mt-10">
    <div class="box-body px-0 py-0 responsive-table-container">
        <div class="row">
            <div class="col-12 pt-20 pb-5">
                <a class="button button-info button-sm pull-right" id="excelDataProfitLoss" target="_blank" href="#"><span><i class="fa fa-file-excel-o"></i>Excel Data Profit Loss</span></a>
            </div>
            <div class="col-12">
                <table class="table" id="table-dataProfitLoss">
                    <thead class="thead-light">
                        <tr>
                            <th colspan="3">Account / Description</th>
                            <th width="200" class="text-right">Saldo</th>
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
<script>
	var firstDateOfYear = '<?=date('01-01-Y')?>',
        firstDateOfMonth = '<?=date('01-m-Y')?>',
        currentDate = '<?=date('d-m-Y')?>',
        lastDateOfMonth = '<?=date('t-m-Y')?>',
        firstDateOfLastMonth = '<?=$firstDateOfLastMonth?>',
        lastDateOfLastMonth = '<?=$lastDateOfLastMonth?>',
        firstDateOfLastYear = '<?=$firstDateOfLastYear?>',
        lastDateOfLastYear = '<?=$lastDateOfLastYear?>',
        url = "<?=BASE_URL_ASSETS_JS?>page-module/profitLoss.js?<?=date("YmdHis")?>";
	$.getScript(url);
</script>