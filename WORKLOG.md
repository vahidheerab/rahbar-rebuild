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
- پیشرفت QA: 14 مورد تکمیل‌شده از 206 مورد
- Rebuild WordPress: فعال روی <http://localhost:8082>
- Rebuild phpMyAdmin: فعال روی <http://localhost:8084>
- MySQL Rebuild: healthy
- قالب فعال: `rahbar` نسخه `0.3.0`
- Repository مقصد: `git@github.com:vahidheerab/rahbar-rebuild.git`
- وضعیت Git: branch `main` روی GitHub همگام است؛ bootstrap در `c3dc417` و گزارش پیوسته در `af554cb` ثبت شد

## کارهای انجام‌شده تا اینجا

### زیرساخت

- ساختار Legacy و Rebuild از هم جدا شد.
- Composeهای مستقل با project، network، volume و پورت جدا ساخته شدند.
- WordPress Rebuild روی پورت 8082 و Legacy روی پورت 8081 تنظیم شد.
- phpMyAdmin مستقل برای Legacy روی 8083 و Rebuild روی 8084 اضافه شد.
- اعتبار Compose هر دو محیط و healthy بودن دیتابیس‌ها بررسی شد.

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

- Block Theme اختصاصی `rahbar` تا نسخه 0.3.0 توسعه یافت.
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
| visual baseline صفحه Home و Product | باز | screenshot و component inventory از Legacy تهیه شود |
| active/inactive افزونه‌های Legacy | باز | دسترسی read-only دیتابیس Legacy و استخراج `active_plugins` |
| pluginهای پرداخت/LoginX/CRM/SMS/SpotPlayer | باز و بحرانی | audit کد، داده و قرارداد رفتاری قبل از migration |
| Evidence پایدار baseline | باز | خروجی redacted در محل امن ثبت شود |

## آخرین checkpoint امن

Rebuild با قالب `rahbar 0.3.0` بالا است، دیتابیس healthy است و صفحه اصلی HTTP 200 می‌دهد. Header/Footer و Benefits در viewportهای 1440، 768 و 375 تأیید شده‌اند. هیچ داده واقعی Legacy، افزونه ثالث یا layout Elementor وارد Rebuild نشده است.

## اقدام بعدی اصلی

Product template prototype را بر اساس baseline محصول نمونه بساز و CTA خرید را در Mobile پیش از محتوای طولانی قابل دسترس نگه دار.

## صف کار پس از اقدام اصلی

1. اجرای `INF-ISO-001` و ثبت project/container/network/volume دو محیط؛
2. استخراج active pluginها و مالکیت داده هر plugin؛
3. اجرای responsive/RTL smoke test کامل روی Home prototype.

## تاریخچه نشست‌ها

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
