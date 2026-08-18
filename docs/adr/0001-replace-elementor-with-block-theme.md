# ADR-0001: جایگزینی Elementor با Block Theme اختصاصی

- وضعیت: پذیرفته‌شده
- تاریخ: 2026-08-18
- دامنه: `rebuild`

## زمینه

Legacy با Hello Elementor، Elementor و Elementor Pro ساخته شده و layoutها، CSS/JS سفارشی و widgetهای آن در طول زمان انباشته و شکننده شده‌اند. Rebuild باید سبک، قابل version-control، تست‌پذیر، RTL-friendly و کم‌وابستگی باشد.

## تصمیم

Rebuild با یک Block Theme اختصاصی Rahbar، `theme.json`، template/template-part و Gutenberg Pattern ساخته می‌شود. block اختصاصی فقط برای componentهایی ایجاد می‌شود که blockهای هسته و composition الگوها پاسخ‌گو نیستند.

Elementor، Elementor Pro، Hello Elementor، JSON layoutها و CSS تولیدشده آن‌ها به Rebuild منتقل نمی‌شوند. Legacy تا پایان دوره rollback مرجع بصری و رفتاری باقی می‌ماند.

منطق WooCommerce، پرداخت، LMS، SpotPlayer و integrationها نباید داخل theme قرار گیرد و باید در pluginهای مستقل نگهداری شود.

## روش انتقال UI

1. صفحات مهم Legacy در viewportهای مصوب screenshot و inventory می‌شوند.
2. tokenهای رنگ، فونت، spacing، radius و breakpoint استخراج می‌شوند.
3. header، footer و componentهای مشترک ابتدا ساخته می‌شوند.
4. templateهای home، page، archive، single، product، course، account، cart، checkout، search و 404 بازسازی می‌شوند.
5. متن و media معتبر منتقل می‌شود؛ layout metadata مربوط به Elementor منتقل نمی‌شود.
6. پذیرش با visual regression، RTL، accessibility و performance انجام می‌شود.

## پیامدها

- وابستگی و هزینه نگهداری Elementor حذف می‌شود.
- صفحات Elementor نیازمند بازسازی انتخابی هستند و migration یک‌کلیکی ندارند.
- تیم محتوا با Patterns و block editor کار خواهد کرد؛ patternهای locked/curated برای جلوگیری از به‌هم‌ریختگی طراحی ارائه می‌شوند.
- حذف Elementor از Legacy تا پایان migration و rollback ممنوع است.

## گزینه‌های ردشده

- انتقال مستقیم Elementor: بدهی و داده layout قدیمی را وارد Rebuild می‌کند.
- نصب تمیز Elementor در Rebuild: زمان اولیه را کم می‌کند ولی وابستگی runtime و ریسک regression را حفظ می‌کند.

## معیار بازبینی تصمیم

اگر prototype صفحه خانه و محصول نتواند نیاز واقعی تیم محتوا یا بودجه زمانی مصوب را برآورده کند، تصمیم فقط با ADR جدید و benchmark مستند بازبینی می‌شود.
