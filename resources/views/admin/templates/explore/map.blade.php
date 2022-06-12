@if(env("APP_ADMIN_MAP_SHOW", true))
    <div class="directory dir-tree dir-open" act="file" href="{{route('admin.directory.index')}}">
        <div class="directory-content">
            <a href="#" class="directory-name">خانه</a>
        </div>
        <ul class="sub-directories">
            @foreach(build_directories_tree() as $directory)
                @include('admin.templates.explore.directory', compact('directory'))
            @endforeach
        </ul>
    </div>
@endif