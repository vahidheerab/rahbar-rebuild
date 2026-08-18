## Context

Rahbar شامل دو Compose مستقل است: Legacy مرجع رفتاری و Rebuild مقصد تمیز. مسیر بازسازی از inventory تا cutover چندروزه است و شامل WordPress، WooCommerce، LMS/SpotPlayer، پرداخت، SEO و داده‌های حساس می‌شود. `MIGRATION-GUIDE.md` runbook و `REBUILD-ROADMAP.md` مرجع تصمیم‌های معماری باقی می‌مانند.

## Goals / Non-Goals

**Goals:**

- یک صف کار ریزدانه و قابل ازسرگیری با شناسه‌های پایدار.
- ثبت شواهد، blocker، مالک، تاریخ و اقدام بعدی.
- جداسازی تست‌های baseline، parity، migration، non-functional، cutover و rollback.
- جلوگیری از عبور مرحله با تست ناقص یا بدون مدرک.

**Non-Goals:**

- اجرای خودکار همه تست‌ها در این change.
- تعیین انتخاب نهایی theme، plugin یا معماری پرداخت.
- انتقال یا تغییر داده واقعی.
- جایگزینی roadmap و migration runbook موجود.

## Decisions

1. `tasks.md` دفتر وضعیت canonical است؛ چون OpenSpec وضعیت checkboxها را مستقیماً گزارش می‌کند. فایل‌های roadmap فقط وضعیت سطح فاز را نگه می‌دارند.
2. شناسه تست‌ها به‌شکل `PHASE-AREA-NNN` ثابت می‌مانند تا در screenshot، log و گزارش خطا قابل ارجاع باشند.
3. وضعیت‌ها با checkbox و برچسب ثبت می‌شوند: `[ ]` شروع‌نشده، `[~]` درحال‌انجام، `[x]` قبول، `[!]` رد/مسدود، `[-]` خارج از دامنه با دلیل. برای سازگاری OpenSpec فقط وضعیت قبول با `[x]` completion محسوب می‌شود.
4. شواهد در `test-evidence/<run-id>/` یا یک لینک بیرونی پایدار ثبت می‌شوند؛ secrets، dump، uploads و اطلاعات شخصی وارد Git نمی‌شوند.
5. تست parity از ماتریس Legacy→Rebuild استفاده می‌کند. تفاوت عمدی فقط با تصمیم ثبت‌شده و expected result جدید پذیرفته می‌شود.
6. Exit Gateها جدا از تست‌ها هستند و تنها پس از تکمیل همه prerequisiteها علامت می‌خورند.

## Risks / Trade-offs

- [چک‌لیست بزرگ و فرسایشی] → تست‌ها بر اساس فاز گروه‌بندی و «اقدام بعدی» هر نشست ثبت می‌شود.
- [تیک بدون اجرای واقعی] → هر تست اجباری نیازمند Evidence و تاریخ است.
- [نشت داده حساس در شواهد] → فقط خروجی redacted و aggregate ذخیره می‌شود.
- [قدیمی‌شدن checklist با تغییر معماری] → هر تصمیم معماری باید تست‌های مرتبط را در همین change به‌روز کند.
- [اشتباه بین دو محیط] → هر دستور و Evidence باید environment و URL را صریح ذکر کند.

## Migration Plan

1. baseline هر دو محیط ثبت شود.
2. inventory و ماتریس parity تکمیل شود.
3. قابلیت‌ها مرحله‌ای پیاده‌سازی و با داده ساختگی تست شوند.
4. حداقل یک rehearsal کامل migration و rollback اجرا شود.
5. Exit Gate فنی و کسب‌وکار امضا شود.
6. cutover اجرا و در دوره hypercare پایش شود.

Rollback این change صرفاً حذف مستندات OpenSpec است؛ rollback سامانه مطابق بخش اختصاصی `tasks.md` و `MIGRATION-GUIDE.md` انجام می‌شود.
