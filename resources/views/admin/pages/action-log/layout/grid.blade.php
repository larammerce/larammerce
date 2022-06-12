@foreach($action_logs as $action_log)
    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 grid-item users">
        <div class="item-container">
            <div class="col-lg-12 col-md-4 col-sm-3 col-xs-5 col">
                <div class="label">شناسه</div>
                <div>{{$action_log->_id}}#</div>
            </div>
            <div class="col-lg-12 col-md-8 col-sm-5 col-xs-7 col">
                <div class="label">نام کاربر انجام دهنده عملیات</div>
                <div>{{$action_log->user->full_name}}</div>
            </div>
            <div class="col-lg-12 col-md-8 col-sm-5 col-xs-7 col">
                <div class="label">عملیات درخواستی</div>
                <div>{{trans("structures.methods.".$action_log->method_name)}}</div>
                <div>{{$action_log->action}}</div>
            </div>
            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 col">
                <div class="label">مدل مرتبط</div>
                <div>{{trans("structures.classes.".$action_log->related_model_type)}}</div>
            </div>
            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 col">
                <div class="label">شناسه مدل مرتبط</div>
                <div>{{$action_log->related_model_id}}</div>
            </div>
            <div class="col-lg-7 col-md-8 col-sm-5 col-xs-7 col">
                <div class="label">دسترسی</div>
                <div>@if($action_log->is_allowed) مجاز@else غیرمجاز@endif</div>
            </div>
            <div class="col-lg-7 col-md-8 col-sm-5 col-xs-7 col">
                <div class="label">تاریخ</div>
                <div>{{TimeService::getDateFrom($action_log->created_at)}}</div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 col col-action">
                <div class="label">عملیات</div>
                <div class="actions-container">
                    <a class="btn btn-sm btn-danger virt-form"
                       data-action="{{ route('admin.action-log.destroy', $action_log) }}"
                       data-method="DELETE" confirm>
                        <i class="fa fa-trash"></i>
                    </a>
                    <a class="btn btn-sm btn-success" href="{{route('admin.action-log.show', $action_log)}}">
                        <i class="fa fa-eye"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endforeach
