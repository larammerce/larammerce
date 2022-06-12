<div id="modal-global-{{$modal->id}}" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="float: left">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="title">{{ $modal->title }}</h4>
            </div>
            <div class="modal-body">
                <p>{{ $modal->text }}</p>
                @if($modal->hasImage())
                    <img src="{{ $modal->image_path }}" alt="image for system message">
                @endif
            </div>
            <div class="modal-footer">
                @foreach ($modal->getDecodedButtons() as $button)
                    @if($button->type === "data-dismiss")
                        <button data-dismiss="modal" type="button"
                                class="{{$button->tag_class}}">{{$button->text}}</button>
                    @else
                        <a class="{{$button->tag_class}}" href="{{$button->link}}">{{$button->text}}</a>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>
