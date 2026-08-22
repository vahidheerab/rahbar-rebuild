# نقشه جامع بازسازی Rahbar روی WordPress تمیز

این سند مرجع تصمیم‌گیری و اجرای بازسازی سایت است. هدف، انتقال همه قابلیت‌ها و داده‌های معتبر سایت `legacy` به پروژه تمیز `rebuild` بدون انتقال آشفتگی‌های معماری، cache، فایل‌های رمزگذاری‌شده غیرضروری، تنظیمات قدیمی و بدهی فنی است.

برای دستورهای اجرایی Docker، انتقال نهایی و rollback به `MIGRATION-GUIDE.md` نیز مراجعه کنید.

## 1. هدف و محدوده

دو محیط مستقل داریم:

| محیط | آدرس | نقش |
|---|---|---|
| Legacy | `http://localhost:8081` | مرجع رفتار، داده و قابلیت‌های سایت فعلی |
| Rebuild | `http://localhost:8082` | WordPress تمیز و مقصد بازسازی |

نتیجه مطلوب:

- تجربه و قابلیت‌های ضروری سایت قبلی حفظ شوند.
- ظاهر فعلی با بیشترین شباهت قابل‌عمل حفظ شود؛ ساختار فنی، امنیت، کارایی و مشکلات responsive بازسازی و اصلاح شوند.
- بازطراحی ظاهری به فاز مستقل پس از Cutover موفق موکول شود.
- فقط داده‌های معتبر و موردنیاز منتقل شوند.
- وابستگی‌های منسوخ، تکراری یا رمزگذاری‌شده بدون توجیه وارد rebuild نشوند.
- انتقال نهایی قابل تکرار، قابل اندازه‌گیری و دارای rollback باشد.

## 2. اصل بنیادی: انتقال انتخابی، نه Clone کامل

کارهای ممنوع:

- کپی کامل `legacy/site/wp-content` به `rebuild/site/wp-content`؛
- import کامل دیتابیس legacy روی دیتابیس rebuild به‌عنوان راه‌حل نهایی؛
- انتقال همه افزونه‌ها صرفاً چون در سایت قدیمی نصب بوده‌اند؛
- جایگزینی URL با SQL خام روی داده‌های serialized؛
- انتقال secretها، cacheها، logها یا فایل‌های backup به سایت جدید؛
- تغییر مستقیم production بدون rehearsal و backup؛
- حذف legacy پیش از پایان دوره rollback.

اگر کل دیتابیس و `wp-content` کپی شوند، سایت جدید همان بدهی فنی و ناسازگاری‌های سایت قدیمی را به ارث می‌برد و دیگر clean rebuild محسوب نمی‌شود.

## 3. دسته‌بندی اجزای سایت

هر جزء باید در یکی از چهار گروه قرار گیرد:

| تصمیم | معنی |
|---|---|
| `REBUILD` | قابلیت با معماری یا کد تمیز دوباره ساخته شود |
| `REPLACE` | ابزار قدیمی با افزونه/سرویس مناسب‌تر جایگزین شود |
| `MIGRATE DATA ONLY` | فقط داده منتقل و پیاده‌سازی قدیمی کنار گذاشته شود |
| `RETIRE` | قابلیت یا داده بدون استفاده حذف شود |

هیچ افزونه، جدول یا قابلیت بدون ثبت یکی از این تصمیم‌ها وارد rebuild نمی‌شود.

## 4. فاز صفر: تثبیت و حفاظت

- [x] تفکیک فیزیکی `legacy` و `rebuild`
- [x] Docker Compose مستقل، network مستقل و database volume مستقل
- [x] dump اولیه legacy و checksum
- [x] اجرای legacy روی PHP 8.1
- [x] اجرای rebuild روی PHP 8.5
- [x] نصب SourceGuardian و ionCube فقط برای سازگاری legacy
- [x] اصلاح permalinkهای legacy برای اجرای Docker
- [ ] ثبت یک baseline جدید پس از پایدارشدن legacy
- [ ] ذخیره شمارش رکوردهای حساس برای reconciliation

قبل از هر migration داده، dump جدید با timestamp و SHA-256 تهیه شود. dumpها داخل `legacy/backups` می‌مانند و commit نمی‌شوند.

## 5. فاز اول: Inventory کامل Legacy

### 5.1 WordPress و محیط اجرا

موارد زیر ثبت شوند:

