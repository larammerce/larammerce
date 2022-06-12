require(['jquery'], function (jQuery) {
    window.printProductList = function (e, element) {
        e.preventDefault();
        let divToPrint = document.getElementById(element);
        let customStyle = `
           <style>
                body{
                    font-family: Shabnam,sans-serif;
                    text-align: right;
                    width: 100%;
                    overflow-y: visible !important;
                    position: relative;
                }
                body *{
                 font-size: 12px;
                }
                html{
                  width: 100%;
                  overflow: visible !important;
                }
                table td,table th{padding: 10px;text-align: center}
                table thead tr{background-color: burlywood;color: white;}
                .row{
                    direction: rtl !important;
                    display: flex;
                    flex-wrap: wrap;
                    margin-right: -15px;
                    margin-left: -15px;
                }
           </style>
        `;
        let newWin = window.open('', element);
        newWin.document.open();
        newWin.document.write(`<html>
                 <head>
                    <meta charset="UTF-8">
                    <meta name="author" content="Larammerce">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.6, initial-scale=1.0"/>
                    <link rel="icon" type="image/png" sizes="32x32" href="/HCMS-assets/img/favicon/favicon-32x32.png">
                    <link rel="icon" type="image/png" sizes="16x16" href="/HCMS-assets/img/favicon/favicon-16x16.png">
                    <link rel="manifest" href="/HCMS-assets/img/favicon/site.webmanifest">
                    <link rel="mask-icon" href="/HCMS-assets/img/favicon/safari-pinned-tab.svg" color="#fabe3c">
                    <link rel="stylesheet" href="/node_modules/bootstrap/dist/css/bootstrap.min.css"/>
                    <link rel="stylesheet" href="/node_modules/font-awesome/css/font-awesome.min.css"/>
                    <link href="https://cdn.fontcdn.ir/Font/Persian/Shabnam/Shabnam.css" rel="stylesheet" type="text/css">
                    <link rel="stylesheet" type="text/css" href="/admin_dashboard/css/app-19-12-18.css"/>
                     ${customStyle}
                </head>
            <body>
            <hr>
            ${divToPrint.innerHTML}
            </body>
            </html>`);

        newWin.document.close();
        setTimeout(function () {
            newWin.print();
            newWin.close();
        }, 500);
    }
});
