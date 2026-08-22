# گزارش پیوسته بازسازی Rahbar

این فایل مرجع سریع ادامه کار بین نشست‌هاست. جزئیات تست‌ها در `openspec/changes/rebuild-qa-checklist/tasks.md` و تصمیم‌های کلان در `REBUILD-ROADMAP.md` نگهداری می‌شوند.

## قانون ثبت از این نقطه به بعد

پس از هر تغییر معنادار، این فایل در همان commit به‌روز می‌شود و شامل موارد زیر خواهد بود:

1. تاریخ و شناسه نشست؛
2. فایل‌ها یا سرویس‌های تغییرکرده؛
3. کار انجام‌شده و نتیجه verification؛
4. تصمیم یا فرض مهم؛
5. blocker و مالک آن؛
6. آخرین checkpoint امن؛
7. دقیقاً یک اقدام بعدی اصلی و صف کوتاه اقدامات پس از آن.

تغییر بدون verification به‌عنوان «انجام‌شده» ثبت نمی‌شود. secrets، dump، uploads و اطلاعات شخصی در این فایل قرار نمی‌گیرند.

## وضعیت فعلی در یک نگاه

- تاریخ checkpoint: 2026-08-18
- فاز جاری: پایه Rebuild و شروع بازسازی UI، همراه با تکمیل Inventory
- پیشرفت QA: 19 مورد تکمیل‌شده از 208 مورد
- Rebuild WordPress: فعال روی <http://localhost:8082>
- Rebuild phpMyAdmin: فعال روی <http://localhost:8084>
- MySQL Rebuild: healthy
- قالب فعال: `rahbar` نسخه `0.5.0`
- WooCommerce Rebuild: نسخه `11.0.1` در runtime فعال؛ کد ثالث ignore شده است
- Repository مقصد: `git@github.com:vahidheerab/rahbar-rebuild.git`
- وضعیت Git: branch `main` روی GitHub همگام است؛ bootstrap در `c3dc417` و گزارش پیوسته در `af554cb` ثبت شد

## کارهای انجام‌شده تا اینجا

### زیرساخت

- ساختار Legacy و Rebuild از هم جدا شد.
- Composeهای مستقل با project، network، volume و پورت جدا ساخته شدند.
- WordPress Rebuild روی پورت 8082 و Legacy روی پورت 8081 تنظیم شد.
- phpMyAdmin مستقل برای Legacy روی 8083 و Rebuild روی 8084 اضافه شد.
- اعتبار Compose هر دو محیط و healthy بودن دیتابیس‌ها بررسی شد.
- ایزولیشن project/container/network/volume/port/mount دو محیط با تست منفی cross-network اثبات شد.

### برنامه‌ریزی و کنترل کیفیت

- OpenSpec در پروژه راه‌اندازی شد.
- change با نام `rebuild-qa-checklist` ساخته و در حالت strict معتبر شد.
- چک‌لیست 202 موردی از baseline تا migration، cutover، rollback و hypercare ایجاد شد.
- Session Log و Evidence policy تعریف شد.
- roadmap، migration guide و plugin inventory به‌روزرسانی شدند.

### تصمیم قالب و UI

- ADR-0001 تصویب شد: Rebuild با Block Theme اختصاصی و Gutenberg ساخته می‌شود.
- انتقال Elementor، Elementor Pro، Hello Elementor و layout metadata آن‌ها رد شد.
- رنگ‌های پرتکرار Legacy استخراج شدند: بنفش `#5F2284`، زرد `#FBB911` و قرمز `#D62B27`.
- کپی فونت‌های ایران‌یکان و کلمه تا تأیید مجوز متوقف ماند.

### پیاده‌سازی Rebuild

- Block Theme اختصاصی `rahbar` تا نسخه 0.5.0 توسعه یافت.
- Product template بومی WooCommerce با visual parity و CTA ثابت mobile اضافه شد.
- `theme.json`، design tokenها، header، footer و templateهای اصلی ایجاد شدند.
- templateهای front page، page، single، archive، search و 404 اضافه شدند.
- patternهای Hero، Benefits، Services و CTA اضافه شدند.
- قالب در Rebuild فعال شد.
- JSON و PHP معتبر، HTTP صفحه اصلی 200 و log بدون PHP error تأیید شد.

### Inventory افزونه‌ها

- 26 افزونه معمولی، 4 MU-plugin و یک cache drop-in شناسایی شدند.
- تصمیم اولیه Keep/Update/Replace/Retire/Audit در `PLUGIN-INVENTORY.md` ثبت شد.
- هم‌پوشانی‌های Elementor، LoginX، cache/performance و payment مشخص شدند.
- وضعیت active/inactive افزونه‌ها هنوز از دیتابیس Legacy خوانده نشده است.

