require(['tinymce'], function () {
    window.filemanagerAccessKey = window.filemanagerAccessKey || '';
    tinymce.init({
        selector: '.tinymce',
        theme: 'modern',
        directionality: 'rtl',
        language: 'fa_IR',
        content_css:'/admin_dashboard/css/tinymce-extras.css',
        plugins: [
            "code advlist autolink lists link image charmap print preview hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars code fullscreen",
            "insertdatetime media nonbreaking save table contextmenu directionality",
            "emoticons paste textcolor colorpicker textpattern imagetools", "responsivefilemanager fullscreen template"
        ],
        toolbar1: "insertfile undo redo | styleselect | bold italic | bullist numlist outdent indent | link image | print preview media | code | fullscreen",
        toolbar2: "fontselect fontsizeselect | alignleft aligncenter alignright alignjustify | forecolor backcolor emoticons | template",
        image_advtab: true,
        menu_bar: 'tools insert',
        fontsize_formats: "8px 10px 12px 14px 16px 18px 20px 24px 26px 28px 30px 36px 72px",
        external_filemanager_path: "/ResponsiveFilemanager/filemanager/",
        filemanager_title: "مدیریت فایل ها",
        external_plugins: {"filemanager": "/ResponsiveFilemanager/filemanager/plugin.min.js"},
        filemanager_access_key: window.filemanagerAccessKey,
        theme_advanced_fonts: "Arial=arial,helvetica,sans-serif;Courier New=courier new,courier,monospace;AkrutiKndPadmini=Akpdmi-n",
        templates: [
            {
                title: 'Blockqoute',
                description: '',
                content: '<blockquote class="blockquote">لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است. چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم است و برای شرایط فعلی تکنولوژی مورد نیاز و کاربردهای متنوع با هدف بهبود ابزارهای کاربردی می باشد. کتابهای زیادی در شصت و سه درصد گذشته، حال و آینده شناخت فراوان جامعه و متخصصان را می طلبد تا با نرم افزارها شناخت بیشتری را برای طراحان رایانه ای علی الخصوص طراحان خلاقی و فرهنگ پیشرو در زبان فارسی ایجاد کرد. در این صورت می توان امید داشت که تمام و دشواری موجود در ارائه راهکارها و شرایط سخت تایپ به پایان رسد وزمان مورد نیاز شامل حروفچینی دستاوردهای اصلی و جوابگوی سوالات پیوسته اهل دنیای موجود طراحی اساسا مورد استفاده قرار گیرد.</blockquote>'
            },
            {
                title: 'List',
                description: '',
                content: '<ul class="list">\n' +
                    '                                <li>لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان\n' +
                    '                                </li>\n' +
                    '                                <li>گرافیک است. چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم است و\n' +
                    '                                </li>\n' +
                    '                            </ul>'
            },
            {
                title: 'List Numeric',
                description: '',
                content: '<ol class="list-numeric">\n' +
                    '                                <li>لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان\n' +
                    '                                </li>\n' +
                    '                                <li>گرافیک است. چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم است و\n' +
                    '                                </li>\n' +
                    '                            </ol>'
            },
            {
                title: 'Product Row',
                description: '',
                content: '<div class="row" style="border-bottom:1px solid #DEDEDD;padding:15px 0;">\n' +
                    '                                <div class="col-lg-8 col-md-7 col-sm-7 col-xs-7">\n' +
                    '                                    <p>لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است. چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم است و برای شرایط فعلی تکنولوژی مورد نیاز و کاربردهای متنوع با هدف بهبود ابزارهای کاربردی می باشد. </p>\n' +
                    '                                </div>\n' +
                    '                                <div class="col-lg-4 col-md-5 col-sm-5 col-xs-5" style="text-align:left">\n' +
                    '                                    <img src="image path" alt="image alt text" class="img-fluid">\n' +
                    '                                </div>\n' +
                    '                            </div>\n' +
                    '                            <div class="row" style="border-bottom:1px solid #DEDEDD;padding:15px 0;">\n' +
                    '                                <div class="col-lg-8 col-md-7 col-sm-7 col-xs-7">\n' +
                    '                                    <p>لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است. چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم است و برای شرایط فعلی تکنولوژی مورد نیاز و کاربردهای متنوع با هدف بهبود ابزارهای کاربردی می باشد. </p>\n' +
                    '                                </div>\n' +
                    '                                <div class="col-lg-4 col-md-5 col-sm-5 col-xs-5" style="text-align:left">\n' +
                    '                                    <img src="image path" alt="image alt text" class="img-fluid">\n' +
                    '                                </div>\n' +
                    '                            </div>'
            },
            {
                title: 'Related Links',
                description: '',
                content: '<div class="related-links">\n' +
                    '                                <ul class="list">\n' +
                    '                                    <li><a href="#">لورم اپیسوم قسمت اول</a></li>\n' +
                    '                                    <li><a href="#">لورم اپیسوم قسمت دوم</a></li>\n' +
                    '                                </ul>\n' +
                    '                            </div>'
            }
        ]

    });
});