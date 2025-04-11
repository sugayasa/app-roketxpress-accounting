<script>
	var thisMonth	=	"<?=$thisMonth?>";
</script>
<div class="row justify-content-between align-items-center mb-10">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3>Dashboard<span> / Summary data and financial statistics per period</span></h3>
		</div>
	</div>
	<div class="col-12 col-lg-auto mb-10"></div>
</div>
<div class="row">
	<div class="col-xlg-3 col-12 mb-30">
		<div class="box">
			<div class="box-head py-3">
				<h4 class="title">Revenue</h4>
			</div>
			<div class="box-body">
				<div class="row py-3 border-bottom">
					<div class="col-2 text-right"><img src="<?=BASE_URL_ASSETS_IMG_CALENDAR?>number-12.png" height="36px"></div>
					<div class="col-5"><h6 class="mb-0">Yesterday</h6><span>12 August 2024</span></div>
					<div class="col-5 text-right"><h6>12,000,000</h6></div>
				</div>
				<div class="row py-3 border-bottom">
					<div class="col-2 text-right"><img src="<?=BASE_URL_ASSETS_IMG_CALENDAR?>number-13.png" height="36px"></div>
					<div class="col-5"><h6 class="mb-0">Today</h6><span>13 August 2024</span></div>
					<div class="col-5 text-right"><h6>17,700,000</h6></div>
				</div>
				<div class="row py-3 border-bottom">
					<div class="col-2 text-right"><img src="<?=BASE_URL_ASSETS_IMG_CALENDAR?>july.png" height="36px"></div>
					<div class="col-5"><h6 class="mb-0">Last Month</h6><span>July 2024</span></div>
					<div class="col-5 text-right"><h6>369,635,000</h6></div>
				</div>
				<div class="row py-3">
					<div class="col-2 text-right"><img src="<?=BASE_URL_ASSETS_IMG_CALENDAR?>august.png" height="36px"></div>
					<div class="col-5"><h6 class="mb-0">This Month</h6><span>August 2024</span></div>
					<div class="col-5 text-right"><h6>411,876,000</h6></div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xlg-6 col-12 mb-30">
		<div class="box">
			<div class="box-head py-3">
				<h4 class="title text-center">Income vs Expenses</h4>
			</div>
			<div class="box-body">
				<div id="example-echart-bar-dataset" class="example-echart-bar-dataset example-echarts"></div>
			</div>
		</div>
	</div>
	<div class="col-xlg-3 col-12 mb-30">
		<div class="box">
			<div class="box-head py-3">
				<h4 class="title text-right">Liabilities</h4>
			</div>
			<div class="box-body">
				<div class="row py-3 border-bottom">
					<div class="col-5"><h6>8,340,000</h6></div>
					<div class="col-5 text-right"><h6 class="mb-0">Yesterday</h6><span>12 August 2024</span></div>
					<div class="col-2"><img src="<?=BASE_URL_ASSETS_IMG_CALENDAR?>number-12.png" height="36px"></div>
				</div>
				<div class="row py-3 border-bottom">
					<div class="col-5"><h6>9,450,000</h6></div>
					<div class="col-5 text-right"><h6 class="mb-0">Today</h6><span>13 August 2024</span></div>
					<div class="col-2"><img src="<?=BASE_URL_ASSETS_IMG_CALENDAR?>number-13.png" height="36px"></div>
				</div>
				<div class="row py-3 border-bottom">
					<div class="col-5"><h6>225,178,000</h6></div>
					<div class="col-5 text-right"><h6 class="mb-0">Last Month</h6><span>July 2024</span></div>
					<div class="col-2"><img src="<?=BASE_URL_ASSETS_IMG_CALENDAR?>july.png" height="36px"></div>
				</div>
				<div class="row py-3">
					<div class="col-5"><h6>314,914,000</h6></div>
					<div class="col-5 text-right"><h6 class="mb-0">This Month</h6><span>August 2024</span></div>
					<div class="col-2"><img src="<?=BASE_URL_ASSETS_IMG_CALENDAR?>august.png" height="36px"></div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row mbn-30">
	<div class="col-lg-9 col-sm-12 mb-30">
		<div class="box">
			<div class="box-head">
				<h4 class="title">Last 30 Days Statistics</h4>
			</div>
			<div class="box-body">
				<div class="example-chartjs" style="height: 300px;">
					<canvas id="example-chartjs-line"></canvas>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-3 col-sm-12 mb-30">
		<div class="box">
			<div class="box-head">
				<h4 class="title">Financial Portion</h4>
			</div>
			<div class="box-body">
				<h6 class="mb-2">August 2024</h6>
				<div id="example-echart-doughnut-chart" class="example-echart-doughnut-chart example-echarts" style="height: 300px;"></div>
			</div>
		</div>
	</div>
</div>
<script>
	var url = "<?=BASE_URL_ASSETS_JS?>page-module/_dashboard.js?<?=date("YmdHis")?>";
	$.getScript(url);
</script>