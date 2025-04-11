<script>
const interval_id = window.setInterval(function(){}, Number.MAX_SAFE_INTEGER);
for (let i = 1; i < interval_id; i++) {
  window.clearInterval(i);
}
if(!window.jQuery){
    window.location = window.location.origin;
}
if(window.location.href != '<?=BASE_URL?>') window.history.replaceState({Title: '<?=APP_NAME?>', Url: '<?=BASE_URL?>'}, '<?=APP_NAME?>', '<?=BASE_URL?>');
</script>
<div class="main-wrapper">
<div class="header-section">
   <div class="container-fluid">
      <div class="row justify-content-between align-items-center">
         <div class="header-logo col-auto">
            <a href="<?=BASE_URL?>">
				<img src="<?=BASE_URL_ASSETS_IMG?>logo-update-text.png" alt="" height="50px">
				<img src="<?=BASE_URL_ASSETS_IMG?>logo-update-text.png" class="logo-light" alt="" height="50px">
            </a>
         </div>
         <div class="header-right flex-grow-1 col-auto">
            <div class="row justify-content-between align-items-center">
               <div class="col-auto">
                  <div class="row align-items-center">
                     <div class="col-auto"><button class="side-header-toggle"><i class="fa fa-align-justify"></i></button></div>
                  </div>
               </div>
               <div class="col-auto">
                  <ul class="header-notification-area">
					 <li class="adomx-dropdown col-auto" id="containerNotificationButton">
						<a class="toggle" href="#" id="containerNotificationIcon"><i class="zmdi zmdi-notifications"></i></a>
						<div class="adomx-dropdown-menu dropdown-menu-notifications" id="containerNotificationIconBodyList">
							<div class="head">
								<h5 class="title"><span id="containerNotificationCounter" class="text-bold"></span> Unread Notification</h5>
							</div>
							<div class="body custom-scroll ps ps--active-y">
								<ul id="containerNotificationList"></ul>
							<div class="ps__rail-x" style="left: 0px; bottom: 3px;"><div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div></div><div class="ps__rail-y" style="top: 0px; height: 275px; right: 3px;"><div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 133px;"></div></div></div>
							<div class="footer">
								<span class="view-all">
									<a href="#" onclick="dismissAllNotification()">Dismiss All</a> | <a href="#" onclick="openListNotification()">See All</a>
								</span>
							</div>
						</div>
					 </li>
                     <li class="adomx-dropdown col-auto">
                        <a class="toggle" href="#">
							<span class="user">
								<span class="avatar">
									<i class="fa fa-user-circle-o"></i>
								</span>
								<span class="name" id="spanNameUser"><?=$userAdminData['name']?></span>
							</span>
                        </a>
                        <div class="adomx-dropdown-menu dropdown-menu-user">
                           <div class="head">
                              <h5 class="name"><a href="#" id="linkNameUser"><?=$userAdminData['name']?></a></h5>
                              <a class="mail" href="#" id="linkLevelUser"><span class="badge badge-primary"><?=$userAdminData['levelName']?></span></a>
                              <a class="mail" href="#" id="linkEmailUser"><?=$userAdminData['email']?></a>
                           </div>
                           <div class="body">
                              <ul>
                                 <li><a href="#" id="linkSetting" data-toggle="modal" data-target="#modal-userProfile"><i class="fa fa-cogs"></i>Settings</a></li>
                                 <li><a href="#" id="linkClearAppData" onclick="clearAppData()"><i class="fa fa-trash-o"></i>Clear App Data</a></li>
                                 <li><a href="#" id="linkLogout"><i class="fa fa-sign-out"></i>Logout</a></li>
                              </ul>
                           </div>
                        </div>
                     </li>
                  </ul>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="side-header show">
   <button class="side-header-close"><i class="fa fa-close"></i></button>
   <div class="side-header-inner custom-scroll">
      <nav class="side-header-menu" id="side-header-menu">
         <ul>
            <li class="menu-item active" data-alias="DASH" data-url="dashboard" id="dashboard-menu">
               <a href="#"><i class="fa fa-home"></i> <span>Main Page</span></a>
            </li>
            <?=$menuElement?>
         </ul>
      </nav>
   </div>
