<div id="confirm-modal" class="modal fade" role="dialog">
    <div class="modal-dialog  modal-sm ">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <div class="button-container">
                    <div class="btn btn-exit" data-dismiss="modal"></div>
                </div>
                <div class="title-container"><h1 class="title"> اعلام موافقت </h1></div>
            </div>
            <div class="modal-body">
                <p class="message"></p></div>
            <div class="modal-footer">
                <button autofocus type="button" class="btn btn-sm btn-default btn-danger" data-dismiss="modal" onclick="window.acceptReq()">
                    بله
                </button>
                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal"
                        onclick="window.rejectReq()">خیر
                </button>
            </div>
        </div>

    </div>
</div>