### Git و محرمانگی

- repository محلی جدید روی branch `main` ایجاد شد.
- remote `origin` به repository جدید GitHub متصل شد.
- `.gitignore` با allowlist مخصوص Rebuild جایگزین شد.
- کل `legacy/`، WordPress core، افزونه‌های ثالث، uploads، cache، log، dump و `.env` واقعی ignore شدند.
- فقط Compose، مستندات، OpenSpec و theme اختصاصی Rahbar stage شدند.
- staged content با الگوهای high-confidence secret اسکن شد و موردی پیدا نشد.

## Blockerها و موارد تصمیم‌گیری

| مورد | وضعیت | مالک/اقدام لازم |
|---|---|---|
| مجوز فونت ایران‌یکان و کلمه | باز | مالک پروژه باید مجوز و فایل منبع مجاز را تأیید کند |
| visual baseline صفحه Home و Product | بسته | inventory و screenshotهای هر دو صفحه ثبت شده‌اند |
| active/inactive افزونه‌های Legacy | باز | دسترسی read-only دیتابیس Legacy و استخراج `active_plugins` |
| pluginهای پرداخت/LoginX/CRM/SMS/SpotPlayer | باز و بحرانی | audit کد، داده و قرارداد رفتاری قبل از migration |
| Evidence پایدار baseline | باز | خروجی redacted در محل امن ثبت شود |

## آخرین checkpoint امن

Rebuild با قالب `rahbar 0.5.0` و WooCommerce `11.0.1` بالا است. Home و Product prototype HTTP 200 می‌دهند؛ صفحه محصول از داده واقعی WooCommerce و CTA قابل دسترس mobile استفاده می‌کند. هیچ افزونه یا layout metadata مربوط به Elementor وارد repository نشده است.

## اقدام بعدی اصلی

فهرست active/inactive افزونه‌های Legacy را مستقیماً از `active_plugins` و `active_sitewide_plugins` استخراج و با `PLUGIN-INVENTORY.md` تطبیق بده؛ مالکیت داده هر افزونه را ثبت کن.

## صف کار پس از اقدام اصلی

1. اجرای responsive/RTL و interaction test کامل روی Home؛
2. تهیه baseline جداگانه برای Footer و سکشن‌های پایین‌تر Home؛
3. تعیین قرارداد canonical ریال/تومان و منبع metadata دوره؛
4. اجرای restart isolation برای Rebuild و سپس Legacy (`INF-ISO-002/003`).

## تاریخچه نشست‌ها

### 2026-08-18 — `compose-isolation-inf-iso-001`

- projectهای `rahbar-legacy` و `rahbar-rebuild` و هر شش container runtime بررسی شدند.
- networkها و volumeهای دیتابیس مستقل و bind mountهای WordPress از sourceهای جدا تأیید شدند.
- پورت‌های 8081/8082 برای WordPress و 8083/8084 برای phpMyAdmin بدون اشتراک ثبت شدند.
- DNS منفی cross-network از هر WordPress به DB محیط مقابل اجرا و در هر دو جهت عمداً شکست خورد.
- DBهای هر دو محیط healthy، endpointهای مدیریتی HTTP 200 و containerها running بودند.
- timeout Home قدیمی به‌عنوان finding عملکرد Legacy ثبت شد، نه failure ایزولیشن.
- Evidence در `docs/infrastructure/compose-isolation-evidence.md` ثبت شد.
- checkpoint: `INF-ISO-001` تکمیل؛ اقدام بعدی استخراج active pluginها و مالکیت داده است.

### 2026-08-18 — `woocommerce-product-prototype-v0.5.0`

- WooCommerce رسمی `11.0.1` فقط در runtime Rebuild نصب و فعال شد؛ کد ثالث در Git ignore است.
- حالت پیش‌فرض Coming Soon خاموش و currency prototype روی تومان با صفر رقم اعشار تنظیم شد.
- محصول نمونه simple/virtual/instock با قیمت عادی و فروش Legacy و تصویر شاخص واقعی ساخته شد.
- `single-product.html` با بلوک‌های native قیمت، sale badge، stock و add-to-cart ساخته شد.
- layout Desktop/Tablet با Legacy تطبیق و overflow بحرانی mobile رفع شد.
- CTA موبایل به نوار ثابت price/add-to-cart تبدیل شد و متن دکمه فارسی شد.
- شواهد و محدودیت‌های price/LMS/SpotPlayer در `docs/baseline/rebuild-product-prototype-report.md` ثبت شدند.
- checkpoint: `WC-PROD-PROT-001` تکمیل؛ اقدام بعدی اجرای `INF-ISO-001` است.

