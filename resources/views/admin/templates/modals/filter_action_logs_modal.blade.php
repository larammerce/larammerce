
<form method="get" action="{{route('admin.action-log.filter')}}">
    <!-- Modal -->
    <div id="filter-action-logs-modal" class="modal fade" role="dialog" style="display: none;">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <div class="button-container">
                        <div class="btn btn-exit" data-dismiss="modal"></div>
                    </div>
                    <div class="title-container"><h1 class="title">فیلتر</h1></div>
                </div>
                <div class="modal-body">
                    <p class="message">جستجو بر اساس:</p>
                    <div class="row" id="search-inputs">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 " style="float: right">
                            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                                <span class="label">{{ trans("structures.attributes._id") }}</span>
                                <input class="form-control input-sm" type="text"
                                       name="_id">
                            </div>
                            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                                <span class="label">{{ trans("structures.attributes.user") }}</span>
                                <select class="form-control" name="user_id">
                                    <option value=""></option>
                                    @foreach(get_system_users() as $system_user)
                                        @if(isset($system_user->user))
                                            <option value="{{$system_user->user->id}}">
                                                {{ $system_user->user->full_name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                                <span class="label">{{ trans("structures.attributes.related_model_type") }}</span>
                                <select class="form-control" name="related_model_type">
                                    <option value=""></option>
                                    @foreach(get_models_entity_names() as $key => $value)
                                        <option value="{{$key}}">
                                            {{ trans("structures.classes.".get_model_entity_name($value)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                                <span class="label">{{ trans("structures.attributes.related_model_id") }}</span>
                                <input class="form-control input-sm" type="text"
                                       name="related_model_id">
                            </div>
                            <h4>انتخاب بازه های زمانی</h4>
                            <div class="row">
                                <div class="col-lg-4 col-md-3 col-sm-12 col-xs-12">
                                    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                                        <span class="label">تا تاریخ</span>
                                        <input class="form-control input-sm" type="date"
                                               name="last_date" value="">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-3 col-sm-12 col-xs-12">
                                    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                                        <span class="label">از تاریخ</span>
                                        <input class="form-control input-sm" type="date"
                                               name="first_date" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="submit-classic-search" type="submit" class="btn btn-sm btn-default btn-success">
                        جستجو
                    </button>
                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal" onclick="window.rejectReq()">انصراف
                    </button>
                </div>
            </div>

        </div>
    </div>
</form>


