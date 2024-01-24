@php use App\Enums\Queue\QueueStatus; @endphp
@extends('admin.form_layout.col_4')

@section('bread_crumb')
    <li class="active"><a href="{{route('admin.setting.queue.index')}}">{{trans('general.setting.queue')}}</a></li>
@endsection

@section('form_title')
    صف های پردازش
@endsection

@section('form_body')
    <div class="accordion" id="accordion">
        <div class="panel-body">
            @foreach($queues as $queue => $data)
                <div class="panel panel-default">
                    <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                        <div class="container">
                            <form class="pb-10" id="{{'toggle-queue-status-'.$queue}}" method="POST"
                                  action="{{route('admin.setting.queue.update')}}">
                                @csrf
                                <p class="mr-10 pb-5 ">{{$queue}}</p>
                                <span
                                    class="mr-10">{{trans("general.setting.queue_count").": "}}<span>{{$data['count']}}</span></span>
                                <span
                                    class="mr-10">{{trans("general.setting.queue_count_failed").": "}}<span>{{$data['failed_count']}}</span></span>
                                <input type="hidden" name="queue" value="{{$queue}}">
                                <input type="hidden" name="toggle-status" value="{{$data['status']}}">
                                <input type="submit"
                                       @if($data['status'] == QueueStatus::RUNNING) class="mr-10 btn btn-warning"
                                       value="@lang("general.setting.queue_stop")"
                                       @else class="mr-10 btn btn-secondary"
                                       value="@lang("general.setting.queue_resume")" @endif>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
