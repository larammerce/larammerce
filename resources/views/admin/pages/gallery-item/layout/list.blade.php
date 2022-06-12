@foreach($gallery_items as $gallery_item)
    <div
        class="col-lg-offset-1 col-lg-10 col-md-offset-0 col-md-12 col-sm-offset-0 col-sm-12 col-xs-offset-0 col-xs-12 list-row roles">
        <div class="col-lg-2 col-md-1 col-sm-2 col-xs-3 col">
            <div class="img-container">
                <img class="img-responsive" src="{{ $gallery_item->getImagePath() }}">
            </div>
        </div>
        <div class="col-lg-2 col-md-1 col-sm-2 col-xs-3 col">
            <div class="label">شناسه</div>
            <div>{{$gallery_item->id}}#</div>
        </div>
        <div class="col-lg-3 col-md-2 col-sm-2 col-xs-6 col">
            <div class="label">شناساگر گالری</div>
            <div>{{$gallery_item->gallery->identifier}}</div>
        </div>
        <div class="col-lg-1 col-md-1 col-sm-2 col-xs-3 col">
            <div class="label">وضعیت</div>
            <div>@if($gallery_item->is_active) فعال  @else غیرفعال @endif</div>
        </div>
        <div class="col-lg-3 col-md-2 col-sm-4 col-xs-12 col">
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
@endforeach
