@foreach($customer_users as $customer_user)
    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 grid-item users">
        <div class="item-container  @if(!$customer_user->is_active) disabled @endif">
            <div class="col-lg-2 col-md-3 col-sm-3 col-xs-3 col">
                <div class="img-container">
                    <img class="img-responsive" src="/admin_dashboard/images/No_image.jpg.png">
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-2 col-xs-5 col">
                <div class="label">شناسه</div>
                <div>{{$customer_user->id}}#</div>
            </div>
            <div class="col-lg-7 col-md-9 col-sm-6 col-xs-6 col">
                <div class="label">نام</div>
                <div>{{$customer_user->user->name}} {{$customer_user->user->family}}</div>
            </div>
            <div class="col-lg-10 col-md-12 col-sm-9 col-xs-12 col">
                <div class="label">شماره تلفن</div>
                <div>{{$customer_user->main_phone}}</div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col col-action">
                <div class="label">عملیات</div>
                <div class="actions-container">
                    <a class="btn btn-sm btn-primary" href="{{route('admin.customer-user.edit', $customer_user)}}">
                        <i class="fa fa-pencil"></i>
                    </a>
                    <a class="btn btn-sm btn-primary"
                       href="{{route('admin.customer-address.index')}}?customer_user_id={{$customer_user->id}}">
                        <i class="fa fa-location-arrow"></i>
                    </a>
                    <a class="btn btn-sm btn-primary"
                       href="{{route('admin.invoice.index')}}?customer_user_id={{$customer_user->id}}">
                        <i class="fa fa-money"></i>
                    </a>
                    <a class="btn btn-sm btn-danger virt-form"
                       data-action="{{ route('admin.customer-user.destroy', $customer_user) }}"
                       data-method="DELETE" confirm>
                        <i class="fa fa-trash"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endforeach