</div>
<div class="content-body" id="main-content"></div>
<div class="modal fade" id="modal-userProfile">
	<div class="modal-dialog" role="document">
		<form class="modal-content form-horizontal" id="form-userProfile">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-userProfile">Account Settings</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="form-group required">
					<label for="name" class="col-sm-12 control-label">Name</label>
					<div class="col-sm-12">
						<input type="text" class="form-control" id="name" name="name" placeholder="Name">
					</div>
				</div>
				<div class="form-group">
					<label for="email" class="col-sm-12 control-label">Email Address</Address></label>
					<div class="col-sm-12">
						<input type="text" class="form-control" id="email" name="email" placeholder="Email Address">
					</div>
				</div>
				<div class="form-group required">
					<label for="username" class="col-sm-12 control-label">Username</label>
					<div class="col-sm-12">
						<input type="text" class="form-control" id="username" autocomplete="off" name="username" placeholder="Username">
					</div>
				</div><br/>
				<p>Fill in the form below to change your password</p>
				<div class="form-group">
					<label for="password" class="col-sm-12 control-label">Old Password</label>
					<div class="col-sm-12">
						<input type="password" class="form-control" id="oldPassword" autocomplete="new-password" name="oldPassword" placeholder="Old Password">
					</div>
				</div>
				<div class="form-group">
					<label for="password" class="col-sm-12 control-label">New Password</label>
					<div class="col-sm-12">
						<input type="password" class="form-control" id="newPassword" autocomplete="new-password" name="newPassword" placeholder="New Password">
					</div>
				</div>
				<div class="form-group">
					<label for="repeatPassword" class="col-sm-12 control-label">Repeat New Password</label>
					<div class="col-sm-12">
						<input type="password" class="form-control" id="repeatPassword" autocomplete="new-password" name="repeatPassword" placeholder="Repeat New Password">
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="button button-primary" id="saveSetting">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" id="modalWarning">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalWarningTitle">Warning</h5>
				<button class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body" id="modalWarningBody">-</div>
			<div class="modal-footer">
				<button class="button button-danger" id="modalWarningBtnOK" data-dismiss="modal">OK</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="footable-confirm-delete" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="footable-editor-title">Action Confirmation</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				Are you sure you want to delete this data?
			</div>
            <div class="modal-footer">
                <button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
                <button class="button button-danger" id="deleteBtn" data-idData="" data-table="">Yes, Delete</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-confirm-action" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="modal-confirm-title">Confirmation</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body" id="modal-confirm-body"></div>
           <div class="modal-footer">
                <button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
                <button class="button button-primary" id="confirmBtn" data-idData="" data-function="">Yes</button>
           </div>
        </div>
    </div>
</div>
<div class="modal loader-modal" id="window-loader" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
			<div class="modal-body">
				<div class="d-flex justify-content-center">
					<div class="spinner-border text-success">
						<span class="sr-only">Loading...</span>
					</div>
				</div><br/>
				<div class="row">
					<div class="col-12 text-center">
						<span>Loading, please wait..</span>
					</div>
				</div>
			</div>
        </div>
    </div>