### 2026-08-18 — `home-legacy-visual-parity-v0.4.0`

- assetهای واقعی logo، Hero chess، wave و education icon از Legacy استخراج و داخل theme مستقل شدند.
- Header، navigation، Hero و Benefits براساس screenshotهای Legacy بازطراحی معکوس شدند؛ layout Elementor منتقل نشد.
- Hero در desktop/tablet دوستونه و در mobile متن-سپس-تصویر شد؛ navigation تبلت به hamburger تبدیل شد.
- ایراد heading hierarchy بخش Benefits با افزودن H2 قابل دسترس اصلاح شد و CTA ثبت‌نام در عرض کم nowrap شد.
- screenshotهای 1440، 768 و 375 با مرجع Legacy مقایسه شدند؛ اختلاف‌های عمدی در `docs/baseline/rebuild-home-visual-parity-report.md` ثبت شدند.
- قالب به `0.4.0` رسید؛ checkpoint بعدی Product template با visual parity است.

### 2026-08-18 — `visual-parity-direction`

- مالک پروژه تعیین کرد نسخه نخست Rebuild باید برای جایگزینی سریع روی هاست، بیشترین شباهت بصری و رفتاری را به Legacy داشته باشد.
- بازطراحی مدرن و تغییر هویت بصری به فاز بعد از Cutover منتقل شد.
- اختلاف با Legacy فقط برای رفع مشکل فنی، responsive، accessibility، performance، امنیت یا حذف Elementor مجاز است.
- ADR-0001 و roadmap با این قید تکمیل شدند.
- prototypeهای فعلی Header، Hero، Benefits و Footer نهایی نیستند و باید visual-parity pass شوند.
- checkpoint: تصمیم محصول ثبت شد؛ اقدام بعدی تطبیق بصری کامل Home با baseline است.

### 2026-08-18 — `rebuild-home-benefits-v0.3.0`

- pattern مستقل `rahbar/benefits` با چهار ارزش پیشنهادی baseline Legacy ساخته و پس از Hero به Home متصل شد.
- CSS Grid با چیدمان چهار ستون desktop، دو ستون tablet و یک ستون mobile اضافه شد.
- pattern در WordPress parse و render شد و وجود دقیقاً چهار کارت در HTML صفحه تأیید شد.
- سه screenshot full-page در عرض‌های 1440، 768 و 375 تولید و بازبینی شد؛ clipping یا overflow افقی مشاهده نشد.
- نسخه قالب به `0.3.0` افزایش یافت و HTTP 200 و بارگذاری stylesheet نسخه جدید تأیید شد.
- Evidence در `docs/baseline/rebuild-home-benefits-smoke-test.md` ثبت شد.
- checkpoint: `UI-HOME-BEN-001` تکمیل؛ اقدام بعدی Product template prototype است.

### 2026-08-18 — `rebuild-header-footer-v0.2.0`

- tokenهای رنگ، spacing، radius و shadow مشترک تثبیت شدند.
- top bar، هویت سایت، جست‌وجوی desktop/mobile، navigation و CTAهای header تکمیل شدند.
- footer سه‌ستونه با لینک‌های راهبری، حساب و تماس تکمیل شد.
- مشکل بارگذاری‌نشدن CSS در frontend با enqueue نسخه‌دار stylesheet اصلاح شد.
- سه smoke test تصویری Playwright در عرض‌های 1440، 768 و 375 اجرا و بازبینی شد؛ clipping و overflow افقی مشاهده نشد.
- PHP، JSON، HTTP و Docker log بررسی شدند و شواهد در `docs/baseline/rebuild-header-footer-smoke-test.md` ثبت شد.
- checkpoint: `RB-THEME-002` تکمیل؛ اقدام بعدی ساخت Benefits pattern چهارگانه است.

### 2026-08-18 — `legacy-product-visual-inventory`

- محصول منتشرشده `39885` با عنوان «دوره آموزش حسابداری طلا» به‌عنوان نمونه پولی انتخاب شد.
- screenshotهای Product در 1440×1200، 768×1100 و 375×900 ثبت و بازبینی شدند.
- ساختار عنوان، تصویر، summary، SpotPlayer notice، instructor، tabs، purchase card و curriculum مستند شد.
- قرارداد read-only محصول ثبت شد: simple، virtual، قیمت عادی 18,000,000، قیمت فروش 11,800,000، instock و قابل خرید.
- finding بحرانی: Mobile horizontal overflow دارد و عنوان، تصویر و summary clip می‌شوند؛ CTA خرید above-the-fold نیست.
- mapping به WooCommerce blocks، LMS metadata و entitlement plugin تعریف شد.
- verification: سه PNG عمومی، metadata WooCommerce و سند baseline بررسی شدند.
- checkpoint: `INV-UI-004` تکمیل؛ اقدام بعدی تثبیت tokenها و تکمیل header/footer است.

