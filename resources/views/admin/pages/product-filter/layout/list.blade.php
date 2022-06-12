@foreach($product_filters as $product_filter)
    <div
        class="col-lg-offset-1 col-lg-10 col-md-offset-0 col-md-12 col-sm-offset-0 col-sm-12 col-xs-offset-0 col-xs-12 list-row product-filters">
        <div class="col-lg-2 col-md-1 col-sm-1 col-xs-6 col">
            <div class="label">شناسه</div>
            <div>{{$product_filter->id}}#</div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 col">
            <div class="label">عنوان</div>
            <div>{{$product_filter->title}}</div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 col">
            <div class="label">کد شناساگر</div>
            <div>{{$product_filter->identifier}}</div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 col">
            <div class="label">عملیات</div>
            <div class="actions-container">
                <a class="btn btn-sm btn-primary" href="{{ route('admin.product-filter.edit', $product_filter) }}">
                    <i class="fa fa-pencil"></i>
                </a>
                <a class="btn btn-sm btn-success"
                   href="{{ route('admin.product.index'). "?product_filter_id={$product_filter->id}" }}">
                    <i class="fa fa-cubes"></i>
                </a>
                <a class="btn btn-sm btn-danger virt-form"
                   data-action="{{ route('admin.product-filter.destroy', $product_filter) }}"
                   data-method="DELETE" confirm>
                    <i class="fa fa-trash"></i>
                </a>
            </div>
        </div>
    </div>
@endforeach
