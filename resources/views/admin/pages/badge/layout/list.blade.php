@foreach($badges as $badge)
    <div
        class="col-lg-offset-1 col-lg-10 col-md-offset-0 col-md-12 col-sm-offset-0 col-sm-12 col-xs-offset-0 col-xs-12 list-row badges">
        <div class="col-lg-1 col-md-2 col-sm-2 col-xs-3 col">
            <div class="img-container">
                @if($badge->hasImage())
                    <img class="img-responsive" src="{{ $badge->getImagePath() }}">
                @else
                    <img class="img-responsive" src="/admin_dashboard/images/No_image.jpg.png">
                @endif

            </div>
        </div>
        <div class="col-lg-2 col-md-1 col-sm-1 col-xs-6 col">
            <div class="label">شناسه</div>
            <div>{{$badge->id}}#</div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 col">
            <div class="label">عنوان</div>
            <div>{{$badge->title}}</div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6 col">
            <div class="label">رنگ</div>
            <div style="background: {{ $badge->color }};height: 70%;width: 100%"></div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6 col">
            <div class="label">آیکون</div>
            <div><a href="#"><i class="{{ $badge->icon }}" style="font-size: 20px"></i></a></div>
        </div>
        <div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 col">
            <div class="label">عملیات</div>
            <div class="actions-container">
                <a class="btn btn-sm btn-primary" href="{{ route('admin.badge.edit', $badge) }}">
                    <i class="fa fa-pencil"></i>
                </a>
                <a class="btn btn-sm btn-danger virt-form"
                   data-action="{{ route('admin.badge.destroy', $badge) }}"
                   data-method="DELETE" confirm>
                    <i class="fa fa-trash"></i>
                </a>
            </div>
        </div>
    </div>
@endforeach
