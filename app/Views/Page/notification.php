<div class="row justify-content-between align-items-center mb-10">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">Notifikasi<span>/ List of received notifications</span></h3>
		</div>
	</div>
</div>
<div class="box mb-10">
	<div class="box-body">
		<ul class="nav nav-tabs" id="tabsPanel">
			<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#unreadNotificationTab"><i class="fa fa-envelope"></i> Unread</a></li>
			<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#readNotificationTab"><i class="fa fa-envelope-open"></i> All</a></li>
		</ul>
	</div>
</div>
<div class="box mb-10">
	<div class="box-body">
		<div class="row">
			<div class="col-lg-3 col-sm-12 mb-5">
				<select id="optionNotificationUserAdminType" name="optionNotificationUserAdminType" class="form-control" option-all="All Types of Notifications"></select>
			</div>
			<div class="col-lg-9 col-sm-12 mb-5">
				<input type="text" class="form-control" id="keywordSearch" name="keywordSearch" placeholder="Type something and press ENTER to search">
			</div>
		</div>
	</div>
</div>
<div class="box">
	<div class="box-body tab-content">
		<div class="tab-pane fade show active" id="unreadNotificationTab">
			<div class="row mt-5">
				<div class="col-lg-9 col-sm-8 mb-5">
					<span id="tableDataCountUnreadNotification"></span>
				</div>
				<div class="col-lg-3 col-sm-4 mb-5 text-right">
					<button type="button" class="button button-warning button-sm" id="btnDismissAllNotification" onclick="dismissAllNotification(true)"><span><i class="fa fa-minus-circle"></i>Dismiss All Notifications</span></button>
				</div>
			</div>
			<div class="row mt-5 tableNotification" id="tableUnreadNotification">
				<div class="col-12 mt-40 mb-30 text-center" id="noDataUnreadNotification">
					<img src="<?=BASE_URL_ASSETS_IMG?>no-data.png" width="120px"/>
					<h5>No Data Found</h5>
					<p>There are no unread notifications</p>
				</div>
			</div>
			<div class="row mt-15">
				<div class="col-sm-6 mb-5">
					<button class="button button-sm button-info d-none" style="width: 120px;" id="btnPreviousPageUnreadNotification"><i class="fa fa-arrow-left"></i><span><</span></button>
				</div>
				<div class="col-sm-6 mb-5">
					<button class="button button-sm button-info d-none button-icon-right pull-right" style="width: 120px;" id="btnNextPageUnreadNotification"><i class="fa fa-arrow-right"></i><span>></span></button>
				</div>
			</div>
		</div>
		<div class="tab-pane fade" id="readNotificationTab">
			<div class="row mt-5">
				<div class="col-lg-12 mb-5">
					<span id="tableDataCountReadNotification"></span>
				</div>
			</div>
			<div class="row mt-5 tableNotification" id="tableReadNotification">
				<div class="col-12 mt-40 mb-30 text-center" id="noDataReadNotification">
					<img src="<?=BASE_URL_ASSETS_IMG?>no-data.png" width="120px"/>
					<h5>No Data Found</h5>
					<p>No notification data is displayed</p>
				</div>
			</div>
			<div class="row mt-15">
				<div class="col-sm-6 mb-5">
					<button class="button button-sm button-info d-none" style="width: 120px;" id="btnPreviousPageReadNotification"><i class="fa fa-arrow-left"></i><span><</span></button>
				</div>
				<div class="col-sm-6 mb-5">
					<button class="button button-sm button-info d-none button-icon-right pull-right" style="width: 120px;" id="btnNextPageReadNotification"><i class="fa fa-arrow-right"></i><span>></span></button>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	var url = "<?=BASE_URL_ASSETS_JS?>page-module/notification.js?<?=date('YmdHis')?>";
	$.getScript(url);
</script>
<style>
.tableNotification {
	min-height: 100px;
	max-height: 600px;
	overflow-y: scroll;
}
</style>