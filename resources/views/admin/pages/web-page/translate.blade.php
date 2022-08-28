@extends("admin.pages.model-translation.edit")

@section("translation_form_header")
    <h3>{{$translatable_object->directory->title}}</h3>
    @include('admin.pages.web-page.dynamic_fields', ["web_page" => $translatable_object])
@endsection

@section("translation_form_footer")
@endsection
