@foreach($directories as $directory)
    <div
        class="col-lg-offset-1 col-lg-10 col-md-offset-0 col-md-12 col-sm-offset-0 col-sm-12 col-xs-offset-0 col-xs-12 list-row users">
        <div class="col-lg-1 col-md-2 col-sm-3 col-xs-3 col">
            <div class="img-container" act="file" href="{{route('admin.directory.show', $directory)}}"
                 edit-href="{{route('admin.directory.edit', $directory)}}" data-file-type="App\Models\Directory"
                 data-file-id="{{$directory->id}}">
                <img class="img-responsive" src="/admin_dashboard/images/folder-close.png">
            </div>
        </div>
        <div class="col-lg-1 col-md-2 col-sm-3 col-xs-3 col">
            <div class="label">شناسه</div>
            <div>{{$directory->id}}#</div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6 col">
            <div class="label">عنوان</div>
            <div>{{$directory->title}}</div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6 col">
            <div class="label">نام بخش url</div>
            <div>{{$directory->url_part}}</div>
        </div>
        <div class="col-lg-4 col-md-12 col-sm-6 col-xs-12 col">
            <div class="label">عملیات</div>
            <div class="actions-container">
                <span class="material-switch">دارای صفحه وب&nbsp
                    <input id="has_web_page_{{$directory->id}}" name="has_web_page_{{$directory->id}}"
                           type="checkbox"
                           @if($directory->has_web_page) checked @endif disabled/>
                    <label for="has_web_page_{{$directory->id}}"></label>
                </span>
                <a class="btn btn-sm btn-primary" href="{{route('admin.directory.edit', $directory)}}">
                    <i class="fa fa-pencil"></i>
                </a>
                <a class="btn btn-sm btn-danger virt-form"
                   data-action="{{ route('admin.directory.destroy', $directory) }}"
                   data-method="DELETE" confirm>
                    <i class="fa fa-trash"></i>
                </a>
                <a class="btn btn-sm btn-success" href="{{route('admin.directory.show', $directory)}}">
                    <i class="fa fa-eye"></i>
                </a>
            </div>
        </div>
    </div>
@endforeach
