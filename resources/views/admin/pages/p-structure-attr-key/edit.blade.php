@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li><a href="{{route('admin.p-structure-attr-key.index')}}">ويژگی های محصولات</a></li>
    <li class="active"><a href="{{route('admin.p-structure-attr-key.index', $attribute_key)}}">ویرایش ويژگی</a></li>

@endsection

@section('form_title')ویرایش ویژگی محصول@endsection

@section('form_attributes') action="{{route('admin.p-structure-attr-key.update', $attribute_key)}}" method="POST" form-with-hidden-checkboxes @endsection

@section('form_body')

    {{ method_field('PUT') }}
    <input type="hidden" name="id" value="{{ $attribute_key->id }}">
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">عنوان ویژگی</span>
        <input class="form-control input-sm" name="title" value="{{ $attribute_key->title }}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">سطح اولویت</span>
        <input class="form-control input-sm" name="priority" value="{{$attribute_key->priority}}">
    </div>
    <div class="input-group filled group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نوع نمایش</span>
        <select name="show_type" class="form-control input-sm">
            @foreach(PSAttrKeyShowType::toMap() as $key => $value )
                <option value="{{$value}}" @if($attribute_key->show_type == $value) selected @endif>
                    {{trans('general.ps_attr_key_show_type.'.$value)}}
                </option>
            @endforeach
        </select>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12"
         style="margin-bottom: 40px;">
            <span class="material-switch pull-right">آپشن محصولات ؟ &nbsp
                <input id="is_model_option" name="is_model_option" type="checkbox" value="1"
                       @if($attribute_key->is_model_option) checked @endif/>
                <label for="is_model_option"></label>
            <input id="is_model_option_hidden" name="is_model_option" type="hidden" value="0"/>
            </span>
    </div>

    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12"
         style="margin-bottom: 40px;">
            <span class="material-switch pull-right">قابل مرتب سازی ؟ &nbsp
                <input id="is_sortable" name="is_sortable" type="checkbox" value="1"
                       @if($attribute_key->is_sortable) checked @endif/>
                <label for="is_sortable"></label>
            <input id="is_sortable_hidden" name="is_sortable" type="hidden" value="0"/>
            </span>
    </div>
    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 input-group tag-manager">
        <div class="tags-container nabs">
            <ul tag-input-name="<%- name %>">
                @foreach($attribute_key->values()->orderBy('priority')->get() as $value)
                    <li class="tag-element"
                        act="link"
                        href="{{route('admin.p-structure-attr-value.edit', $value)}}">
                        <span class="tag-text">{{$value->name}}</span>
                        <button class="remove-tag virt-form"
                                data-action="{{route('admin.p-structure-attr-value.destroy', $value)}}"
                                data-method="DELETE" confirm>
                            <i class="fa fa-times"></i>
                        </button>
                    </li>
                @endforeach
                <li class="action btn btn-sm btn-success" act="link"
                    href="{{route('admin.p-structure-attr-value.create')}}?id={{ $attribute_key->id }}">
                    <i class="fa fa-plus"></i>
                </li>
            </ul>
        </div>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
    <a type="submit" class="btn btn-info btn-sm"
       href="{{route("admin.p-structure-attr-value.index")}}?p_structure_attr_key_id={{$attribute_key->id}}">نمایش لیست
        مقادیر</a>
@endsection
