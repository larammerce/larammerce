<div class="form-layout-container">
    <div class="extra-link-container">
        <a href="{{route("developer.send-mobile-auth-code")}}" class="extra-link pull-right">
           send one time code
        </a>
    </div>
    <hr>
    @if(session()->has('error'))
        @foreach(session()->get('error') as $error)
            <h4>{{$error}}</h4>
        @endforeach
    @elseif(session()->has('success'))
        @foreach(session()->get('success') as $success)
            <h4>{{$success}}</h4>
        @endforeach
    @endif
    <div class="top-line-content">
        <form action="{{route("developer.submit-invoice-warehouse-permission")}}" method="POST">
        {{ csrf_field() }}
            <div class="title-container">Submit WareHouse Permission</div>
            <hr>
            <div class="form-body">
                {{ method_field('POST') }}
                <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                    <span class="label"></span>
                    <input class="form-control input-sm" required
                           placeholder="invoice transaction id" name="transactionId" value="{{old("transactionId")}}">
                </div>
                <br>
                <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                    <span class="label"></span>
                    <input class="form-control input-sm"
                           placeholder="invoice trans number" name="transNumber" value="{{old("transNumber")}}">
                </div>
                <br>
                <div class="input-group group-sm col-lg-12 col-sm-12 col-md-12 col-xs-12">
                    <span class="label"></span>
                    <input class="form-control input-sm" required
                           placeholder="one time code" name="code" value="{{old("code")}}">
                </div>
            </div>
            <br>
            <div class="form-footer">
                <button type="submit" class="btn btn-default btn-sm">submit</button>
            </div>
    </form>
    </div>
</div>

<a href='/developer'>go back</a>