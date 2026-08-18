# فهرست و تصمیم افزونه‌های Rahbar

تاریخ inventory: 2026-08-18. این سند تصمیم اولیه برای Rebuild است؛ حذف فیزیکی از Legacy مجاز نیست. وضعیت فعال/غیرفعال در 2026-08-18 مستقیماً از option `active_plugins` دیتابیس Legacy خوانده و با فایل‌های موجود تطبیق داده شد. این بررسی read-only بود.

معانی تصمیم‌ها:

- **Keep/Update**: نصب تمیز نسخه مصوب در Rebuild و migration فقط تنظیمات لازم.
- **Replace**: قابلیت حفظ می‌شود ولی plugin/پیاده‌سازی فعلی منتقل نمی‌شود.
- **Retire**: فقط پس از اثبات عدم استفاده، export داده لازم و تأیید rollback حذف می‌شود.
- **Audit first**: کد، داده، license و مسیرهای کسب‌وکار پیش از تصمیم نهایی بررسی می‌شوند.
- **Dev only**: فقط محیط توسعه/عیب‌یابی و نه production دائمی.

## خلاصه inventory

| مورد | تعداد/وضعیت |
|---|---|
| افزونه معمولی Legacy | 26 |
| MU-plugin | 4 |
| cache drop-in | 1 |
| نسخه‌های تکراری آشکار | `loginx` و `loginx1` |
| stackهای دارای هم‌پوشانی | cache/performance، payment، editor/page-builder |
| وضعیت active/inactive | 22 افزونه فعال و موجود، 4 افزونه غیرفعال و موجود، 1 ورودی فعال ولی مفقود (`wp-crontrol`) |

## تصمیم‌های قطعی مالک پروژه

- Legacy فقط برای الگو و تست است؛ هیچ پاک‌سازی یا توسعه‌ای روی آن انجام نمی‌شود.
- `wp-crontrol`، Kadence Security، TeraWallet و Rahbar CRM Connector به Rebuild منتقل نمی‌شوند.
- حذف فیزیکی موارد بالا از Legacy در scope نیست؛ فقط در مقصد نصب/مهاجرت نمی‌شوند و داده حساس لازم پیش از cutover برای reconciliation بررسی می‌شود.
- هنگام رسیدن به فاز نصب و جایگزینی افزونه‌ها، هر افزونه جداگانه با مالک پروژه بررسی و پس از تأیید او تعیین تکلیف می‌شود.

## وضعیت واقعی فعال‌بودن در Legacy

منبع حقیقت این بخش `wp_options.active_plugins` است. option مربوط به network activation وجود نداشت؛ این نصب multisite فعال ندارد.

- **فعال و موجود (22):** SMS.ir، Antispam Bee، Change Mail Sender، Classic Editor، Code Snippets، Elementor، Elementor Pro، Zibal Multiplexing Gateway، LiteSpeed Cache، LoginX 1.4 (`loginx`)، Perfmatters، Rahbar CRM Connector، Rank Math SEO/Pro، SpotPlayer، Advanced Editor Tools، MediaMan، Checkout Field Editor Pro، Advanced Order Export، TeraWallet، WooCommerce و WP-Parsidate.
- **غیرفعال و موجود (4):** Kadence Security Basic، LoginX 1.5 (`loginx1`)، Query Monitor و Rahbar IPG Gateway.
- **فعال ولی فایل مفقود (1):** `wp-crontrol/wp-crontrol.php` در option فعال است اما دایرکتوری آن در snapshot موجود نیست. این مورد پیش از هر migration یا بازسازی cron باید تعیین تکلیف شود؛ نصب یا حذف آن در Legacy در این مرحله انجام نشد.
- **همیشه فعال:** چهار MU-plugin ثبت‌شده در بخش پایین؛ `advanced-cache.php` نیز drop-in است و وضعیت آن مستقل از `active_plugins` است.

## مالکیت داده مشاهده‌شده

این جدول از نام جدول‌های واقعی دیتابیس و فایل‌های افزونه‌ها ساخته شده است. مالکیت option/meta/cron/endpoint هنوز باید در audit عمیق هر integration تکمیل شود؛ نبودن نام یک افزونه در این جدول به معنی بی‌داده‌بودن آن نیست.

