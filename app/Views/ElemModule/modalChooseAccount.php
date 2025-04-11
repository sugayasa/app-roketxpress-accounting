<div class="modal fade" tabindex="-1" id="modal-chooseAccount" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <form class="modal-content form-horizontal" id="form-chooseAccount" autocomplete="off">
            <div class="modal-header">
                <h4 class="modal-title" id="editor-title-chooseAccount">Choose Account</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-pills mb-20 pb-20 border-bottom" id="chooseAccount-tabGroup">
                    <li class="nav-item"><a class="nav-link active" data-toggle="pill" href="#chooseAccount">Choose Account</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#listAccount">List Account</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade active show" id="chooseAccount">
                        <div class="row mbn-20">
                            <div class="col-sm-12 mb-10">
                                <div class="form-group">
                                    <label for="chooseAccount-optionAccountMain" class="control-label">Main Account</label>
                                    <select id="chooseAccount-optionAccountMain" name="chooseAccount-optionAccountMain" class="form-control"></select>
                                </div>
                            </div>
                            <div class="col-sm-12 mb-10">
                                <div class="form-group">
                                    <label for="chooseAccount-optionAccountSub" class="control-label">Sub Account</label>
                                    <select id="chooseAccount-optionAccountSub" name="chooseAccount-optionAccountSub" class="form-control"></select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="listAccount">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="120">Account Code</th>
                                            <th>Account Name</th>
                                            <th width="40"><label id="labelCheckBoxAllAccount" class="adomx-checkbox"><input type="checkbox" id="checkBoxAllAccount"/> <i class="icon"></i></label></th>
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
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="button button-primary">Choose Account</button>
                <button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div>