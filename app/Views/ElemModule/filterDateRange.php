<div class="col-lg-8 col-sm-12 pt-15">
    <div class="form-group">
        <div class="adomx-checkbox-radio-group inline">
            <label class="adomx-radio-2"><input type="radio" name="radioDateRangeType" class="radioDateRangeType" value="1" checked> <i class="icon"></i> This Month</label>
            <label class="adomx-radio-2"><input type="radio" name="radioDateRangeType" class="radioDateRangeType" value="2"> <i class="icon"></i> Last Month</label>
            <label class="adomx-radio-2"><input type="radio" name="radioDateRangeType" class="radioDateRangeType" value="3"> <i class="icon"></i> Year To Date</label>
            <label class="adomx-radio-2"><input type="radio" name="radioDateRangeType" class="radioDateRangeType" value="4"> <i class="icon"></i> Year To Last Month</label>
            <label class="adomx-radio-2"><input type="radio" name="radioDateRangeType" class="radioDateRangeType" value="5"> <i class="icon"></i> Last Year</label>
            <label class="adomx-radio-2"><input type="radio" name="radioDateRangeType" class="radioDateRangeType" value="6"> <i class="icon"></i> Custom Date</label>
        </div>
    </div>
</div>
<div class="col-lg-2 col-sm-6">
    <input type="text" class="form-control input-date-single text-center" id="dateStart" name="dateStart" disabled value="<?=date('01-m-Y')?>">
</div>
<div class="col-lg-2 col-sm-6">
    <input type="text" class="form-control input-date-single text-center" id="dateEnd" name="dateEnd" disabled value="<?=date('t-m-Y')?>">
</div>