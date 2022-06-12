@foreach($reviews as $review)
    @if(isset($review->reviewable) and $review->reviewable != null)
        <div
            class="col-lg-offset-1 col-lg-10 col-md-offset-0 col-md-12 col-sm-offset-0 col-sm-12 col-xs-offset-0 col-xs-12 list-row roles @if($review->needs_review) disabled @endif">
            <div class="col-lg-1 col-md-2 col-sm-2 col-xs-4 col">
                <div class="img-container">
                    <img class="img-responsive" src="{{ ImageService::getImage($review->reviewable) }}">
                </div>
            </div>
            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-4 col">
                <div class="label">شناسه</div>
                <div>{{$review->id}}#</div>
            </div>
            <div class="col-lg-3 col-md-2 col-sm-1 col-xs-4 col">
                <div class="label">نوع محتوا</div>
                <div>{{$review->reviewable->getType()}} ({{$review->reviewable->id}})</div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-7 col">
                <div class="label">نام</div>
                <div>{{$review->reviewable->getTitle()}}</div>
            </div>
            <div class="col-lg-2 col-md-1 col-sm-2 col-xs-2 col">
                <div class="label">تعداد ویرایش‌ها</div>
                <div>{{$review->edit_count}}</div>
            </div>
            <div class="col-lg-1 col-md-2 col-sm-2 col-xs-3 col">
                <div class="label">عملیات</div>
                <div class="actions-container">
                    @if($review->needs_review)
                        <a class="btn btn-sm btn-success virt-form"
                           data-action="{{ route('admin.review.set-as-checked', $review) }}"
                           data-method="PUT" confirm>
                            <i class="fa fa-check"></i>
                        </a>
                    @endif
                    <a class="btn btn-sm btn-info" href="{{$review->reviewable->getAdminEditUrl()}}">
                        <i class="fa fa-eye"></i>
                    </a>
                </div>
            </div>
        </div>
    @endif
@endforeach
