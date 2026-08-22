# گزارش prototype صفحه Contact و Blog

تاریخ: 2026-08-22

## Contact

- template اختصاصی `page-contact.html` با heading hierarchy صحیح ایجاد شد.
- تلفن ثابت، تلفن همراه و ایمیل به لینک‌های `tel:` و `mailto:` قابل استفاده تبدیل شدند.
- راهنمای پیگیری سفارش و هشدار عدم ارسال اطلاعات بانکی اضافه شد.
- layout سه‌ستونه desktop و تک‌ستونه mobile با screenshot بازبینی شد.
- فرم واقعی تا انتخاب mail delivery، validation، rate limit و anti-spam اضافه نشده است.

## Blog

- template اختصاصی `home.html` با Query نوشته‌ها، pagination و empty-state ایجاد شد.
- صفحه `/blog/` به‌عنوان posts page تنظیم شد و template صحیح را رندر می‌کند.
- کارت نوشته در desktop/mobile بازبینی شد.
- نوشته پیش‌فرض WordPress هنوز در محیط تست دیده می‌شود و بدون تصمیم صریح حذف نشد.

## شواهد

- `docs/baseline/rebuild-contact-desktop-1440.png`
- `docs/baseline/rebuild-contact-mobile-375.png`
- `docs/baseline/rebuild-blog-desktop-1440.png`
- `docs/baseline/rebuild-blog-mobile-375.png`

هر دو مسیر HTTP 200 هستند و log اخیر WordPress خطای PHP ندارد. `UI-PAGE-001` و `UI-BLOG-001` تا تکمیل breadcrumb، archive، taxonomy و داده واقعی همچنان باز می‌مانند.
