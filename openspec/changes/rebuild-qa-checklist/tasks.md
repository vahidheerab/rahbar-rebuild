# چک‌لیست اجرایی بازسازی Rahbar

مرجع وضعیت روزانه همین فایل است. برای هر مورد تکمیل‌شده، در انتهای خط `— Evidence: <path/link>, YYYY-MM-DD` ثبت کنید. اطلاعات محرمانه، dump، uploads یا داده شخصی را commit نکنید.

وضعیت پیشنهادی در یادداشت کنار هر task: `TODO`، `DOING`، `BLOCKED: دلیل`، `N/A: دلیل`. فقط تست واقعاً قبول‌شده را `[x]` کنید.

## 1. کنترل نشست و نقطه ادامه

- [ ] 1.1 شناسه اجرای جاری را با قالب `YYYYMMDD-HHMM-<phase>` تعیین و پوشه Evidence محلی/امن را ثبت کن
- [ ] 1.2 تاریخ، اپراتور، branch/commit، محیط هدف و هدف نشست را در Session Log انتهای فایل ثبت کن
- [ ] 1.3 آخرین checkpoint سالم و آخرین backup/checksum معتبر را ثبت کن
- [ ] 1.4 blockerهای باز، مالک هر blocker و شرط رفع آن را ثبت کن
- [ ] 1.5 دقیقاً یک «اقدام بعدی» قابل اجرا برای نشست بعد تعیین کن
- [ ] 1.6 در پایان نشست، وضعیت فاز را در `MIGRATION-GUIDE.md` و `REBUILD-ROADMAP.md` همگام کن

## 2. Baseline و جداسازی زیرساخت

- [x] 2.1 `INF-CMP-001` اعتبار `docker compose -f legacy/compose.yaml config` را تأیید کن — Evidence: اجرای موفق 2026-08-18
- [x] 2.2 `INF-CMP-002` اعتبار `docker compose -f rebuild/compose.yaml config` را تأیید کن — Evidence: اجرای موفق 2026-08-18
- [x] 2.3 `INF-DB-001` healthy بودن MySQL Legacy را تأیید کن — Evidence: `rahbar-legacy-db-1` healthy، 2026-08-18
- [x] 2.4 `INF-DB-002` healthy بودن MySQL Rebuild را تأیید کن — Evidence: `rahbar-rebuild-db-1` healthy، 2026-08-18
- [x] 2.5 `INF-WEB-001` پاسخ WordPress Legacy روی `http://localhost:8081` را تأیید کن — Evidence: container up، 2026-08-18
- [x] 2.6 `INF-WEB-002` پاسخ WordPress Rebuild روی `http://localhost:8082` را تأیید کن — Evidence: container up، 2026-08-18
- [x] 2.7 `INF-DBA-001` پاسخ HTTP 200 پنل Legacy روی `http://localhost:8083` را تأیید کن — Evidence: HTTP 200، 2026-08-18
- [x] 2.8 `INF-DBA-002` پاسخ HTTP 200 پنل Rebuild روی `http://localhost:8084` را تأیید کن — Evidence: HTTP 200، 2026-08-18
- [x] 2.9 `INF-ISO-001` نام project، container، network و volume دو Compose را استخراج و عدم اشتراک را ثبت کن — Evidence: `docs/infrastructure/compose-isolation-evidence.md`، cross-network DNS negative test و HTTP/health probes، 2026-08-18
- [ ] 2.10 `INF-ISO-002` Rebuild را restart کن و ثابت کن Legacy بدون restart و بدون تغییر داده در دسترس می‌ماند
- [ ] 2.11 `INF-ISO-003` Legacy را restart کن و ثابت کن Rebuild بدون restart و بدون تغییر داده در دسترس می‌ماند
- [ ] 2.12 `INF-VER-001` نسخه دقیق WordPress، PHP، MySQL و image digest هر دو محیط را ثبت کن
- [ ] 2.13 `INF-LOG-001` logهای startup مربوط به PHP، Apache، WordPress و MySQL را از خطای بحرانی پاک تأیید کن
- [ ] 2.14 `INF-SEC-001` نبود secrets، `.env`، dump، uploads، cache و log در فایل‌های tracked را تأیید کن
- [ ] 2.15 `INF-BKP-001` dump تازه Legacy و SHA-256 آن را در محل امن خارج از Git ثبت کن
- [ ] 2.16 `INF-BKP-002` restore آزمایشی همان dump را در دیتابیس disposable اجرا و صحت login/query را تأیید کن
- [ ] 2.17 `GATE-BASELINE` همه تست‌های اجباری بخش 2 قبول و baseline امضا شده باشد

## 3. Inventory کامل Legacy

