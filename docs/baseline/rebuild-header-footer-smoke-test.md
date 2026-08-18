# تست واکنش‌گرایی Header و Footer بازسازی

- تاریخ اجرا: 2026-08-18
- نسخه قالب: `rahbar 0.2.0`
- محیط: `http://localhost:8082`

## دامنه تغییر

- تثبیت tokenهای رنگ، فاصله، radius و shadow مشترک Home و Product؛
- تکمیل top bar، هویت سایت، جست‌وجو، navigation و CTAهای حساب/سبد خرید؛
- تکمیل footer سه‌ستونه و لینک‌های دسترسی سریع؛
- بارگذاری صریح stylesheet قالب در frontend؛
- جلوگیری از overflow افقی و تعریف breakpointهای desktop، tablet و mobile.

## شواهد تصویری

- `rebuild-home-header-footer-desktop-1440.png` — viewport برابر 1440×900؛
- `rebuild-home-header-footer-tablet-768.png` — viewport برابر 768×1024؛
- `rebuild-home-header-footer-mobile-375.png` — viewport برابر 375×812.

اسکرین‌شات‌ها با Playwright و Microsoft Edge از خود محیط Rebuild گرفته شدند.

## ریزتست‌ها و نتیجه

| تست | نتیجه |
|---|---|
| پاسخ صفحه اصلی | Pass — HTTP 200 |
| نمایش top bar و CTA ثبت‌نام | Pass در هر سه viewport |
| جست‌وجوی desktop | Pass — فقط در desktop نمایش داده می‌شود |
| جست‌وجوی mobile/tablet | Pass — تمام‌عرض و بدون clipping |
| navigation موبایل | Pass — در عرض 375 به منوی همبرگری تبدیل می‌شود |
| CTAهای حساب و سبد خرید | Pass — داخل viewport و قابل‌مشاهده |
| Hero در mobile | Pass — تک‌ستونه و بدون برش افقی |
| PHP syntax | Pass |
| parse شدن `theme.json` | Pass |
| خطای PHP در Docker log | Pass — موردی مشاهده نشد |

## یافته اصلاح‌شده

در اجرای نخست، `style.css` فقط به editor معرفی شده بود و frontend آن را صریح بارگذاری نمی‌کرد؛ در نتیجه breakpointهای موبایل اعمال نمی‌شدند. enqueue نسخه‌دار stylesheet به `functions.php` اضافه و اسکرین‌شات‌ها پس از اصلاح دوباره تولید شدند.
