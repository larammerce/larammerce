@extends('admin.form_layout.col_8')

@section('bread_crumb')
    <li><a href="{{route('admin.p-structure.index')}}">قالب محصولات</a></li>
    <li class="active"><a href="{{route('admin.p-structure.edit', $p_structure)}}">ویرایش قالب محصول</a></li>

@endsection

@section('form_title')ویرایش قالب محصول@endsection

@section('form_attributes') action="{{route('admin.p-structure.update', $p_structure)}}" method="POST" enctype="multipart/form-data" form-with-hidden-checkboxes @endsection

@section('form_body')
    {{ method_field('PUT') }}
    <input type="hidden" name="id" value="{{ $p_structure->id }}">
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">عنوان قالب</span>
        <input class="form-control input-sm" name="title" value="{{ $p_structure->title }}">
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">نام بلید</span>
        <select class="form-control" name="blade_name">
            @foreach(get_template_views() as $view)
                <option value="{{$view}}"
                        @if($p_structure->blade_name === $view) selected @endif>{{$view}}</option>
            @endforeach
        </select>
    </div>
    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="material-switch pull-right">آیا محصولات این ساختار قابل ارسال برای مشتری هستند ؟
            <input id="is_shippable" name="is_shippable" type="checkbox" value="1"
                   @if($p_structure->is_shippable) checked @endif/>
            <label for="is_shippable"></label>
            <input id="is_shippable_hidden" name="is_shippable" type="hidden" value="0"/>
        </span>
    </div>
    <hr>
    <div class="filled tag-manager input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <span class="label">ویژگی ها</span>
        <textarea act="tag" class="form-control input-sm attachable" name="tags"
                  data-save="{{ route('admin.p-structure-attr-key.store') }}"
                  data-query="{{ route('admin.p-structure-attr-key.query') }}"
                  data-attach="{{ route('admin.p-structure.attach-attribute-key', $p_structure) }}"
                  data-detach="{{ route('admin.p-structure.detach-attribute-key', $p_structure) }}"
                  data-field-name="title"
                  data-open-tag="{{ route('admin.p-structure-attr-key.edit', -1) }}"
                  data-container=".form-layout-container"></textarea>
        <ul act="tag-data">
            @foreach($p_structure->attributeKeys as $attributeKey)
                <li data-id="{{$attributeKey->id}}" data-text="{{$attributeKey->title}}"></li>
            @endforeach
        </ul>
    </div>
@endsection

@section('form_footer')
    <button type="submit" class="btn btn-default btn-sm">ذخیره</button>
    <input type="submit" class="btn btn-warning btn-sm" name="exit" value="ذخیره و خروج">
@endsection
