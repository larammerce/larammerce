@foreach($system_users as $system_user)
    <div
        class="col-lg-offset-1 col-lg-10 col-md-offset-0 col-md-12 col-sm-offset-0 col-sm-12 col-xs-offset-0 col-xs-12 list-row users ">
        <div class="col-lg-1 col-md-2 col-sm-3 col-xs-3 col">
            <div class="img-container">
                <img class="img-responsive" src="{{$system_user->getImagePath()}}">
            </div>
        </div>
        <div class="col-lg-1 col-md-2 col-sm-3 col-xs-3 col">
            <div class="label">شناسه</div>
            <div>{{$system_user->id}}#</div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6 col">
            <div class="label">نام</div>
            <div>{{$system_user->user->name}} {{$system_user->user->family}}</div>
        </div>
        <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 col">
            <div class="label">عملیات</div>
            <div class="actions-container">
                <span class="material-switch">کاربر ارشد
                    <input id="is_super_user_{{$system_user->id}}" name="is_super_user_{{$system_user->id}}"
                           type="checkbox"
                           @if($system_user->is_super_user) checked @endif disabled/>
                    <label for="is_super_user_{{$system_user->id}}"></label>
                </span>
                <a class="btn btn-sm btn-primary" href="{{route('admin.system-user.edit', $system_user)}}">
                    <i class="fa fa-pencil"></i>
                </a>
                <a class="btn btn-sm btn-danger virt-form"
                   data-action="{{ route('admin.system-user.destroy', $system_user) }}"
                   data-method="DELETE" confirm>
                    <i class="fa fa-trash"></i>
                </a>
            </div>
        </div>
    </div>
@endforeach
