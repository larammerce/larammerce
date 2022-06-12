@foreach($customer_users_legal_info  as $customer_user_legal_info)
    <div
        class="col-lg-offset-1 col-lg-10 col-md-offset-0 col-md-12 col-sm-offset-0 col-sm-12 col-xs-offset-0 col-xs-12 list-row users ">
        <div class="col-lg-1 col-md-2 col-sm-3 col-xs-3 col">
            <div class="img-container">
                <img class="img-responsive" src="/admin_dashboard/images/No_image.jpg.png">
            </div>
        </div>
        <div class="col-lg-1 col-md-2 col-sm-3 col-xs-3 col">
            <div class="label">شناسه</div>
            <div>{{$customer_user_legal_info->id}}#</div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6 col">
            <div class="label">نام</div>
            <div>{{$customer_user_legal_info->customerUser->user->full_name}}</div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 col">
            <div class="label">نام شرکت</div>
            <div>{{$customer_user_legal_info->company_name}}</div>
        </div>
        <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 col">
            <div class="label">عملیات</div>
            <div class="actions-container">
                <a class="btn btn-sm btn-primary"
                   href="{{route('admin.customer-user-legal-info.edit', $customer_user_legal_info)}}">
                    <i class="fa fa-pencil"></i>
                </a>
                <a class="btn btn-sm btn-danger virt-form"
                   data-action="{{ route('admin.customer-user-legal-info.destroy', $customer_user_legal_info) }}"
                   data-method="DELETE" confirm>
                    <i class="fa fa-trash"></i>
                </a>
                <a class="btn btn-sm btn-success"
                   href="{{route('admin.customer-user-legal-info.show', $customer_user_legal_info)}}">
                    <i class="fa fa-eye"></i>
                </a>
            </div>
        </div>
    </div>
@endforeach
