@foreach($webPages as $webPage)
    <div
        class="col-lg-offset-1 col-lg-10 col-md-offset-0 col-md-12 col-sm-offset-0 col-sm-12 col-xs-offset-0 col-xs-12 list-row users ">
        <div class="col-lg-1 col-md-2 col-sm-3 col-xs-3 col">
            <div class="img-container">
                <img class="img-responsive" src="{{ ImageService::getImage($webPage, 'thumb') }}">
            </div>
        </div>
        <div class="col-lg-1 col-md-2 col-sm-3 col-xs-3 col">
            <div class="label">شناسه</div>
            <div>{{$webPage->id}}#</div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6 col">
            <div class="label">نام دایرکتوری مربوط</div>
            <div>{{$webPage->directory->title}}</div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 col">
            <div class="label">نام بلید</div>
            <div>{{$webPage->blade_name}}</div>
        </div>
        <div class="col-lg-1 col-md-4 col-sm-2 col-xs-6 col">
            <div class="label">تاریخ ساخت</div>
            <div>{{TimeService::getDateFrom($webPage->created_at)}}</div>
        </div>
        <div class="col-lg-3 col-md-8 col-sm-6 col-xs-12 col">
            <div class="label">عملیات</div>
            <div class="actions-container">
                <a class="btn btn-sm btn-primary" href="{{route('admin.web-page.edit', $webPage)}}">
                    <i class="fa fa-pencil"></i>
                </a>
                <a class="btn btn-sm btn-danger virt-form"
                   data-action="{{ route('admin.web-page.destroy', $webPage) }}"
                   data-method="DELETE" confirm>
                    <i class="fa fa-trash"></i>
                </a>
                <a class="btn btn-sm btn-success" href="{{route('admin.web-page.show', $webPage)}}">
                    <i class="fa fa-eye"></i>
                </a>
            </div>
        </div>
    </div>
@endforeach
