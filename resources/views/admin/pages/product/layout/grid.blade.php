@foreach($products as $product)
    <div act="file" href="{{$product->getFrontUrl()}}" target="_blank"
         class="file-container col-lg-1 col-md-2 col-sm-3 col-xs-6" data-file-id="{{$product->id}}"
         edit-href="{{route('admin.product.edit', $product)}}" data-file-type="App\Models\Product">
        <div class="file-checkbox">
            <i class="fa fa-check-circle-o"></i>
            <i class="fa fa-circle-o"></i>
        </div>
        <a href="{{ route('admin.product.show', $product) }}" class="file-content">
            <div class="h-icon icon-product square-ratio"
                 style="background-image: url('{{ImageService::getImage($product, "thumb")}}') ;"></div>
            <div class="file-detail">
                <h3 class="file-title">#{{ $product->id }}</h3>
                <h3 class="file-title">{{ $product->title }}</h3>
                <h3 class="file-title">{{ $product->code }}</h3>
            </div>
        </a>
    </div>
@endforeach