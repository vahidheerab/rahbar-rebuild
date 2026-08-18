# راهنمای کامل بازسازی و انتقال Rahbar

این سند runbook اصلی انتقال از سایت فعلی (`legacy`) به وردپرس تمیز (`rebuild`) است. مراحل را به‌ترتیب انجام دهید و هیچ مرحله داده‌ای را بدون backup معتبر جلو نبرید.

نقشه کامل شناسایی قابلیت‌ها، معماری مقصد، روش انتقال هر نوع داده و معیار پایان کار در `REBUILD-ROADMAP.md` نگه‌داری می‌شود. این دو سند باید هم‌زمان به‌روز شوند.

## 1. وضعیت مبنا

| مورد | Legacy | Rebuild |
|---|---|---|
| مسیر | `legacy/site` | `rebuild/site` |
| URL لوکال | `http://localhost:8081` | `http://localhost:8082` |
| WordPress | 7.0.2 | 7.0.2 تمیز |
| PHP | 8.1.32 | 8.5 |
| دیتابیس Docker | MySQL 8.4.3 | MySQL 8.4.3 |
| Compose project | `rahbar-legacy` | `rahbar-rebuild` |

> از 2026-08-18 نام پروژه از `lastrahbar` به `rahbar` تغییر کرده است. چون نام Compose بخشی از نام volumeهاست، محیط‌های ساخته‌شده با نام قبلی به‌صورت خودکار به volumeهای جدید متصل نمی‌شوند؛ پیش از جابه‌جایی داده از dump و checksum معتبر استفاده کنید و volume قدیمی را تا پایان تأیید import حذف نکنید.

Legacy همچنین SourceGuardian Loader و ionCube Loader رسمی PHP 8.1 را برای اجرای فایل‌های رمزگذاری‌شده موجود در snapshot نصب می‌کند. این loaderها فقط نیاز سازگاری legacy هستند و نباید بدون اثبات نیاز به rebuild منتقل شوند.

مبنای 2026-08-17: تعداد 137 جدول، حدود 339.72 MB داده و 4.46 GB فایل. dump اولیه در `legacy/backups/legacy-before-docker.sql` است و نباید commit شود.

## 2. پیش‌نیازها

- Docker Desktop اجرا باشد و `docker version` هر دو بخش Client و Server را نشان دهد.
- پورت‌های 8081 و 8082 آزاد باشند.
- حداقل 12 GB فضای آزاد برای imageها، دو دیتابیس، uploads و cache در نظر بگیرید.
- پیش از کار روی داده واقعی، ابزارهای backup/antivirus نباید فایل‌ها را قفل کرده باشند.

## 3. راه‌اندازی Legacy

```powershell
docker compose -f legacy/compose.yaml config
docker compose -f legacy/compose.yaml up -d --build
docker compose -f legacy/compose.yaml ps
```

در اولین اجرا، MySQL فقط وقتی volume خالی است dump را import می‌کند. سپس `http://localhost:8081`، ورود مدیریت، صفحات کلیدی، محصول/دوره، checkout و callback پرداخت را بررسی کنید. حذف volume دیتابیس فقط با backup معتبر مجاز است.

## 4. راه‌اندازی Rebuild تمیز

```powershell
docker compose -f rebuild/compose.yaml config
docker compose -f rebuild/compose.yaml up -d
docker compose -f rebuild/compose.yaml ps
```

در `http://localhost:8082` نصب تمیز را کامل و زبان، timezone، permalink و نقش‌های پایه را تنظیم کنید. هیچ افزونه یا theme قدیمی را در این مرحله کپی نکنید. نسخه PHP و WordPress را در Site Health کنترل کنید.

### نکته permalink در Legacy

نسخه Laragon در subdirectory به نام `/rahbar/` اجرا می‌شود، اما Docker آن را در ریشه پورت 8081 سرو می‌کند. بنابراین WordPress rewrite در `legacy/site/.htaccess` باید `RewriteBase /` و مقصد `/index.php` داشته باشد. بازگرداندن `/rahbar/` باعث حلقه internal redirect و HTTP 500 روی تمام صفحات داخلی می‌شود.

## 5. فهرست‌برداری پیش از انتقال

- theme فعال، child theme و customizationها؛
- افزونه‌ها و مالک هر قابلیت؛
- post type، taxonomy، shortcode و block سفارشی؛
- کاربران، نقش‌ها و capabilityها؛
- WooCommerce: محصولات، سفارش، coupon، webhook و درگاه؛
- LMS/SpotPlayer: دوره، دسترسی و کلید خارجی؛
- cron، email، API، callback و secret؛
- media، فایل خصوصی و مسیر غیرعمومی؛
- SEO metadata، redirect، sitemap و analytics؛
- جدول‌های سفارشی، حجم و حساسیت آن‌ها.

