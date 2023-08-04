@foreach($discount_groups as $discount_group)
    <div
        class="col-lg-offset-1 col-lg-10 col-md-offset-0 col-md-12 col-sm-offset-0 col-sm-12 col-xs-offset-0 col-xs-12 list-row discount-groups @if(!$discount_group->is_active) disabled @endif">
        <div class="col-lg-1 col-md-3 col-sm-6 col-xs-3 col">
            <div class="label">شناسه</div>
            <div>{{$discount_group->id}}#</div>
        </div>
        <div class="col-lg-2 col-md-3 col-sm-6 col-xs-3 col">
            <div class="label">عنوان</div>
            <div>{{$discount_group->title}}</div>
        </div>
        <div class="col-lg-2 col-md-3 col-sm-6 col-xs-3 col">
            <div class="label">کد پیشین</div>
            <div>{{$discount_group->prefix}}</div>
        </div>
        <div class="col-lg-2 col-md-3 col-sm-6 col-xs-3 col">
            <div class="label">کد پسین</div>
            <div>{{$discount_group->postfix}}</div>
        </div>
        <div class="col-lg-2 col-md-3 col-sm-6 col-xs-3 col">
            <div class="label">انقضا</div>
            <div>{{$discount_group->has_expiration ? JDate::forge($discount_group->expiration_date)->format("Y/m/d") : "-"}}</div>
        </div>
        <div class="col-lg-3 col-md-9 col-sm-6 col-xs-12 col">
            <div class="label">عملیات</div>
            <div class="actions-container">
                <a class="btn btn-sm btn-primary" href="{{ route('admin.discount-group.edit', $discount_group) }}">
                    <i class="fa fa-pencil"></i>
                </a>
                <a class="btn btn-sm @if($discount_group->is_active) btn-danger @else btn-info @endif virt-form"
                   data-action="{{ route('admin.discount-group.destroy', $discount_group) }}"
                   data-method="DELETE" confirm>
                    @if($discount_group->is_active)
                        <i class="fa fa-stop"></i>
                    @else
                        <i class="fa fa-play"></i>
                    @endif
                </a>
                <a class="btn btn-sm btn-success" href="{{ route('admin.discount-group.show', $discount_group) }}">
                    <i class="fa fa-ticket"></i>
                </a>
                <a class="btn btn-sm btn-success"
                   href="{{ route('admin.discount-group.product-filter.index', $discount_group) }}">
                    <i class="fa fa-cubes"></i>
                </a>

                @if((!$discount_group->is_active)AND(((Carbon\Carbon::now())->diffInDays($discount_group->updated_at))>7))
                    <a class="btn btn-sm btn-danger virt-form" data-action="{{ route('admin.discount-group.soft-delete', $discount_group) }}" data-method="DELETE" confirm>
                        <i class="fa fa-remove"></i>
                    </a>
                @endif
            </div>
        </div>
    </div>
@endforeach
