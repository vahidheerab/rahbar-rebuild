# چک‌لیست دستی انتقال داده Rahbar

این سند چک‌لیست اپراتور برای rehearsal و Cutover است. اجرای هر مرحله و تأیید نتیجه دستی است، اما انتقال رکوردها باید با اسکریپت نسخه‌شده و تکرارپذیر انجام شود؛ ویرایش مستقیم SQL، کپی کامل جدول‌ها و `REPLACE` خام روی URLها مجاز نیست.

اسکریپت اجرایی واحد در `scripts/migration/Invoke-RahbarCutover.ps1` قرار دارد. این فایل اکنون preflight، snapshot، SHA-256، baseline، high-water mark و reconciliation را اجرا می‌کند. اجرای `Cutover` تا ارائه migration adapter تأییدشده برای مدل نهایی payment/LMS/SpotPlayer عمداً fail-closed است.

```powershell
.\scripts\migration\Invoke-RahbarCutover.ps1 -Action Preflight
.\scripts\migration\Invoke-RahbarCutover.ps1 -Action Baseline -EvidenceDirectory D:\rahbar-migration-evidence\run-001
.\scripts\migration\Invoke-RahbarCutover.ps1 -Action Snapshot -EvidenceDirectory D:\rahbar-migration-evidence\run-001 -WhatIf
.\scripts\migration\Invoke-RahbarCutover.ps1 -Action Snapshot -EvidenceDirectory D:\rahbar-migration-evidence\run-001
.\scripts\migration\Invoke-RahbarCutover.ps1 -Action Cutover -EvidenceDirectory D:\rahbar-migration-evidence\run-final -MigrationAdapter D:\secure\rahbar-migration-adapter.ps1 -ProductionConfirmed -FreezeConfirmed
```

> دیتابیس Legacy محیط توسعه تقریباً یک ماه قدیمی است. شمارش‌های فعلی فقط baseline توسعه‌اند. روز انتقال، تمام شمارش‌ها و checksumها باید از سایت زنده production دوباره گرفته شوند.

## 1. ماتریس تصمیم انتقال

| داده | تصمیم | روش | کنترل دستی الزامی |
|---|---|---|---|
| کاربران پایه | `migrate` | انتقال `users` و metaهای هویت ضروری با حفظ password hash سازگار | count، سه login نمونه، duplicate email/username |
| role و capability | `transform` | mapping فقط به نقش‌های مصوب Rebuild | تست دسترسی مثبت و منفی هر نقش |
| session، OTP موقت و reset token | `retire` | منتقل نشود؛ sessionهای مقصد تازه باشند | ورود مجدد کاربران و reset password |
| نوشته‌ها و برگه‌های مصوب | `migrate` | post، taxonomy و metaهای محتوایی allowlist | count، slug، تاریخ، نویسنده و ۱۰ نمونه تصادفی |
| Elementor layout/template meta | `retire` | `_elementor_*` منتقل نشود | صفحات مقصد با Block Theme بررسی شوند |
| پرسش‌های `qa` | `transform` | post، پاسخ `_qa_answer` و ویدئوی موردنیاز به مدل مقصد | count، سؤال/پاسخ و video URL نمونه |
| محصولات WooCommerce | `migrate/transform` | محصول، category، attribute، قیمت، stock، تصویر و metaهای تجاری allowlist | count، SKU/slug، قیمت، موجودی و خرید نمونه |
| metaهای سفارشی محصول دوره | `transform` | `jalasat`، `time`، `sath`، `madrak`، `zaban`، `dars` و خانواده `p*` پس از mapping | تطبیق صفحه محصول و مشخصات دوره نمونه |
| schema/SEO قدیمی محصول | `transform` | از Yoast/Rank Math/schema پراکنده به یک مدل SEO مقصد | title، canonical، description و schema نمونه |
| سفارش‌ها | `migrate` | سفارش، customer، status، total، currency، تاریخ و note با حفظ source ID | count هر status، مجموع مالی، ۲۰ سفارش نمونه |
| line item سفارش | `migrate` | product/variation، qty، subtotal، total، tax و coupon | total سفارش و اتصال به محصول/کاربر |
| transaction reference | `migrate-restricted` | `_transaction_id` و referenceهای لازم Zibal با redaction در log | uniqueness و تطبیق با سفارش؛ اطلاعات کارت نمایش داده نشود |
| status سفارشی `wc-arrival-shipment` | `transform` | mapping به status مصوب یا حفظ کنترل‌شده | شمارش ۹۳ رکورد snapshot و رفتار سفارش نمونه |
| Wallet/TeraWallet | `archive-only` | ledger و مانده فقط برای سوابق و reconciliation خارج از قابلیت فعال مقصد | مجموع مانده و sign-off مالی؛ wallet در Rebuild فعال نشود |
| دوره و درس Tutor | `transform` | `courses`، `lesson`، topic، prerequisite و metaهای آموزشی به مدل مصوب | count، ترتیب درس، preview و prerequisite |
| enrollment/progress Tutor | `transform` | انتقال فقط پس از قرارداد entitlement | کاربر نمونه، درصد پیشرفت و عدم دسترسی غیرمجاز |
| SpotPlayer | `transform-restricted` | mapping کاربر/سفارش/دوره و license با idempotency | ۱۰ entitlement نمونه، retry و refund/revoke |
| coupon | `migrate` | code، amount، type، expiry، restriction و usage | coupon فعال/منقضی و usage count |
| media عمومی | `migrate` | فایل، attachment، alt، parent و metadata با checksum | count/checksum و broken link scan |
| media خصوصی/دانلودی | `transform-restricted` | انتقال خارج از public path با کنترل دسترسی | کاربر مجاز/غیرمجاز و URL غیرقابل حدس |
| redirect و SEO metadata | `transform` | allowlist به یک افزونه SEO مقصد | crawl نمونه، redirect بدون chain/loop |
| cache، transient و session | `retire` | منتقل نشود و در مقصد بازسازی شود | purge و cold/warm smoke test |
| log و analytics خام | `archive-only` | خارج از DB عملیاتی مقصد نگهداری شود | retention و دسترسی محدود |
| Code Snippets و کد داخل DB | `retire/replace` | متن کد اجرا یا migrate نشود؛ قابلیت لازم در plugin نسخه‌شده بازسازی شود | code review و test ID هر قابلیت |
| داده pluginهای orphan/retired | `archive-only/retire` | فقط پس از تأیید مالک حذف از scope انتقال | snapshot قابل‌بازیابی و sign-off |
| secret و credential | `retire/re-provision` | از production کپی نشود؛ در مقصد جداگانه تنظیم شود | اتصال sandbox/production و عدم افشا در Git/log |

