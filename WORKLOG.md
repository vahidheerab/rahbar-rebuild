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
- پیشرفت QA: 17 مورد تکمیل‌شده از 208 مورد
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