- نسخه WordPress، PHP و MySQL؛
- permalink، timezone، locale و multisite؛
- ثابت‌های مهم `wp-config.php` بدون ثبت secret؛
- cronها و WP-Cron؛
- اندازه دیتابیس و uploads؛
- محدودیت‌های حافظه، upload و execution time؛
- cacheها، object cache، CDN و reverse proxy؛
- email transport و صف‌ها.

### 5.2 قالب و UI

- قالب فعال و child theme؛
- فایل‌های تغییرکرده قالب؛
- template overrideهای WooCommerce/LMS؛
- header، footer، menu و mega menu؛
- فونت‌ها، رنگ‌ها، spacing، breakpoints و componentها؛
- صفحات Elementor و widgetهای اختصاصی؛
- CSS/JS سفارشی در theme، Elementor، Customizer و افزونه‌ها؛
- رفتار responsive، RTL و accessibility؛
- صفحه‌های 404، search، archive و taxonomy.

### 5.3 افزونه‌ها

برای هر افزونه ثبت شود:

| فیلد | توضیح |
|---|---|
| نام و نسخه | نسخه دقیق نصب‌شده |
| وضعیت | فعال، غیرفعال، network یا must-use |
| مالک قابلیت | چه نیاز کسب‌وکار را پوشش می‌دهد |
| داده | option، post type، taxonomy و جدول‌های ساخته‌شده |
| integration | API، webhook، cron، email و callback |
| امنیت | secret، سطح دسترسی و endpoint عمومی |
| سازگاری | WordPress 7، PHP 8.5 و MySQL 8.4 |
| تصمیم | rebuild، replace، migrate data only یا retire |

افزونه غیرفعال به معنی افزونه بی‌استفاده نیست؛ ممکن است داده تاریخی آن هنوز لازم باشد.

### 5.4 مدل داده WordPress

- همه `post_type`ها و تعداد رکوردهای هرکدام؛
- همه taxonomyها و termها؛
- meta keyهای مهم و مالک آن‌ها؛
- optionهای autoload و optionهای بسیار بزرگ؛
- جدول‌های خارج از هسته WordPress؛
- رابطه کاربران، سفارش‌ها، دوره‌ها و entitlementها؛
- attachmentهای فاقد فایل یا metadata؛
- URLهای قدیمی، absolute URLها و داده serialized؛
- duplicateها، orphanها و داده‌های آزمایشی.

### 5.5 کاربران و دسترسی

- تعداد کاربران بر اساس role؛
- role و capabilityهای سفارشی؛
- اطلاعات پروفایل و شماره موبایل؛
- روش login، OTP، reset password و session؛
- مدیران غیرفعال یا مشکوک؛
- consent، privacy و retention؛
- ارتباط کاربران با سفارش، دوره و لایسنس.

### 5.6 WooCommerce

- محصولات ساده، متغیر، مجازی و دانلودی؛
- category، tag، attribute و variation؛
- قیمت، تخفیف، stock و tax؛
- سفارش‌ها و نوع storage آن‌ها، شامل HPOS؛
- order itemها، refundها، couponها و noteها؛
- مشتریان و billing/shipping؛
- download permissionها؛
- payment gatewayها و callbackها؛
- shipping، invoice، SMS و email؛
- webhookها و scheduled actionها؛
- گزارش‌های مالی موردنیاز reconciliation.

### 5.7 LMS، دوره و ویدئو

- افزونه LMS و نسخه آن؛
- course، lesson، topic، quiz و certificate؛
- enrollment و progress؛
- مدرس و دانشجو؛
- پیش‌نیاز و drip content؛
- ارتباط سفارش با دسترسی دوره؛
- فایل‌ها و ویدئوهای خصوصی؛
- SpotPlayer: license، device، course mapping و callback؛
- entitlementهایی که نباید در migration گم شوند.

### 5.8 پرداخت و امور حساس

- درگاه‌های فعال و غیرفعال؛
- merchant ID و secretها فقط به‌صورت نام متغیر، نه مقدار؛
- callback URL، verify، refund و idempotency؛
- وضعیت‌های سفارش و transitionها؛
- logهای موردنیاز برای reconciliation؛
- رفتار callback تکراری، timeout و پرداخت ناموفق؛
- وابستگی‌های رمزگذاری‌شده با ionCube/SourceGuardian.

### 5.9 محتوا، SEO و بازاریابی

- نوشته، صفحه، landing page و محتوای Elementor؛
- title، description، canonical و schema؛
- redirectها و URLهای دارای رتبه؛
- sitemap، robots.txt و noindexها؛
- analytics، tag manager و conversion eventها؛
- فرم، lead و CRM؛
- لینک‌های داخلی و خارجی؛
- تصاویر، alt، caption، thumbnail و فایل‌های شکسته.

