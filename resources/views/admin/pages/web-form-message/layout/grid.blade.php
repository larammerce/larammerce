@foreach($web_form_messages as $web_form_message)
    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 grid-item roless">
        <div class="item-container">
            <div class="col-lg-2 col-md-3 col-sm-3 col-xs-3 col">
                <div class="label">شناسه</div>
                <div>{{$web_form_message->id}}#</div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 col">
                <div class="label">شناساگر فرم</div>
                <div>{{$web_form_message->form->identifier}}</div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 col">
                <div class="label">وضعیت</div>
                @if(isset($web_form_message->user_id) and $web_form_message->user_id != null)
                    <div>خوانده شده توسط {{$web_form_message->user()->name}} {{$web_form_message->user()->family}}</div>
                @else
                    <div>خوانده نشده</div>
                @endif
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 col">
                <div class="label">پیام</div>
                <a href="{{route('admin.web-form-message.show', $web_form_message)}}">
                    <span class="btn">مشاهده جزئیات</span>
                </a>
            </div>
        </div>
    </div>
    </div>
@endforeach
