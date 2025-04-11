<div class="row justify-content-between align-items-center mb-10">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">Template Journal<span>/ List of frequently used template journals</span></h3>
		</div>
	</div>
	<div class="col-12 col-lg-auto mb-10">
		<div class="page-date-range">
			<button class="button button-success button-sm pull-right" id="btnAddTemplateJournal" data-toggle="modal" data-target="#modal-editorTemplateJournal"><span><i class="fa fa-plus"></i>New Template</span></button>
		</div>
	</div>
</div>
<div class="box">
    <div class="box-body">
        <div class="row">
            <div class="col-lg-3 col-sm-6">
                <div class="form-group">
                    <label for="optionAccountMain" class="control-label">Main Account</label>
                    <select id="optionAccountMain" name="optionAccountMain" class="form-control" option-all="All Account"></select>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="form-group">
                    <label for="optionAccountSub" class="control-label">Sub Account</label>
                    <select id="optionAccountSub" name="optionAccountSub" class="form-control" option-all="All Account"></select>
                </div>
            </div>
            <div class="col-lg-6 col-sm-12">
                <div class="form-group">
                    <label for="searchKeyword" class="control-label">Search</label>
                    <input type="text" class="form-control mb-10" id="searchKeyword" name="searchKeyword" placeholder="Type something & press ENTER">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="box mt-10">
    <div class="box-body px-0 py-0 responsive-table-container">
        <div class="row">
            <div class="col-12 pt-15 pr-20 pb-10">
                <span class="pl-15" id="tableDataCount-dataTemplateJournal">0 data found</span>
            </div>
            <div class="col-12">
                <table class="table" id="table-dataTemplateJournal">
                    <thead class="thead-light">
                        <tr>
                            <th width="250">Template Name</th>
                            <th >Description</th>
                            <th width="350">Account Debit</th>
                            <th width="350">Account Credit</th>
                            <th width="60"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-center">No data found</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-sm-12 mb-10 pl-30 pr-30 pb-10">
                <hr/>
                <ul class="pagination" id="tablePagination-dataTemplateJournal"></ul>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" id="modal-editorTemplateJournal" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-lg" role="document">
		<form class="modal-content form-horizontal" id="form-editorTemplateJournal" autocomplete="off">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-editorTemplateJournal">Editor Template Journal</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
                <div class="row mbn-20">
                    <div class="col-sm-12 mb-10">
                        <div class="form-group required">
                            <label for="editorTemplateJournal-templateName" class="control-label">Name</label>
                            <input type="text" class="form-control mb-10" id="editorTemplateJournal-templateName" name="editorTemplateJournal-templateName">
                        </div>
                    </div>
                    <div class="col-sm-12 mb-10">
                        <div class="form-group required">
                            <label for="editorTemplateJournal-templateDescription" class="control-label">Description</label>
                            <input type="text" class="form-control mb-10" id="editorTemplateJournal-templateDescription" name="editorTemplateJournal-templateDescription">
                        </div>
                    </div>
                    <div class="col-sm-12 mb-20">
                        <div class="alert alert-primary" role="alert">
                            <i class="zmdi zmdi-info"></i> <span>The description will also be displayed as the default journal description input</span>
                        </div>
                    </div>
                    <div class="col-sm-12 mb-10">
                        <h6>Journal Account <button class="button button-success button-sm pull-right" type="button" data-toggle="modal" data-target="#modal-addAccountTemplateJournal"><span><i class="fa fa-plus"></i> Account</span></button></h6>
                        <table class="table" id="editorTemplateJournal-tableDataAccount">
                            <thead class="thead-light">
                                <tr>
                                    <th width="50%">Debit</th>
                                    <th width="50%">Credit</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr id="editorTemplateJournal-trEmptyAccountTemplateJournal">
                                    <td colspan="2" class="text-center">No data</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
			</div>
			<div class="modal-footer">
                <input type="hidden" id="editorTemplateJournal-idJournalTemplateRecap" name="editorTemplateJournal-idJournalTemplateRecap" value="0">
				<button type="submit" class="button button-primary">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" tabindex="-1" id="modal-addAccountTemplateJournal" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-md" role="document">
		<form class="modal-content form-horizontal" id="form-addAccountTemplateJournal" autocomplete="off">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-addAccountTemplateJournal">Choose Account</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
                <div class="row mbn-20">
                    <div class="col-sm-12 mb-10">
                        <div class="form-group">
                            <label for="addAccountTemplateJournal-optionAccountMain" class="control-label">Main Account</label>
                            <select id="addAccountTemplateJournal-optionAccountMain" name="addAccountTemplateJournal-optionAccountMain" class="form-control"></select>
                        </div>
                    </div>
                    <div class="col-sm-12 mb-10">
                        <div class="form-group">
                            <label for="addAccountTemplateJournal-optionAccountSub" class="control-label">Sub Account</label>
                            <select id="addAccountTemplateJournal-optionAccountSub" name="addAccountTemplateJournal-optionAccountSub" class="form-control"></select>
                        </div>
                    </div>
                    <div class="col-sm-12 mb-20">
                        <div class="alert alert-primary" role="alert">
                            <i class="zmdi zmdi-info"></i> <span>Default position for selected account :<br/>[ + ] <b id="addAccountTemplateJournal-textDefaultPositionPlus"></b><br/>[ - ] <b id="addAccountTemplateJournal-textDefaultPositionMinus"></b></span>
                        </div>
                    </div>
                    <div class="col-12 mb-10">
                        <div class="form-group">
                            <label for="addAccountTemplateJournal-debitCredit" class="control-label">Position</label>
                            <div class="adomx-checkbox-radio-group inline">
                                <label class="adomx-radio-2"><input type="radio" name="addAccountTemplateJournal-debitCredit" value="DR" checked> <i class="icon"></i> Debit</label>
                                <label class="adomx-radio-2"><input type="radio" name="addAccountTemplateJournal-debitCredit" value="CR"> <i class="icon"></i> Credit</label>
                            </div>
                        </div>
                    </div>
                </div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="button button-primary">Add</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>
<script>
	var url = "<?=BASE_URL_ASSETS_JS?>page-module/templateJournal.js?<?=date("YmdHis")?>";
	$.getScript(url);
</script>