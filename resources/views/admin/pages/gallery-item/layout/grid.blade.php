@foreach($gallery_items as $gallery_item)
    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 grid-item users">
        <div class="item-container">
            <div class="col-lg-2 col-md-3 col-sm-3 col-xs-3 col">
                <div class="img-container">
                    <img class="img-responsive" src="{{ $gallery_item->getImagePath() }}">
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-2 col-xs-5 col">
                <div class="label">شناسه</div>
                <div>{{$gallery_item->id}}#</div>
            </div>
            <div class="col-lg-7 col-md-12 col-sm-12 col-xs-12 col">
                <div class="label">شناساگر گالری</div>
                <div>{{$gallery_item->gallery->identifier}}</div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col col-action">
                <div class="label">عملیات</div>
                <div class="actions-container">
                    <a class="btn btn-sm btn-primary" href="{{route('admin.gallery-item.edit', $gallery_item)}}">
                        <i class="fa fa-pencil"></i>
                    </a>
                    <a class="btn btn-sm btn-danger virt-form"
                       data-action="{{ route('admin.gallery-item.destroy', $gallery_item) }}"
                       data-method="DELETE" confirm>
                        <i class="fa fa-trash"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endforeach
