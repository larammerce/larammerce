@foreach($users as $user)
    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 grid-item users">
        <div class="item-container">
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 col">
                <div class="img-container">
                    <img class="img-responsive" src="{{ ImageService::getImage($user, 'thumb') }}">
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 col">
                <div class="label">شناسه</div>
                <div>{{$user->id}}#</div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 col">
                <div class="label">نام کاربری</div>
                <div>{{$user->username}}</div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 col">
                <div class="label">تاریخ</div>
                <div>{{TimeService::getDateFrom($user->created_at)}}</div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 col">
                <div class="label">نام</div>
                <div>{{$user->name}} {{$user->family}}</div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col">
                <div class="label">ایمیل</div>
                <div>{{$user->email}}</div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col col-action">
                <div class="label">عملیات</div>
                <div class="actions-container">
                    <div class="col-lg-5 col-md-12">
                        <a class="btn btn-sm btn-primary" href="{{route('admin.user.edit', $user)}}">
                            <i class="fa fa-pencil"></i>
                        </a>
                        <a class="btn btn-sm btn-danger virt-form"
                           data-action="{{ route('admin.user.destroy', $user) }}"
                           data-method="DELETE" confirm>
                            <i class="fa fa-trash"></i>
                        </a>
                    </div>
                    <div class="col-lg-7 col-md-12">
                        <span class="material-switch">
                        مدیر&nbsp
                        <input id="is_system_user_{{$user->id}}" name="is_system_user_{{$user->id}}" type="checkbox"
                               @if($user->is_system_user) checked @endif disabled/>
                        <label for="is_system_user_{{$user->id}}"></label>
                </span>
                        <span class="material-switch">
                        خریدار&nbsp
                        <input id="is_customer_user_{{$user->id}}" name="is_customer_user_{{$user->id}}" type="checkbox"
                               @if($user->is_customer_user) checked @endif disabled/>
                        <label for="is_customer_user_{{$user->id}}"></label>
                </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