- [ ] 3.1 `INV-WP-001` تنظیمات site URL، locale، timezone، permalink، cron و email را ثبت کن
- [ ] 3.2 `INV-THEME-001` theme/child-theme فعال، نسخه، license و customizationها را ثبت کن
- [ ] 3.3 `INV-UI-001` sitemap صفحات عمومی و template مورد استفاده هرکدام را ثبت کن
- [ ] 3.4 `INV-UI-002` header، footer، menu، widget، popup و responsive breakpointها را screenshot بگیر
- [x] 3.5 `INV-PLG-001` فهرست plugin و mu-plugin با وضعیت، نسخه، مالک قابلیت و تصمیم migrate/replace/retire بساز — Evidence: `PLUGIN-INVENTORY.md`، تطبیق read-only با `active_plugins` و جدول‌های Legacy، 2026-08-18
- [x] 3.6 `INV-CODE-001` shortcode، block، hook، snippet و کد اختصاصی فعال را فهرست کن — Evidence: `docs/baseline/legacy-custom-code-inventory.md`، metadata read-only دیتابیس و static scan، 2026-08-18
- [ ] 3.7 `INV-DATA-001` post type، taxonomy، status و meta key سفارشی را ثبت کن — DOING: شمارش post type/status، taxonomyها و خانواده‌های پرریسک meta در `docs/baseline/legacy-data-model-inventory.md` ثبت شد؛ دسته‌بندی کامل meta بر اساس مالکیت باز است، 2026-08-22
- [ ] 3.8 `INV-DATA-002` همه جدول‌ها، engine، collation، حجم و مالک افزونه را ثبت کن
- [ ] 3.9 `INV-USER-001` role، capability، تعداد کاربران و مسیرهای registration/login/reset/profile را ثبت کن
- [ ] 3.10 `INV-WC-001` تنظیمات WooCommerce، currency، tax، shipping، coupon، email و order statusها را ثبت کن
- [ ] 3.11 `INV-WC-002` نوع محصولات، variation، downloadable file، stock و attributeها را ثبت کن
- [ ] 3.12 `INV-PAY-001` gatewayها، callback/webhookها، secretها، idempotency و reconciliation را بدون افشای secret ثبت کن
- [ ] 3.13 `INV-LMS-001` دوره، درس، مدرس، enrollment، progress، certificate و prerequisiteها را ثبت کن
- [ ] 3.14 `INV-SPOT-001` قرارداد SpotPlayer، license/entitlement و failure modeها را ثبت کن
- [ ] 3.15 `INV-SEO-001` title/meta، schema، canonical، sitemap، robots و redirectها را ثبت کن
- [ ] 3.16 `INV-EXT-001` cron، API، webhook، SMTP، analytics و سرویس‌های بیرونی را ثبت کن
- [ ] 3.17 `INV-MEDIA-001` مسیرها، حجم، mime type، private media و broken attachmentها را ثبت کن
- [ ] 3.18 `INV-COUNT-001` شمارش baseline همه entityهای حساس و مجموع‌های مالی را در جدول reconciliation ثبت کن
- [x] 3.20 `INV-UI-003` visual inventory صفحه Home را در سه viewport ثبت و mapping مقصد را تعریف کن — Evidence: `docs/baseline/legacy-home-visual-inventory.md` و سه screenshot، 2026-08-18
- [x] 3.22 `INV-UI-004` visual inventory صفحه Product نمونه را در سه viewport ثبت و قرارداد قیمت/CTA/course meta را تعریف کن — Evidence: `docs/baseline/legacy-product-visual-inventory.md` و سه screenshot، 2026-08-18
- [ ] 3.23 `GATE-INVENTORY` هر قابلیت Legacy مالک، تصمیم مقصد، معیار پذیرش و وابستگی مشخص داشته باشد

## 4. قرارداد معماری و ماتریس parity

- [ ] 4.1 `ARC-SCOPE-001` قابلیت‌های must-have، deferred و retired را با تأیید کسب‌وکار جدا کن
- [ ] 4.2 `ARC-MAP-001` برای هر قابلیت Legacy مقصد Rebuild و روش rebuild/replace/migrate را ثبت کن
- [ ] 4.3 `ARC-DATA-001` قرارداد ID، slug، status، taxonomy، meta و serialization مقصد را تعریف کن
- [ ] 4.4 `ARC-AUTH-001` قرارداد role/capability و اصل least privilege را تعریف کن
- [ ] 4.5 `ARC-WC-001` source of truth سفارش، پرداخت، refund و entitlement را تعریف کن
- [ ] 4.6 `ARC-URL-001` قرارداد URL، redirect، canonical و سیاست حفظ backlink را تعریف کن
- [ ] 4.7 `ARC-ERR-001` قرارداد logging، correlation ID، alert و نمایش خطا به کاربر را تعریف کن
- [ ] 4.8 `ARC-PII-001` داده شخصی، retention، export/erase و redaction را تعریف کن
- [x] 4.9 `ARC-ADR-001` تصمیم قالب Block Theme و عدم انتقال Elementor را ADR کن — Evidence: `docs/adr/0001-replace-elementor-with-block-theme.md`، 2026-08-18
- [ ] 4.10 `ARC-TEST-001` برای هر سطر parity حداقل یک test ID و expected result تعیین کن
- [ ] 4.11 `GATE-ARCH` معماری، مدل داده و تفاوت‌های عمدی پیش از پیاده‌سازی تأیید شده باشند

