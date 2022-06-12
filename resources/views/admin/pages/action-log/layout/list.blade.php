@foreach($action_logs as $action_log)
    <div
        class="col-lg-offset-1 col-lg-10 col-md-offset-0 col-md-12 col-sm-offset-0 col-sm-12 col-xs-offset-0 col-xs-12 list-row roles @if(!$action_log->is_active) disabled @endif">
        <div class="col-lg-3 col-md-1 col-sm-1 col-xs-3 col">
            <div class="label">شناسه</div>
            <div>{{$action_log->_id}}#</div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 col">
            <div class="label">نام کاربر انجام دهنده عملیات</div>
            <div>{{$action_log->user->full_name}}</div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 col">
            <div class="label">عملیات درخواستی</div>
            <div>{{trans("structures.methods.".$action_log->method_name)}}</div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 col">
            <div class="label">مدل مرتبط</div>
            <div>{{trans("structures.classes.".$action_log->related_model_type)}}</div>
        </div>
        <div class="col-lg-1 col-md-2 col-sm-2 col-xs-6 col">
            <div class="label">دسترسی</div>
            <div>@if($action_log->is_allowed) مجاز@else غیرمجاز@endif</div>
        </div>
        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-3 col">
            <div class="label">تاریخ</div>
            <div><span class="label label-primary"
                       style="padding: 6px">{{TimeService::getDateFrom($action_log->created_at)}}</span></div>
        </div>
        <div class="col-lg-1 col-md-2 col-sm-2 col-xs-6 col">
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
@endforeach
