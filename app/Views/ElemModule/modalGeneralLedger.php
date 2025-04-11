<div class="modal fade" tabindex="-1" id="modalGeneral-generalLedger" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content form-horizontal" id="modalGeneral-generalLedger-content" autocomplete="off">
			<div class="modal-header">
				<h4 class="modal-title" id="modalGeneral-generalLedger-title">General Ledger</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 mb-10">
                        <h6 id="modalGeneral-generalLedger-detailAccountDatePeriod"></h6>
                    </div>
                    <div class="col-sm-12 pl-0 pr-0 tableFixHead" style="height: 400px">
                        <table class="table">
                            <thead class="thead-light">
                                <tr>
                                    <th width="120">Date</th>
                                    <th width="140">Reff. Number</th>
                                    <th> Description</th>
                                    <th width="130" class="text-right">Debit</th>
                                    <th width="130" class="text-right">Credit</th>
                                </tr> 
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="3"><b>Beginning Balance<b/></td>
                                    <td align="right"><b id="modalGeneral-generalLedger-beginningBalanceDebit"></b></td>
                                    <td align="right"><b id="modalGeneral-generalLedger-beginningBalanceCredit"></b></td>
                                </tr>
                                <tr id="modalGeneral-generalLedger-trTotalMutation">
                                    <td colspan="3"><b>Total<b/></td>
                                    <td align="right"><b id="modalGeneral-generalLedger-totalMutationDebit"></b></td>
                                    <td align="right"><b id="modalGeneral-generalLedger-totalMutationCredit"></b></td>
                                </tr>
                            </tbody>
                        </table> 
                    </div>
                </div>
			</div>
			<div class="modal-footer pt-0 pl-0 pr-0">
                <table class="table">
                    <thead class="thead-light">
                        <tr>
                            <th width="33%" class="border-right">Beginning Balance <span class="pull-right" id="modalGeneral-generalLedger-footerBeginningBalance"></span></th>
                            <th width="33%" class="border-right">Total Mutation <span class="pull-right" id="modalGeneral-generalLedger-footerTotalMutation"></span></th>
                            <th width="33%">Ending Balance <span class="pull-right" id="modalGeneral-generalLedger-footerEndingBalance"></span></th>
                        </tr> 
                    </thead>
                </table> 
			</div>
        </div>
	</div>
</div>