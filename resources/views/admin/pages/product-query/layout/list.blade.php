@foreach($product_queries as $product_query)
    <div
        class="col-lg-offset-1 col-lg-10 col-md-offset-0 col-md-12 col-sm-offset-0 col-sm-12 col-xs-offset-0 col-xs-12 list-row product-queries">
        <div class="col-lg-2 col-md-1 col-sm-1 col-xs-6 col">
            <div class="label">شناسه</div>
            <div>{{$product_query->id}}#</div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 col">
            <div class="label">عنوان</div>
            <div>{{$product_query->title}}</div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 col">
            <div class="label">کد شناساگر</div>
            <div>{{$product_query->identifier}}</div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 col">
            <div class="label">عملیات</div>
            <div class="actions-container">
                <a class="btn btn-sm btn-primary" href="{{ route('admin.product-query.edit', $product_query) }}">
                    <i class="fa fa-pencil"></i>
                </a>
                <a class="btn btn-sm btn-danger virt-form"
                   data-action="{{ route('admin.product-query.destroy', $product_query) }}"
                   data-method="DELETE" confirm>
                    <i class="fa fa-trash"></i>
                </a>
            </div>
        </div>
    </div>
@endforeach