برای هر مورد تصمیم `rebuild`، `replace`، `migrate data only` یا `retire` ثبت شود.

## 6. ترتیب بازسازی

1. design system، header/footer و layout پایه؛
2. مدل محتوا و fieldها؛
3. صفحات عمومی و جست‌وجو؛
4. حساب کاربری و نقش‌ها؛
5. فروشگاه و checkout؛
6. LMS/ویدئو و entitlement؛
7. پرداخت و callback؛
8. SEO، redirect، analytics و email؛
9. performance، accessibility و security hardening.

هر قابلیت ابتدا با داده ساختگی تکمیل شود؛ داده واقعی آخرین مرحله است.

## 7. انتقال داده آزمایشی

1. dump تازه legacy و checksum آن را با timestamp ثبت کنید.
2. پیش از import از دیتابیس rebuild snapshot بگیرید.
3. migration script باید تکرارپذیر و idempotent باشد؛ SQL دستی ثبت‌نشده ممنوع است.
4. users/roles، سپس content/taxonomy، سپس commerce/LMS و در پایان metadata منتقل شود.
5. URLها را با WP-CLI `search-replace` یا ابزار serialization-aware عوض کنید؛ SQL replace خام ممنوع است.
6. شمارش رکوردها، نمونه تصادفی و مجموع‌های مالی را تطبیق دهید.
7. زمان، خطا و rollback هر rehearsal ثبت شود.

## 8. Exit Gate

- صفحات کلیدی روی desktop/mobile تأیید شده‌اند.
- login، reset password، نقش‌ها و محتوای خصوصی درست است.
- checkout موفق/ناموفق، callback تکراری و refund در sandbox تست شده‌اند.
- کاربران، محتوا، سفارش‌ها و entitlementها با reconciliation مطابق‌اند.
- لینک شکسته، mixed content، خطای PHP/console و job شکست‌خورده وجود ندارد.
- Core Web Vitals، cache، backup/restore و security scan قابل قبول‌اند.
- canonical، robots، sitemap، schema و redirectها تأیید شده‌اند.
- secretهای لوکال با secretهای محیط مقصد جایگزین شده‌اند.

## 9. Cutover

1. TTL دامنه را از قبل کاهش دهید و maintenance window/مسئول rollback را تعیین کنید.
2. legacy را read-only کنید و زمان freeze را ثبت کنید.
3. dump نهایی و media delta بگیرید؛ checksum و شمارش‌ها را تأیید کنید.
4. migration نهایی و search-replace دامنه را اجرا کنید.
5. smoke test صفحه اصلی، login، checkout، پرداخت، callback و email انجام دهید.
6. DNS/reverse proxy را تغییر دهید و logها را پایش کنید.
7. legacy و backup نهایی را تا پایان دوره نگه‌داری حفظ کنید.

## 10. Rollback

در خرابی پرداخت، login، entitlement، integrity یا availability: نوشتن rebuild را متوقف کنید؛ route/DNS را به legacy برگردانید؛ داده ایجادشده در بازه cutover را برای reconciliation نگه دارید؛ هیچ volume یا dump را حذف نکنید؛ علت و داده نیازمند ادغام را ثبت کنید.

## 11. عملیات روزمره

```powershell
docker compose -f legacy/compose.yaml ps
docker compose -f rebuild/compose.yaml ps
docker compose -f legacy/compose.yaml logs --tail=200
docker compose -f rebuild/compose.yaml logs --tail=200
docker compose -f legacy/compose.yaml stop
docker compose -f rebuild/compose.yaml stop
```

از `down -v` در کار روزمره استفاده نکنید؛ `-v` دیتابیس را حذف می‌کند.

## 12. وضعیت

چک‌لیست ریزدانه، Evidence و نقطه ادامه روزانه در `openspec/changes/rebuild-qa-checklist/tasks.md` نگهداری می‌شود. وضعیت این بخش خلاصه فازهاست و باید در پایان هر نشست با آن چک‌لیست همگام شود.

- [x] baseline و dump اولیه
- [x] تفکیک `legacy` و `rebuild`
- [x] زیرساخت Docker مستقل
- [ ] اجرای Docker و import کامل legacy
- [ ] نصب تمیز rebuild
- [ ] inventory قابلیت‌ها و داده‌ها
- [ ] پیاده‌سازی نسخه جدید
- [ ] rehearsal انتقال داده
- [ ] QA و Exit Gate
- [ ] cutover و پایش
