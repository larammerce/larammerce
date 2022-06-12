@foreach($articles as $article)
    <div
        class="col-lg-offset-1 col-lg-10 col-md-offset-0 col-md-12 col-sm-offset-0 col-sm-12 col-xs-offset-0 col-xs-12 list-row articles">
        <div class="col-lg-1 col-md-2 col-sm-2 col-xs-3 col">
            @if(isset($directory) and $directory->directory_id != $directory->id)
                <i class="link-file fa fa-link"></i>
            @endif
            <div class="img-container" act="file" href="{{$article->getFrontUrl()}}" target="_blank"
                 edit-href="{{route('admin.article.edit', $article)}}" data-file-type="App\Models\Article"
                 data-file-id="{{$article->id}}">
                <img class="img-responsive" src="{{ ImageService::getImage($article, 'thumb') }}">
            </div>
        </div>
        <div class="col-lg-1 col-md-2 col-sm-2 col-xs-3 col">
            <div class="label">شناسه</div>
            <div>{{$article->id}}#</div>
        </div>
        <div class="col-lg-4 col-md-2 col-sm-2 col-xs-3 col">
            <div class="label">عنوان</div>
            <div>{{$article->title}}</div>
        </div>
        <div class="col-lg-2 col-md-6 col-sm-6 col-xs-6 col">
            <div class="label">نویسنده</div>
            <div>{{$article->author->user->username}}</div>
        </div>
        <div class="col-lg-1 col-md-3 col-sm-2 col-xs-3 col">
            <div class="label">تاریخ ساخت</div>
            <div>{{TimeService::getDateFrom($article->created_at)}}</div>
        </div>
        <div class="col-lg-3 col-md-9 col-sm-7 col-xs-12 col">
            <div class="label">عملیات</div>
            <div class="actions-container">
                <a class="btn btn-sm btn-primary" href="{{route('admin.article.edit', $article)}}">
                    <i class="fa fa-pencil"></i>
                </a>
                <a class="btn btn-sm btn-danger virt-form"
                   data-action="{{ route('admin.article.destroy', $article) }}"
                   data-method="DELETE" confirm>
                    <i class="fa fa-trash"></i>
                </a>
                <a class="btn btn-sm btn-success" href="{{$article->getFrontUrl()}}">
                    <i class="fa fa-eye"></i>
                </a>
            </div>
        </div>
    </div>
@endforeach
