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

### قید شباهت بصری تا زمان Cutover

به تصمیم مالک پروژه در 2026-08-18، فاز نخست Rebuild «بازطراحی ظاهری» نیست. خروجی قابل جایگزینی روی هاست باید بیشترین شباهت عملی به Legacy را در ساختار، ترتیب سکشن‌ها، رنگ، typography، تصویر، فاصله‌گذاری و جزئیات بصری داشته باشد. تفاوت فقط زمانی مجاز است که برای رفع نقص فنی، overflow، accessibility، performance، امنیت یا حذف وابستگی Elementor لازم باشد.

بازطراحی و تغییر هویت بصری به فاز مستقلی پس از Cutover موفق منتقل می‌شود. prototypeهای اولیه‌ای که صرفاً ساختار فنی را اثبات کرده‌اند، خروجی نهایی تلقی نمی‌شوند و باید پیش از پذیرش UI با baseline تصویری Legacy تطبیق داده شوند.

## روش انتقال UI

1. صفحات مهم Legacy در viewportهای مصوب screenshot و inventory می‌شوند.
2. tokenهای رنگ، فونت، spacing، radius و breakpoint استخراج می‌شوند.
3. header، footer و componentهای مشترک ابتدا ساخته می‌شوند.
4. templateهای home، page، archive، single، product، course، account، cart، checkout، search و 404 بازسازی می‌شوند.
5. متن و media معتبر منتقل می‌شود؛ layout metadata مربوط به Elementor منتقل نمی‌شود.
6. پذیرش با visual regression در برابر Legacy، RTL، accessibility و performance انجام می‌شود؛ هر اختلاف بصری عمدی باید مستند و دارای دلیل فنی باشد.

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
