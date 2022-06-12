@foreach($reviews as $review)
    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 grid-item roless">
        <div class="item-container @if($review->needs_review) disabled @endif">
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 col">
                <div class="img-container">
                    <img class="img-responsive" src="{{ ImageService::getImage($review->reviewable) }}">
                </div>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-4 col-xs-3 col">
                <div class="label">شناسه</div>
                <div>{{$review->id}}#</div>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-5 col-xs-6 col">
                <div class="label">نوع محتوا</div>
                <div>{{$review->reviewable->getType()}}</div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-9 col-xs-9 col">
                <div class="label">نام</div>
                <div>{{$review->reviewable->getTitle()}}</div>
            </div>
            <div class="clearfix"></div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 col">
                <div class="label">تعداد ویرایش‌ها</div>
                <div>{{$review->edit_count}}</div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-5 col-xs-5 col col-action">
                <div class="label">عملیات</div>
                <div class="actions-container pull-left">
                    <div class="col-lg-12 col-md-12 col-xs-12">
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
        </div>
    </div>
@endforeach