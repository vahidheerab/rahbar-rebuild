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
- پیشرفت QA: 10 مورد تکمیل‌شده از 202 مورد
- Rebuild WordPress: فعال روی <http://localhost:8082>
- Rebuild phpMyAdmin: فعال روی <http://localhost:8084>
- MySQL Rebuild: healthy
- قالب فعال: `rahbar` نسخه `0.1.0`
- Repository مقصد: `git@github.com:vahidheerab/rahbar-rebuild.git`
- وضعیت Git هنگام ایجاد این checkpoint: فایل‌ها stage شده‌اند؛ commit/push نهایی هنوز باید تکمیل شود

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

- Block Theme اختصاصی `rahbar` نسخه 0.1.0 ساخته شد.
- `theme.json`، design tokenها، header، footer و templateهای اصلی ایجاد شدند.
- templateهای front page، page، single، archive، search و 404 اضافه شدند.
- patternهای Hero، Services و CTA اضافه شدند.
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

Rebuild با قالب `rahbar 0.1.0` بالا است، دیتابیس healthy است و صفحه اصلی HTTP 200 می‌دهد. هیچ داده واقعی Legacy، افزونه ثالث یا layout Elementor وارد Rebuild نشده است. فایل‌های مجاز برای اولین commit آماده‌اند.

## اقدام بعدی اصلی

اولین commit را ایجاد و branch `main` را به `origin` push کن؛ سپس hash commit و نتیجه push را در همین فایل ثبت کن.

## صف کار پس از اقدام اصلی

1. visual inventory کامل Home و Product در Legacy؛
2. تثبیت design tokenها و تکمیل header/footer مطابق baseline؛
3. اجرای `INF-ISO-001` و ثبت project/container/network/volume دو محیط؛
4. استخراج active pluginها و مالکیت داده هر plugin؛
5. ساخت prototype قابل مقایسه Home در Rebuild و اجرای responsive/RTL smoke test.

## تاریخچه نشست‌ها

### 2026-08-18 — `repo-bootstrap-and-continuity-log`

- `WORKLOG.md` به‌عنوان مرجع ادامه کار ایجاد شد.
- وضعیت واقعی Docker، قالب فعال و شمارش checklist دوباره بررسی شد.
- checkpoint فعلی، blockerها و اقدام بعدی ثبت شدند.
- نتیجه: آماده ایجاد اولین commit و push.
