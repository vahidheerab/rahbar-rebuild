# Visual Inventory صفحه Product در Legacy

- تاریخ ثبت: 2026-08-18
- محصول نمونه: `39885` — دوره آموزش حسابداری طلا
- URL: `http://localhost:8081/product/gold-accounting-training/`
- محیط: `rahbar-legacy`
- نقش سند: قرارداد اولیه ساختار صفحه محصول؛ باگ‌های layout الزام parity نیستند

## Evidence

| Viewport | فایل | نتیجه |
|---|---|---|
| Desktop 1440×1200 | `legacy-product-gold-desktop-1440.png` | ثبت موفق |
| Tablet 768×1100 | `legacy-product-gold-tablet-768.png` | ثبت موفق |
| Mobile 375×900 | `legacy-product-gold-mobile-375.png` | ثبت موفق با horizontal overflow شدید |

تصاویر viewport اولیه هستند و full-page نیستند. محصول عمومی است و صفحه authenticated یا داده شخصی ثبت نشده است.

## قرارداد داده محصول نمونه

| فیلد | مقدار Legacy |
|---|---|
| نوع WooCommerce | `simple` |
| قیمت عادی | 18,000,000 |
| قیمت فروش | 11,800,000 |
| وضعیت موجودی | `instock` |
| مجازی | بله |
| دانلودی | خیر |
| قابل خرید | بله |
| gallery | صفر؛ یک تصویر شاخص در UI استفاده شده |
| category IDs | `160,138,139,15,300` |

واحد نمایش در UI «تومان» است. قرارداد ذخیره و تبدیل ریال/تومان باید پیش از migration قیمت‌ها صریح و تست شود.

## ساختار قابل مشاهده

### 1. Header مشترک

همان top bar و header صفحه Home استفاده می‌شود. در Desktop اطلاعات تماس، search، لوگو، navigation، cart، account و CTA کافه سؤال وجود دارند. در Tablet navigation به hamburger تبدیل می‌شود. در Mobile فقط لوگو، cart و account باقی می‌مانند.

### 2. عنوان محصول

- عنوان کامل در بالای صفحه و راست‌چین است؛
- در Desktop و Tablet عرض مناسبی دارد؛
- در Mobile عنوان از viewport بیرون می‌زند و ابتدای/انتهای آن clip می‌شود.

### 3. Product introduction

- Desktop/Tablet دو ستونه: تصویر شاخص در چپ و خلاصه دوره در راست؛
- تصویر 16:9 با radius حدود 12px؛
- summary داخل سطح بنفش شامل bulletهای ویژگی دوره؛
- notice یاسی درباره دسترسی از طریق license اسپات‌پلیر؛
- ردیف مدرس شامل تصویر، نام، لینک مشاهده رزومه و CTA اینستاگرام؛
- پس‌زمینه کلی این بخش یاسی بسیار روشن.

### 4. Navigation داخلی محصول

سه tab/pill قابل مشاهده است:

1. سرفصل دوره؛
2. توضیحات این دوره آموزشی؛
3. دیدگاه شما.

رفتار anchor/sticky و keyboard navigation این tabs باید در inventory تعاملی بعدی بررسی شود.

### 5. Purchase card

- card جدا در ستون چپ Desktop و Tablet؛
- عنوان «هزینه شرکت در دوره»؛
- badge تخفیف ۳۴٪؛
- قیمت عادی strike-through و قیمت فروش 11,800,000 تومان؛
- CTA بنفش «افزودن به سبد خرید»؛
- پیام scarcity با متن «فروش شگفت‌انگیز»؛
- metadata دوره شامل تعداد دانشجویان (۵۹۰ نفر در snapshot)، مدت آموزش و تعداد جلسات؛
- placement در Mobile viewport اولیه قابل مشاهده نیست و باید در full-page/interaction test بررسی شود.

### 6. سرفصل‌ها

- accordion با عنوان «سرفصل‌های این دوره»؛
- heading محتوایی «سرفصل‌های دوره آموزش حسابداری طلا»؛
- لیست bullet موضوعات در panel خاکستری؛
- در Desktop purchase card در کنار accordion قرار گرفته است.

### 7. Floating action

دکمه زرد account/support در Desktop و Tablet روی content شناور است و احتمال پوشاندن محتوا وجود دارد. در طراحی مقصد باید label، `aria-label`، safe area و عدم overlap تضمین شود یا component حذف شود.