### 2026-08-18 — `legacy-home-visual-inventory`

- Legacy و هر سه سرویس آن healthy تأیید شدند.
- screenshotهای Home در 1440×900، 768×1024 و 375×812 ثبت شدند.
- ساختار top bar، header، navigation، Hero، benefit cards و floating action مستند شد.
- tokenهای رنگ و typography و mapping به Block Theme ثبت شدند.
- finding کارایی: Home حدود 648KB HTML دارد؛ یک درخواست بیش از یک دقیقه طول کشید و استخراج کامل HTML بعدی به‌دلیل زمان غیرعادی متوقف شد.
- باگ‌هایی که نباید بازتولید شوند ثبت شدند: کارت‌های باریک tablet و حذف search/navigation واضح در mobile.
- verification: فایل‌های PNG بازبینی بصری و سند baseline ایجاد شد.
- checkpoint: `INV-UI-003` تکمیل؛ اقدام بعدی inventory صفحه Product است.

### 2026-08-18 — `repo-bootstrap-and-continuity-log`

- `WORKLOG.md` به‌عنوان مرجع ادامه کار ایجاد شد.
- وضعیت واقعی Docker، قالب فعال و شمارش checklist دوباره بررسی شد.
- checkpoint فعلی، blockerها و اقدام بعدی ثبت شدند.
- commit اولیه پروژه با hash `c3dc417` روی `origin/main` ثبت شد.
- `WORKLOG.md` با commit `af554cb` اضافه و با موفقیت به GitHub push شد.
- نتیجه: repository همگام و اقدام بعدی visual inventory صفحه Home است.

### 2026-08-18 — `legacy-plugin-state-and-data-ownership`

- option `active_plugins` به‌صورت read-only مستقیماً از دیتابیس Legacy خوانده و با پوشه‌های واقعی plugin تطبیق داده شد.
- 22 افزونه فعال و موجود، 4 افزونه غیرفعال و موجود، و یک ورودی فعال با فایل مفقود (`wp-crontrol`) شناسایی شد.
- چهار MU-plugin و drop-in کش مستقل از option فعال‌سازی تأیید شدند.
- همه جدول‌های Legacy فهرست و مالکیت داده‌های WooCommerce، Elementor، Code Snippets، Login/OTP، SMS، payment، wallet، SEO، security، cache و media در `PLUGIN-INVENTORY.md` ثبت شد.
- جدول‌های محتمل orphan/dependency حذف‌شده Tutor، Quiz Maker، ticket، YITH و Slider به‌عنوان finding باز ثبت شدند و هیچ داده یا افزونه‌ای حذف نشد.
- checkpoint: `INV-PLG-001` تکمیل؛ اقدام بعدی اجرای `INV-CODE-001` با export امن metadata مربوط به snippetهای فعال و inventory کد اختصاصی است.

### 2026-08-18 — `legacy-scope-decisions-and-custom-code-inventory`

- تصمیم مالک پروژه ثبت شد: Legacy فقط مرجع الگو/تست است و هیچ توسعه یا پاک‌سازی روی آن انجام نمی‌شود.
- `wp-crontrol`، Kadence Security، TeraWallet و Rahbar CRM Connector در فهرست قطعی «عدم انتقال به Rebuild» قرار گرفتند؛ CRM در مقصد بازسازی نمی‌شود.
- قاعده فاز افزونه‌ها ثبت شد: افزونه‌ها باید یکی‌یکی با مالک پروژه بررسی و سپس migrate/replace/retire شوند.
- metadata ده Code Snippet بدون خواندن/اجرای متن کد بررسی شد؛ دو shortcode فعال و هشت snippet غیرفعال شناسایی شدند.
- کد دامنه‌ای پراکنده در Hello Elementor، MU-pluginها، SpotPlayer، SMS و payment با static scan در `docs/baseline/legacy-custom-code-inventory.md` ثبت شد.
- checkpoint: `INV-CODE-001` تکمیل؛ اقدام بعدی `INV-DATA-001` و شمارش entity و metaهای سفارشی Legacy به‌صورت read-only است.

### 2026-08-19 — `home-section-parity-reassessment`

