<form method="post" action="{{route('admin.modal-route.store')}}">
    @csrf
    <input type="hidden" name="modal_id" value="{{$modal->id}}"/>
    <!-- Modal -->
    <div id="add-route-modal" class="modal fade" role="dialog" style="display: none;">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <div class="button-container">
                        <div class="btn btn-exit" data-dismiss="modal"></div>
                    </div>
                    <div class="title-container"><h1 class="title">مسیر جدید</h1></div>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                                <span class="label">مسیر</span>
                                <input class="form-control input-sm" id="new-route" name="route">
                            </div>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                                    <span class="material-switch pull-right">تمامی مسیرهای ادامه حساب شود &nbsp
                                        <input id="children-included" class="check-route-includes" type="checkbox"
                                               value="1"/>
                                        <label for="children-included"></label>
                                        <input id="children-included_hidden" type="hidden" value="0"/>
                                        <input id="children-included_value" name="children_included"
                                               type="hidden" value="0">
                                    </span>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                                    <span class="material-switch pull-right">خود مسیر حساب شود &nbsp
                                        <input id="self-included" class="check-route-includes" type="checkbox"
                                               value="1"/>
                                        <label for="self-included"></label>
                                        <input id="self-included_hidden" type="hidden" value="0"/>
                                        <input id="self-included_value" name="self_included"
                                               type="hidden" value="0">
                                    </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="submit-add-route" type="submit" class="btn btn-sm btn-default btn-success">
                        ایجاد
                    </button>
                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">انصراف
                    </button>
                </div>
            </div>

        </div>
    </div>
</form>

