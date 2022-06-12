@foreach($articles as $article)
    <div act="file" href="{{$article->getFrontUrl()}}" target="_blank"
         class="file-container col-lg-1 col-md-2 col-sm-3 col-xs-6"
         edit-href="{{route('admin.article.edit', $article)}}" data-file-type="App\Models\Article"
         data-file-id="{{$article->id}}">
        <div class="file-checkbox">
            <i class="fa fa-check-circle-o"></i>
            <i class="fa fa-circle-o"></i>
        </div>
        <a href="{{ route('admin.article.show', $article) }}" class="file-content">
            <div class="h-icon icon-article square-ratio"
                 style="background-image: url('{{ImageService::getImage($article, "thumb")}}') ;"></div>
            <div class="file-detail">
                <h3 class="file-title">{{ $article->title }}</h3>
            </div>
        </a>
    </div>
@endforeach