## 5. پایه Rebuild و مدیریت WordPress

- [x] 5.0 `RB-THEME-001` اسکلت Block Theme اختصاصی Rahbar را ایجاد، اعتبارسنجی و روی Rebuild فعال کن — Evidence: theme `rahbar` v0.1.0، HTTP 200 و بدون PHP error در log، 2026-08-18
- [x] 5.1 `RB-WP-001` نصب تمیز، locale فارسی، timezone تهران و permalink مصوب را تأیید کن — Evidence: `docs/baseline/rebuild-base-pages-initialization.md`، initializer تکرارپذیر و HTTP 200 مسیرهای پایه، 2026-08-22
- [ ] 5.2 `RB-WP-002` Site Health را بدون issue بحرانی و با نسخه‌های pinned ثبت کن
- [ ] 5.3 `RB-ADM-001` ورود/خروج مدیر و انقضای session را تست کن
- [ ] 5.4 `RB-ADM-002` ایجاد، ویرایش، preview، publish، schedule، trash و restore نوشته را تست کن
- [ ] 5.5 `RB-ADM-003` upload تصویر، تولید thumbnail، alt text، و حذف attachment را تست کن
- [ ] 5.6 `RB-ADM-004` menu، widget/site editor و تنظیمات theme را تست کن
- [ ] 5.7 `RB-CRON-001` اجرای WP-Cron و jobهای زمان‌بندی‌شده را بدون duplicate تأیید کن
- [ ] 5.8 `RB-MAIL-001` ارسال ایمیل آزمایشی، From/Reply-To، HTML و ثبت failure را تست کن
- [ ] 5.9 `RB-CACHE-001` purge cache پس از تغییر محتوا و عدم نمایش محتوای stale را تست کن
- [ ] 5.10 `RB-ERR-001` نبود warning/notice/fatal در مسیرهای اصلی و logها را تأیید کن
- [x] 5.11 `RB-THEME-002` design tokenها و Header/Footer واکنش‌گرای قالب Rahbar را تثبیت کن — Evidence: theme `rahbar` v0.2.0، سه screenshot در 1440/768/375، HTTP 200 و گزارش `docs/baseline/rebuild-header-footer-smoke-test.md`، 2026-08-18
- [ ] 5.12 `GATE-FOUNDATION` مدیریت پایه Rebuild برای توسعه قابلیت‌ها پایدار باشد

## 6. UI، محتوا و دسترس‌پذیری