</div>
<?=$arrElemModule['modalGeneralLedger']?>
<input type="hidden" id="lastMenuAlias" name="lastMenuAlias" value="">
<style>
@keyframes animate{
   30%{
     opacity: 0.4;
   }
   60%{
     opacity: 0.6;
   }
   90%{
     opacity: 0.8;
   }
}
</style>
<script>
	localStorage.setItem('lastApplicationLoadTime', '<?=gmdate("YmdHis")?>');
	localStorage.setItem('allowNotifList', '<?=json_encode($allowNotifList)?>');
	var baseURL					=	'<?=BASE_URL?>',
		loaderElem				=	"<center class='mt-5'>"+
									"	<img src='<?=BASE_URL_ASSETS_IMG?>loader_content.gif'/><br/><br/>"+
									"	Loading Content..."+
									"</center>",
		arrBadgeType			=	['dark', 'primary', 'warning', 'info', 'success', 'secondary', 'danger'],
		notificationSound		=	new Audio('<?=BASE_URL_ASSETS_SOUND?>notification.mp3'),
		notificationSoundAlarm	=	new Audio('<?=BASE_URL_ASSETS_SOUND?>old_alarm_sound.mp3'),
		notificationSoundSS		=	new Audio('<?=BASE_URL_ASSETS_SOUND?>sixth_sense.mp3');
		
	$.ajaxSetup({ cache: true });

	function getAllFunctionName() {
		var allFunctionName = [];
		for (var i in window) {
			if ((typeof window[i]).toString() == "function") {
				allFunctionName.push(window[i].name);
			}
		}

		return allFunctionName;
	}
	
	function clearAppData(showWarning = true){
		var localStorageKeys	=	Object.keys(localStorage),
			localStorageIdx		=	localStorageKeys.length,
			allFunctionName		=	getAllFunctionName();
		for(var i=0; i<localStorageIdx; i++){
			var keyName			=	localStorageKeys[i];
			if(keyName.substring(0, 5) == "form_"){
				localStorage.removeItem(keyName);
			}
		}

		for(var i=0; i<allFunctionName.length; i++){
			var functionName	=	allFunctionName[i];
			if(functionName.slice(-4) === "Func"){
				window[functionName]	=	null;
			}
		}

		if(showWarning){
			$("#modalWarning").on("show.bs.modal", function () {
				$("#modalWarningBody").html("App data has been cleared");
			});
			$("#modalWarning").modal("show");
		}
	}
	clearAppData(false);
</script>
<script>
	var optionHour			=	localStorage.setItem('optionHour','<?=$optionHour?>'),
		optionMinuteInterval=	localStorage.setItem('optionMinuteInterval','<?=$optionMinuteInterval?>'),
		optionMonth			=	localStorage.setItem('optionMonth','<?=$optionMonth?>'),
		optionYear			=	localStorage.setItem('optionYear','<?=$optionYear?>'),
		dateToday			=	'<?=date('d-m-Y')?>';
</script>
<script>
	var url = "<?=BASE_URL_ASSETS_JS?>select2.full.min.js";
	$.getScript(url);
</script>
<script>
	var url = "<?=BASE_URL_ASSETS_JS?>footable.min.js";
	$.getScript(url);
</script>
<script>
	var url = "<?=BASE_URL_ASSETS_JS?>bootstrap-select.min.js";
	$.getScript(url);
</script>
<script>
	var url = "<?=BASE_URL_ASSETS_JS?>daterangepicker.js";
	$.getScript(url);
</script>
<script>
	var url = "<?=BASE_URL_ASSETS_JS?>jquery.uploadfile.min.js";
	$.getScript(url);
</script>
<script>
	var url = "<?=BASE_URL_ASSETS_JS?>jquery.raty.js";
	$.getScript(url);
</script>
<script>
	var url = "<?=BASE_URL_ASSETS_JS?>sortable.js";
	$.getScript(url);
</script>
<script>
	var url = "<?=BASE_URL_ASSETS_JS?>jquery.scrollTo.min.js";
	$.getScript(url);
</script>
<script>
	var url = "<?=BASE_URL_ASSETS_JS?>jquery.slideToUnlock.js";
	$.getScript(url);
</script>
<script>
	var url = "<?=BASE_URL_ASSETS_JS?>jquery.smartWizard.min.js";
	$.getScript(url);
</script>
<script>
	var url = "<?=BASE_URL_ASSETS_JS?>jquery.tagify.min.js";
	$.getScript(url);
</script>
<script>
	var url = "<?=BASE_URL_ASSETS_JS?>toastr.min.js";
	$.getScript(url, function(){
		toastr.options = {
		  "closeButton": false,
		  "debug": false,
		  "newestOnTop": false,
		  "progressBar": false,
		  "rtl": false,
		  "positionClass": "toast-top-right",
		  "preventDuplicates": false,
		  "onclick": null,
		  "showDuration": 300,
		  "hideDuration": 300,
		  "timeOut": 6000,
		  "extendedTimeOut": 0,
		  "showEasing": "swing",
		  "hideEasing": "linear",
		  "showMethod": "fadeIn",
		  "hideMethod": "fadeOut"
		}
	});
</script>
<script>
	var url = "<?=BASE_URL_ASSETS_JS?>main.js?<?=date('YmdHis')?>";
	$.getScript(url);
</script>