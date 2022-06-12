@foreach($rates as $rate)
    @if(isset($rate->object) and $rate->object != null)
        <div
            class="col-lg-offset-1 col-lg-10 col-md-offset-0 col-md-12 col-sm-offset-0 col-sm-12 col-xs-offset-0 col-xs-12 list-row roles @if(!$rate->is_reviewed) disabled @endif">
            <div class="col-lg-1 col-md-2 col-sm-2 col-xs-4 col">
                <div class="img-container">
                    <img class="img-responsive" src="{{ ImageService::getImage($rate->object) }}">
                </div>
            </div>
            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-4 col">
                <div class="label">شناسه</div>
                <div>{{$rate->id}}#</div>
            </div>
            <div class="col-lg-3 col-md-2 col-sm-1 col-xs-4 col">
                <div class="label">کاربر</div>
                <div>{{$rate->customerUser->user->full_name}}</div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-7 col">
                <div class="label">نام</div>
                <div>{{$rate->object->getTitle()}}</div>
            </div>
            <div class="col-lg-2 col-md-1 col-sm-2 col-xs-2 col">
                <div class="label">پیام</div>
                <div>@if($rate->comment) {{substr($rate->comment , 0,50) . ' ... '}} @else --- @endif</div>
            </div>
            <div class="col-lg-1 col-md-2 col-sm-2 col-xs-3 col">
                <div class="label">عملیات</div>
                <div class="actions-container">
                    @if($rate->needs_review)
                        <a class="btn btn-sm btn-success virt-form"
                           data-action="{{ route('admin.review.set-as-checked', $rate) }}"
                           data-method="PUT" confirm>
                            <i class="fa fa-check"></i>
                        </a>
                    @endif
                    <a class="btn btn-sm btn-primary" href="{{route('admin.rate.edit', $rate)}}">
                        <i class="fa fa-edit"></i>
                    </a>
                <!--a class="btn btn-sm btn-success" href="{{route('admin.rate.show', $rate)}}">
                        <i class="fa fa-eye"></i>
                    </a-->
                </div>
            </div>
        </div>
    @endif
@endforeach