- [ ] 6.1 `UI-HOME-001` صفحه خانه desktop/tablet/mobile را با baseline مصوب مقایسه کن
- [ ] 6.2 `UI-NAV-001` header، menu، submenu، CTA و navigation کیبورد را تست کن
- [ ] 6.3 `UI-FOOT-001` footer، لینک‌ها، اطلاعات تماس و social linkها را تست کن
- [ ] 6.4 `UI-PAGE-001` template صفحه عمومی، breadcrumb و hierarchy heading را تست کن — DOING: template عمومی و Contact واکنش‌گرا ساخته و heading hierarchy بررسی شد؛ breadcrumb و سایر نمونه‌ها باز است، Evidence: `docs/baseline/rebuild-contact-blog-prototype.md`، 2026-08-22
- [x] 6.5 `UI-BLOG-001` archive، pagination، single، category، tag و author را تست کن — templateهای home/single/archive/category/tag/author و loop/pagination مشترک تکمیل شد؛ تست داده‌محور پس از migration تکرار می‌شود، Evidence: `docs/baseline/rebuild-blog-completion-report.md`، 2026-08-22
- [x] 6.6 `UI-SEARCH-001` جست‌وجوی نتیجه‌دار، بدون نتیجه، فارسی/لاتین و ورودی خاص را تست کن — Evidence: `docs/baseline/rebuild-search-404-completion-report.md`، 2026-08-22
- [x] 6.7 `UI-404-001` صفحه 404، لینک بازگشت و status code واقعی 404 را تست کن — missing URL با HTTP 404 واقعی، Evidence: `docs/baseline/rebuild-search-404-completion-report.md`، 2026-08-22
- [ ] 6.8 `UI-FORM-001` validation سمت کاربر/سرور، پیام فارسی، success و duplicate submit فرم‌ها را تست کن
- [x] 6.9 `UI-RTL-001` RTL، فونت، اعداد، قیمت، تاریخ و mixed-direction text را تست کن — با مقاله/محصول واقعی و Shop/Blog بررسی شد، Evidence: `docs/baseline/rebuild-public-sample-content-report.md`، 2026-08-22
- [x] 6.10 `UI-RESP-001` viewportهای 320، 375، 768، 1024 و 1440 را بدون overflow تست کن — ماتریس ۸ مسیر × ۵ viewport پاس شد، Evidence: `scripts/rebuild/responsive.spec.js` و `docs/baseline/rebuild-public-sample-content-report.md`، 2026-08-22
- [ ] 6.11 `UI-BRW-001` آخرین Chrome، Firefox و Edge و یک مرورگر موبایل را smoke test کن
- [x] 6.12 `A11Y-KBD-001` کل مسیرهای اصلی را فقط با keyboard و focus visible تست کن — focus عمومی، فرم تماس، جدول و carousel بررسی شد، Evidence: `docs/baseline/rebuild-accessibility-report.md`، 2026-08-22
- [x] 6.13 `A11Y-SR-001` label، landmark، heading، alt و announcement خطا را با screen reader بررسی کن — قرارداد معنایی با axe/ARIA roleها پاس شد؛ بازآزمایی دستی NVDA/VoiceOver در pre-launch باقی است، Evidence: `docs/baseline/rebuild-accessibility-report.md`، 2026-08-22
- [x] 6.14 `A11Y-CON-001` contrast، zoom 200% و prefers-reduced-motion را بررسی کن — Evidence: `docs/baseline/rebuild-accessibility-report.md`، 2026-08-22
- [x] 6.15 `UI-HOME-BEN-001` Benefits چهارگانه Home را با layout چهار/دو/یک ستونه پیاده‌سازی و smoke test کن — Evidence: theme `rahbar` v0.3.0، سه screenshot full-page و گزارش `docs/baseline/rebuild-home-benefits-smoke-test.md`، 2026-08-18
- [x] 6.16 `UI-HOME-PAR-001` صفحه Home را سکشن‌به‌سکشن با Legacy در desktop/tablet/mobile تطبیق بده — Evidence: قالب Rahbar `0.7.4`، screenshot کامل 1440/768/375 و `docs/baseline/rebuild-home-final-parity-report.md`، 2026-08-22
- [x] 6.17 `UI-CONTACT-PAR-001` صفحه تماس را با ساختار Legacy، اطلاعات عمومی، فرم امن و layout واکنش‌گرا تکمیل کن — Evidence: `docs/baseline/legacy-contact-visual-inventory.md` و `docs/baseline/rebuild-contact-parity-report.md`، 2026-08-22
- [ ] 6.18 `GATE-UI` همه templateهای must-have و معیارهای accessibility مصوب قبول باشند

## 7. کاربران، احراز هویت و حریم خصوصی

- [ ] 7.1 `AUTH-REG-001` ثبت‌نام موفق، duplicate email/username و validation را تست کن
- [ ] 7.2 `AUTH-LOG-001` login درست، رمز غلط، کاربر ناموجود، logout و redirect را تست کن
- [ ] 7.3 `AUTH-RST-001` forgot/reset password، token منقضی و reuse token را تست کن
- [ ] 7.4 `AUTH-ROLE-001` دسترسی مثبت و منفی همه roleهای مصوب را تست کن
- [ ] 7.5 `AUTH-SES-001` remember-me، expiry، logout همه sessionها و cookie flags را تست کن
- [ ] 7.6 `AUTH-PRO-001` مشاهده/ویرایش profile و validation اطلاعات حساس را تست کن
- [ ] 7.7 `AUTH-BRU-001` rate limit یا کنترل brute-force و پیام بدون user enumeration را تست کن
- [ ] 7.8 `PRIV-EXP-001` export داده شخصی و completeness خروجی را تست کن
- [ ] 7.9 `PRIV-DEL-001` erase/anonymize داده طبق retention مالی و قانونی را تست کن
- [ ] 7.10 `GATE-AUTH` نقش‌ها، sessionها و عملیات privacy تأیید شده باشند

## 8. WooCommerce و مسیر خرید

