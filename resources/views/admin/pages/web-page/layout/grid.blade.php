@foreach($webPages as $webPage)
    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 grid-item users">
        <div class="item-container">
            <div class="col-lg-2 col-md-3 col-sm-3 col-xs-3 col">
                <div class="img-container">
                    <img class="img-responsive" src="{{ ImageService::getImage($webPage, 'thumb') }}">
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-2 col-xs-5 col">
                <div class="label">شناسه</div>
                <div>{{$webPage->id}}#</div>
            </div>
            <div class="col-lg-7 col-md-9 col-sm-6 col-xs-6 col">
                <div class="label">نام دایرکتوری مربوط</div>
                <div>{{$webPage->directory->title}}</div>
            </div>
            <div class="col-lg-10 col-md-9 col-sm-9 col-xs-6 col">
                <div class="label">تاریخ ساخت</div>
                <div>{{TimeService::getDateFrom($webPage->created_at)}}</div>
            </div>
            <div class="col-lg-5 col-md-6 col-sm-9 col-xs-6 col">
                <div class="label">نام بلید</div>
                <div>{{$webPage->blade_name}}</div>
            </div>
            <div class="col-lg-5 col-md-6 col-sm-12 col-xs-6 col col-action">
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
    </div>
@endforeach