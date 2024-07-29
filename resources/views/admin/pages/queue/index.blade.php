@php
    use App\Enums\Queue\QueueStatus;
    $firstKey = array_key_first($queues);
@endphp
@extends('admin.layout')

@section('bread_crumb')
    <li class="active"><a href="{{route('admin.setting.queue.index')}}">{{trans('general.setting.queue')}}</a></li>
@endsection

@section('main_content')
    <div class="form-layout-container">
        <div act="main-form">
            <div
                class="form-window-container col-lg-4 col-lg-offset-4 col-md-4 col-offset-md-4 col-sm-8 col-sm-offset-2 col-xs-12 col-xs-offset-0">
                <div class="form-window">
                    <div class="top-bar">
                        <div class="title-container"><h1>صف های پردازش</h1></div>
                    </div>
                    <div class="form-body">
                        <div class="accordion" id="accordion">
                            <div class="panel-body">
                                <form class="pb-10" id="{{'toggle-queue-status'}}" method="post"
                                      action="{{route('admin.setting.queue.update')}}">
                                    @csrf
                                    @foreach($queues as $queue => $data)
                                        <div class="panel panel-default">
                                            <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                                                <div class="container">
                                                    @php
                                                        $queueRunning = $data['status'] == QueueStatus::RUNNING;
                                                    @endphp
                                                    <input id="queue-{{$queue}}" type="radio" name="queue" value="{{$queue}}"
                                                           @if($firstKey == $queue) checked @endif">
                                                    <label for="queue-{{$queue}}" class="mr-10 pb-5 ">{{$queue}}</label>
                                                    <div>
                                                    <span
                                                        class="mr-10">{{trans("general.setting.queue_count").": "}}<span>{{$data['count']}}</span></span>
                                                        <span
                                                            class="mr-10">{{trans("general.setting.queue_count_failed").": "}}<span>{{$data['failed_count']}}</span></span>
                                                    </div>
                                                    <p @if($queueRunning) class="text-success my-10" @else class="text-danger my-10" @endif>@if($queueRunning)
                                                            @lang("general.setting.queue_running")
                                                        @else
                                                            @lang("general.setting.queue_stopped")
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    <input type="hidden" name="toggle_status" value="0">
                                    <button type="submit" class="btn btn-warning">تغییر وضعیت صف</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
