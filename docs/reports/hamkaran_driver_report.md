# سند ارایه گزارش جهت ساخت درایور مالی همکاران سیستم در سامانه مدیریت فروشگاهی هینزا
در این سند روال ساخت و پیاده سازی درایور اتصال فروشگاه اینترنتی به نرم‌افزار همکاران سیستم توضیح داده می‌شود.

وب سرویس های استفاده شده جهت پیاده سازی این سیستم :

### `get_list_of_all_customers`, `getCustomerByPhone`, `getCustomerByRelation`:
این وب سرویس ها توسط شرکت همکاران سیستم تعبیه نشده و در پیاده سازی درایور مربوطه نادیده گرفته شدند.
عدم وجود این وب سرویس ها باعث می‌شود که مشتریانی که در حال حاضر در سامانه همکاران سیستم ثبت شده اند و به صورت سنتی با شرکت سیال کنترل همکاری دارند امکان ثبت نام و خرید از سایت ریتاپی را نداشته باشند.
به عنوان مثال در صورتی که `آرش خواجه لو` به شماره موبایل `۰۹۱۲۹۷۹۱۱۴۶` و شماره ملی `۰۰۱۶۷۸۹۱۴۸` قبلا از مشتریان سیال کنترل بوده باشد، دیگر با این کد ملی امکان ثبت نام در وبسایت را نخواهد داشت.

### `add_new_customer`:
این وب سرویس در حال حاضر موجود و قابل استفاده است.
اطلاعات مشتری از جمله `نام`، `نام خانوادگی`، `ایمیل`، `کدملی`، `شماره تماس`، و `جنسیت` در سامانه همکاران سیستم ذخیره می‌شود.

### `edit_customer`:
این وب سرویس توسط شرکت همکاران سیستم تعبیه شده است اما اجرای آن با خطا رو به رو می‌شود.
لذا بروز خطاها و عدم موفقیت آزمایش ها به منزله عدم وجود وب سرویس مربوطه تلقی می‌شود.
عدم وجود وب این وب سرویس موجب می‌شود که امکان تغییر اطلاعات کاربر پس از ثبت نام در وبسایت دیگر وجود نداشته باشد.
علاوه بر این امکان اضافه شدن آدرس و یا ویرایش اطلاعات آدرس های مشتریان نیز به واسطه این اشکال وجود ندارد.
```
البته لازم به ذکر است که این مشکل با توجه به هماهنگی انجام شده
 با نماینده شرکت همکاران سیستم با قرار دادن آدرس مشتری  در توضیحات 
 فاکتور مرتفع شده که نیاز به تایید کارفرما دارد.
```

### `get_all_products`:
این وب سرویس توسط شرکت همکاران سیستم تعبیه نشده و در پیاده سازیی درایور مربوطه نادیده گرفته شده است.
عدم وجود این وب سرویس موجب می‌شود که امکان بررسی اختلاف قیمت و موجودی کالاها به صورت کلی وجود نداشته باشد و نتیجتا روال به روز رسانی قیمت و موجودی کالاها در این درایور خیلی زمان بر شود.
حدود ۱۵ دقیقه به ازای هر ۱۰۰۰ محصول.

### `get_product`:
این وب سرویس توسط شرکت همکاران سیستم به صورت ERA تعبیه شده است و کد محصول را دریافت و در خروجی `موجودی`، `قیمت خام` و `شناسه` محصول را باز‌می‌گرداند.

### `get_product_count`:
این وب سرویس در همکاران سیستم تعبیه نشده است و در پیاده سازی درایور نادیده گرفته شده است.
عدم وجود این وب سرویس موجود پایین آمدن دقت ثبت سفارشات و افزایش بروز خطا در هنگان ثبت فاکتور می‌شود.

### `add_preinvoice`:
این وب سرویس در همکاران سیستم تعبیه شده و قابل استفاده است.

نکته: می‌بایست راهکاری برای ثبت هزینه ارسال در سیستم تعبیه شود.

نکته: در صورتی که فروش رسمی انجام شود می‌بایست راهکاری برای ثبت مالیات و عوارض تعبیه شود.

### `delete_preinvoice`:
این وب سرویس در همکاران سیستم تعبیه شده و قابل استفاده است.

### `submit_warehouse_permission`:
این وب سرویس در همکاران سیستم تعبیه شده و قابل استفاده است.

### `check_exit_tab`:
این وب سرویس در همکاران سیستم تعبیه شده و قابل استفاده است.

