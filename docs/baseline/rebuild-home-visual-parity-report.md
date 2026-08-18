# گزارش Visual Parity صفحه Home

- تاریخ: 2026-08-18
- قالب: `rahbar 0.4.0`
- مرجع: screenshotهای Home محیط Legacy
- مقصد: `http://localhost:8082`

## اجزای تطبیق‌داده‌شده

- top bar بنفش، عنوان «آموزش صفر» و CTA زرد ثبت نام؛
- هدر اصلی با لوگوی واقعی رهبر حساب، search مرکزی و اطلاعات تماس؛
- navigation دسکتاپ و hamburger تبلت؛
- کنترل‌های ورود، سبد خرید و کافه سؤال؛
- Hero با متن واقعی Legacy، card سفید، گوشه بنفش، badge زرد، موج رنگی و تصویر واقعی شطرنج؛
- Benefits چهارگانه با ترتیب RTL، آیکن کلاه و سطح خاکستری متناوب؛
- ترتیب responsive مشابه Legacy: Hero دوستونه در desktop/tablet و متن سپس تصویر در mobile.

## Evidence

| Viewport | Legacy | Rebuild |
|---|---|---|
| 1440×900 | `legacy-home-desktop-1440.png` | `rebuild-home-parity-desktop-1440.png` |
| 768×1024 | `legacy-home-tablet-768.png` | `rebuild-home-parity-tablet-768.png` |
| 375×812 | `legacy-home-mobile-375.png` | `rebuild-home-parity-mobile-375.png` |

## اختلاف‌های عمدی یا باز

- فونت IranYekan/Kalameh تا تأیید مجوز منتقل نشده و fallback فارسی استفاده می‌شود.
- آیکن‌های account/cart/phone با کنترل‌های سبک و دسترس‌پذیر جایگزین شده‌اند؛ asset یا کتابخانه آیکن Elementor منتقل نشده است.
- search و menu در mobile مسیر دسترسی واضح دارند یا در ساختار سبک‌تر نمایش داده می‌شوند؛ نقص ناپدیدشدن کنترل‌ها در Legacy تکرار نشده است.
- اندازه و فاصله‌ها برای جلوگیری از overflow و باریک‌شدن کارت‌های tablet اندکی اصلاح شده‌اند.
- Footer به‌دلیل نبودن در screenshotهای viewport مرجع، از baseline ساختاری فعلی پیروی می‌کند و برای parity دقیق به evidence جدا نیاز دارد.

## ریزتست‌ها

| تست | نتیجه |
|---|---|
| assetهای Hero و logo محلی و مستقل از Legacy runtime | Pass |
| PHP patternها | Pass |
| parse و render شدن Header/Hero/Benefits | Pass |
| HTTP صفحه اصلی | Pass — 200 |
| بارگذاری stylesheet نسخه `0.4.0` | Pass |
| visual comparison در سه viewport | Pass با اختلاف‌های مستند بالا |
| منوی تبلت | Pass — hamburger |
| Hero موبایل | Pass — متن سپس تصویر و بدون clipping قابل مشاهده |
| خطای PHP در Docker log | Pass — موردی مشاهده نشد |

## نتیجه

prototype عمومی اولیه به نسخه visual-parity تبدیل شد. این خروجی هنوز جایگزین تست کامل محتوای Home، تعامل منو/search، فونت نهایی و regression خودکار نیست، اما بخش visible baseline دیگر بازطراحی آزاد محسوب نمی‌شود و هویت Legacy را دنبال می‌کند.