- [ ] 8.1 `WC-CAT-001` shop/category/tag/filter/sort/pagination و empty state را تست کن
- [ ] 8.2 `WC-PROD-001` محصول simple، variable، virtual و downloadable را تست کن
- [ ] 8.3 `WC-PRICE-001` قیمت عادی/فروش، مالیات، rounding و نمایش تومان/ریال را تست کن
- [ ] 8.4 `WC-STOCK-001` موجودی، backorder، out-of-stock و concurrency آخرین موجودی را تست کن
- [ ] 8.5 `WC-CART-001` add/update/remove cart، persistence و mini-cart را تست کن
- [ ] 8.6 `WC-COUP-001` coupon معتبر، منقضی، محدودیت کاربر/محصول و stacking را تست کن
- [ ] 8.7 `WC-CHK-001` checkout مهمان/عضو، validation آدرس و terms consent را تست کن
- [ ] 8.8 `WC-SHIP-001` روش و هزینه حمل برای سناریوهای مصوب را تست کن
- [ ] 8.9 `WC-ORD-001` ایجاد سفارش، شماره، line item، totals و status اولیه را تست کن
- [ ] 8.10 `WC-EMAIL-001` ایمیل‌های مشتری/مدیر برای transitionهای سفارش را تست کن
- [ ] 8.11 `WC-ACC-001` لیست/جزئیات سفارش، download و دسترسی مالک/غیرمالک را تست کن
- [ ] 8.12 `WC-CAN-001` cancel، failed، refund جزئی/کامل و بازگشت موجودی را تست کن
- [ ] 8.13 `WC-ADM-001` جست‌وجو، filter، note، edit مجاز و export سفارش در مدیریت را تست کن
- [x] 8.14 `WC-PROD-PROT-001` Product template نمونه را با داده واقعی WooCommerce، visual parity و CTA قابل دسترس mobile بساز — Evidence: theme `rahbar` v0.5.0، WooCommerce runtime 11.0.1، سه screenshot و گزارش `docs/baseline/rebuild-product-prototype-report.md`، 2026-08-18
- [ ] 8.15 `GATE-WC` مسیر کامل browse→checkout→order→email→account قبول باشد

## 9. پرداخت و callback

- [ ] 9.1 `PAY-INIT-001` ساخت درخواست پرداخت با مبلغ، order ID و callback صحیح را تست کن
- [ ] 9.2 `PAY-SUC-001` پرداخت موفق و transition دقیق سفارش را در sandbox تست کن
- [ ] 9.3 `PAY-CAN-001` انصراف کاربر و بازگشت امن به سفارش را تست کن
- [ ] 9.4 `PAY-FAIL-001` پاسخ ناموفق gateway و امکان retry بدون سفارش تکراری را تست کن
- [ ] 9.5 `PAY-TMO-001` timeout/network failure و recovery قابل فهم را تست کن
- [ ] 9.6 `PAY-CBK-001` callback معتبر، signature/token و مبلغ/سفارش را verify کن
- [ ] 9.7 `PAY-CBK-002` callback تکراری را تست و idempotency را اثبات کن
- [ ] 9.8 `PAY-CBK-003` callback دستکاری‌شده، مبلغ نابرابر و order ناموجود را رد کن
- [ ] 9.9 `PAY-RACE-001` callback همزمان و refresh صفحه را بدون double fulfillment تست کن
- [ ] 9.10 `PAY-LOG-001` log تراکنش را با correlation ID و بدون secret/PII حساس بررسی کن
- [ ] 9.11 `PAY-REC-001` reconciliation مبلغ، transaction ID، سفارش و entitlement را تست کن
- [ ] 9.12 `PAY-REF-001` refund و لغو entitlement مطابق قرارداد را تست کن
- [ ] 9.13 `GATE-PAY` مالک کسب‌وکار سناریوهای موفق/شکست/تکرار/refund را تأیید کند

## 10. LMS، دوره و SpotPlayer

- [ ] 10.1 `LMS-CAT-001` archive/search/filter دوره و صفحه دوره را تست کن
- [ ] 10.2 `LMS-BUY-001` خرید دوره و ایجاد enrollment/entitlement دقیقاً یک‌بار را تست کن
- [ ] 10.3 `LMS-ACC-001` کاربر مجاز درس/ویدئو را ببیند و غیرمجاز پاسخ امن بگیرد
- [ ] 10.4 `LMS-PRO-001` شروع، تکمیل، resume و درصد progress را تست کن
- [ ] 10.5 `LMS-REQ-001` prerequisite، ترتیب درس و drip schedule را تست کن
- [ ] 10.6 `LMS-CER-001` صدور/دانلود certificate و دسترسی مالک را تست کن
- [ ] 10.7 `LMS-REF-001` اثر cancel/refund روی enrollment طبق قرارداد را تست کن
- [ ] 10.8 `SPOT-LIC-001` ایجاد/بازیابی license و اتصال آن به کاربر/دوره را تست کن
- [ ] 10.9 `SPOT-DUP-001` retry و callback تکراری SpotPlayer را idempotent تست کن
- [ ] 10.10 `SPOT-ERR-001` timeout، API error، credential غلط و recovery را تست کن
- [ ] 10.11 `LMS-MOB-001` تجربه درس و player را روی موبایل و اتصال کند تست کن
- [ ] 10.12 `GATE-LMS` خرید→دسترسی→پیشرفت→refund برای دوره قبول باشد

