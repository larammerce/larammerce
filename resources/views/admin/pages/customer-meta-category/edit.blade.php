@extends('admin.form_layout.col_12')

@section('bread_crumb')
    <li><a href="{{route('admin.customer-meta-category.index')}}">متادیتای کاربران</a></li>
    <li class="active"><a href="{{route('admin.customer-meta-category.edit', $customer_meta_category)}}">ویرایش</a></li>
@endsection

@section('form_title')ویرایش متادیتای کاربران@endsection

@section('form_attributes')
    id="customer-meta-category-edit" action="{{route('admin.customer-meta-category.update', $customer_meta_category)}}"
    method="POST" form-with-hidden-checkboxes
@endsection

@section('form_body')
    <script>window.current_page = "admin.pages.customer-meta-category.edit";</script>
    <input name="id" type="hidden" value="{{ $customer_meta_category->id }}">
    {{ method_field('PUT') }}
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">عنوان</span>
            <input class="form-control input-sm" name="title" value="{{ $customer_meta_category->title }}"
                   maxlength="100">
        </div>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="material-switch pull-right">نیاز به تایید مدیر بعد از پر کردن فرم وجود دارد ؟
            <input id="needs_admin_confirmation" name="needs_admin_confirmation" type="checkbox" value="1"
                   @if($customer_meta_category->needs_admin_confirmation) checked @endif/>
            <label for="needs_admin_confirmation"></label>
            <input id="needs_admin_confirmation_hidden" name="needs_admin_confirmation" type="hidden" value="0"/>
        </span>
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">نام بلید</span>
            <select class="form-control" name="form_blade_name">
                @foreach(get_template_views() as $view)
                    <option value="{{$view}}"
                            @if($customer_meta_category->form_blade_name === $view) selected @endif>{{$view}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <h4>فیلد های فرم اطلاعات</h4>
    </div>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="dynamic-form-rows-container"
         data-rows='{{$customer_meta_category->data}}'>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
@endsection

@section('outer_content')
    @include('admin.templates.modals.add_caption')
@stop
