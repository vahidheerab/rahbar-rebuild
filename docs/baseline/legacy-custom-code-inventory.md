# موجودی کد اختصاصی Legacy

تاریخ بررسی: 2026-08-18

## دامنه و روش

Legacy فقط مرجع رفتار و محیط تست است. این بررسی read-only انجام شد و هیچ افزونه، داده یا تنظیمی در آن تغییر نکرد. برای جلوگیری از افشای secret یا اجرای کد ناامن، ستون `code` جدول `wp_snippets` نه اجرا و نه داخل repository ذخیره شد؛ فقط metadata و وجود الگوهای hook/shortcode با query بررسی شد. فایل‌های PHP موجود نیز به‌صورت static scan بررسی شدند.

## Code Snippets

- مجموع snippetها: 10
- فعال: 2
- غیرفعال/تاریخی: 8
- هر دو snippet فعال scope سراسری و priority برابر 10 دارند و shortcode ثبت می‌کنند:
  - `custom_pricing_table_nezamian`
  - `grad_pricing_section_shortcode_nezamian`
- در snippetهای غیرفعال نشانه‌هایی از API، SpotPlayer، category API و ثبت post type دیده شد. این‌ها به مقصد منتقل یا فعال نمی‌شوند مگر هنگام بررسی قابلیت مربوط، نیاز واقعی با مالک پروژه تأیید شود.
- تصمیم: کد DB مستقیماً import نمی‌شود. هر shortcode فعال ابتدا با نمونه ورودی/خروجی و محل استفاده بررسی و سپس در plugin یا block مقصد، version-controlled و تست‌پذیر بازنویسی می‌شود.

## کد اختصاصی داخل قالب Legacy

قالب فعال `hello-elementor` علاوه بر ظاهر، منطق دامنه‌ای زیر را در خود نگه می‌دارد:

| نوع | شناسه | محل مشاهده‌شده | تصمیم مقصد |
|---|---|---|---|
| Post type | `law_library` | `functions.php` | داده و URL inventory؛ در صورت نیاز به plugin دامنه‌ای منتقل شود، نه theme |
| Taxonomy | `law_category`، `law_tag` | `functions.php` | hierarchy و term count پیش از تصمیم مهاجرت ثبت شود |
| Shortcode | `askari_timer_product_sale_countdown` | `functions.php` | رفتار شمارش‌گر با قابلیت native/Block جایگزین شود |
| Shortcode | `reza_video_player` | `includes/video-metabox.php` | قرارداد media و دسترسی بررسی؛ کد Legacy کپی نشود |
| Shortcode | `ha_list` | `includes/product-metaboxes.php` | محل استفاده و وابستگی product meta بررسی شود |
| Post type/shortcode | `qa` و `qa_answer` | `includes/qa-post-type.php` | تعداد داده، دسترسی و نمایش پاسخ‌ها قبل از تصمیم مهاجرت ثبت شود |

این پخش‌شدن منطق در theme یک بدهی معماری Legacy است. در Rebuild، theme فقط مالک presentation خواهد بود و منطق دامنه‌ای تأییدشده داخل plugin مستقل قرار می‌گیرد.

## MU-pluginها و integrationهای حساس

| جزء | قرارداد مشاهده‌شده | تصمیم |
|---|---|---|
| `codex-performance.php` | shortcode با نام `codex_header_promo`، cache purge و cron روزانه `codex_rankmath_cleanup_404` | کپی مستقیم ممنوع؛ shortcode در UI مقصد و cleanup فقط در صورت نیاز با تست بازسازی شود |
| `rahbar-wc-rest-optimizer.php` | تغییر رفتار WooCommerce REST | قرارداد endpoint و permission در audit مستقل بررسی شود |
| SpotPlayer | endpoint حساب `licenses` و shortcode `spotplayer_courses` | در audit SpotPlayer، خرید/صدور/نمایش/revoke یکپارچه تست شود |
| Zibal | callback از مسیر WooCommerce API | در audit پرداخت با idempotency و reconciliation بررسی شود |
| SMS.ir | AJAX مربوط به OTP/Digits و hookهای متعدد سفارش/موجودی | فقط جریان‌های موردنیاز پس از تأیید کاربر بازسازی شوند |
| Rahbar CRM Connector | cron یک‌دقیقه‌ای، sync سفارش/محصول و admin actions | طبق تصمیم مالک پروژه به Rebuild منتقل نمی‌شود |

## یافته‌های باز و Gate

- محل استفاده واقعی شش shortcode شناخته‌شده باید با جست‌وجوی محتوای دیتابیس تعیین شود.
- تعداد entityهای `law_library` و `qa` و termهایشان در `INV-DATA-001` ثبت شود.
- cronهای ثبت‌شده و callbackهای بیرونی در `INV-EXT-001` با وضعیت واقعی inventory شوند.
- هیچ snippet یا کد theme به Rebuild منتقل نمی‌شود مگر قابلیت آن جداگانه با کاربر بررسی و پذیرش شود.

## اقدام بعدی

اجرای `INV-DATA-001`: شمارش read-only post type، taxonomy، status و meta keyهای سفارشی Legacy، با تمرکز بر `law_library`، `qa`، محصول و داده‌های دوره.