## 6. خروجی الزامی Inventory

یک ماتریس تصمیم ساخته شود:

| قابلیت | پیاده‌سازی فعلی | داده‌های مرتبط | تصمیم | مقصد جدید | ریسک | تست پذیرش |
|---|---|---|---|---|---|---|
| فروشگاه | WooCommerce | محصول/سفارش/مشتری | حفظ و پاک‌سازی | WooCommerce جدید | بالا | خرید و refund |
| صفحه‌ساز | Elementor | JSON و template | بررسی موردی | block/theme یا Elementor | متوسط | visual regression |
| LMS | نام افزونه فعلی | course/progress | بعد از inventory | تعیین شود | بالا | دسترسی دانشجو |
| پرداخت | درگاه فعلی | order meta/log | بازبینی امنیتی | plugin مستقل | بحرانی | callback/idempotency |
| SpotPlayer | integration فعلی | license/entitlement | تعیین شود | integration تمیز | بحرانی | صدور/لغو لایسنس |

تا زمانی که این ماتریس کامل نشده، انتقال افزونه‌ها شروع نمی‌شود.

## 7. فاز دوم: طراحی معماری Rebuild

### 7.1 اصول

- منطق کسب‌وکار داخل theme قرار نگیرد.
- هر integration اختصاصی plugin مستقل داشته باشد.
- secretها فقط از environment یا secret manager خوانده شوند.
- کدهای payment، LMS و SpotPlayer در چند افزونه پراکنده نشوند.
- برای تغییر معماری مهم ADR نوشته شود.
- dependencyها و imageها version pin شوند.
- cache یک optimization است، نه محل نگهداری داده اصلی.

### 7.2 انتخاب قالب و صفحه‌ساز

تصمیم معماری در 2026-08-18 ثبت شد: Rebuild با Block Theme اختصاصی، `theme.json` و Gutenberg Patterns ساخته می‌شود و Elementor/Elementor Pro/Hello Elementor و layout metadata آن‌ها منتقل نمی‌شوند. جزئیات و شرط بازبینی در `docs/adr/0001-replace-elementor-with-block-theme.md` است.

فهرست و تصمیم اولیه همه افزونه‌های Legacy در `PLUGIN-INVENTORY.md` نگهداری می‌شود؛ حذف از Legacy فقط پس از Gate مستند همان فایل مجاز است.

قبل از انتخاب نهایی ارزیابی شود:

- Gutenberg/Block Theme در برابر Elementor؛
- هزینه بازسازی templateها؛
- نیاز تیم محتوا؛
- performance و Core Web Vitals؛
- RTL و accessibility؛
- وابستگی به widgetهای اختصاصی؛
- امکان version control و تست.

### 7.3 قرارداد مدل داده

قبل از migration مشخص شود:

- post typeها و taxonomyهای مقصد؛
- meta keyهای رسمی و schema آن‌ها؛
- IDهای لازم برای حفظ رابطه؛
- mapping ID قدیم به جدید؛
- unique keyهای کاربران، سفارش‌ها و entitlementها؛
- policy برای داده deprecated؛
- version هر migration script.

## 8. فاز سوم: ترتیب بازسازی قابلیت‌ها

ترتیب پیشنهادی:

1. design system و componentهای عمومی؛
2. header، footer، navigation و layout؛
3. مدل محتوا و fieldها؛
4. صفحات عمومی، search و archive؛
5. ثبت‌نام، ورود، نقش و حساب کاربری؛
6. کاتالوگ محصول؛
7. cart و checkout؛
8. payment gatewayها؛
9. LMS و course rendering؛
10. enrollment و entitlement؛
11. SpotPlayer؛
12. email، SMS، CRM و webhook؛
13. SEO، redirect و analytics؛
14. cache، performance و security hardening.

هر قابلیت با داده ساختگی، تست پذیرش و rollback محلی کامل شود؛ سپس سراغ migration داده واقعی بروید.

## 9. استراتژی انتقال داده

دیتابیس Legacy محیط توسعه snapshot تقریبی یک ماه قبل است و فقط برای شناخت schema، توسعه script و rehearsal استفاده می‌شود. منبع حقیقت Cutover دیتابیس زنده production خواهد بود. انتقال نهایی باید full snapshot تازه، delta یک‌طرفه idempotent، freeze کوتاه و reconciliation مالی/entitlement داشته باشد؛ کپی مستقیم snapshot فعلی ممنوع است.

### 9.1 قواعد مشترک

