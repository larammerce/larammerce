<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>
        لیست محصولات موجود و فعال فروشگاه {{get_cms_setting("company_name")}}
    </title>
    <style type="text/css">
        body {
            direction: rtl;
        }

        .lst-table th {
            border: 1px solid #bbbbbb;
            padding: 5px;
            text-align: center;
        }

        * {
            font-family: Tahoma;
            font-size: 11px;
        }

        .lst-table {
            width: 100%;
            border-collapse: collapse;
        }

        .lst-table td {
            border: 1px solid #bbbbbb;
            padding: 5px;
        }

        .lst-header td {
            line-height: 2;
        }

        .lst-item td {
            background-color: #ecf7ff;
        }

        .lst-item:nth-child(2n+1) td {
            background-color: #c2e6ff;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <h1>لیست محصولات موجود و فعال فروشگاه {{get_cms_setting("company_name")}}</h1>

                    <table class="lst-table" cellspacing="1" id="GridView1"
                           style="font-family:Tahoma;">
                        <thead>
                        <th class="id">شناسه</th>
                        <th class="title">عنوان</th>
                        <th class="img">تصویر</th>
                        <th class="price">قیمت (تومان)</th>
                        </thead>
                        <tbody>

                        @foreach($products as $product)
                            <tr>
                                <td>
                                    {{$product->id}}
                                </td>
                                <td>
                                    <a href='{{$product->getFrontUrl()}}'>
                                        {{$product->title}}
                                    </a>
                                </td>
                                <td style="text-align : center">
                                    <img style="max-width: 64px; max-height: 48px;"
                                         src="{{$product->main_photo}}"
                                         alt='{{$product->title}}'
                                         title='{{$product->title}}'/>
                                </td>
                                <td class="price">
                                    {{format_price($product->latest_price)}}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
