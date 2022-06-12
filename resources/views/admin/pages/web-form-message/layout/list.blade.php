@foreach($web_form_messages as $web_form_message)
    <div
        class="col-lg-offset-1 col-lg-10 col-md-offset-0 col-md-12 col-sm-offset-0 col-sm-12 col-xs-offset-0 col-xs-12 list-row roles">
        <div class="col-lg-1 col-md-1 col-sm-4 col-xs-3 col">
            <div class="label">شناسه</div>
            <div>{{$web_form_message->id}}#</div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-3 col">
            <div class="label">شناساگر فرم</div>
            <div>{{$web_form_message->form->identifier}}</div>
        </div>
        <div class="col-lg-1 col-md-1 col-sm-4 col-xs-3 col">
            <div class="label">تاریخ ارسال پیام</div>
            <div><span class="label label-primary"
                       style="padding: 6px;">{{TimeService::getDateFrom($web_form_message->created_at)}}</span>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-6 col">
            <div class="label">وضعیت</div>
            @if(isset($web_form_message->user_id) and $web_form_message->user_id != null)
                <div>خوانده شده توسط {{$web_form_message->user->name}} {{$web_form_message->user->family}}</div>
            @else
                <div>خوانده نشده</div>
            @endif
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 col">
            <div class="label">عملیات</div>
            <div class="actions-container">
                <a class="btn btn-sm btn-success" href="{{route('admin.web-form-message.show', $web_form_message)}}">
                    <i class="fa fa-eye"></i>
                </a>
            </div>
        </div>
    </div>
@endforeach
