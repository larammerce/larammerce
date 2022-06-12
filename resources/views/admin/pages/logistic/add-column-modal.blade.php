<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog" style="display: none;">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <div class="button-container">
                    <div class="btn btn-exit" data-dismiss="modal"></div>
                </div>
                <div class="title-container"><h1 class="title">اضافه کردن ستون زمانی جدید</h1></div>
            </div>
            <div class="modal-body">
                <p class="message">اضافه کردن ستون زمانی جدید:</p>
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 " style="float: right">
                        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                            <span class="label">از ساعت</span>
                            <input class="form-control input-sm" type="time" id="add-start-hour"
                                   name="start_hour" value="00:00:00">
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" style="float: left">
                        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                            <span class="label">تا ساعت</span>
                            <input class="form-control input-sm" type="time" id="add-finish-hour"
                                   name="finish_hour" value="00:00:00">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="add-new-column" type="button" class="btn btn-sm btn-default btn-success"
                        data-dismiss="modal">
                    ثبت
                </button>
                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal" onclick="window.rejectReq()">
                    انصراف
                </button>
            </div>
        </div>

    </div>
</div>

