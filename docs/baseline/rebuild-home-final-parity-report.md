# گزارش نهایی تطبیق صفحه Home در Rebuild

تاریخ بررسی: 2026-08-22

## محدوده

صفحه `http://localhost:8082` با baseline بلند Legacy به‌صورت سکشن‌به‌سکشن بررسی شد. هدف، حفظ ترتیب، هویت بصری و محتوای اصلی Legacy بدون انتقال Elementor و بدون بازتولید باگ‌های شناخته‌شده آن بود.

## نتیجه

- Header، Hero، پنج کارت مزیت، دوره‌های جدید، راهبر مالی، آموزش رایگان، سؤال‌های پرتکرار، اخبار و بخشنامه‌ها، ایزی اینویس، اینستاگرام، منتورما، تجربه دانشجویان، همکاران و Footer در Rebuild موجودند.
- Hero در موبایل به‌صورت عمودی و بدون خروج محتوا از viewport رندر می‌شود.
- navigation و actionهای جست‌وجو/حساب در موبایل قابل دسترس‌اند.
- ستون‌های Footer در موبایل عمودی شدند و متن و مجوزها دیگر فشرده نمی‌شوند.
- ارتفاع کارت محصول در موبایل کاهش یافت و فضای خالی غیرضروری Legacy بازتولید نشد.
- محصول جعلی برای پرکردن carousel ساخته نشد؛ Query فقط داده واقعی WooCommerce مقصد را نمایش می‌دهد.
- بخش آموزش رایگان تا migration انتخابی داده، empty-state صریح دارد.

## شواهد تصویری

- `docs/baseline/rebuild-home-final-desktop-1440.png`
- `docs/baseline/rebuild-home-final-tablet-768.png`
- `docs/baseline/rebuild-home-final-mobile-375.png`
- مرجع: `docs/baseline/legacy-home-long-desktop-1440.png`

## اعتبارسنجی

- صفحه Home: HTTP 200
- `docker compose -f rebuild/compose.yaml config --quiet`: موفق
- `php -l rebuild/site/wp-content/themes/rahbar/functions.php`: موفق
- `openspec.cmd validate rebuild-qa-checklist --strict`: موفق
- log اخیر WordPress: بدون PHP fatal، warning، parse error یا notice
- قالب Rahbar: `0.7.4`

## محدودیت‌های باز خارج از task تطبیق Home

مقصد تعدادی از لینک‌های Header و Footer هنوز ساخته نشده و HTTP 404 می‌دهد؛ از جمله Page، Blog، Shop، Account، Cart، Contact و مسیرهای خدمات. ساخت و تست این مقصدها در taskهای `UI-PAGE-001`، `UI-BLOG-001`، `WC-CAT-001`، `AUTH-*` و taskهای مرتبط انجام می‌شود. به همین دلیل `GATE-UI` هنوز بسته نشده است.