| مالک/قابلیت | داده یا جدول مشاهده‌شده | نتیجه برای Rebuild |
|---|---|---|
| WooCommerce / Action Scheduler | `wp_wc_*`، `wp_woocommerce_*`، `wp_actionscheduler_*` و order/product meta | migration و reconciliation سفارش، محصول، دانلود و jobها اجباری است؛ جدول‌های HPOS نیز موجودند. |
| Elementor / Elementor Pro | `wp_e_*` به‌علاوه metadata داخل `wp_postmeta` | submissionها قبل از retire export شوند؛ layout metadata به قالب مقصد منتقل نمی‌شود. |
| Code Snippets | `wp_snippets` | snippetهای فعال باید جداگانه export، review و به کد version-controlled تبدیل شوند. |
| LoginX / سامانه ورود | `wp_loginx_types*`، `wp_logini_ips` و جدول‌های `wp_digits_*` | دو نسل داده ورود/OTP دیده می‌شود؛ مالک دقیق جدول‌های Digits و سازگاری حساب‌ها باید audit شود. |
| SMS.ir | `wp_sms_ir_app_log` و `wp_sms_ir_app_notifications` | تنظیمات، templateها و وضعیت ارسال بدون انتقال credential خام inventory شوند. |
| Zibal / payment | `wp_gf_zibal` و داده‌های سفارش/پرداخت WooCommerce | callback، شناسه تراکنش و idempotency پیش از consolidation تطبیق داده شوند. |
| TeraWallet | `wp_woo_wallet_transactions`، `wp_woo_wallet_transaction_meta`، `wp_woo_wallet_referrals` | مانده و ledger تعهد مالی‌اند و باید checksum/reconciliation مستقل داشته باشند. |
| Rank Math | `wp_rank_math_*` | redirect، 404، internal-link و analytics جدا از post meta نگهداری می‌شوند و باید تصمیم migrate/retire داشته باشند. |
| Kadence/iThemes Security | `wp_itsec_*` | افزونه اکنون غیرفعال است اما داده امنیتی باقی مانده؛ انتقال مستقیم این جداول توصیه نمی‌شود. |
| LiteSpeed Cache | `wp_litespeed_*` و `advanced-cache.php` | داده cache مهاجرت نمی‌کند؛ هنگام cutover باید purge/regenerate شود. |
| MediaMan | `wp_mclean_refs` و `wp_mclean_scan` | صرفاً داده ابزار inventory است و به production مقصد منتقل نمی‌شود. |
| LMS/Quiz/Ticket/سایر کدهای قدیمی | `wp_tutor_*`، `wp_quizmaker_*`، `wp_tkt_*`، `wp_xsmscore_*` و چند جدول YITH/Slider | جدول‌ها وجود دارند ولی افزونه مالک در snapshot نصب‌شده دیده نشد؛ این‌ها orphan یا dependency حذف‌شده محتمل‌اند و در `INV-DATA-002` باید تعیین مالک شوند. |

## افزونه‌های معمولی Legacy