- پذیرش قبلی `UI-HOME-PAR-001` پس گرفته شد؛ baseline قبلی فقط viewport اولیه را پوشش می‌داد و شباهت کافی نبود.
- screenshot بلند 1440×10000 از Legacy ثبت و ترتیب کامل سکشن‌های Home استخراج شد.
- تعداد واقعی کارت‌های مزیت از چهار به پنج اصلاح و متن‌های واقعی Legacy وارد شد.
- Header و Hero در desktop/mobile دوباره با مرجع تصویری تنظیم شدند.
- بلوک‌های عمومی و نامرتبط Services/CTA از Home حذف و سکشن «جدیدترین دوره‌های ما» با Query واقعی محصولات WooCommerce اضافه شد.
- checkpoint: سه بخش ابتدایی در pass تطبیق سکشن‌به‌سکشن هستند؛ اقدام بعدی تکمیل کارت دوره‌ها و سپس سکشن «راهبر مالی» است.

### 2026-08-19 — `home-section-parity-body-pass`

- ترتیب کامل بدنه Home از screenshot بلند و Elementor metadata به‌صورت read-only استخراج شد.
- سکشن‌های جدیدترین دوره‌ها، راهبر مالی، آموزش رایگان، سؤال‌های پرتکرار، اخبار و بخشنامه‌ها، ایزی اینویس، اینستاگرام، منتورما، تجربه دانشجویان و همکاران ساخته شدند.
- assetهای هر سکشن به‌صورت انتخابی از Legacy ارزیابی و در قالب Rebuild کپی شدند؛ هیچ plugin/theme یا مجموعه uploads به‌صورت دسته‌جمعی منتقل نشد.
- Query دوره‌ها فقط داده واقعی WooCommerce مقصد را نمایش می‌دهد؛ به‌دلیل نبود داده مهاجرت‌شده، آموزش رایگان empty-state صریح دارد و محصول جعلی ساخته نشد.
- HTTP 200، asset probes، Compose config و نبود PHP fatal/warning بررسی شد.
- checkpoint: بدنه Home تا پیش از Footer پیاده شده؛ اقدام بعدی visual regression کامل desktop/tablet/mobile و سپس parity دقیق Footer است.

### 2026-08-19 — `home-footer-parity-pass`

- Footer تیره و کوتاه prototype با ساختار روشن و چندردیفه نزدیک به Legacy جایگزین شد.
- معرفی مؤسسه، دسترسی سریع، لینک‌های مرتبط، اطلاعات تماس، promise cardها، سه مجوز واقعی و نوار کپی‌رایت اضافه شدند.
- assetهای مجوز به‌صورت انتخابی از Footer Legacy منتقل و با HTTP 200 تأیید شدند.
- baseline بلند Rebuild پس از تکمیل بدنه و Footer دوباره تولید شد.
- checkpoint: ساختار کامل Home از Header تا Footer موجود است؛ `UI-HOME-PAR-001` تا پایان بازبینی و اصلاح desktop/tablet/mobile همچنان DOING است.

### 2026-08-22 — `home-final-responsive-parity`

- صفحه Home با Playwright و viewport واقعی 1440، 768 و 375 به‌صورت full-page ثبت و با baseline بلند Legacy بازبینی شد.
- overflow ساختاری Hero در موبایل رفع و ترتیب card/image به layout عمودی تبدیل شد.
- actionهای جست‌وجو و حساب و navigation موبایل قابل دسترس شدند.
- ستون‌های Footer در موبایل عمودی و مجوزها و promise cardها خوانا شدند.
- ارتفاع کارت دوره در موبایل کاهش یافت؛ داده جعلی برای carousel یا آموزش رایگان ساخته نشد.
- HTTP 200، Compose config، PHP lint، OpenSpec strict validation و نبود خطاهای PHP در log تأیید شد.
- مقصد چند لینک Header/Footer هنوز 404 است و ذیل taskهای template، account و commerce باقی می‌ماند؛ `GATE-UI` بسته نشد.
- checkpoint: `UI-HOME-PAR-001` تکمیل؛ اقدام بعدی `INV-DATA-001` و سپس ساخت مقصدهای پایه لینک‌های Home است.

### 2026-08-22 — `legacy-data-model-inventory-start`