## 11. SEO، لینک‌ها و یکپارچه‌سازی‌ها

- [ ] 11.1 `SEO-META-001` title، description، canonical و robots صفحات نمونه را مقایسه کن
- [ ] 11.2 `SEO-SCH-001` schema JSON-LD را برای home/article/product/course validate کن
- [ ] 11.3 `SEO-MAP-001` sitemap index و sitemapهای فرزند را از URL خطادار/غیرمجاز پاک تأیید کن
- [ ] 11.4 `SEO-ROB-001` robots.txt و noindex محیط staging را بررسی و برنامه production را ثبت کن
- [ ] 11.5 `SEO-RED-001` redirect map URLهای Legacy را بدون loop و chain بیش از یک hop تست کن
- [ ] 11.6 `SEO-404-001` نمونه URLهای پرترافیک و backlinkها را برای 200/301/404 صحیح تست کن
- [ ] 11.7 `SEO-OG-001` OpenGraph/Twitter card و تصویر share صفحات نمونه را بررسی کن
- [ ] 11.8 `INT-SMTP-001` تحویل ایمیل، SPF/DKIM/DMARC محیط مقصد را پیش از production تأیید کن
- [ ] 11.9 `INT-ANA-001` analytics/consent و عدم ارسال PII ناخواسته را تست کن
- [ ] 11.10 `INT-WHK-001` webhookهای خروجی را برای auth، retry، idempotency و failure log تست کن
- [ ] 11.11 `GATE-SEO` redirect، indexability و integrationهای must-have تأیید شده باشند

## 12. امنیت، کارایی و پایداری

- [ ] 12.1 `SEC-TLS-001` برنامه HTTPS production، redirect و mixed content را تست کن
- [ ] 12.2 `SEC-HDR-001` headerهای امنیتی مصوب و cookieهای Secure/HttpOnly/SameSite را بررسی کن
- [ ] 12.3 `SEC-CSRF-001` nonce/CSRF همه عملیات state-changing نمونه را تست کن
- [ ] 12.4 `SEC-XSS-001` escape/sanitize ورودی و خروجی فرم‌ها و فیلدهای rich text را تست کن
- [ ] 12.5 `SEC-SQL-001` queryهای سفارشی را برای prepared statement و privilege محدود بازبینی کن
- [ ] 12.6 `SEC-UPL-001` upload type/size، filename، اجرای فایل و دسترسی private media را تست کن
- [ ] 12.7 `SEC-API-001` REST endpointها را برای auth، permission callback و data exposure تست کن
- [ ] 12.8 `SEC-DBG-001` خاموش بودن debug display، directory listing و editor فایل در مقصد را تأیید کن
- [ ] 12.9 `SEC-DEP-001` نسخه و vulnerability افزونه/theme سفارشی و ثالث را ثبت و blocker بحرانی را رفع کن
- [ ] 12.10 `PERF-WEB-001` TTFB/LCP/CLS/INP صفحات home/product/course/checkout را در cold/warm cache ثبت کن
- [ ] 12.11 `PERF-DB-001` slow query و query count مسیرهای اصلی را ثبت و regression بحرانی را رفع کن
- [ ] 12.12 `PERF-LOAD-001` بار همزمان مصوب را برای browse، login و checkout بدون پرداخت واقعی اجرا کن
- [ ] 12.13 `PERF-CACHE-001` cache hit/miss، purge و عدم cache صفحه حساب/cart/checkout را تست کن
- [ ] 12.14 `REL-CRON-001` اجرای jobها، lock، retry و جلوگیری از اجرای تکراری را تست کن
- [ ] 12.15 `REL-FAIL-001` restart WordPress/MySQL و recovery بدون corruption را تست کن
- [ ] 12.16 `GATE-NFR` هیچ finding بحرانی امنیتی و regression کارایی بالاتر از بودجه مصوب باقی نماند

## 13. Migration rehearsal و تطبیق داده

