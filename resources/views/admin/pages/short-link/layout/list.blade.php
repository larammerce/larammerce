@foreach($short_links as $short_link)
    <div
        class="col-lg-offset-1 col-lg-10 col-md-offset-0 col-md-12 col-sm-offset-0 col-sm-12 col-xs-offset-0 col-xs-12 list-row short-links">
        <div class="col-lg-2 col-md-1 col-sm-1 col-xs-6 col">
            <div class="label">شناسه</div>
            <div>{{$short_link->id}}#</div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 col">
            <div class="label">لینک اصلی</div>
            <div>{{$short_link->link}}</div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 col">
            <div class="label">لینک کوتاه شده</div>
            <div>{{"https://".$short_domain.'/'.$short_link->shortened_link}}</div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 col">
            <div class="label">عملیات</div>
            <div class="actions-container">
                <a class="btn btn-sm btn-primary" href="{{ route('admin.short-link.statistics', $short_link) }}">
                    <i class="fa fa-area-chart"></i>
                </a>
                <a class="btn btn-sm btn-primary" href="{{ route('admin.short-link.edit', $short_link) }}">
                    <i class="fa fa-pencil"></i>
                </a>
                <a class="btn btn-sm btn-danger virt-form"
                   data-action="{{ route('admin.short-link.destroy', $short_link) }}"
                   data-method="DELETE" confirm>
                    <i class="fa fa-trash"></i>
                </a>
            </div>
        </div>
    </div>
@endforeach