- پس از smoke check موفق Home، اجرای read-only مورد `INV-DATA-001` شروع شد.
- post typeها، statusها، taxonomyها و شمارش meta keyها مستقیماً از دیتابیس Legacy استخراج شدند.
- ۴۲ محصول، ۸٬۵۸۳ سفارش، ۵۸۶ سؤال و داده‌های هم‌زمان WooCommerce، Tutor، SpotPlayer، Elementor و SEO شناسایی شدند.
- ۵۳۵ post meta key، ۱۹۵ user meta key و ۱۱ term meta key متمایز وجود دارد؛ انتقال خام metaها رد و نیاز به allowlist ثبت شد.
- status سفارشی `wc-arrival-shipment` و خانواده‌های حساس Zibal، SpotPlayer، Wallet و checkout به‌عنوان موارد نیازمند قرارداد ثبت شدند.
- Evidence اولیه در `docs/baseline/legacy-data-model-inventory.md` ثبت شد.
- checkpoint: `INV-DATA-001` در حال انجام؛ اقدام بعدی دسته‌بندی meta keyها به تفکیک post type و مالکیت افزونه است.

### 2026-08-22 — `production-data-source-contract`

- مالک پروژه اعلام کرد snapshot دیتابیس Legacy محیط توسعه تقریباً یک ماه قدیمی است.
- snapshot فعلی فقط برای inventory، توسعه migration script و rehearsal معتبر اعلام شد؛ منبع حقیقت Cutover دیتابیس زنده production است.
- گروه‌های داده قابل انتقال و موارد پیش‌فرض خارج از scope در `MIGRATION-GUIDE.md` ثبت شدند.
- قرارداد انتقال روز Cutover شامل full snapshot تازه، high-water mark، delta یک‌طرفه idempotent، freeze کوتاه و reconciliation ثبت شد.
- sync دوطرفه یا SQL دستی ثبت‌نشده ممنوع شد؛ در صورت نیاز به همگام‌سازی پیوسته، طراحی CDC/queue مستقل لازم است.
- checkpoint: قرارداد منبع داده ثبت شد؛ `INV-DATA-001` باید allowlist دقیق هر entity/meta را تکمیل کند.

### 2026-08-22 — `manual-migration-allowlist-and-runbook`

- meta keyها بدون خواندن مقدار یا PII به تفکیک product، order، course، lesson، question و content شمارش شدند.
- ماتریس `migrate/transform/archive-only/retire` برای کاربران، محتوا، commerce، LMS، SpotPlayer، SEO، media و داده‌های retired ثبت شد.
- allowlist اولیه metaهای کاربر، محصول، سفارش و محتوا تعریف شد.
- ترتیب دستی rehearsal و Cutover، جدول reconciliation، triggerهای توقف/rollback و فرم ثبت Run ایجاد شد.
- ورود رکوردبه‌رکورد، کپی کامل جدول، sync دوطرفه و SQL دستی ثبت‌نشده ممنوع ثبت شد؛ اپراتور مراحل و نتایج را دستی تأیید می‌کند اما انتقال با script نسخه‌شده انجام می‌شود.
- Evidence اجرایی در `docs/migration/MANUAL-DATA-MIGRATION-CHECKLIST.md` ثبت شد.
- checkpoint: runbook اولیه آماده است؛ تصمیم تجاری metaهای دوره، status سفارشی سفارش، SEO canonical و قرارداد entitlement پیش از freeze نهایی لازم است.

### 2026-08-22 — `cutover-orchestrator-scaffold`

- اسکریپت واحد `scripts/migration/Invoke-RahbarCutover.ps1` با actionهای Preflight، Baseline، Snapshot، Reconcile و Cutover ایجاد شد.
- snapshot تراکنشی، SHA-256، countهای کلیدی، مجموع مبلغ سفارش و high-water markها پیاده‌سازی شد.
- reconciliation در صورت هر اختلاف fail می‌شود و Cutover را متوقف می‌کند.
- Cutover بدون تأیید صریح production/freeze و migration adapter بازبینی‌شده عمداً اجرا نمی‌شود.
- checkpoint: orchestration و safety آماده است؛ adapter واقعی پس از تثبیت schema و قرارداد payment/LMS/SpotPlayer پیاده‌سازی می‌شود.

### 2026-08-22 — `rebuild-base-pages-and-permalinks`

- پیش از تغییر داده، snapshot تراکنشی و SHA-256 دیتابیس Legacy و Rebuild در Evidence محلی خارج از Git ساخته شد.
- initializer تکرارپذیر صفحات پایه Rebuild در `scripts/rebuild/` ایجاد و اجرا شد.
- permalink، timezone تهران و locale فارسی تنظیم و rewrite استاندارد WordPress فقط برای Rebuild ثبت شد.
- ۱۵ مقصد لینک Header/Footer ایجاد و صفحات WooCommerce فارسی شدند.
- اجرای دوم initializer بدون duplicate موفق بود و مسیرهای Shop، Cart، Account، Blog و Contact پاسخ HTTP 200 دادند.
- rollback به dump `target-before` همان Run در `docs/baseline/rebuild-base-pages-initialization.md` ثبت شد.
- checkpoint: `RB-WP-001` تکمیل؛ اقدام بعدی تکمیل template و محتوای صفحه عمومی/Contact و سپس Blog است.

