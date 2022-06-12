@extends('admin.form_layout.col_8')

@section('extra_style')
    <link rel="stylesheet" type="text/css" href="/admin_dashboard/vendor/select2/dist/css/select2.min.css">
@endsection

@section('bread_crumb')
    <li><a href="{{route('admin.directory.index')}}">پوشه ها</a></li>
    @foreach($directory->getParentDirectories() as $parent_directory)
        <li><a href="{{route('admin.directory.edit', $parent_directory)}}">{{$parent_directory->title}}</a></li>
    @endforeach
    <li class="active"><a href="{{route('admin.directory.index')}}">ویرایش پوشه</a></li>

@endsection

@section('form_title')
    ویرایش پوشه
@endsection

@section('form_attributes')
    action="{{route('admin.directory.update', $directory)}}" method="POST" enctype="multipart/form-data"
    form-with-hidden-checkboxes
@endsection

@section('form_body')
    {{ method_field('PUT')}}
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">عنوان</span>
            <input class="form-control input-sm" name="title" value="{{ $directory->title }}">
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">بخش url</span>
            <input class="form-control input-sm" name="url_part" value="{{ $directory->url_part }}">
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">اولویت</span>
            <input class="form-control input-sm" name="priority" type="number" value="{{ $directory->priority | 0 }}">
        </div>
        <hr/>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">نوع محتوا</span>
            <select class="form-control input-sm" name="content_type" act="select-control" disabled>
                @foreach(\App\Models\Directory::getContentTypes() as $type => $content)
                    <option value="{{ $content }}" @if($directory->content_type == $content) selected @endif
                    @if($type == 'blog') data-target-container=".article-type" @endif>
                        @lang('general.directory.type.'.$content)
                    </option>
                @endforeach
            </select>
        </div>
        @if($directory->content_type == \App\Models\Enums\DirectoryType::BLOG)
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12 article-type">
                <span class="label">نوع وبلاگ</span>
                <select class="form-control input-sm" name="data_type" disabled>
                    @foreach(get_article_types() as $type => $content)
                        <option value="{{ $type }}" @if($directory->data_type == $type) selected @endif>
                            {{ $content['trans'] }}
                        </option>
                    @endforeach
                </select>
            </div>
        @elseif($directory->content_type == \App\Models\Enums\DirectoryType::PRODUCT)
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="label">نوع عدم موجودی</span>
                <select class="form-control input-sm" name="inaccessibility_type">
                    @foreach(get_product_inaccessibility_types() as $type => $content)
                        <option value="{{ $type }}" @if($directory->inaccessibility_type == $type) selected @endif>
                            {{ $content['trans'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12 article-type">
                <span class="label">نوع محصول</span>
                <select class="form-control input-sm" name="data_type" disabled>
                    @foreach(get_product_types() as $type => $content)
                        <option value="{{ $type }}" @if($directory->data_type == $type) selected @endif>
                            {{ $content['trans'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="material-switch pull-right">نمایش لندینگ به صورت پیشفرض
                    <input id="force_show_landing" name="force_show_landing" type="checkbox" value="1"
                           @if($directory->force_show_landing) checked @endif/>
                    <label for="force_show_landing"></label>
                    <input id="force_show_landing_hidden" name="force_show_landing" type="hidden" value="0"/>
                </span>
            </div>
        @endif
        <hr/>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="material-switch pull-right">لینک داخلی
            <input id="is_internal_link" name="is_internal_link" type="checkbox" value="1"
                   @if($directory->is_internal_link) checked @endif/>
            <label for="is_internal_link"></label>
            <input id="is_internal_link_hidden" name="is_internal_link" type="hidden" value="0"/>
        </span>
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="material-switch pull-right">قابل دسترس بدون ورود به حساب
            <input id="is_anonymously_accessible" name="is_anonymously_accessible" type="checkbox" value="1"
                   @if($directory->is_anonymously_accessible) checked @endif/>
            <label for="is_anonymously_accessible"></label>
            <input id="is_anonymously_accessible_hidden" name="is_anonymously_accessible" type="hidden" value="0"/>
        </span>
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="material-switch pull-right"> نمایش در منو &nbsp;
                    <input id="show_in_navbar" name="show_in_navbar" type="checkbox" value="1"
                           @if($directory->show_in_navbar) checked @endif/>
                    <label for="show_in_navbar"></label>
                    <input id="show_in_navbar_hidden" name="show_in_navbar" type="hidden" value="0"/>
                </span>
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="material-switch pull-right"> نمایش در اپ موبایل &nbsp;
                    <input id="show_in_app_navbar" name="show_in_app_navbar" type="checkbox" value="1"
                           @if($directory->show_in_app_navbar) checked @endif/>
                    <label for="show_in_app_navbar"></label>
                    <input id="show_in_app_navbar_hidden" name="show_in_app_navbar" type="hidden" value="0"/>
                </span>
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="material-switch pull-right"> نمایش در پاورقی &nbsp;
                    <input id="show_in_footer" name="show_in_footer" type="checkbox" value="1"
                           @if($directory->show_in_footer) checked @endif/>
                    <label for="show_in_footer"></label>
                    <input id="show_in_footer_hidden" name="show_in_footer" type="hidden" value="0"/>
                </span>
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="material-switch pull-right">دارای صفحه وب
            <input id="has_web_page" name="has_web_page" type="checkbox" value="1"
                   @if($directory->has_web_page) checked @endif/>
            <label for="has_web_page"></label>
            <input id="has_web_page_hidden" name="has_web_page" type="hidden" value="0"/>
        </span>
            <a class="btn btn-sm btn-primary pull-left {{!$directory->has_web_page ? 'disabled' : ''}}"
               href="{{$directory->has_web_page ? route('admin.web-page.edit', $directory->webPage) : '#'}}">ویرایش صفحه
                وب
            </a>
        </div>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <div class="text-editor group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12" @roleinput($directory,
        "description")>
            <span class="label">متن کوتاه</span>
            <textarea class="tinymce"
                      name="description">@if($errors->count() > 0)
                    {{ old('description') }}
                @else
                    {{ $directory->description }}
                @endif</textarea>
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <label>تصویر</label>
            @if(!$directory->hasImage())
                (حداقل کیفیت: {{ get_image_min_height('directory') }}*{{ get_image_min_width('directory') }}
                و نسبت: {{ get_image_ratio('directory') }})
                <input class="form-control input-sm" name="image" type="file" multiple="true">
            @else
                <div class="photo-container">
                    <a href="{{ route('admin.directory.remove-image', $directory)  }}"
                       class="btn btn-sm btn-danger btn-remove">x</a>
                    <img src="{{ $directory->getImagePath() }}" style="width: 200px;">
                </div>
            @endif
        </div>
        <div class="input-group filled group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">نقش</span>
            <input
                type="text"
                multiple
                class="tags-multi-select attachable"
                value=""
                data-initial-value="{{ json_encode($directory->systemRoles) }}"
                data-user-option-allowed="false"
                data-url="/admin/system-role"
                data-load-once="true"
                placeholder="نقش مورد نظر خود را انتخاب کنید"
                data-attach="{{route('admin.directory.attach-role', $directory)}}"
                data-detach="{{route('admin.directory.detach-role', $directory)}}"
            />
        </div>
        <div
            class="input-group filled group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12" @roleinput($directory, "badges")>
            <span class="label">نشان</span>
            <input type="text"
                   multiple
                   class="tags-multi-select attachable"
                   value=""
                   data-initial-value='{{ json_encode($directory->badges) }}'
                   data-user-option-allowed="false"
                   data-url="{{route('admin.badge.index')}}"
                   data-load-once="true"
                   placeholder="نشان مورد نظر خود را انتخاب کنید"
                   data-attach="{{route('admin.directory.attach-badge', $directory)}}"
                   data-detach="{{route('admin.directory.detach-badge', $directory)}}"
            />
        </div>
        <hr/>
        @if($directory->content_type == \App\Models\Enums\DirectoryType::PRODUCT)
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <a class="btn btn-sm btn-primary pull-right"
                   href="{{route('admin.directory-location.index') . '?directory_id=' . $directory->id}}">
                    تعیین محدوده جغرافیایی
                </a>
                <a class="btn btn-sm btn-primary pull-right mr-15"
                   href="{{route('admin.directory.special-price.edit', $directory)}}">
                    فروش ویژه گروهی کالاها
                </a>
            </div>

            <hr/>
            <h5>فرم اطلاعات کاربران</h5>
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12 article-type">
                <span class="label">فرم انتخابی برای محصولات این دسته</span>
                <select class="form-control input-sm" name="cmc_id">
                    <?php $customer_meta_categories = get_customer_meta_categories() ?>
                    <option @if(!$directory->hasCustomerMetaCategory()) selected @endif disabled value>
                        بدون فرم
                    </option>
                    @if($directory->hasCustomerMetaCategory() and !in_array($directory->cmc_id, $customer_meta_categories->pluck("id")->toArray()))
                        <option selected value="{{$directory->cmc_id}}">
                            {{$directory->customerMetaCategory->title}} - شخصی سازی شده
                        </option>
                    @endif
                    @foreach($customer_meta_categories as $index => $customer_meta_category)
                        <option value="{{ $customer_meta_category->id }}"
                                @if($directory->cmc_id == $customer_meta_category->id) selected @endif>
                            {{ $customer_meta_category->title }}
                        </option>
                    @endforeach
                </select>
                @if($directory->hasCustomerMetaCategory() and !$directory->hasUniqueCustomerMetaCategory())
                    <p>
                        به دلیل اینکه مالک اصلی این فرم دایرکتوری فعلی نیست، تغییر آن از این قسمت امکان پذیر نمی‌باشد.
                        در صورت نیاز به تغییر این فرم به صورت کلی از بخش مدیریت فرم ها و یا دایرکتوری اصلی اقدام کنید.
                        <br/>
                        در غیراینصورت یا فرم مورد نظر را شخصی سازی کنید یا غرم جدید ایجاد کنید.
                    </p>
                @endif
                <a class="btn btn-sm btn-primary pull-right"
                   href="{{route("admin.customer-meta-category.create")}}" style="margin-left: 15px">ایجاد فرم جدید
                </a>
                <a class="btn btn-sm btn-primary pull-right {{$directory->hasUniqueCustomerMetaCategory() ? "" : "disabled"}}"
                   href="{{$directory->hasUniqueCustomerMetaCategory() ? route("admin.customer-meta-category.edit", $directory->customerMetaCategory) : "#"}}"
                   style="margin-left: 15px">تغییر فرم جاری
                </a>
                <a class="btn btn-sm btn-primary pull-right virt-form {{($directory->hasCustomerMetaCategory() and !$directory->hasUniqueCustomerMetaCategory()) ? "" : "disabled"}}"
                   data-action="{{ route('admin.customer-meta-category.clone', $directory)}}"
                   style="margin-left: 15px" data-method="PUT" confirm>شخصی
                    سازی فرم برای این دسته بندی
                </a>
            </div>

            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="label">تنظیم متن اعلان محصولات این شاخه</span>
                <input class="form-control input-sm" name="notice"
                       placeholder="این متن در تمام محصولات این دسته ثبت خواهد شد"
                       value="{{$directory->notice}}">
            </div>

        @endif
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
@endsection
