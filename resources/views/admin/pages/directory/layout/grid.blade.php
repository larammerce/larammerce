@foreach($directories as $directory)
    <div act="file" href="{{route('admin.directory.show', $directory)}}" target="_blank"
         class="file-container col-lg-1 col-md-2 col-sm-3 col-xs-6" data-file-id="{{$directory->id}}"
         edit-href="{{route('admin.directory.edit', $directory)}}" data-file-type="App\Models\Directory">
        <div class="file-checkbox">
            <i class="fa fa-check-circle-o"></i>
            <i class="fa fa-circle-o"></i>
        </div>
        <a href="{{ route('admin.directory.show', $directory) }}" class="file-content">
            <div class="h-icon icon-folder square-ratio"></div>
            <div class="file-detail">
                <h3 class="file-title">{{ $directory->title }}</h3>
                <span class="real-directory">{{ $directory->content_type_title }}</span>
            </div>
        </a>
    </div>
@endforeach