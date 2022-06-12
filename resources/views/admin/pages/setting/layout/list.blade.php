@foreach($settings as $setting)
    <div
        class="col-lg-offset-1 col-lg-10 col-md-offset-0 col-md-12 col-sm-offset-0 col-sm-12 col-xs-offset-0 col-xs-12 list-row roles">
        <div class="col-lg-1 col-md-1 col-sm-3 col-xs-3 col">
            <div class="img-container">
                <img class="img-responsive" src="/admin_dashboard/images/No_image.jpg.png">
            </div>
        </div>
        <div class="col-lg-1 col-md-1 col-sm-3 col-xs-3 col">
            <div class="label">شناسه</div>
            <div>{{$setting->id}}#</div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-3 col-xs-3 col">
            <div class="label">کلید</div>
            <div>{{$setting->key}}</div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 col">
            <div class="label">مقدار</div>
            <div>{{$setting->value}}</div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 col">
            <div class="label">عملیات</div>
            <div class="actions-container">
                <a class="btn btn-sm btn-primary" href="{{route('admin.setting.edit', $setting)}}">
                    <i class="fa fa-pencil"></i>
                </a>
                <a class="btn btn-sm btn-danger virt-form"
                   data-action="{{ route('admin.setting.destroy', $setting) }}"
                   data-method="DELETE" confirm>
                    <i class="fa fa-trash"></i>
                </a>
                <a class="btn btn-sm btn-success" href="{{route('admin.setting.show', $setting)}}">
                    <i class="fa fa-eye"></i>
                </a>

                <span class="material-switch pull-right">شخصی&nbsp
                    <input type="checkbox" @if($setting->user_id) checked @endif disabled/>
                    <label></label>
                </span>
            </div>
        </div>
    </div>
@endforeach