| Plugin | نسخه محلی | تصمیم Rebuild | اولویت | دلیل و اقدام لازم |
|---|---:|---|---|---|
| Antispam Bee | 2.11.12 | Keep فقط اگر comment فعال است؛ وگرنه Retire | کم | نسخه محلی فعلاً جاری است؛ نیاز واقعی comment و تنظیمات privacy تست شود. |
| Kadence Security Basic (`better-wp-security`) | 10.0.2 | Do not migrate | قطعی | طبق تصمیم مالک پروژه به Rebuild منتقل نمی‌شود؛ امنیت مقصد مستقل طراحی و تست می‌شود. |
| Change Mail Sender | 1.3.0 | Replace | متوسط | From/Reply-To در integration ایمیل/SMTP version-controlled تنظیم شود؛ plugin تک‌کاربردی منتقل نشود. |
| Classic Editor | 1.7.0 | Retire | متوسط | تصمیم ADR-0001 استفاده از Block Editor است؛ فقط تا پایان تبدیل محتوای ناسازگار در Legacy بماند. |
| Code Snippets | 3.9.6 | Replace | بحرانی | همه snippetهای فعال export و code-review شوند، سپس هر قابلیت به plugin اختصاصی/version-controlled منتقل شود؛ اجرای کد از DB در Rebuild ممنوع. |
| Elementor | 4.1.3 | Replace/Retire | بحرانی | مطابق ADR-0001، layout metadata منتقل نشود و صفحات با Block Theme بازسازی شوند. |
| Elementor Pro | 4.1.0 | Replace/Retire | بحرانی | Theme Builder، Form، Popup و widgetهای استفاده‌شده inventory و با block/plugin مناسب جایگزین شوند. |
| Zibal Multiplexing Gateway | 1.6 | Audit first/Replace | بحرانی | gateway، callback، signature، split/multiplex و idempotency audit؛ در payment plugin مستقل و تست‌شده ادغام شود. |
| LiteSpeed Cache | 7.8.1 | Retire در Docker فعلی / تصمیم مجدد برای production | بالا | Apache فعلی LiteSpeed نیست؛ cache اختصاصی LiteSpeed server در دسترس نیست. فقط یک لایه optimization انتخاب شود؛ قبل از حذف purge و پاک‌سازی drop-in انجام شود. |
| LoginX (`loginx`) | 1.4 | Retire پس از audit | بحرانی | نسخه تکراری/قدیمی؛ داده و hookها با `loginx1` مقایسه و سپس حذف شود. |
| LoginX (`loginx1`) | 1.5.0 | Audit first/Replace | بحرانی | registration/login/OTP به plugin احراز هویت پشتیبانی‌شده و تست‌پذیر منتقل شود؛ coexistence دو نسخه ممنوع. |
| Perfmatters | 2.6.4 | Audit first | بالا | overlap با LiteSpeed و MU performance؛ فقط در صورت benchmark و license معتبر، یک مالک optimization باقی بماند. |
| Query Monitor | 4.0.7 | Dev only | کم | برای profiling مفید است؛ در production دائمی فعال نباشد و دسترسی debug عمومی نشود. |
| Rahbar CRM Connector | 1.0.1 | Do not migrate | قطعی | CRM دیگر موردنیاز نیست؛ cron و اتصال بیرونی آن در Rebuild ایجاد نمی‌شود. Legacy دست‌نخورده می‌ماند. |
| Rahbar IPG Gateway | 1.1.0 | Audit first/Consolidate | بحرانی | با درگاه Zibal و Payment Bridge هم‌پوشانی بررسی؛ یک مالک واحد برای payment/callback انتخاب شود. |
| Rank Math SEO | 1.0.272 | Update | بالا | نسخه عمومی 1.0.276 است؛ ابتدا backup metadata و staging upgrade، سپس sitemap/schema/canonical regression. |
| Rank Math SEO Pro | 3.0.115 | Update as matched pair | بالا | license و سازگاری دقیق با نسخه Core مصوب تأیید؛ free/pro جداگانه و بدون staging آپدیت نشوند. |
| SMS.ir (`SMSIRApp`) | 1.0.27 | Audit first/Replace | بالا | مسیرهای OTP/order notification، credential، retry و consent بررسی؛ adapter مستقل یا نسخه رسمی پشتیبانی‌شده انتخاب شود. |
| SpotPlayer | 17.0.1 | Audit first/Rebuild | بحرانی | course mapping، license، device، retry، revoke/refund و داده entitlement باید حفظ و integration تمیز ساخته شود. |
| Advanced Editor Tools | 5.9.2 | Retire محتمل | کم | با Block Editor نیاز آن باید موردی ثابت شود؛ فقط برای محتوای legacy ناسازگار موقتاً نگه‌داری شود. |
| MediaMan / Where is this Image Used | 1.0.2 | Migration tool only | کم | در inventory رسانه مفید است؛ به production Rebuild منتقل نشود. |
| Checkout Field Editor Pro | 2.1.9 | Audit first/Replace | بالا | fieldها، validation، order meta و email display inventory؛ ترجیح با Checkout Blocks/API یا plugin سازگار و حداقلی است. |
| WooCommerce | 10.8.1 | Keep/Update | بحرانی | نسخه عمومی 11.0.1 است؛ ارتقای major فقط پس از audit gateway/LMS/checkout/HPOS و اجرای regression کامل. |
| Advanced Order Export for WooCommerce | 4.1.0 | Keep if used | متوسط | نسخه محلی جاری و شامل fixهای SQLi/XSS است؛ preset/exportهای مالی inventory، و در صورت بی‌استفاده بودن retire شود. |
| TeraWallet | 1.6.4 | Do not migrate | قطعی | طبق تصمیم مالک پروژه قابلیت کیف پول به Rebuild منتقل نمی‌شود؛ مانده و ledger قدیمی فقط برای reconciliation و سوابق مالی حفظ می‌شوند. |
| WP-Parsidate | 6.0 | Update | متوسط | نسخه عمومی 6.2.1 است؛ تاریخ سفارش، sale schedule، timezone، sitemap و نمایش قیمت WooCommerce regression شود. |