### 2026-08-22 — `contact-and-blog-prototype`

- template اختصاصی Contact با اطلاعات تماس قابل کلیک، راهنمای پشتیبانی و CTA حساب کاربری ساخته شد.
- layout Contact در desktop سه‌ستونه و mobile تک‌ستونه شد.
- template صفحه عمومی با hero/content container مشترک بهبود یافت.
- `home.html` برای Blog با Query، pagination، کارت نوشته و empty-state ایجاد شد.
- initializer یک Home page پایدار ساخت و `page_on_front/page_for_posts` را idempotent تنظیم کرد؛ front-page فعلی حفظ شد.
- Contact و Blog روی desktop/mobile screenshot و با HTTP 200 تأیید شدند؛ log بدون PHP error بود.
- checkpoint: prototype Contact و Blog آماده است؛ اقدام بعدی تکمیل breadcrumb و templateهای single/archive/category/tag/author است.

### 2026-08-22 — `contact-legacy-parity`

- صفحه Contact در Rebuild بر اساس baseline تصویری Legacy برای desktop، tablet و mobile تکمیل شد.
- بنر، راهنمای خرید، CTA، چهار کارت اطلاعات، فرم و جایگاه نقشه با ترتیب مرجع پیاده‌سازی شدند.
- افزونه first-party و مستقل `rahbar-contact` برای فرم ساخته شد؛ nonce، honeypot، rate limit، sanitize، محدودیت طول و مقصد `admin_email` دارد.
- initializer افزونه را idempotent فعال می‌کند و اجرای مجدد آن duplicate نساخت.
- مسیر `/contact/` پاسخ HTTP 200 داد و lint کد، Compose config و بررسی log بدون خطای PHP بود.
- ارسال ایمیل واقعی عمداً در QA انجام نشد؛ تست تحویل SMTP پس از تنظیم mail provider محیط مقصد باقی می‌ماند.
- Evidence: `docs/baseline/legacy-contact-visual-inventory.md` و `docs/baseline/rebuild-contact-parity-report.md` و شش screenshot مرجع/Rebuild.
- checkpoint: `UI-CONTACT-PAR-001` تکمیل؛ اقدام بعدی ادامه `UI-BLOG-001` با templateهای single/archive/category/tag/author است.

### 2026-08-22 — `blog-template-completion`

- Blog home، single، archive، category، tag و author با templateهای اختصاصی تکمیل شدند.
- loop مشترک کارت‌ها شامل تصویر شاخص، تاریخ، دسته‌بندی، excerpt، empty-state و pagination ایجاد شد.
- نوشته تکی شامل دسته‌بندی، عنوان، تاریخ، نویسنده، زمان مطالعه، تصویر شاخص، محتوای خوانا، برچسب و پیمایش قبلی/بعدی شد.
- نسخه قالب Rahbar به `1.0.0` رسید و routeهای Blog، نوشته نمونه و دسته موجود پاسخ HTTP 200 دادند.
- برای آلوده نکردن Rebuild، نوشته جعلی جهت پرکردن grid یا pagination ساخته نشد؛ سناریوهای چندصفحه‌ای و tag/author پرشده پس از migration محتوا دوباره تست می‌شوند.
- Evidence: `docs/baseline/rebuild-blog-completion-report.md` و screenshotهای final Blog/Single.
- checkpoint: `UI-BLOG-001` تکمیل؛ اقدام بعدی تکمیل templateهای Search/404 و سپس آزمون جامع responsive/accessibility است.

### 2026-08-22 — `search-and-404-completion`

- templateهای Search و 404 با طراحی واکنش‌گرا، متن فارسی و مسیرهای بازیابی تکمیل شدند.
- Search نتیجه‌دار، بدون نتیجه، عبارت فارسی، لاتین و ورودی HTML-like تست شد؛ ورودی خاص به raw script تبدیل نشد.
- نتیجه‌ها برای post/page و سایر content typeهای قابل جست‌وجو، همراه تاریخ، دسته، excerpt و pagination رندر می‌شوند.
- URL عمداً ناموجود status واقعی HTTP 404 داد و لینک‌های Home، Contact و Blog بررسی شدند.
- نسخه قالب Rahbar به `1.1.0` رسید و چهار screenshot دسکتاپ/موبایل ثبت شد.
- Evidence: `docs/baseline/rebuild-search-404-completion-report.md`.
- checkpoint: `UI-SEARCH-001` و `UI-404-001` تکمیل؛ اقدام بعدی اجرای `UI-RTL-001` و `UI-RESP-001` و سپس accessibility است.

