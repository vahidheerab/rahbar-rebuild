# تست Benefits صفحه اصلی Rebuild

- تاریخ اجرا: 2026-08-18
- نسخه قالب: `rahbar 0.3.0`
- محیط: `http://localhost:8082`
- الگو: `rahbar/benefits`

## دامنه

چهار ارزش پیشنهادی ثبت‌شده در baseline Legacy، بدون انتقال Elementor، به یک Block Pattern مستقل تبدیل شدند:

1. بیش از ۲۰ دوره کاربردی؛
2. ارائه مدرک معتبر؛
3. کافه سؤال حسابداری؛
4. پشتیبانی ۷/۲۴.

## رفتار واکنش‌گرا

| Viewport | چیدمان مورد انتظار | نتیجه | Evidence |
|---|---|---|---|
| 1440×900 | چهار ستون | Pass | `rebuild-home-benefits-desktop-1440.png` |
| 768×1024 | دو ستون و دو ردیف | Pass | `rebuild-home-benefits-tablet-768.png` |
| 375×812 | یک ستون | Pass | `rebuild-home-benefits-mobile-375.png` |

فایل‌های Evidence به‌صورت full-page و با Playwright/Microsoft Edge از محیط Rebuild تولید شدند.

## ریزتست‌ها

| تست | نتیجه |
|---|---|
| ثبت و parse شدن pattern در WordPress | Pass |
| رندر چهار کارت در صفحه اصلی | Pass — دقیقاً ۴ کارت |
| ترتیب و محتوای RTL | Pass |
| clipping یا overflow افقی قابل مشاهده | Pass — مشاهده نشد |
| خوانایی متن و کنتراست کارت‌های متناوب | Pass در smoke test بصری |
| syntax فایل‌های PHP | Pass |
| بارگذاری stylesheet نسخه `0.3.0` | Pass |
| پاسخ صفحه اصلی | Pass — HTTP 200 |
| خطای PHP در Docker log | Pass — موردی مشاهده نشد |

## تصمیم پیاده‌سازی

چیدمان با CSS Grid و breakpointهای قالب ساخته شد تا محدودیت باریک‌شدن چهار ستون Legacy در tablet تکرار نشود. نشان‌های کارت‌ها با pseudo-element تزئینی CSS ساخته شدند و محتوای اصلی هر کارت در heading و paragraph باقی ماند.
