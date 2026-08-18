# راهنمای ایجنت‌های مخزن Rahbar

این مخزن یک بازسازی کنترل‌شده وردپرس است و دو پروژه مستقل دارد. قبل از هر تغییر، scope را مشخص و تغییرات نامرتبط کاربر را حفظ کن.

## ساختار اصلی

```text
legacy/site/            Document Root سایت فعلی (WordPress 7.0.2 / PHP 8.1)
legacy/backups/         dump محلی؛ هرگز commit یا public نشود
legacy/compose.yaml     محیط Docker مستقل legacy
rebuild/site/           Document Root نسخه تمیز (WordPress 7.0.2 / PHP 8.5)
rebuild/compose.yaml    محیط Docker مستقل rebuild
MIGRATION-GUIDE.md      runbook و چک‌لیست انتقال
REBUILD-ROADMAP.md      نقشه قابلیت‌ها، معماری و انتقال انتخابی
```

## قواعد عمومی

1. `legacy` مرجع رفتاری و داده‌ای است؛ بدون درخواست صریح، feature یا refactor در آن انجام نده.
2. توسعه جدید فقط در `rebuild` انجام می‌شود. کد legacy را دسته‌جمعی کپی نکن؛ هر theme/plugin باید جداگانه ارزیابی و تست شود.
3. دیتابیس‌ها، volumeها، networkها و پورت‌های دو Compose مستقل‌اند. بین آن‌ها volume مشترک نساز.
4. `legacy/backups/**`، فایل‌های `.env`، uploads، cache و log محرمانه/تولیدی هستند و نباید commit شوند.
5. پیش از migration داده، از دیتابیس مبدأ dump و checksum بگیر. import و search-replace باید در `MIGRATION-GUIDE.md` ثبت شود.
6. نسخه imageها را ثابت نگه دار. ارتقای WordPress، PHP یا MySQL یک تغییر مستقل و نیازمند تست و مستندسازی است.
7. URLهای استاندارد: legacy روی `http://localhost:8081` و rebuild روی `http://localhost:8082`.
8. Compose را از پوشه همان پروژه یا با `-f` صحیح اجرا کن؛ دستورات یک پروژه را روی دیگری اجرا نکن.

## Rahbar Payment Bridge

برای ایجاد، ویرایش، بازبینی یا تست در `legacy/site/wp-content/plugins/rahbar-payment-bridge/**` یا `rebuild/site/wp-content/plugins/rahbar-payment-bridge/**`:

1. `AGENTS.md` داخل همان افزونه را کامل بخوان.
2. `ARCHITECTURE.md` داخل همان افزونه را کامل بخوان.
3. فاز فعال roadmap و Exit Gate را بررسی کن.
4. invariants، لایه‌بندی، امنیت و تست‌های سند را رعایت کن.
5. تغییر معماری بدون ADR و به‌روزرسانی سند ممنوع است.

کد افزونه نباید در پوسته، mu-plugin، WooCommerce، SpotPlayer یا افزونه پرداخت دیگری پخش شود. در snapshot فعلی این افزونه وجود ندارد؛ اگر اضافه شد، قواعد بالا فوراً اعمال می‌شوند.

## حداقل معیار تحویل

- `docker compose config` معتبر باشد.
- پروژه تغییرکرده بدون اثر روی پروژه دیگر بالا بیاید.
- خطاهای PHP/WordPress و healthcheck دیتابیس بررسی شوند.
- تغییر schema یا داده rollback مستند داشته باشد.
- وضعیت مرحله مربوطه در `MIGRATION-GUIDE.md` به‌روز شود.
