<div class="row justify-content-between align-items-center mb-10">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">Setting - User Admin<span>/ List of user admin application</span></h3>
		</div>
	</div>
	<div class="col-12 col-lg-auto mb-10">
		<div class="page-date-range">
			<button class="button button-success button-sm pull-right" id="btnAddUserAdmin" data-toggle="modal" data-target="#modal-editorUserAdmin"><span><i class="fa fa-plus"></i>New User Admin</span></button>
		</div>
	</div>
</div>
<div class="box">
    <div class="box-body row">
        <div class="col-lg-3 col-sm-12">
            <div class="form-group">
                <label for="optionLevelUserAdmin" class="control-label">Level User</label>
                <select id="optionLevelUserAdmin" name="optionLevelUserAdmin" class="form-control" option-all="All User Level"></select>
            </div>
        </div>
        <div class="col-lg-9 col-sm-12">
            <div class="form-group">
                <label for="searchKeyword" class="control-label">Search by name/email/username</label>
                <input type="text" class="form-control mb-10" id="searchKeyword" name="searchKeyword" placeholder="Type something and press ENTER to search">
            </div>
        </div>
    </div>
</div>
<div class="box mt-10">
    <div class="box-body row responsive-table-container py-0 px-3">
        <div class="col-12 px-0 py-0">
            <table class="table" id="table-dataUserAdmin">
                <thead class="thead-light">
                    <tr>
                        <th width="140">User Level</th>
                        <th >Name</th>
                        <th width="300">Email</th>
                        <th width="250">Username</th>
                        <th width="100">Status</th>
                        <th width="80"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="5" class="text-center">No data found</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" id="modal-editorUserAdmin" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-md" role="document">
		<form class="modal-content form-horizontal" id="form-editorUserAdmin" autocomplete="off">
			<div class="modal-header">
				<h4 class="modal-title" id="footable-editor-title-userAdmin">User Admin Editor</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
                <div class="row border-bottom mb-20 pb-10">
                    <div class="col-sm-12 form-group required">
                        <label for="editorUserAdmin-nameUser" class="control-label">Name</label>
                        <input type="text" class="form-control" id="editorUserAdmin-nameUser" name="editorUserAdmin-nameUser" placeholder="Name">
                    </div>
                    <div class="col-sm-12 form-group required">
                        <label for="editorUserAdmin-userEmail" class="control-label">Email</label>
                        <input type="text" class="form-control" id="editorUserAdmin-userEmail" name="editorUserAdmin-userEmail" placeholder="Email">
                    </div>
                    <div class="col-lg-6 col-sm-12 form-group required">
                        <label for="editorUserAdmin-optionUserAdminLevel" class="control-label">User Level</label>
                        <select id="editorUserAdmin-optionUserAdminLevel" name="editorUserAdmin-optionUserAdminLevel" class="form-control"></select>
                    </div>
                    <div class="col-lg-6 col-sm-12 form-group required">
                        <label for="editorUserAdmin-username" class="control-label">Username</label>
                        <input type="text" class="form-control" id="editorUserAdmin-username" autocomplete="off" name="editorUserAdmin-username" placeholder="Username">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 mb-20" id="editorUserAdmin-containerWarningUpdatePassword">
                        <div class="alert alert-primary py-2" role="alert">
                            <i class="zmdi zmdi-info"></i> Please fill the form below if you want to change the password
                        </div>
                    </div>
                    <div class="col-sm-12 form-group" id="editorUserAdmin-oldPasswordContainer">
                        <label for="editorUserAdmin-oldPassword" class="control-label">Old Password</label>
                        <input type="password" class="form-control" id="editorUserAdmin-oldPassword" autocomplete="new-password" name="editorUserAdmin-oldPassword" placeholder="Old Password">
                    </div>
                    <div class="col-lg-6 col-sm-12 form-group">
                        <label for="editorUserAdmin-newPassword" class="control-label">New Password</label>
                        <input type="password" class="form-control" id="editorUserAdmin-newPassword" autocomplete="new-password" name="editorUserAdmin-newPassword" placeholder="Password">
                    </div>
                    <div class="col-lg-6 col-sm-12 form-group">
                        <label for="editorUserAdmin-repeatPassword" class="control-label">Repeat Password</label>
                        <input type="password" class="form-control" id="editorUserAdmin-repeatPassword" autocomplete="new-password" name="editorUserAdmin-repeatPassword" placeholder="Repeat Password">
                    </div>
                </div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="editorUserAdmin-idUserAdmin" name="editorUserAdmin-idUserAdmin" value="">
				<button type="submit" class="button button-primary">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>
<script>
	var url = "<?=BASE_URL_ASSETS_JS?>page-module/settings/userAdmin.js?<?=date("YmdHis")?>";
	$.getScript(url);
</script>