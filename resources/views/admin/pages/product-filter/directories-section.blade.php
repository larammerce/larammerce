<ul id="directory_{{isset($root_id) ? $root_id : 0}}" data-searchable-id="{{isset($root_id) ? $root_id : 0}}"
    data-searchable-title="{{isset($root_title) ? $root_title : "تمام دایرکتوری‌ها"}}"
    @if(isset($root_id)) class="collapse searchable-list" @else class="collapse in searchable-list"
    aria-expanded="true" @endif>
    @foreach($directories as $directory)
        <li style="margin-bottom: 8px; margin-top: 8px" data-searchable-title="{{$directory->title}}">
            <input type="checkbox" name="data[directories][]" value="{{$directory->id}}"
                   class="filter-select" data-filter-select-title="پوشه:{{$directory->title}}"
                   id="directory_input_{{$directory->id}}"
                   @if(in_array($directory->id, old("data") !== null ? old("data")["directories"] : []) or in_array($directory->id, $product_filter->getDirectoryIds())) checked @endif/>
            <button type="button" class="btn btn-dark" data-toggle="collapse"
                    data-target="#directory_{{$directory->id}}">
                {{$directory->title}}
            </button>

            @if($directory->directories()->count() > 0)
                @include("admin.pages.product-filter.directories-section",
                    [
                        "directories" => $directory->directories,
                        "root_id" => $directory->id,
                        "root_title" => $directory->title
                        ])
            @endif
        </li>
    @endforeach
</ul>