### 2026-08-22 — `public-samples-and-responsive-matrix`

- پیش از تغییر داده، snapshot و SHA-256 مقصد در evidence محلی خارج Git ثبت شد.
- importer نسخه‌شده ۶ مقاله و ۶ محصول عمومی Legacy را همراه تصویر شاخص به Rebuild وارد کرد؛ داده کاربر/سفارش/پرداخت/دسترسی/دانلود وارد نشد.
- اجرای اول ۱۲ رکورد ساخت و اجرای مجدد همان ۱۲ رکورد را update کرد؛ duplicate ساخته نشد.
- Blog، مقاله طولانی، Shop و محصول واقعی بررسی و Shop برای grid سه/دو ستونه، CTA فارسی و کارت‌های همسان اصلاح شد.
- آزمون Playwright روی Home/Blog/Article/Shop/Product/Contact/Search/404 در عرض‌های 320/375/768/1024/1440 اجرا شد.
- overflow هدر در 320 و تصویر ثابت مقاله پیدا و اصلاح شد؛ ۲۴ حالت بزرگ‌تر و سپس ۱۶ حالت موبایل پاس شدند.
- Evidence: `docs/baseline/rebuild-public-sample-content-report.md` و شش screenshot محتوای واقعی.
- checkpoint: `UI-RTL-001` و `UI-RESP-001` تکمیل؛ اقدام بعدی keyboard/focus و screen-reader/contrast accessibility است.

### 2026-08-22 — `latest-courses-carousel`

- اسکرول افقی قابل مشاهده سکشن «جدیدترین دوره‌های ما» با carousel واقعی RTL جایگزین شد.
- نمایش ۴/۳/۲/۱ کارت بر اساس viewport، کنترل قبلی/بعدی، swipe، scroll snap و scrollbar مخفی پیاده‌سازی شد.
- وضعیت disabled ابتدا/انتها، keyboard arrows، focus visible و prefers-reduced-motion اضافه شد.
- تست رفتاری Playwright و smoke overflow در عرض‌های 320 و 1440 پاس شدند.
- Evidence: `docs/baseline/rebuild-course-carousel-report.md` و `rebuild-course-carousel-mobile-375.png`.

### 2026-08-22 — `accessibility-aa-automation`

- اسکن axe-core برای Home/Blog/Article/Shop/Product/Contact/Search/404 با قواعد WCAG 2.0/2.1 A/AA اجرا شد.
- کنتراست CTA زرد، لینک ادامه مطلب و دکمه ارسال Contact اصلاح شد.
- focus عناصر مخفی drawer سبد WooCommerce، landmark اصلی Home و جدول عریض مقاله اصلاح شدند.
- keyboard order فرم، نام قابل‌خواندن فیلدها، role=alert، zoom 200% و reduced-motion تست شدند.
- نتیجه نهایی همه مسیرها بدون violation جدی/بحرانی بود؛ تست دستی کوتاه NVDA/VoiceOver در pre-launch حفظ شد.
- نسخه قالب Rahbar به `1.4.0` رسید؛ Evidence: `docs/baseline/rebuild-accessibility-report.md`.
- checkpoint: `A11Y-KBD-001`، `A11Y-SR-001` و `A11Y-CON-001` تکمیل؛ اقدام بعدی browser matrix و سپس فرم/مسیرهای commerce است.

### 2026-08-22 — `commerce-public-flow`

- قالب اختصاصی دارای header/footer برای Cart، Checkout و My Account اضافه شد و نسخه قالب به `1.5.0` رسید.
- بسته رسمی ترجمه فارسی WooCommerce 11.0.1 با checksum ثابت نصب و اسکریپت تکرارپذیر نصب آن ثبت شد.
- کشور پایه/فروش ایران و ارز IRT تثبیت شد؛ خرید مهمان برای جلوگیری از سفارش بدون حساب غیرفعال و ثبت‌نام حساب فعال شد.
- تست واقعی add/persistence/remove cart، validation تسویه‌حساب و فرم ورود با Playwright پاس شد (`3 passed`).
- هیچ سفارش یا پرداختی ایجاد نشد؛ درگاه sandbox و قرارداد اتصال entitlement هنوز باقی است.
- Evidence: `docs/baseline/rebuild-commerce-flow-report.md` و سه screenshot صفحه‌های Commerce.
