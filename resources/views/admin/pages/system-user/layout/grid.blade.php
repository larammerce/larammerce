@foreach($system_users as $system_user)
    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 grid-item users">
        <div class="item-container">
            <div class="col-lg-2 col-md-3 col-sm-3 col-xs-3 col">
                <div class="img-container">
                    <img class="img-responsive" src="{{$system_user->getImagePath()}}">
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-2 col-xs-5 col">
                <div class="label">شناسه</div>
                <div>{{$system_user->id}}#</div>
            </div>
            <div class="col-lg-7 col-md-9 col-sm-6 col-xs-6 col">
                <div class="label">نام</div>
                <div>{{$system_user->user->name}} {{$system_user->user->family}}</div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col col-action">
                <div class="label">عملیات</div>
                <div class="actions-container">
                    <div class="col-lg-5 col-md-6 col-xs-6">
                        <a class="btn btn-sm btn-primary" href="{{route('admin.system-user.edit', $system_user)}}">
                            <i class="fa fa-pencil"></i>
                        </a>
                        <a class="btn btn-sm btn-danger virt-form"
                           data-action="{{ route('admin.system-user.destroy', $system_user) }}"
                           data-method="DELETE" confirm>
                            <i class="fa fa-trash"></i>
                        </a>
                    </div>
                    <div class="col-lg-7 col-md-6 col-xs-6">
                        <span class="material-switch">کاربر ارشد
                            <input id="is_super_user_{{$system_user->id}}" name="is_super_user_{{$system_user->id}}"
                                   type="checkbox"
                                   @if($system_user->is_super_user) checked @endif disabled/>
                            <label for="is_super_user_{{$system_user->id}}"></label>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