- migration scriptها داخل version control باشند.
- اجرای دوباره نباید رکورد تکراری بسازد؛ migration باید idempotent باشد.
- هر اجرا دارای batch ID، timestamp و log باشد.
- روی serialized data فقط ابزار serialization-aware استفاده شود.
- migration مستقیم production ممنوع؛ ابتدا rehearsal روی snapshot.
- جدول mapping شناسه قدیم و جدید حفظ شود.
- پس از هر مرحله source count، destination count و exception count ثبت شود.

### 9.2 کاربران

ترتیب:

1. user اصلی؛
2. role/capability؛
3. profile meta معتبر؛
4. ارتباط با سفارش و دوره؛
5. تست login/reset password.

در صورت حفظ hash رمز WordPress، الگوریتم و سازگاری بررسی شود. کاربران مدیر جداگانه بازبینی شوند.

### 9.3 محتوا

- ابتدا taxonomy و term؛
- سپس post/page و author/date/status؛
- سپس meta و relationship؛
- سپس media mapping؛
- در پایان shortcode/block rendering و لینک داخلی.

محتوای Elementor کورکورانه منتقل نشود. صفحات مهم ترجیحاً بازسازی شوند؛ صفحات کم‌ریسک می‌توانند پس از validation داده منتقل شوند.

### 9.4 رسانه

- فهرست تمام `_wp_attached_file`ها استخراج شود.
- وجود فایل، اندازه و checksum بررسی شود.
- thumbnailهای لازم بازسازی شوند.
- attachmentهای فاقد فایل یا metadata گزارش شوند.
- absolute URLها به URL مقصد تبدیل شوند.
- فایل خصوصی خارج از public uploads نگه‌داری شود.
- alt و caption حفظ شوند.

### 9.5 WooCommerce

محصول، variation، order و customer با رابطه صحیح منتقل شوند. موارد زیر جداگانه reconcile شوند:

- تعداد محصول و variation؛
- تعداد سفارش به تفکیک status؛
- مجموع مبلغ سفارش و refund؛
- coupon usage؛
- download permission؛
- order note و transaction ID؛
- ارتباط سفارش با کاربر و course entitlement.

در migration تاریخی، هیچ email، SMS، webhook یا callback واقعی نباید دوباره ارسال شود.

### 9.6 LMS و SpotPlayer

ابتدا مدل مقصد تثبیت شود، سپس:

1. course/lesson/topic؛
2. instructor؛
3. enrollment؛
4. progress/completion؛
5. quiz/certificate در صورت نیاز؛
6. order-to-course mapping؛
7. SpotPlayer license و entitlement؛
8. reconciliation نمونه‌ای و کامل.

صدور مجدد ناخواسته لایسنس یا ارسال notification در زمان import باید غیرفعال باشد.

### 9.7 SEO و URL

- URLهای ارزشمند تا حد امکان حفظ شوند.
- برای URL تغییرکرده redirect 301 ثبت شود.
- canonical به production مقصد اشاره کند، نه localhost.
- noindex محیط توسعه تا cutover حفظ شود.
- sitemap و schema بعد از انتقال اعتبارسنجی شوند.
- redirect chain و loop وجود نداشته باشد.

## 10. تست‌ها و معیار پذیرش

### 10.1 تست فنی

- healthcheck دیتابیس؛
- خطاهای PHP، WordPress و browser console؛
- broken link و media 404؛
- cron و Action Scheduler؛
- email/SMS در sandbox؛
- API و webhook با mock/sandbox؛
- backup و restore واقعی؛
- security scan و permissionها.

### 10.2 تست کسب‌وکار

- ثبت‌نام و ورود؛
- reset password و OTP؛
- مشاهده محصول و دوره؛
- افزودن به سبد و checkout؛
- پرداخت موفق، ناموفق و تکراری؛
- refund و تغییر وضعیت سفارش؛
- ایجاد enrollment؛
- صدور/مشاهده/لغو SpotPlayer license؛
- دسترسی کاربر قدیمی به خرید و دوره قبلی؛
- فرم، lead، email و SMS.

### 10.3 تست داده

برای هر entity:

| کنترل | مثال |
|---|---|
| Count | تعداد کاربران/محصولات/سفارش‌ها |
| Sum | مجموع مبلغ سفارش‌ها و refundها |
| Relationship | سفارش به کاربر، دوره به دانشجو |
| Sample | نمونه تصادفی و نمونه‌های مرزی |
| Exception | رکوردهای ردشده با علت |
| Duplicate | unique key و mapping |

