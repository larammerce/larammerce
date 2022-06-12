@extends('admin.form_layout.col_4')
@section('extra_style')
    <link rel="stylesheet" type="text/css" href="/admin_dashboard/vendor/select2/dist/css/select2.min.css">
@endsection
@section('bread_crumb')
    <li><a href="{{route('admin.discount-group.index')}}">پلن های تخفیف</a></li>
    <li class="active">
        <a href="{{route('admin.discount-group.show', $discount_group)}}">{{$discount_group->title}}</a>
    <li class="active">
        <a href="{{route('admin.discount-card.index')}}?discount_group_id={{$discount_group->id}}">
            اضافه کردن کارت جدید
        </a>
    </li>
@endsection

@section('form_title')اضافه کردن گروه تخفیف@endsection

@section('form_attributes') action="{{route('admin.discount-card.store')}}" method="POST" @endsection

@section('form_body')
    <input type="hidden" name="discount_group_id" value="{{$discount_group->id}}">
    @if($discount_group->has_directory)
        <div class="tag-manager filled input-group form-group col-lg-12 col-sm-12 col-md-12 col-xs-12" )>
            <span class="label" style="top: -17px">دسته بندی ها</span>
            <select class="form-control directory-list" name="directories[]" style="width: 100%;margin-top: 15px"
                    data-placeholder="انتخاب دسته بندی"
                    multiple id="directory-list">
                @foreach($directories as $dir)
                    <option
                        value="{{$dir->id}}">{{join(" > ", $dir->getParentDirectories()->pluck("title")->toArray())}}</option>
                @endforeach
            </select>
        </div>
    @endif
    @if($discount_group->is_event and ! $discount_group->is_assigned)
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">کد مورد نظر </span>
            <input class="form-control input-sm" name="code" value="{{old('code')}}">
        </div>
    @endif
    @if($discount_group->is_assigned)
        <div class="filled tag-manager input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">مشتریان</span>
            <textarea act="tag" class="form-control input-sm attachable" name="users"
                      data-query="{{ route('admin.api.v1.user.query') }}?only_customer_users=true"
                      data-field-name="users"
                      data-container=".form-layout-container"></textarea>
            <ul act="tag-data">
            </ul>
        </div>
    @elseif(!$discount_group->is_event)
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">تعداد</span>
            <input class="form-control input-sm" name="count" value="{{old('count')}}">
        </div>
    @endif

@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
@endsection
