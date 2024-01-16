@extends("admin.pages.model-translation.edit")

@section("translation_form_footer")
    <div class="clearfix"></div>
    @foreach($translatable_object->gallery->getGalleryFields() as $key => $field)
        @if(strpos($key, 'description') !== false)
            <textarea class="tinymce"
                      name="data[{{ $key }}]">{{ $translatable_object->getField($key)->getContent() }}</textarea>
        @else
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="label">{{ $field->getTitle() }}</span>
                <input class="form-control input-sm" name="data[{{ $key }}]"
                       value="{{ $translatable_object->getField($key)->getContent() }}">
            </div>
        @endif
    @endforeach
@endsection