## رفتار Responsive

### Desktop

- hierarchy و دو ستون اصلی خوانا هستند؛
- عرض content زیاد ولی کنترل‌شده است؛
- purchase card از محتوای دوره جدا و قابل تشخیص است.

### Tablet

- introduction همچنان دو ستونه است و قابل استفاده می‌ماند؛
- purchase card بسیار باریک می‌شود ولی هنوز CTA دیده می‌شود؛
- accordion فضای اصلی را در کنار card می‌گیرد؛
- header به hamburger تبدیل می‌شود.

### Mobile

- horizontal overflow شدید در عنوان، تصویر شاخص و summary وجود دارد؛
- عرض componentها از 375px بیشتر است و سمت راست/چپ content clip می‌شود؛
- search و menu واضح header نمایش داده نمی‌شوند؛
- CTA خرید above-the-fold نیست؛
- متن bulletها نزدیک لبه و بخشی خارج از viewport است؛
- این رفتار یک defect بحرانی responsive است و نباید در Rebuild تکرار شود.

## رفتارهایی که باید حفظ شوند

- عنوان روشن و تصویر شاخص دوره؛
- خلاصه سریع ویژگی‌ها پیش از توضیحات بلند؛
- توضیح شفاف روش دسترسی SpotPlayer؛
- هویت و رزومه مدرس؛
- قیمت عادی، تخفیف، قیمت نهایی و CTA خرید؛
- نمایش metadata تصمیم‌ساز مانند تعداد دانشجو، مدت و جلسه؛
- tabs و accordion برای کاهش طول شناختی صفحه؛
- اتصال مستقیم محصول به cart و entitlement دوره.

## مشکلاتی که نباید بازتولید شوند

- horizontal overflow و clipping کامل Mobile؛
- دو ستونه ماندن بخش‌های حساس در عرض نامناسب؛
- پنهان‌شدن CTA خرید در عمق صفحه Mobile؛
- floating control روی content؛
- متن‌های طولانی بدون max-width و wrapping قابل اعتماد؛
- وابستگی layout به positioning و اندازه ثابت Elementor؛
- نبود gallery واقعی با وجود presentation سفارشی تصویر؛
- نامشخص بودن واحد canonical قیمت در ذخیره‌سازی نسبت به نمایش تومان.

## Mapping به Rebuild

| Legacy component | مقصد Rebuild | تصمیم |
|---|---|---|
| عنوان و تصویر | WooCommerce Single Product template | block-native و responsive |
| summary بنفش | Product Summary pattern/blocks | داده featureها باید schema مشخص داشته باشد |
| SpotPlayer notice | entitlement/access notice plugin block | منطق در theme ممنوع |
| Instructor card | Course/Instructor block | مالکیت داده LMS مشخص شود |
| tabs | WooCommerce/Product Details blocks | keyboard و deep-link تست شود |
| purchase card | Product Price + Add to Cart + course meta | mobile sticky CTA فقط پس از accessibility test |
| discount badge | محاسبه از regular/sale price | عدم hard-code درصد |
| student/duration/session | course metadata block | منبع حقیقت LMS تعیین شود |
| curriculum accordion | LMS curriculum block | progressive enhancement و بدون JS نیز قابل خواندن |
| floating action | حذف یا component دسترس‌پذیر | تصمیم کسب‌وکار لازم است |

## معیار پذیرش Product prototype

- هیچ overflow افقی در 320، 375، 768، 1024 و 1440px وجود نداشته باشد؛
- قیمت، تخفیف، availability و CTA خرید در Mobile بدون اسکرول طولانی قابل دسترس باشند؛
- CTA خرید duplicate order یا duplicate entitlement ایجاد نکند؛
- قیمت نمایش‌داده‌شده با cart/checkout و واحد پول یکسان باشد؛
- تصویر دارای alt معتبر و decorative assetها مخفی از screen reader باشند؛
- tabs و accordion با keyboard کار کنند و focus visible داشته باشند؛
- course meta از منبع حقیقت واحد خوانده شود؛
- notice دسترسی SpotPlayer فقط در صورت نیاز همان محصول نمایش داده شود؛
- Product structured data با قیمت و availability واقعی هماهنگ باشد.

## اقدام بعدی

design tokenهای Home/Product تثبیت و header/footer قالب Rahbar تکمیل شوند؛ سپس Benefits pattern و Product template prototype ساخته شوند.
