<div class="row justify-content-between align-items-center mb-10">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">User Level Menu <span> / List menu by user admin level</span></h3>
		</div>
	</div>
	<div class="col-12 col-lg-auto mb-10">
		<div class="page-date-range">
            <button class="button button-primary button-sm pull-right" id="btnSaveUserLevelMenu"><span><i class="fa fa-save"></i> Save Change</span></button>
		</div>
	</div>
</div>
<div class="box mb-10">
    <div class="box-body row" id="formUserLevelMenu">
        <div class="col-lg-3 col-sm-12">
            <div class="form-group">
                <label for="cari" class="control-label">User Admin Level</label>
                <select class="form-control" name="optionLevelUserAdmin" id="optionLevelUserAdmin"></select>
            </div>
        </div>
        <div class="col-lg-9 col-sm-12 mb-10">
            <div class="form-group">
                <label for="searchKeyword" class="control-label">Search by group/menu name</label>
                <input type="text" class="form-control mb-10" id="searchKeyword" name="searchKeyword" placeholder="Type something and press ENTER to search">
            </div>
        </div>
	</div>
</div>
<div class="box">
    <div class="box-body row">
        <div class="col-sm-12 px-0 py-0">
            <table class="table" id="table-dataUserLevelMenu">
                <thead class="thead-light">
                    <tr>
                        <th width="300">Grup Menu</th>
                        <th>Menu</th>
                        <th width="200">Access Permission</th>
                    </tr>
                </thead>
                <tbody id="bodyUserLevelMenu">
                    <tr><td colspan="3" align="center">No data shown</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-confirmSaveUserLevelMenu" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="footable-editor-title">Confirmation</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">Save menu changes on level <b id="confirmSaveUserLevelMenu-userLevelName"></b>?</div>
            <div class="modal-footer">
                <button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
                <button class="button button-success" id="confirmSaveUserLevelMenu-btnConfirmSaveUserLevelMenu">Yes, Save</button>
            </div>
        </div>
    </div>
</div>
<script>
	var url = "<?=BASE_URL_ASSETS_JS?>page-module/settings/userLevelMenu.js?<?=date("YmdHis")?>";
	$.getScript(url);
</script>