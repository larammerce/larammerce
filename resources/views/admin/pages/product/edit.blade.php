@extends('admin.form_layout.col_12')

@section('extra_style')

@endsection
@section('bread_crumb')
    <li><a href="{{route('admin.product.index')}}">محصولات</a></li>
    <li class="active"><a href="{{route('admin.product.edit', $product)}}">ویرایش محصول</a></li>

@endsection

@section('form_title')
    ویرایش محصول
@endsection

@section('form_attributes')
    action="{{route('admin.product.update', $product)}}" method="POST" form-with-hidden-checkboxes
@endsection

@section('form_body')
    <script>window.PAGE_ID = "admin.pages.product.edit";</script>
    <input name="id" type="hidden" value="{{ $product->id }}">
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <h4>اطلاعات عمومی</h4>
        {{ method_field('PUT') }}
        <div class="input-group with-icon group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">نام پوشه مربوطه</span>
            <i class="fa fa-folder"></i>
            <input class="form-control input-sm" name="directory" value="{{ $product->directory->title }}" disabled>
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <span class="label">نام قالب محصول</span>
            <select class="form-control input-sm" name="p_structure_id">
                @foreach($p_structures as $p_structure)
                    <option value="{{ $p_structure->id }}"
                            @if($product->p_structure_id == $p_structure->id) selected @endif>
                        {{ $p_structure->title }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12" @roleinput($product,
        "title")>
        <span class="label">عنوان</span>
        <input class="form-control input-sm" name="title" value="{{ $product->title }}" maxlength="100">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">متن اعلان محصول</span>
        <input class="form-control input-sm" name="notice"
               value="{{$product->notice}}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12"
         @roleinput($product, "priority")>
    <span class="label">اولویت</span>
    <input class="form-control input-sm" name="priority" value="{{ $product->priority }}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12"
         style="margin-bottom: 40px;" @roleinput($product, "is_visible")>
    <span class="material-switch pull-right">در فروشگاه نمایش داده شود ؟ &nbsp
                    <input id="is_visible" name="is_visible" type="checkbox" value="1"
                           @if($product->is_visible) checked @endif/>
                    <label for="is_visible"></label>
                <input id="is_visible_hidden" name="is_visible" type="hidden" value="0"/>
                </span>
    </div>

    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12"
         style="margin-bottom: 40px;" @roleinput($product, "is_important")>
    <span class="material-switch pull-right">بین محصولات مهم قرار گیرد ؟ &nbsp
                <input id="is_important" name="is_important" type="checkbox" value="1"
                       @if($product->is_important) checked @endif/>
                <label for="is_important"></label>
            <input id="is_important_hidden" name="is_important" type="hidden" value="0"/>
            </span>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12"
         @roleinput($product, "gallery")>
    <span class="label">گالری تصاویر</span>
    <div class="my-dropzone form-control col-lg-12 col-md-12 col-sm-12 col-xs-12"
         dz-action="{{ route('admin.product-image.store') }}"
         dz-caption="{{ route('admin.product-image.update', -1)}}"
         dz-remove="{{ route('admin.product-image.destroy', -1) }}"
         dz-main="{{ route('admin.product-image.set-as-main-image', -1) }}"
         dz-secondary="{{ route('admin.product-image.set-as-secondary-image', -1) }}"
         dz-id="product-gallery">
        <ul class="form-data hidden">
            <li name="product_id" value="{{$product->id}}"></li>
        </ul>
        <div class="dz-message needsclick">
            تصاویر خود را در این قسمت آپلود کنید.<br>
            <span class="note needsclick">[با کلیک بر روی این قسمت و یا کشیدن فایل مورد نظر در این کادر می توانید آن را آپلود کنید]</span>
        </div>
        <ul class="existing-files">
            {{-- @name : real name of the file --}}
            {{-- @size : size of file on bytes --}}
            {{-- @url : the url of uploaded file on server --}}
            @foreach($product->images()->orderBy("priority", "ASC")->get() as $image)
                <li class="existing-file"
                    file-id="{{ $image->id }}"
                    file-name="{{ $image->real_name }}"
                    file-size="{{ File::exists(public_path().$image->getImagePath()) ? filesize(public_path().$image->getImagePath()) : 0}}"
                    file-url="{{ ImageService::getImage($image) }}"
                    file-caption="{{ $image->caption }}"
                    file-link="{{ $image->link }}"
                    file-priority="{{ $image->priority }}"
                    @if($image->is_main) is-main @endif
                    @if($image->is_secondary) is-secondary @endif></li>
            @endforeach
        </ul>
    </div>
    <span>*             (حداقل کیفیت: {{ get_image_min_height('product') }}*{{ get_image_min_width('product') }}
                و نسبت: {{ get_image_ratio('product') }})
            </span>
    </div>
    <div class="input-group filled group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12" @roleinput($product, "colors")>
    <div class="input-group group-sm col-lg-10 col-md-10 col-sm-12 col-xs-12 pull-right">
        <span class="label">رنگ</span>
        <input type="text"
               multiple
               class="tags-multi-select attachable"
               value=""
               data-initial-value='{{ json_encode($product->colors) }}'
               data-user-option-allowed="false"
               data-url="{{route('admin.color.index')}}"
               data-load-once="true"
               placeholder="رنگ مورد نظر خود را انتخاب کنید"
               data-attach="{{route('admin.product.attach-color', $product)}}"
               data-detach="{{route('admin.product.detach-color', $product)}}"
        />
    </div>
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 actions pull-right">
        <a class="btn btn-sm btn-primary pull-left"
           href="{{route('admin.color.index')}}">
            <i class="fa fa-list"></i>
        </a>
        <a class="btn btn-sm btn-success pull-left"
           href="{{route('admin.color.create')}}">
            <i class="fa fa-plus"></i>
        </a>
    </div>
    </div>
    <hr>
    <div class="input-group col-lg-12 col-sm-12 col-md-12 col-xs-12 no-padding" @roleinput($product, "badges")>
    <div class="input-group group-sm col-lg-10 col-md-10 col-sm-12 col-xs-12 pull-right">
        <span class="label">نشان ها</span>
        <input type="text"
               multiple
               class="tags-multi-select attachable"
               value=""
               data-initial-value='{{ json_encode($product->badges) }}'
               data-user-option-allowed="false"
               data-url="{{route('admin.badge.index')}}"
               data-load-once="true"
               placeholder="نشان مورد نظر خود را انتخاب کنید"
               data-attach="{{route('admin.product.attach-badge', $product)}}"
               data-detach="{{route('admin.product.detach-badge', $product)}}"
        />
    </div>
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 actions pull-right">
        <a class="btn btn-sm btn-primary pull-left"
           href="{{route('admin.badge.index')}}">
            <i class="fa fa-list"></i>
        </a>
        <a class="btn btn-sm btn-success pull-left"
           href="{{route('admin.badge.create')}}">
            <i class="fa fa-plus"></i>
        </a>
    </div>
    </div>
    <hr>
    <h4>گروه بندی کالا</h4>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12" @roleinput($product, "code")>
    <span class="label">کد کالا</span>
    <input class="form-control input-sm" name="code" value="{{ $product->code }}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12" @roleinput($product, "model_id")>
    <span class="label">شناسه گروه کالاها</span>
    <input class="form-control input-sm" name="model_id" value="{{ $product->model_id }}">
    </div>
    <hr/>
    <h4>لوازم جانبی</h4>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12"
         style="margin-bottom: 40px;" @roleinput($product, "is_accessory")>
    <span class="material-switch pull-right">این محصول لوازم جانبی است ؟ &nbsp
                <input id="is_accessory" name="is_accessory" type="checkbox" value="1"
                       @if($product->is_accessory) checked @endif/>
                <label for="is_accessory"></label>
            <input id="is_accessory_hidden" name="is_accessory" type="hidden" value="0"/>
            </span>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12" @roleinput($product, "accessory_for")>
    <span class="label">در لوازم جانبی بودن، مربوط به کدام گروه محصولات است؟</span>
    <input class="form-control input-sm" name="accessory_for" value="{{ $product->accessory_for }}">
    </div>
    @if($product->is_package)
        <hr/>
        <h4>پکیج محصولات</h4>
        <div lass="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12"
             style="margin-bottom: 10px;">
            <h6>پکیج محصولات</h6>
            <a href="{{route('admin.product-package.edit', $product)}}"
               class="btn btn-sm btn-primary">
                ویرایش آیتم ها
            </a>
        </div>
        <br>
    @endif
    <hr>
    <h4>مدیریت انبار و امور مالی</h4>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12"
         style="margin-bottom: 40px;" @roleinput($product, "has_discount")>
    <span class="material-switch pull-right">با تخفیف نمایش داده شود ؟ &nbsp
                <input id="has_discount" name="has_discount" type="checkbox" value="1"
                       @if($product->has_discount) checked @endif/>
                <label for="has_discount"></label>
            <input id="has_discount_hidden" name="has_discount" type="hidden" value="0"/>
            </span>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12"
         style="margin-bottom: 40px;" @roleinput($product, "is_discountable")>
    <span class="material-switch pull-right">آیا امکان اعمال تخفیف روی این محصول وجود دارد ؟ &nbsp
                <input id="is_discountable" name="is_discountable" type="checkbox" value="1"
                       @if($product->is_discountable) checked @endif/>
                <label for="is_discountable"></label>
            <input id="is_discountable_hidden" name="is_discountable" type="hidden" value="0"/>
            </span>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12"
         @roleinput($product, "max_purchase_count")>
    <span class="label">حداکثر تعداد سفارش</span>
    <input class="form-control input-sm" name="max_purchase_count" value="{{ $product->max_purchase_count }}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12"
         @roleinput($product, "min_purchase_count")>
    <span class="label">حداقل تعداد سفارش</span>
    <input class="form-control input-sm" name="min_purchase_count" value="{{ $product->min_purchase_count }}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12"
         @roleinput($product, "min_allowed_count")>
    <span class="label">حد اقل موجودی مجاز</span>
    <input class="form-control input-sm" name="min_allowed_count" value="{{ $product->min_allowed_count }}">
    </div>
    @if(is_manual_stock())
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12"
             @roleinput($product, "count")>
        <span class="label">تعداد موجود</span>
        <input class="form-control input-sm" @if($product->is_package) disabled @endif
        name="count" value="{{ $product->count }}">
        </div>
        <div class="input-group col-lg-12 col-sm-12 col-md-12 col-xs-12 no-padding"
             @roleinput($product, "latest_price")>
        <div
            class="input-group with-icon with-unit group-sm col-lg-10 col-md-10 col-sm-12 col-xs-12 pull-right">
            <span class="label">آخرین قیمت</span>
            <i class="fa fa-dollar"></i>
            <input class="form-control input-sm"
                   name="latest_price" value="{{ $product->latest_price }}"
                   act="price">
            <span class="unit">تومان</span>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 actions pull-right">
            <a class="btn btn-sm btn-primary pull-left"
               href="{{route('admin.product-price.index')}}?product_id={{ $product->id }}">
                <i class="fa fa-line-chart"></i>
            </a>
            <a class="btn btn-sm btn-success pull-left"
               href="{{route('admin.product-price.create')}}?product_id={{ $product->id }}">
                <i class="fa fa-plus"></i>
            </a>
        </div>
        </div>
    @endif

    <div class="input-group col-lg-12 col-sm-12 col-md-12 col-xs-12 no-padding"
         @roleinput($product, "latest_special_price")>
    <div class="input-group with-icon with-unit group-sm col-lg-10 col-md-10 col-sm-12 col-xs-12 pull-right">
        <span class="label">آخرین قیمت ویژه</span>
        <i class="fa fa-dollar"></i>
        <input class="form-control input-sm"
               name="latest_special_price" value="{{ $product->latest_special_price }}"
               act="price">
        <span class="unit">تومان</span>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 actions pull-right">
        <a class="btn btn-sm btn-primary pull-left"
           href="{{route('admin.product-special-price.index')}}?product_id={{ $product->id }}">
            <i class="fa fa-line-chart"></i>
        </a>
        <a class="btn btn-sm btn-success pull-left"
           href="{{route('admin.product-special-price.create')}}?product_id={{ $product->id }}">
            <i class="fa fa-plus"></i>
        </a>
    </div>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نوع عدم موجودی</span>
        <select class="form-control input-sm" name="inaccessibility_type" @if($product != null)  @endif>
            @foreach(get_product_inaccessibility_types() as $type => $content)
                <option value="{{ $type }}" @if(old('inaccessibility_type') == $type or
                                ($product != null and $product->inaccessibility_type == $type)) selected @endif>
                    {{ $content['trans'] }}
                </option>
            @endforeach
        </select>
    </div>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <h4>مدیریت سئو</h4>
        <div class="filled tag-manager input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12"
             @roleinput($product,
        "tags")>
        <span class="label">تگ ها</span>
        <textarea act="tag" class="form-control input-sm attachable" name="tags"
                  data-save="{{ route('admin.tag.store') }}"
                  data-query="{{ route('admin.tag.query') }}"
                  data-attach="{{ route('admin.product.attach-tag', $product) }}"
                  data-detach="{{ route('admin.product.detach-tag', $product) }}"
                  data-field-name="name"
                  data-open-tag="{{ route('admin.tag.edit', -1) }}"
                  data-container=".form-layout-container"></textarea>
        <ul act="tag-data">
            @foreach($product->tags as $tag)
                <li data-id="{{$tag->id}}" data-text="{{$tag->name}}"></li>
            @endforeach
        </ul>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12" @roleinput($product, 'seo_keywords')>
    <span class="label">تگ‌های سئو</span>
    <input class="form-control input-sm" name="seo_keywords" value="{{ $product->seo_keywords }}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12" @roleinput($product, 'seo_title')>
    <span class="label">عنوان سئو</span>
    <input class="form-control input-sm" name="seo_title" value="{{ $product->seo_title }}">
    </div>
    <div
        class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12" @roleinput($product, 'seo_description')>
    <span class="label">توضیحات سئو</span>
    <textarea class="form-control input-sm"
              name="seo_description">{{ $product->seo_description }}</textarea>
    </div>
    <hr>
    <h4>ویژگی‌های محصول</h4>
    @foreach($product->productStructure->attributeKeys as $attributeKey)
        <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12 filled"
             @roleinput($product, "attributes") style="padding-left: 50px;">
        <span class="label">{{ $attributeKey->title }} @if($attributeKey->is_sortable)
                (قابل مرتب
                سازی)
            @endif</span>
        <select multiple
                class="tags-multi-select attachable"
                data-user-option-allowed="true"
                data-load-once="true"
                data-attr-text="@if($product->attributes_content != null){{json_decode($product->attributes_content)}}@else {{1}} @endif"
                placeholder="گزینه های مورد نظر خود را انتخاب کنید"
                data-attach="{{route('admin.product.attach-attribute', [$product, $attributeKey])}}"
                data-detach="{{route('admin.product.detach-attribute', [$product, $attributeKey])}}"
                data-parent-name="{{ $attributeKey->title }}">
            @foreach($attributeKey->values as $value)
                <option data-json='{{json_encode($value)}}' value="{{ $value->id }}"
                        @if($product->hasValue($value)) selected @endif>
                    {{ $value->name }}
                </option>
            @endforeach
        </select>
        <a class="btn btn-sm btn-primary"
           style="position: absolute;top: 10px; left: 10px ;width: 30px;height: 30px;border-radius: 15px;"
           href="{{route('admin.p-structure-attr-key.edit', $attributeKey)}}" target="_blank">
            <i class="fa fa-pencil" style="display: inline-block;margin-right: -1px;"></i>
        </a>
        </div>

    @endforeach
    <div id="sortable" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 extra-properties ui-sortable"
         @roleinput($product, "extra_properties")>
    <h1 class="title">ویژگی های اضافه</h1>
    <ul act="extra-properties-data" class="hidden">
        @foreach($product->getExtraProperties() as $extraProperty)
            <li data-key="{{isset($extraProperty->key) ? $extraProperty->key : 'NON'}}"
                data-value="{{isset($extraProperty->value) ? $extraProperty->value : 'NON'}}"
                data-type="{{isset($extraProperty->type) ? $extraProperty->type : 'NON'}}"
            ></li>
        @endforeach
    </ul>
    </div>
    <div class="text-editor col-lg-12 col-sm-12 col-md-12 col-xs-12" @roleinput($product, "description")>
    <br>
    <label for="description">توضیحات</label>
    <textarea class="tinymce"
              name="description">@if($errors->count() > 0)
            {{ old('description') }}
        @else
            {{ $product->description }}
        @endif</textarea>
    </div>
    </div>

@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
@endsection

@section('outer_content')
    @include('admin.templates.modals.add_caption')
@stop
