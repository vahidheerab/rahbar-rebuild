# Inventory مدل داده Legacy

وضعیت: در حال انجام (`INV-DATA-001`)

تاریخ شروع: 2026-08-22

این بررسی مستقیماً از دیتابیس Legacy و فقط با queryهای `SELECT` انجام شد. هیچ مقدار meta، داده شخصی، secret یا محتوای سفارش در این سند ثبت نشده است.

> **محدودیت زمانی snapshot:** دیتابیس Legacy موجود در محیط توسعه تقریباً یک ماه از production عقب‌تر است. شمارش‌های این سند فقط برای شناخت schema، وابستگی‌ها، طراحی allowlist و rehearsal معتبرند و نباید به‌عنوان شمارش نهایی migration یا منبع حقیقت Cutover استفاده شوند. منبع حقیقت انتقال نهایی، دیتابیس زنده سایت اصلی در زمان پنجره انتقال است.

## خلاصه Post Typeها

| خانواده | نمونه‌ها و شمارش مهم |
|---|---|
| محتوای عمومی | `page`: 35، `post`: 158، `attachment`: 956 |
| فروشگاه | `product`: 42، `shop_coupon`: 152، `shop_order`: 8,583 |
| سؤال و آموزش | `qa`: 586، `courses`: 6، `lesson`: 32، `topics`: 26 |
| Tutor/LMS باقیمانده | `tutor_quiz`: 13، `tutor_assignments`: 13، `tutor_enrolled`: 3، `tutor_announcements`: 1 |
| ویدئو و Story | `pp_video_block`: 158، `herowp-story`: 11، `aparat_slider`: 21 |
| Elementor/layout | `elementor_library`: 27، `herowpheader`: 9، `herowpfooter`: 9، `herowpmega`: 2 |
| سایر مدل‌های سفارشی | `library`: 5، `law_library`: 2، `portfolio`: 1، `teacher`: 1، `blogger`: 1 |

## وضعیت سفارش‌ها

| Status | Count |
|---|---:|
| `wc-completed` | 5,483 |
| `wc-processing` | 2,550 |
| `wc-cancelled` | 407 |
| `wc-arrival-shipment` | 93 |
| `wc-on-hold` | 43 |
| `wc-failed` | 6 |
| `wc-pending` | 1 |

`wc-arrival-shipment` یک status سفارشی است و باید پیش از migration مالک رفتار، transition و اثر آن روی پرداخت/تحویل مشخص شود.

## Taxonomyهای مشاهده‌شده

- WordPress: `category`، `post_tag`، `nav_menu` و `post_format`
- WooCommerce: `product_cat`، `product_type`، `product_visibility` و attributeهای `pa_*`
- آموزش: `course-category` و `course-tag`
- مدل‌های سفارشی: `law_category`، `library_category`، `portfolio_category` و `dwqa-question_category`
- ابزارها/layout: taxonomyهای Elementor، WPCode و theme

نام سه product attribute فارسی در خروجی CLI به‌دلیل encoding کنسول مخدوش بود؛ slug دقیق آن‌ها باید با اتصال `utf8mb4` دوباره ثبت شود.

## Meta keyهای پرریسک و مالکیت اولیه

۵۳۵ post meta key، ۱۹۵ user meta key و ۱۱ term meta key متمایز مشاهده شد. انتقال allowlist ضروری است.

| خانواده | کلیدهای شاخص | تصمیم اولیه |
|---|---|---|
| سفارش WooCommerce | `_order_total`، `_order_currency`، `_customer_user`، `_transaction_id` و billing/shipping meta | migrate و reconcile؛ داده حساس در Evidence ثبت نشود |
| پرداخت Zibal | `zibal_payment_ref_number`، `zibal_payment_card_number` | audit درگاه و redaction پیش از migration |
| SpotPlayer | `_spotplayer_data` | قرارداد entitlement/license پیش از انتقال |
| Wallet | `_wallet_rechargeable_order` | طبق تصمیم مالک به Rebuild منتقل نشود؛ فقط reconciliation تاریخی |
| Checkout سفارشی | `_billing_myhead`، `_billing_myid`، `_billing_post`، `_billing_sherkat`، `_billing_rfact` | mapping و ضرورت قانونی هر فیلد تعیین شود |
| Elementor | `_elementor_data`، `_elementor_page_settings`، `_elementor_template_type` | layout metadata منتقل نشود |
| SEO | `rank_math_*`، `_yoast_wpseo_*` و schema metaهای پراکنده | یک source of truth مقصد انتخاب شود |
| سؤال/ویدئو | `_qa_answer`، `reza_video_url`، `reza_video_poster` | مالک post type و قرارداد مقصد تعیین شود |

## یافته‌های باز

1. مرز میان مدل‌های فعال فعلی و داده‌های orphan مربوط به Tutor، YITH، HeroWP و ابزارهای قبلی هنوز تعیین نشده است.
2. وجود هم‌زمان Rank Math، Yoast و schema metaهای سفارشی نیازمند تصمیم canonical SEO است.
3. status سفارشی سفارش و metaهای پرداخت/SpotPlayer باید پیش از طراحی migration به قرارداد رفتاری متصل شوند.
4. فهرست کامل meta keyها باید بر اساس post type دسته‌بندی و به `migrate`، `transform`، `archive-only` یا `retire` تقسیم شود.

## ادامه مستقیم

- استخراج meta keyها به تفکیک post type بدون خواندن مقدارها؛
- ثبت slug صحیح attributeهای فارسی با اتصال `utf8mb4`؛
- تفکیک مدل‌های فعال از orphanها با تطبیق plugin ownership؛
- ساخت allowlist اولیه migration برای content، commerce، LMS و SEO.

Allowlist اولیه و چک‌لیست اجرای دستی در `docs/migration/MANUAL-DATA-MIGRATION-CHECKLIST.md` ثبت شد. تصمیم نهایی metaهای سفارشی دوره، status سفارش، SEO و entitlement به تأیید مالک/مسئول مربوطه وابسته است.

## سیاست منبع داده در انتقال نهایی

- snapshot فعلی برای توسعه migration script و rehearsal استفاده می‌شود.
- هیچ ID، count، مبلغ کل یا timestamp این snapshot معیار reconciliation نهایی نیست.
- پیش از Cutover از production یک baseline تازه و checksum‌دار گرفته می‌شود.
- سفارش، پرداخت، کاربر، entitlement و تغییرات محتوایی ایجادشده پس از baseline با delta migration منتقل می‌شوند.
- در لحظه نهایی، Legacy برای یک بازه کوتاه read-only می‌شود تا آخرین delta بدون race منتقل و reconcile شود.
- در صورت نیاز واقعی به همگام‌سازی پیوسته پیش از freeze، باید ابزار CDC/queue جداگانه طراحی و تست شود؛ اجرای query دستی یا sync دوطرفه بدون idempotency مجاز نیست.