## 2. Allowlist اولیه Meta

این فهرست اولیه است و پیش از اولین rehearsal باید در migration script به نسخه مشخص تبدیل شود.

### کاربران

- شناسه مبدأ، login، email، password hash سازگار، display name و تاریخ ثبت؛
- first/last name و اطلاعات profile مصوب؛
- role/capability فقط پس از mapping؛
- metaهای session، cache، OTP موقت، tracking و UI preference منتقل نشوند.

### محصولات

- `_sku`، `_regular_price`، `_sale_price`، `_price`؛
- `_stock`، `_stock_status`، `_manage_stock`، `_backorders`؛
- `_virtual`، `_downloadable`، `_download_limit`، `_download_expiry`؛
- `_thumbnail_id`، gallery و taxonomyهای product؛
- metaهای دوره فقط پس از mapping مستند؛
- `_elementor_*`، edit lock، view count و cache meta منتقل نشوند.

### سفارش‌ها

- `_customer_user`، `_order_key`، `_order_currency` و total/tax/shipping/discount؛
- billing/shipping موردنیاز عملیات و الزامات مالی با حداقل‌سازی PII؛
- `_payment_method`، `_payment_method_title`، `_transaction_id` و reference لازم gateway؛
- `_date_paid`، `_date_completed` و status؛
- line itemهای `_product_id`، `_variation_id`، `_qty`، subtotal/total/tax؛
- `_spotplayer_data` فقط از مسیر adapter انتقال entitlement؛
- Wallet، attribution marketing، user agent، IP و export flags فقط در صورت نیاز قانونی/عملیاتی؛ پیش‌فرض منتقل نشوند.

### محتوا و SEO

- title، content، excerpt، status، slug، author، dates و taxonomy؛
- featured image و alt؛
- canonical SEO fields منتخب پس از تصمیم یک source of truth؛
- Elementor، schemaهای تکراری، edit lock و metadata ابزارهای retired منتقل نشوند.

## 3. آماده‌سازی قبل از Rehearsal

- [ ] نسخه migration script و commit دقیق ثبت شود.
- [ ] mapping شناسه‌های source→target برای همه entityها وجود داشته باشد.
- [ ] script روی اجرای مجدد duplicate نسازد.
- [ ] مقصد قبل از import snapshot و راه restore داشته باشد.
- [ ] dump منبع با timestamp و SHA-256 در محل خارج از Git ثبت شود.
- [ ] هیچ secret، dump، PII یا log حساس وارد repository نشود.
- [ ] جدول count و مجموع مالی قبل از انتقال پر شود.

## 4. ترتیب اجرای دستی Rehearsal

