@foreach($customer_meta_categories as $customer_meta_category)
    <div
        class="col-lg-offset-1 col-lg-10 col-md-offset-0 col-md-12 col-sm-offset-0 col-sm-12 col-xs-offset-0 col-xs-12 list-row">
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 col">
            <div class="label">شناسه</div>
            <div>{{$customer_meta_category->id}}#</div>
        </div>
        <div class="col-lg-4 col-md-3 col-sm-4 col-xs-9 col">
            <div class="label">عنوان</div>
            <div>{{$customer_meta_category->title}}</div>
        </div>
        <div class="col-lg-3 col-md-2 col-sm-2 col-xs-3 col">
            <div class="label">نوع</div>
            <div>{{trans("general.customer_meta_category_type.{$customer_meta_category->type}")}}</div>
        </div>
        <div class="col-lg-3 col-md-9 col-sm-7 col-xs-9 col">
            <div class="label">عملیات</div>
            <div class="actions-container">
                <a class="btn btn-sm btn-primary"
                   href="{{route('admin.customer-meta-category.edit', $customer_meta_category)}}">
                    <i class="fa fa-pencil"></i>
                </a>
                <a class="btn btn-sm btn-danger virt-form"
                   data-action="{{ route('admin.customer-meta-category.destroy', $customer_meta_category) }}"
                   data-method="DELETE" confirm>
                    <i class="fa fa-trash"></i>
                </a>
            </div>
        </div>
    </div>
@endforeach
