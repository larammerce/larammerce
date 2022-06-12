@foreach($districts as $district)
    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 grid-item roles">
        <div class="item-container">
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 col">
                <div class="img-container">
                    <img class="img-responsive" src="/admin_dashboard/images/No_image.jpg.png">
                </div>
            </div>
            <div class="col-lg-4 col-md-3 col-sm-3 col-xs-3 col">
                <div class="label">شناسه</div>
                <div>{{$district->id}}#</div>
            </div>
            <div class="col-lg-5 col-md-3 col-sm-4 col-xs-6 col">
                <div class="label">نام منطقه</div>
                <div>{{$district->name}}</div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-6 col">
                <div class="label">نام شهر</div>
                <div>{{$district->city->name}}</div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-6 col">
                <div class="label">نام استان</div>
                <div>{{$district->city->state->name}}</div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col col-action">
                <div class="label">عملیات</div>
                <div class="actions-container">
                    <div class="col-lg-5 col-md-12 col-xs-12">
                        <a class="btn btn-sm btn-primary" href="{{route('admin.city.edit', $district)}}">
                            <i class="fa fa-pencil"></i>
                        </a>
                        <a class="btn btn-sm btn-danger virt-form"
                           data-action="{{ route('admin.district.destroy', $district) }}"
                           data-method="DELETE" confirm>
                            <i class="fa fa-trash"></i>
                        </a>
                        @if($district->has_district)
                            <a class="btn btn-sm btn-success" href="{{route('admin.district.show', $district)}}">
                                <i class="fa fa-eye"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach