@foreach($webPage->getContents() as $content)
    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 web-page-content">
        <h1 class="title">{{$content->getTitle()}}</h1>
        @if($content->getType() === ContentTypes::TEXT)
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="label">محتوا</span>
                <input class="form-control input-sm" type="text" name="data__text__content__{{$content->getId()}}"
                       value="{{ $content->getContent() }}">
            </div>
        @elseif($content->getType() === ContentTypes::LINK)
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="label">آدرس لینک</span>
                <input class="form-control input-sm" type="text" name="data__link__href__{{$content->getId()}}"
                       value="{{ $content->getHref() }}">
            </div>
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="label">متن لینک</span>
                <input class="form-control input-sm" type="text" name="data__text__content__{{$content->getId()}}"
                       value="{{ $content->getContent() }}">
            </div>
        @elseif($content->getType() === ContentTypes::RICH_TEXT)
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="label">محتوا</span>
                <textarea class="tinymce" name="data__rich_text__content__{{$content->getId()}}"
                >{{$content->getContent()}}</textarea>
            </div>
        @elseif($content->getType() === ContentTypes::IMAGE)
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <label>فایل تصویر</label>
                @if(strlen($content->getSrc()) !== 0)
                    <div class="photo-container">
                        <img src="{{ $content->getSrc() }}" style="height: 200px;">
                    </div>
                @endif
                [حداقل کیفیت: {{ get_image_min_height('web_page') }}*{{ get_image_min_width('web_page') }}
                و نسبت: {{ get_image_ratio('web_page') }}]
                <input class="form-control" name="data__image__src__{{$content->getId()}}" type="file" multiple="true">
            </div>
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <span class="label">توضیحات تصویر</span>
                <input class="form-control input-sm" type="text" name="data__image__alt__{{$content->getId()}}"
                       value="{{ $content->getAlt() }}">
            </div>
        @elseif($content->getType() === ContentTypes::FILE)
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
            </div>
        @elseif($content->getType() === ContentTypes::AUDIO)
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <audio controls>
                    <source
                        src=@if(strlen($content->getSrc()) !== 0) {{ $content->getSrc() }} @else "" @endif
                    type=@if(strlen($content->getSrc()) !== 0) {{ $content->getFormat() }} @else "audio/mp3" @endif
                    >
                </audio>
                <br>
                <label>فایل mp3</label>
                ( حداکثر حجم {{ get_file_max_size('web_page') }} مگابایت)
                <input class="form-control" name="data__audio__src__{{$content->getId()}}" type="file" multiple="true">
            </div>
        @elseif($content->getType() === ContentTypes::VIDEO)
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <video
                    @if($content->hasControls()) controls @endif
                @if($content->hasAutoPlay()) autoplay @endif
                    @if($content->hasLoop()) loop @endif
                    poster=@if(strlen($content->getPoster()) !== 0) {{ $content->getPoster() }} @else "" @endif
                    width="320" height="240">
                    <source
                        src=@if(strlen($content->getSrc()) !== 0) {{ $content->getSrc() }} @else "" @endif
                    type=@if(strlen($content->getSrc()) !== 0) {{ $content->getFormat() }} @else "video/mp4" @endif
                    >
                </video>
                <br>
                <span class="material-switch pull-right">&nbsp; کنترل های پخش &nbsp;&nbsp;
                    <input id="data__video__controls__{{$content->getId()}}"
                           name="data__video__controls__{{$content->getId()}}"
                           type="checkbox" value="1"
                           @if($content->hasControls()) checked @endif/>
                    <label for="data__video__controls__{{$content->getId()}}"></label>
                    <input id="data__video__controls__{{$content->getId()}}_hidden"
                           name="data__video__controls__{{$content->getId()}}"
                           type="hidden" value="0"/>
                </span>

                <span class="material-switch pull-right">&nbsp; پخش خودکار &nbsp;
                    <input id="data__video__autoPlay__{{$content->getId()}}"
                           name="data__video__autoPlay__{{$content->getId()}}"
                           type="checkbox" value="1"
                           @if($content->hasAutoPlay()) checked @endif/>
                    <label for="data__video__autoPlay__{{$content->getId()}}"></label>
                    <input id="data__video__autoPlay__{{$content->getId()}}_hidden"
                           name="data__video__autoPlay__{{$content->getId()}}"
                           type="hidden" value="0"/>
                </span>

                <span class="material-switch pull-right">&nbsp; تکرار پخش &nbsp;
                    <input id="data__video__loop__{{$content->getId()}}"
                           name="data__video__loop__{{$content->getId()}}"
                           type="checkbox" value="1"
                           @if($content->hasLoop()) checked @endif/>
                    <label for="data__video__loop__{{$content->getId()}}"></label>
                    <input id="data__video__loop__{{$content->getId()}}_hidden"
                           name="data__video__loop__{{$content->getId()}}"
                           type="hidden" value="0"/>
                </span>

            </div>
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <label>پستر‌ ویدئو</label>
                [حداقل کیفیت: {{ get_image_min_height('web_page') }}*{{ get_image_min_width('web_page') }}
                و نسبت: {{ get_image_ratio('web_page') }}]
                <input class="form-control" name="data__video__poster__{{$content->getId()}}"
                       type="file" multiple="true">
            </div>
            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <label>فایل mp4</label>
                ( حداکثر حجم {{ get_file_max_size('web_page') }} مگابایت)
                <input class="form-control" name="data__video__src__{{$content->getId()}}"
                       type="file" multiple="true">
            </div>
        @endif
    </div>
@endforeach
