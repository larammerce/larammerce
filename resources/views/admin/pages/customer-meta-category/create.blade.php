@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.customer-meta-category.index')}}">متادیتای کاربران</a></li>
    <li class="active"><a href="{{route('admin.customer-meta-category.create')}}">اضافه کردن</a></li>
@endsection

@section('form_title')اضافه کردن متادیتای کاربران@endsection

@section('form_attributes') action="{{route('admin.customer-meta-category.store')}}" method="POST"
form-with-hidden-checkboxes
@endsection

@section('form_body')

    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">عنوان</span>
        <input class="form-control input-sm" name="title" value="{{ old("title") }}" maxlength="62">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="material-switch pull-right">نیاز به تایید مدیر بعد از پر کردن فرم وجود دارد ؟
            <input id="needs_admin_confirmation" name="needs_admin_confirmation" type="checkbox" value="1"
                   @if(old("needs_admin_confirmation")) checked @endif/>
            <label for="needs_admin_confirmation"></label>
            <input id="needs_admin_confirmation_hidden" name="needs_admin_confirmation" type="hidden" value="0"/>
        </span>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نام بلید</span>
        <select class="form-control" name="form_blade_name">
            @foreach(get_template_views() as $view)
                <option value="{{$view}}" @if(old("form_blade_name") === $view) selected @endif>{{$view}}</option>
            @endforeach
        </select>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
@endsection