- [ ] 13.1 `MIG-PLAN-001` ترتیب migration، script/version، owner، زمان و rollback هر مرحله را freeze کن — DOING: ماتریس انتقال، ترتیب اجرا، reconciliation و triggerهای rollback در `docs/migration/MANUAL-DATA-MIGRATION-CHECKLIST.md` ثبت شد؛ نسخه script و ownerهای روز Cutover هنوز باز است، 2026-08-22
- [ ] 13.1a `MIG-SRC-001` ثابت کن منبع full snapshot و delta نهایی دیتابیس زنده production است و snapshot قدیمی توسعه فقط برای rehearsal استفاده می‌شود
- [ ] 13.1b `MIG-DELTA-001` high-water mark، mapping شناسه، ترتیب delta یک‌طرفه، idempotency و write-freeze روز Cutover را تعریف و تست کن
- [ ] 13.2 `MIG-BKP-001` dump و checksum منبع و snapshot مقصد پیش از rehearsal را ثبت کن
- [ ] 13.3 `MIG-USR-001` users/roles/capabilities را migrate و count/نمونه/auth را تطبیق بده
- [ ] 13.4 `MIG-CNT-001` posts/pages/CPT/taxonomy/meta را migrate و count/نمونه را تطبیق بده
- [ ] 13.5 `MIG-MED-001` media را migrate و count/checksum/broken reference/private access را تطبیق بده
- [ ] 13.6 `MIG-WC-001` products/variations/attributes/stock را migrate و count/نمونه را تطبیق بده
- [ ] 13.7 `MIG-WC-002` customers/orders/items/totals/status/notes را migrate و مجموع مالی را تطبیق بده
- [ ] 13.8 `MIG-LMS-001` courses/lessons/enrollments/progress/entitlements را migrate و نمونه را تطبیق بده
- [ ] 13.9 `MIG-SEO-001` SEO metadata/redirectها را migrate و URL نمونه را تطبیق بده
- [ ] 13.10 `MIG-URL-001` search-replace serialization-aware را اجرا و باقی‌مانده URL قدیمی را گزارش کن
- [ ] 13.11 `MIG-IDM-001` migration را دوباره اجرا و idempotency/عدم duplicate را اثبات کن
- [ ] 13.12 `MIG-EXC-001` exceptionها را با entity ID، علت، تصمیم و مالک ثبت کن
- [ ] 13.13 `MIG-TIM-001` زمان هر مرحله و کل rehearsal را برای پنجره cutover ثبت کن
- [ ] 13.14 `MIG-SMK-001` smoke کامل public/login/order/course/admin را روی داده مهاجرت‌شده اجرا کن
- [ ] 13.15 `MIG-RBK-001` rollback rehearsal را اجرا و RTO/RPO واقعی را ثبت کن
- [ ] 13.16 `GATE-MIG` reconciliation بدون اختلاف حل‌نشده و rollback در زمان مصوب کامل شده باشد

## 14. UAT و Exit Gate نهایی

- [ ] 14.1 `UAT-PUB-001` مالک محصول مسیرهای عمومی must-have را تأیید کند
- [ ] 14.2 `UAT-USR-001` نماینده پشتیبانی registration/account/reset را تأیید کند
- [ ] 14.3 `UAT-WC-001` نماینده فروش catalog/cart/checkout/order/refund را تأیید کند
- [ ] 14.4 `UAT-PAY-001` مسئول مالی پرداخت/reconciliation/refund را تأیید کند
- [ ] 14.5 `UAT-LMS-001` مسئول آموزش خرید/دسترسی/progress/SpotPlayer را تأیید کند
- [ ] 14.6 `UAT-SEO-001` مسئول SEO metadata/redirect/sitemap/indexability را تأیید کند
- [ ] 14.7 `UAT-OPS-001` عملیات backup/restore/log/alert/runbook را تأیید کند
- [ ] 14.8 `UAT-DEF-001` همه defectهای P0/P1 بسته و P2/P3ها disposition و owner داشته باشند
- [ ] 14.9 `UAT-SIGN-001` sign-off فنی، کسب‌وکار، مالی و عملیات ثبت شود
- [ ] 14.10 `GATE-RELEASE` همه Gateهای قبلی قبول و تصمیم Go/No-Go ثبت شده باشد

## 15. Cutover و rollback آماده

- [ ] 15.1 `CUT-CHK-001` زمان، افراد، کانال ارتباط، freeze و maintenance message تأیید شود
- [ ] 15.2 `CUT-DNS-001` DNS/TTL، TLS، CDN/cache و دسترسی تغییر تنظیمات آماده باشد
- [ ] 15.3 `CUT-BKP-001` backup نهایی Legacy و checksum آن پیش از freeze ثبت شود
- [ ] 15.4 `CUT-FRZ-001` write freeze را اعمال و عدم ایجاد سفارش/کاربر جدید را تأیید کن
- [ ] 15.5 `CUT-DEL-001` delta migration نهایی را اجرا و reconciliation را تکرار کن
- [ ] 15.6 `CUT-SWK-001` endpoint/DNS را طبق runbook به Rebuild تغییر بده
- [ ] 15.7 `CUT-SMK-001` home/login/product/checkout/payment/course/admin را بلافاصله smoke test کن
- [ ] 15.8 `CUT-MON-001` error rate، latency، payment failure، email و queue/cron را پایش کن
- [ ] 15.9 `RBK-TRG-001` triggerهای rollback عددی و decision owner را پیش از شروع تأیید کن
- [ ] 15.10 `RBK-RUN-001` دستور بازگشت endpoint، داده و cache به Legacy آماده و dry-run شده باشد
- [ ] 15.11 `RBK-VAL-001` پس از rollback فرضی، smoke و reconciliation Legacy تعریف شده باشد
- [ ] 15.12 `CUT-DONE-001` پایان cutover، تصمیم ادامه/rollback و timestamp ثبت شود

