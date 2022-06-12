@foreach($colors as $color)
    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 grid-item roless">
        <div class="item-container">
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 col">
                <div class="img-container" style="background-color: {{$color->hex_code}}"></div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 col">
                <div class="label">شناسه</div>
                <div>{{$color->id}}#</div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 col">
                <div class="label">نام</div>
                <div>{{$color->name}}</div>
            </div>
            <div class="col-lg-5 col-md-3 col-sm-4 col-xs-6 col">
                <div class="label">کد هگز</div>
                <div>{{$color->hex_code}}</div>
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12 col col-action">
                <div class="label">عملیات</div>
                <div class="actions-container pull-left">
                    <div class="col-lg-12 col-md-12 col-xs-12">
                        <a class="btn btn-sm btn-primary" href="{{route('admin.color.edit', $color)}}">
                            <i class="fa fa-pencil"></i>
                        </a>
                        <a class="btn btn-sm btn-danger virt-form"
                           data-action="{{ route('admin.color.destroy', $color) }}"
                           data-method="DELETE" confirm>
                            <i class="fa fa-trash"></i>
                        </a>
                        <a class="btn btn-sm btn-success" href="{{route('admin.color.show', $color)}}">
                            <i class="fa fa-eye"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach