# Visual Inventory صفحه Home در Legacy

- تاریخ ثبت: 2026-08-18
- URL: `http://localhost:8081/`
- محیط: `rahbar-legacy`
- نقش سند: مرجع ساختار و هویت بصری؛ باگ‌های Legacy الزام parity نیستند

## Evidence

| Viewport | فایل | نتیجه |
|---|---|---|
| Desktop 1440×900 | `legacy-home-desktop-1440.png` | ثبت موفق |
| Tablet 768×1024 | `legacy-home-tablet-768.png` | ثبت موفق |
| Mobile 375×812 | `legacy-home-mobile-375.png` | ثبت موفق |

تصاویر فقط viewport اولیه را ثبت می‌کنند و full-page نیستند. داده شخصی یا صفحه authenticated در آن‌ها وجود ندارد.

## ساختار قابل مشاهده

### 1. نوار بالایی

- پس‌زمینه بنفش تیره؛
- لینک «آموزش صفر»؛
- CTA زرد «ثبت نام»؛
- در هر سه viewport قابل مشاهده است.

### 2. سربرگ Desktop

- لوگو در سمت راست؛
- search بزرگ در مرکز با متن «چی دوست داری یاد بگیری؟»؛
- اطلاعات تماس و آیکون تلفن در سمت چپ؛
- ردیف دوم navigation اصلی؛
- cart، ورود/ثبت‌نام و CTA «کافه سوال راهبرحساب» در سمت چپ؛
- border پایین و پس‌زمینه سفید.

### 3. سربرگ Tablet

- لوگو، search و تماس همچنان نمایش داده می‌شوند؛
- navigation متنی با hamburger جایگزین می‌شود؛
- cart و ورود باقی می‌مانند؛
- CTA کافه سوال حذف می‌شود.

### 4. سربرگ Mobile

- نوار بالا باقی می‌ماند؛
- فقط لوگو، cart و دکمه حساب نمایش داده می‌شوند؛
- search، تماس، CTA کافه سوال و navigation/hamburger در viewport ثبت‌شده دیده نمی‌شوند؛
- فضای خالی قابل توجه اطراف لوگو وجود دارد.

### 5. Hero

- ساختار دو ستونه در Desktop: تصویر مهره شطرنج در چپ و card محتوا در راست؛
- shadow بزرگ مهره شاه در پس‌زمینه؛
- خطوط موجی gradient و pattern نقطه‌ای تزئینی؛
- card سفید با border ظریف و گوشه تأکیدی بنفش؛
- badge زرد با icon؛
- پیام اصلی: «موسسه آموزشی و خدمات مالی و مالیاتی رهبر حساب»؛
- supporting text درباره همایش حسابداری.

در Mobile ترتیب به متن و سپس تصویر تبدیل می‌شود. card تقریباً تمام عرض را می‌گیرد و تزئینات موجی قسمتی از مرز بین متن و تصویر هستند.

### 6. کارت‌های ارزش پیشنهادی

چهار کارت در viewport Desktop/Tablet دیده می‌شوند:

1. بیش از ۲۰ دوره کاربردی؛
2. ارائه مدرک معتبر؛
3. کافه سؤال حسابداری؛
4. پشتیبانی ۷/۲۴.

کارت‌ها با icon کلاه فارغ‌التحصیلی، border، radius و سطح خاکستری متناوب طراحی شده‌اند.

### 7. اقدام شناور

- دکمه دایره‌ای زرد account/support در گوشه پایین صفحه دیده می‌شود؛
- رفتار و مقصد دقیق آن باید در inventory تعاملی مشخص شود.

## Design Tokenهای استخراج‌شده

| Token | مقدار Legacy | تصمیم اولیه Rebuild |
|---|---|---|
| Primary purple | `#5F2284` | حفظ به‌عنوان رنگ اصلی |
| Primary dark | `#361E50` | حفظ برای header/footer و heading |
| Accent yellow | `#FBB911` | حفظ برای CTA و badge |
| Danger/red | `#D62B27` | فقط status/error؛ نه CTA اصلی |
| Surface | `#FFFFFF` | حفظ |
| Muted text | حدود `#7A7A7A` | با contrast قابل قبول بازتعریف شود |
| Legacy fonts | IranYekan و Kalameh | فقط پس از تأیید مجوز؛ fallback فعلی Tahoma/Arial |
| Border radius | حدود 12–20px | tokenهای 10/16/20px در prototype ارزیابی شوند |

## رفتارهایی که باید حفظ شوند

- هویت بنفش/زرد و حس آموزشی-حرفه‌ای؛
- دسترسی سریع به search، account، cart و دوره‌ها؛
- Hero با پیام ارزش روشن؛
- نمایش اعتمادساز دوره، مدرک، سؤال تخصصی و پشتیبانی؛
- RTL کامل و CTAهای فارسی؛
- navigation جمع‌شونده در صفحه کوچک.

## مشکلاتی که نباید بازتولید شوند

- Home در cold/warm request مشاهده‌شده بسیار کند است؛ یک پاسخ موفق بیش از یک دقیقه طول کشید و درخواست بعدی طولانی متوقف شد؛
- HTML پاسخ موفق حدود 648KB بود، پیش از احتساب assetها؛
- چهار کارت Tablet بیش از حد باریک می‌شوند و خوانایی متن افت می‌کند؛
- search و navigation در Mobile بدون جایگزین واضح ناپدید می‌شوند؛
- فضای سفید زیاد و hierarchy نامتوازن در header موبایل؛
- reliance زیاد بر decoration و positioning مطلق؛
- floating control بدون label قابل مشاهده؛
- contrast بعضی متن‌های خاکستری و focus stateها نیازمند تست است.

## Mapping به Block Theme Rahbar

| Legacy component | مقصد Rebuild | وضعیت |
|---|---|---|
| Top bar | template part سربرگ / گروه مجزا | طراحی نشده |
| Logo + navigation | `parts/header.html` | پایه موجود، نیازمند تکمیل |
| Search | Search block داخل header desktop/tablet و overlay موبایل | طراحی نشده |
| Account/cart actions | pattern یا blockهای WooCommerce/account | وابسته به WooCommerce |
| Hero | `patterns/hero.php` | prototype موجود، نیازمند تطبیق |
| چهار value card | pattern جدید Benefits | طراحی نشده |
| Floating action | حذف یا تبدیل به control دارای label/ARIA | نیازمند تصمیم کسب‌وکار |
| Decorative chess/waves | assetهای بهینه SVG/WebP با alt/aria صحیح | asset نهایی موجود نیست |

## معیار پذیرش prototype بعدی

- header در 375px حتماً search یا راه دسترسی واضح به search و menu داشته باشد؛
- هیچ horizontal overflow در 375، 768 و 1440px وجود نداشته باشد؛
- کارت‌ها در tablet حداکثر دو ستونه و در mobile تک‌ستونه شوند؛
- CTA اصلی contrast و focus قابل مشاهده داشته باشد؛
- تصاویر تزئینی `aria-hidden` و تصویر محتوایی alt معتبر داشته باشد؛
- HTML اولیه و asset budget پس از prototype اندازه‌گیری شود؛
- cold TTFB محلی به‌عنوان baseline جدید ثبت شود.

## اقدام بعدی

visual inventory صفحه Product در Legacy ثبت شود؛ سپس header/footer و Benefits pattern در Rebuild بر اساس این سند تکمیل شوند.
