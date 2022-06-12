@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.web-form-message.show', $web_form_message)}}">فیلد ها</a></li>
    <li class="active"><a href="{{route('admin.web-form-message.index')}}">لیست پیام ها</a></li>

@endsection

@section('main_content')
    <div class="inner-container" style="overflow: scroll">
        <div class="col-lg-10 col-md-offset-0 col-md-12 col-sm-offset-0 col-sm-12 col-xs-offset-0 col-xs-12" dir="rtl"
             style="margin-bottom: 30px">
            <div class="date-show-web-form" style=""> تاریخ ارسال پیام :
                <span class="label label-primary"
                      style="padding: 6px 15px;">{{TimeService::getDateTimeFrom($web_form_message->created_at)}}</span>
            </div>
            @foreach($web_form_message_fields as $key => $value)
                {{--TODO : check here Better than this--}}
                @if(str_contains($key, \App\Http\Controllers\MessageController::FILE_UPLOAD_PREFIX))
                    <hr>
                    <strong> فایل ضمیمه :</strong>
                    <a target="_blank" href="{{$value}}">{{$key}}</a>
                @else
                    @if($value !=null)
                        <hr>
                        <strong dir="rtl">{{trans(trans('validation.attributes.'.$key))}} : </strong>
                        <span dir="rtl">{{$value}}</span>
                    @endif
                @endif
            @endforeach
        </div>
    </div>
@endsection