## 16. Hypercare و پایان بازسازی

- [ ] 16.1 `HYP-1H-001` شاخص‌ها و مسیرهای بحرانی یک ساعت پس از cutover بررسی شوند
- [ ] 16.2 `HYP-24H-001` سفارش، پرداخت، enrollment، email، error و performance پس از 24 ساعت تطبیق شوند
- [ ] 16.3 `HYP-72H-001` الگوهای خطا، ticketها، SEO crawl و jobهای دوره‌ای پس از 72 ساعت بررسی شوند
- [ ] 16.4 `HYP-BKP-001` اولین backup production Rebuild restore-test شود
- [ ] 16.5 `HYP-DOC-001` runbook، credential ownership، alert و support handoff نهایی شوند
- [ ] 16.6 `HYP-LEG-001` مدت نگهداری read-only Legacy و برنامه decommission تصویب شود
- [ ] 16.7 `HYP-ARC-001` پس از تحقق Definition of Done، change با OpenSpec validate و archive شود
- [ ] 16.8 `GATE-DONE` بازسازی با sign-off نهایی و بدون blocker باز بسته شود

## Session Log

### نشست 2026-08-18 — شروع پیاده‌سازی Block Theme

```text
Run ID: 20260818-block-theme-foundation
Date/Operator: 2026-08-18 / Codex + project owner
Branch/Commit: unavailable — .git در مسیر جدید وجود ندارد
Environment: Rebuild local Docker / http://localhost:8082
Current phase/gate: Phase 5 / GATE-FOUNDATION (در کنار تکمیل Inventory)
Completed test IDs: RB-THEME-001
Failed/blocked test IDs: none
Evidence location: rebuild/site/wp-content/themes/rahbar؛ HTTP 200؛ PHP/JSON validation و Docker log check در session output
Last safe checkpoint: قالب Rahbar 0.1.0 فعال است و صفحه اصلی بدون PHP error رندر می‌شود
Open blockers + owner: مجوز فونت‌های ایران‌یکان/کلمه و inventory دقیق UI Legacy باید توسط مالک پروژه تأیید/تکمیل شود
Exact next action: تکمیل visual inventory صفحات Home و Product و سپس تثبیت tokenها و header/footer بر اساس baseline
Notes/decisions: هیچ فایل، JSON layout یا کد Elementor به Rebuild کپی نشده است
```

### نشست 2026-08-18 — ایجاد برنامه QA

```text
Run ID: 20260818-rebuild-qa-plan
Date/Operator: 2026-08-18 / Codex + project owner
Branch/Commit: unavailable — .git در مسیر جدید وجود ندارد
Environment: Legacy + Rebuild local Docker
Current phase/gate: Phase 2 / GATE-BASELINE و توسعه انتخابی UI/WooCommerce
Completed test IDs: INF-CMP-001, INF-CMP-002, INF-DB-001, INF-DB-002, INF-WEB-001, INF-WEB-002, INF-DBA-001, INF-DBA-002, INF-ISO-001, ARC-ADR-001, RB-THEME-001, RB-THEME-002, UI-HOME-BEN-001, UI-HOME-PAR-001, WC-PROD-PROT-001
Failed/blocked test IDs: none tested and failed
Evidence location: `docs/infrastructure/compose-isolation-evidence.md`، `docs/baseline/` و مدارک inline هر test
Last safe checkpoint: هر دو stack ایزوله و healthy؛ Home و Product prototype در Rebuild فعال هستند
Open blockers + owner: مجوز فونت و قرارداد price/LMS/SpotPlayer نیازمند تصمیم مالک و audit فنی است
Exact next action: اجرای `INV-DATA-001` با شمارش read-only post type، taxonomy، status و meta keyهای سفارشی Legacy
Notes/decisions: tasks.md مرجع canonical وضعیت روزانه است؛ secrets و dump وارد Git نشوند
```

این بلوک را برای هر نشست کپی کنید؛ جدیدترین نشست بالاتر باشد.

```text
Run ID:
Date/Operator:
Branch/Commit:
Environment:
Current phase/gate:
Completed test IDs:
Failed/blocked test IDs:
Evidence location:
Last safe checkpoint:
Open blockers + owner:
Exact next action:
Notes/decisions:
```