## MU-pluginها و drop-in

| Component | تصمیم Rebuild | دلیل و اقدام لازم |
|---|---|---|
| `astra.php` | Retire | blocker آپدیت Astra است، در حالی‌که قالب Legacy Hello Elementor و مقصد Block Theme اختصاصی است؛ ابتدا اثبات کن hook حیاتی دیگری ندارد. |
| `codex-litespeed-guide.php` | Retire | راهنمای deployment نباید MU-plugin runtime باشد؛ محتوای مفید به docs منتقل شود. |
| `codex-performance.php` | Replace | tweakها خط‌به‌خط inventory؛ موارد معتبر به theme/plugin/config مقصد منتقل و با benchmark اثبات شوند. |
| `rahbar-wc-rest-optimizer.php` | Audit first | endpoint، permission، cache و اثر consistency بررسی؛ فقط در صورت benchmark و تست قرارداد به plugin مستقل منتقل شود. |
| `advanced-cache.php` | Retire/Regenerate | drop-in متعلق به cache فعلی است؛ دستی کپی نشود و فقط توسط راهکار cache نهایی مقصد ساخته شود. |

## ترتیب اجرا

1. وضعیت active/inactive و network/MU را از دیتابیس Legacy استخراج کن.
2. جدول مالکیت داده بساز: option، table، post type، taxonomy، meta، cron و endpoint هر plugin.
3. موارد بحرانی را ابتدا audit کن: payment، LoginX، CRM، SMS، SpotPlayer، Code Snippets، checkout و wallet.
4. prototype Block Theme را بدون Elementor بساز و قابلیت‌های Elementor Pro را یکی‌یکی map کن.
5. WooCommerce و pluginهای keep را روی نسخه‌های pinned در Rebuild تمیز نصب و تست کن؛ پوشه Legacy را کپی نکن.
6. pluginهای replace را فقط پس از پذیرش قابلیت جایگزین و migration داده retire کن.
7. pluginهای utility/dev را از production حذف و نتیجه را در QA checklist ثبت کن.

## Gate حذف هر افزونه

هیچ افزونه‌ای حذف نمی‌شود مگر اینکه همه موارد زیر ثبت شده باشد:

- فعال/غیرفعال بودن و آخرین استفاده تأیید شده؛
- داده، cron، endpoint، shortcode/block و dependency آن inventory شده؛
- export/migration و rollback داده موجود باشد؛
- قابلیت جایگزین تست و توسط مالک کسب‌وکار پذیرفته شده باشد؛
- log و smoke test پس از deactivate خطای جدید نشان ندهد؛
- حداقل یک backup و checksum معتبر پیش از حذف وجود داشته باشد.

## منابع نسخه عمومی

- [WooCommerce](https://wordpress.org/plugins/woocommerce/)
- [Rank Math SEO](https://wordpress.org/plugins/seo-by-rank-math/)
- [LiteSpeed Cache](https://wordpress.org/plugins/litespeed-cache/)
- [Kadence Security](https://wordpress.org/plugins/better-wp-security/)
- [TeraWallet](https://wordpress.org/plugins/woo-wallet/)
- [WP-Parsidate](https://wordpress.org/plugins/wp-parsidate/)
- [Advanced Order Export](https://wordpress.org/plugins/woo-order-export-lite/)
- [Advanced Editor Tools](https://wordpress.org/plugins/tinymce-advanced/)