- [ ] 1. Rebuild را backup و checksum کن.
- [ ] 2. کاربران و mapping شناسه را منتقل کن.
- [ ] 3. role/capability را transform و login نمونه را تست کن.
- [ ] 4. taxonomyها و media عمومی را منتقل کن.
- [ ] 5. نوشته‌ها، برگه‌ها، سؤال‌ها و SEO منتخب را منتقل کن.
- [ ] 6. محصولات، attributeها، قیمت و stock را منتقل کن.
- [ ] 7. سفارش‌ها، line itemها، couponها و transaction referenceها را منتقل کن.
- [ ] 8. دوره، درس، enrollment و progress را transform کن.
- [ ] 9. entitlement و SpotPlayer mapping را اجرا کن.
- [ ] 10. URLها را فقط با ابزار serialization-aware جایگزین کن.
- [ ] 11. cache و rewrite مقصد را rebuild کن.
- [ ] 12. migration را بار دوم اجرا و عدم duplicate را ثابت کن.
- [ ] 13. reconciliation و smoke test را کامل کن.
- [ ] 14. rollback rehearsal را اجرا و زمان واقعی را ثبت کن.

## 5. جدول کنترل قبل و بعد

این جدول باید برای هر rehearsal و Cutover با اعداد همان dump تکمیل شود؛ اعداد snapshot فعلی را روز Cutover کپی نکن.

| شاخص | مبدأ قبل | مقصد بعد | اختلاف | تأیید اپراتور |
|---|---:|---:|---:|---|
| کاربران کل |  |  |  | [ ] |
| کاربران هر role |  |  |  | [ ] |
| نوشته/برگه منتشرشده |  |  |  | [ ] |
| سؤال و پاسخ |  |  |  | [ ] |
| محصولات منتشرشده |  |  |  | [ ] |
| مجموع stock محصولات مدیریت‌شده |  |  |  | [ ] |
| coupon فعال/منقضی |  |  |  | [ ] |
| سفارش هر status |  |  |  | [ ] |
| مجموع مبلغ سفارش هر status/currency |  |  |  | [ ] |
| transaction ID یکتا |  |  |  | [ ] |
| دوره و درس |  |  |  | [ ] |
| enrollment/progress |  |  |  | [ ] |
| entitlement/SpotPlayer license |  |  |  | [ ] |
| attachment و فایل media |  |  |  | [ ] |
| redirect و SEO record |  |  |  | [ ] |

هر اختلاف باید entity ID، علت، تصمیم و مالک داشته باشد. اختلاف حل‌نشده در سفارش، مبلغ، transaction یا entitlement مانع Cutover است.

## 6. اجرای دستی روز Cutover

- [ ] مسئول فنی، مالی و تصمیم rollback حاضر باشند.
- [ ] full dump تازه production و checksum گرفته شود.
- [ ] high-water mark کاربران، سفارش‌ها، محتوا و media ثبت شود.
- [ ] full migration روی Rebuild اجرا شود.
- [ ] تا قبل از freeze، delta یک‌طرفه و idempotent اجرا شود.
- [ ] Legacy read-only و timestamp دقیق freeze ثبت شود.
- [ ] delta نهایی کاربران، سفارش‌ها، پرداخت‌ها، محتوا، media و entitlement اجرا شود.
- [ ] جدول reconciliation بخش 5 کامل و توسط مسئول مالی/فنی امضا شود.
- [ ] Home، login، product، cart، checkout، payment callback، account و course smoke test شوند.
- [ ] فقط پس از تأیید، DNS/route به Rebuild تغییر کند.
- [ ] Legacy و backupها تا پایان hypercare حذف نشوند.

## 7. Triggerهای توقف و Rollback

در هرکدام از موارد زیر Cutover متوقف یا rollback شود:

- اختلاف حل‌نشده در تعداد یا مجموع مالی سفارش‌ها؛
- transaction تکراری یا سفارش بدون payment mapping؛
- entitlement مفقود، تکراری یا متعلق به کاربر اشتباه؛
- login کاربران یا دسترسی مدیر مختل باشد؛
- callback پرداخت idempotent نباشد؛
- فایل خصوصی برای کاربر غیرمجاز قابل دریافت باشد؛
- migration اجرای مجدد امن نداشته باشد؛
- backup/restore یا مسیر بازگشت قابل اثبات نباشد.

## 8. ثبت هر اجرا

| مورد | مقدار |
|---|---|
| Run ID |  |
| تاریخ و timezone |  |
| اپراتور |  |
| source host/database | در محل امن، خارج از Git |
| source dump timestamp |  |
| source SHA-256 | در محل امن، خارج از Git |
| migration commit/version |  |
| full migration start/end |  |
| freeze timestamp |  |
| final delta start/end |  |
| reconciliation result |  |
| smoke result |  |
| تصمیم Go/No-Go |  |
| rollback owner |  |
