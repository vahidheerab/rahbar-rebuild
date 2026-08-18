# گزارش Product Template Prototype

- تاریخ: 2026-08-18
- قالب: `rahbar 0.5.0`
- WooCommerce runtime: `11.0.1`
- محصول نمونه Rebuild: ID `13`، slug برابر `gold-accounting-training`
- مرجع: محصول Legacy با ID `39885`

## پیاده‌سازی

- `templates/single-product.html` به‌عنوان override رسمی Block Theme ساخته شد.
- عنوان، تصویر، summary بنفش، notice دسترسی، معرفی مدرس، tabs، محتوای دوره و purchase card با ساختاری نزدیک به Legacy پیاده‌سازی شدند.
- قیمت عادی، قیمت فروش، badge فروش، موجودی، تصویر و فرم افزودن به سبد از داده واقعی WooCommerce رندر می‌شوند.
- محصول نمونه simple، virtual، instock و قابل خرید است؛ قیمت عادی `18,000,000` و قیمت فروش `11,800,000` با currency نمایشی تومان ثبت شد.
- در mobile، تصویر پیش از summary قرار می‌گیرد و purchase card به نوار ثابت پایین شامل قیمت و CTA واقعی تبدیل می‌شود.
- WooCommerce فقط در runtime محیط Rebuild نصب شده و طبق `.gitignore` کد افزونه ثالث وارد repository نمی‌شود.

## Evidence

| Viewport | Legacy | Rebuild |
|---|---|---|
| 1440×1200 | `legacy-product-gold-desktop-1440.png` | `rebuild-product-gold-desktop-1440.png` |
| 768×1100 | `legacy-product-gold-tablet-768.png` | `rebuild-product-gold-tablet-768.png` |
| 375×900 | `legacy-product-gold-mobile-375.png` | `rebuild-product-gold-mobile-375.png` |

## ریزتست‌ها

| تست | نتیجه |
|---|---|
| فعال‌بودن WooCommerce و ثبت product post type | Pass |
| override شدن `single-product.html` قالب | Pass |
| HTTP محصول عمومی | Pass — 200 |
| تصویر شاخص و alt | Pass |
| قیمت عادی/فروش و حذف اعشار غیرضروری | Pass |
| موجودی و purchasable بودن محصول | Pass |
| فرم native افزودن به سبد | Pass |
| CTA در viewport موبایل 375×900 | Pass — در نوار ثابت پایین قابل مشاهده |
| clipping بحرانی Legacy در mobile | Pass — بازتولید نشد |
| سه visual smoke test | Pass |
| خطای PHP در Docker log | Pass — موردی مشاهده نشد |

## محدودیت‌ها و Gateهای باز

- قرارداد canonical ریال/تومان و migration قیمت هنوز نهایی نشده است؛ داده فعلی prototype با واحد نمایشی تومان ساخته شده است.
- آمار دانشجو، مدت و تعداد جلسات داخل theme ثابت نشده‌اند؛ منبع حقیقت LMS و plugin مالک metadata باید تعیین شود.
- notice واقعی SpotPlayer و entitlement باید توسط plugin مستقل و براساس محصول رندر شود؛ متن فعلی فقط presentation prototype است.
- اطلاعات مدرس باید پس از تعیین مدل داده Instructor از منبع واقعی خوانده شود.
- cart، checkout، order، email، پرداخت، entitlement و duplicate-submit هنوز Gate کامل خود را نگذرانده‌اند.
