@foreach($users as $user)
    <div
        class="col-lg-offset-1 col-lg-10 col-md-offset-0 col-md-12 col-sm-offset-0 col-sm-12 col-xs-offset-0 col-xs-12 list-row users">
        <div class="col-lg-1 col-md-2 col-sm-2 col-xs-3 col">
            <div class="img-container">
                <img class="img-responsive" src="{{ ImageService::getImage($user, 'thumb') }}">
            </div>
        </div>
        <div class="col-lg-1 col-md-2 col-sm-2 col-xs-3 col">
            <div class="label">شناسه</div>
            <div>{{$user->id}}#</div>
        </div>
        <div class="col-lg-1 col-md-3 col-sm-2 col-xs-3 col">
            <div class="label">تاریخ ساخت</div>
            <div>{{TimeService::getDateFrom($user->created_at)}}</div>
        </div>
        <div class="col-lg-1 col-md-2 col-sm-2 col-xs-3 col">
            <div class="label">نام کاربری</div>
            <div>{{$user->username}}</div>
        </div>
        <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6 col">
            <div class="label">نام</div>
            <div>{{$user->name}} {{$user->family}}</div>
        </div>
        <div class="col-lg-2 col-md-3 col-sm-5 col-xs-6 col">
            <div class="label">ایمیل</div>
            <div>{{$user->email}}</div>
        </div>
        <div class="col-lg-4 col-md-9 col-sm-7 col-xs-12 col">
            <div class="label">عملیات</div>
            <div class="actions-container">
                <a class="btn btn-sm btn-success" href="{{route('admin.login-as', $user)}}">
                    <i class="fa fa-sign-in"></i>
                </a>
                <a class="btn btn-sm btn-primary" href="{{route('admin.user.edit', $user)}}">
                    <i class="fa fa-pencil"></i>
                </a>
                <a class="btn btn-sm btn-danger virt-form"
                   data-action="{{ route('admin.user.destroy', $user) }}"
                   data-method="DELETE" confirm>
                    <i class="fa fa-trash"></i>
                </a>
                <span class="material-switch pull-right">
                        مدیر&nbsp
                        <input id="is_system_user_{{$user->id}}" name="is_system_user_{{$user->id}}" type="checkbox"
                               @if($user->is_system_user) checked @endif disabled/>
                        <label for="is_system_user_{{$user->id}}"></label>
                </span>
                <span class="material-switch pull-right">
                        خریدار&nbsp
                        <input id="is_customer_user_{{$user->id}}" name="is_customer_user_{{$user->id}}" type="checkbox"
                               @if($user->is_customer_user) checked @endif disabled/>
                        <label for="is_customer_user_{{$user->id}}"></label>
                </span>
            </div>
        </div>
    </div>
@endforeach