## 11. Rehearsal انتقال

حداقل دو اجرای کامل آزمایشی انجام شود:

1. snapshot تازه legacy؛
2. reset کنترل‌شده محیط staging؛
3. اجرای تمام migrationها؛
4. ثبت مدت هر مرحله؛
5. reconciliation؛
6. smoke test؛
7. ثبت خطا و اصلاح script؛
8. اجرای مجدد برای اثبات idempotency؛
9. تمرین rollback.

خروجی rehearsal باید زمان downtime موردنیاز cutover را مشخص کند.

## 12. Cutover نهایی

### قبل از پنجره انتقال

- TTL دامنه کاهش یابد.
- backup و restore تأیید شود.
- مسئول اجرا و مسئول rollback مشخص شوند.
- checklist و کانال ارتباطی آماده باشد.
- secretهای production در مقصد تنظیم شوند.
- gatewayها تا لحظه مناسب در sandbox بمانند.

### در پنجره انتقال

1. legacy production به maintenance/read-only برود.
2. زمان freeze ثبت شود.
3. dump نهایی و media delta گرفته شود.
4. checksum و سلامت backup تأیید شود.
5. migration نهایی اجرا شود.
6. reconciliation بحرانی انجام شود.
7. URLها با ابزار serialization-aware به دامنه production تبدیل شوند.
8. cacheها بازسازی شوند.
9. smoke test login، checkout، payment، entitlement و email اجرا شود.
10. DNS یا reverse proxy به rebuild تغییر کند.
11. log و metricها به‌صورت پیوسته پایش شوند.

## 13. Rollback

شرایط rollback:

- اختلال گسترده login یا checkout؛
- خرابی payment callback؛
- اختلاف داده مالی؛
- از دست‌رفتن entitlement یا SpotPlayer license؛
- خطای شدید performance/availability؛
- آسیب امنیتی یا افشای داده.

مراحل:

1. نوشتن روی rebuild متوقف شود.
2. route/DNS به legacy برگردد.
3. داده ایجادشده بعد از cutover جداگانه export شود.
4. هیچ volume، dump یا log حذف نشود.
5. اختلاف‌ها برای merge بعدی ثبت شوند.
6. incident report و تصمیم اجرای مجدد تهیه شود.

## 14. Definition of Done

بازسازی وقتی کامل است که:

- تمام قابلیت‌های inventory تصمیم و مالک داشته باشند.
- هیچ قابلیت بحرانی فقط به‌صورت شفاهی تأیید نشده باشد.
- کاربران، سفارش‌ها، محصولات و entitlementها reconcile شده باشند.
- payment و SpotPlayer در سناریوهای مرزی تست شده باشند.
- URL، SEO و redirectها تأیید شده باشند.
- performance و accessibility به معیار توافق‌شده برسند.
- backup/restore و rollback آزمایش شده باشند.
- مستندات عملیات و توسعه کامل باشند.
- legacy تا پایان دوره نگه‌داری توافق‌شده قابل بازگشت باشد.

## 15. وضعیت اجرایی

| فاز | وضعیت | خروجی مورد انتظار |
|---|---|---|
| تثبیت محیط‌ها | در حال تکمیل | legacy و rebuild پایدار |
| Inventory | در حال انجام | inventory افزونه، کد و UI انجام شده؛ مدل داده و integrationها باز هستند |
| طراحی معماری | در حال انجام | ADRها و مدل مقصد؛ ADR قالب ثبت شده |
| بازسازی UI | در حال انجام | Block Theme Rahbar 0.7.4 فعال؛ parity صفحه Home تکمیل و Product prototype موجود است |
| بازسازی commerce | شروع نشده | محصول تا پرداخت |
| بازسازی LMS/SpotPlayer | شروع نشده | course تا entitlement |
| migration scriptها | شروع نشده | انتقال idempotent |
| rehearsal | شروع نشده | گزارش زمان و reconciliation |
| QA/Exit Gate | شروع نشده | تأیید فنی و کسب‌وکار |
| Cutover | شروع نشده | انتقال production |
| پایش/بستن | شروع نشده | گزارش نهایی و archive |

## 16. قدم بعدی قطعی

قدم بعدی: اجرای read-only مورد `INV-DATA-001` برای ثبت post typeها، taxonomyها، statusها و meta keyهای سفارشی Legacy، سپس تکمیل مقصدهای پایه‌ای که لینک‌های Header/Footer به آن‌ها وابسته‌اند. هیچ داده واقعی تا عبور Gate مربوطه منتقل نخواهد شد.
