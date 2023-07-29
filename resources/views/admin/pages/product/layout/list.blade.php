@foreach($products as $product)
    <div
        class="col-lg-offset-1 col-lg-10 col-md-offset-0 col-md-12 col-sm-offset-0 col-sm-12 col-xs-offset-0 col-xs-12 list-row products @if(!$product->is_active) disabled @endif">
        <div class="col-lg-1 col-md-2 col-sm-2 col-xs-4 col">
            @if(isset($directory) and $product->directory_id != $directory->id)
                <i class="link-file fa fa-link"></i>
            @endif
            <div class="img-container" act="file" href="{{$product->getFrontUrl()}}" target="_blank"
                 edit-href="{{route('admin.product.edit', $product)}}" data-file-type="App\Models\Product"
                 data-file-id="{{$product->id}}">
                <img class="img-responsive" src="{{ ImageService::getImage($product, 'thumb') }}">
            </div>
        </div>
        <div class="col-lg-1 col-md-2 col-sm-2 col-xs-4 col">
            <div class="label">شناسه</div>
            <div>{{$product->id}}#</div>
        </div>
        <div class="col-lg-1 col-md-3 col-sm-2 col-xs-4 col">
            <div class="label">کد محصول</div>
            <div>{{$product->code}}</div>
        </div>
        <div class="col-lg-4 col-md-3 col-sm-4 col-xs-9 col">
            <div class="label">عنوان</div>
            <div>{{$product->title}}</div>
        </div>
        <div class="col-lg-1 col-md-2 col-sm-2 col-xs-3 col">
            <div class="label">موجودی</div>
            <div>{{$product->count}}</div>
        </div>
        <div class="col-lg-1 col-md-3 col-sm-5 col-xs-3 col">
            <div class="label">آخرین قیمت</div>
            <div>{{format_price($product->latest_price)}}</div>
        </div>
        <div class="col-lg-3 col-md-9 col-sm-7 col-xs-9 col">
            <div class="label">عملیات</div>
            <div class="actions-container">
                <a class="btn btn-sm btn-primary" href="{{route('admin.product.edit', $product)}}">
                    <i class="fa fa-pencil"></i>
                </a>
                @if(isset($directory) and $product->directory_id != $directory->id)
                    <a class="btn btn-sm btn-danger virt-form"
                       data-action="{{ route('admin.directory.unlink-product', [$directory, $product->id]) }}"
                       data-method="POST" title="حذف محصول مرتبط" confirm>
                        <i class="fa fa-cut"></i>
                    </a>
                @else
                    <a class="btn btn-sm btn-danger virt-form"
                       data-action="{{ route('admin.product.destroy', $product) }}"
                       data-method="DELETE" confirm>
                        <i class="fa fa-trash"></i>
                    </a>
                    <a class="btn btn-sm btn-success" href="{{route('public.view-product', $product)}}">
                        <i class="fa fa-eye"></i>
                    </a>
                    <a class="btn btn-sm btn-info virt-form"
                       data-action="{{ route('admin.product.clone', $product) }}"
                       data-method="PUT" title="ایجاد محصول مشابه" confirm>
                        <i class="fa fa-clone"></i>
                    </a>
                @endif
                @if($product->model_id != null)
                    <a class="btn btn-sm btn-success" href="{{route('admin.product.models', $product)}}"
                       title="مشاهده‌ی محصولات مشابه">
                        <i class="fa fa-bars"></i>
                    </a>
                @endif
                @if($product->discount_group_id !== null)
                    <a class="btn btn-sm btn-warning"
                       href="{{route('admin.discount-group.edit', $product->discount_group_id)}}"
                       title="پلن تخفیف">
                        <i class="fa fa-percent"></i>
                    </a>
                @endif
            </div>
        </div>
    </div>
@endforeach
