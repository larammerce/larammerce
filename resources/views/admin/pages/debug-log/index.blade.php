@extends('admin.layout')

@section('bread_crumb')
    <li><a href="{{route('admin.setting.appliances')}}">تنظیمات</a></li>
    <li class="active"><a href="{{route('admin.debug-log.index')}}">لاگ دیباگ</a></li>

@endsection

@section('main_content')
    <div class="inner-container">
        <div class="view-port">
            <div class="container-fluid" style="margin-top: 20px">
                <div class="row">
                    <!-- Left Side -->
                    <div class="col-md-3">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">نوع لاگ فایل ها</h3>
                            </div>
                            <div class="panel-body">
                                <form action="{{route("admin.debug-log.index")}}" method="GET">
                                    <!-- Dropdown for log types -->
                                    <div class="form-group">
                                        <select id="logType" name="debug_log_type" class="form-control">
                                            @foreach($types as $key => $type)
                                                <option
                                                    value="{{ $key }}" {{ $debug_log_type == $key ? 'selected' : '' }}>{{ trans($type) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-info btn-sm">لیست فایل‌ها</button>
                                </form>
                            </div>
                        </div>

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">لیست لاگ فایل ها</h3>
                            </div>
                            <div class="panel-body">

                                <!-- File List -->
                                <ul class="list-group" style="padding: 0">
                                    @foreach($files as $file)
                                        <li class="list-group-item">
                                            {{ $file }}
                                            <span class="pull-right">
                                                <a href="{{ route('admin.debug-log.view', ['debug_log_type' => $debug_log_type, 'file_name' => $file]) }}"
                                                   class="btn btn-xs btn-primary">نمایش</a>
                                                <a href="{{ route('admin.debug-log.download', ['debug_log_type' => $debug_log_type, 'file_name' => $file]) }}"
                                                   class="btn btn-xs btn-success">دانلود</a>
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <!-- Search Form -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title"> جستجوی لاگ‌ها <b>{{$file_name ?? ""}}</b></h3>
                            </div>
                            <div class="panel-body">
                                <form action="{{ route('admin.debug-log.search') }}" method="GET" class="form-inline">
                                    <input type="hidden" name="debug_log_type" value="{{ $debug_log_type }}">
                                    <input type="hidden" name="file_name" value="{{ $file_name ?? '' }}">
                                    <div class="form-group">
                                        <input type="text" name="keyword" placeholder="Search Keyword"
                                               value="{{ $keyword ?? '' }}" class="form-control">
                                    </div>
                                    <button type="submit" class="btn btn-default" {{ isset($file_name) ? "" : "disabled" }}>جستجو</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side -->
                    <div class="col-md-9">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">محتوای لاگ فایل <b>{{$file_name ?? ""}}</b></h3>
                            </div>
                            <div class="panel-body">
                                <!-- Show last lines -->
                                @isset($lines)
                                    <pre class="well well-sm" style="direction: ltr">{{ implode("\n", $lines) }}</pre>
                                @endisset

                                <!-- Search Results -->
                                @isset($stack_traces)
                                    @foreach($stack_traces as $trace)
                                        <pre class="well well-sm" style="direction: ltr">{{ $trace }}</pre>
                                    @endforeach
                                @endisset

                                <!-- Load More -->
                                <button id="load-more" class="btn btn-primary btn-block">نمایش بیشتر</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
