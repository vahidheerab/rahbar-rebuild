# راه‌اندازی صفحات پایه Rebuild

تاریخ: 2026-08-22

## نتیجه

- permalink روی `/%postname%/` تنظیم شد.
- timezone روی `Asia/Tehran` و locale روی `fa_IR` تنظیم شد.
- rewrite استاندارد WordPress فقط در `.htaccess` محیط Rebuild ثبت شد.
- عنوان صفحات WooCommerce شامل Shop، Cart، Checkout و My Account فارسی شد.
- ۱۵ صفحه پایه مقصد لینک‌های Header/Footer به‌صورت idempotent ایجاد شدند.
- اجرای دوم initializer صفحات موجود را update کرد و duplicate نساخت.
- مسیرهای `/shop/`، `/cart/`، `/my-account/`، `/blog/` و `/contact/` با HTTP 200 تأیید شدند.

## ابزار تکرارپذیر

```powershell
.\scripts\rebuild\Initialize-RebuildPages.ps1 -WhatIf
.\scripts\rebuild\Initialize-RebuildPages.ps1
```

کد تغییرات WordPress در `scripts/rebuild/initialize-pages.php` است و فقط از طریق CLI داخل کانتینر Rebuild اجرا می‌شود.

## Backup و rollback

پیش از تغییر، snapshot تراکنشی و SHA-256 مبدأ و مقصد با `Invoke-RahbarCutover.ps1 -Action Snapshot` در Evidence محلی خارج از Git ساخته شد.

در صورت rollback، ابتدا Rebuild متوقف و dump `target-before` همان Run به دیتابیس Rebuild restore می‌شود. snapshot Legacy فقط مرجع است و نباید روی Rebuild import شود.

## محدودیت

صفحات ایجادشده مقصد معتبر و HTTP 200 دارند، اما محتوای تخصصی آن‌ها placeholder کنترل‌شده است. تکمیل Blog، Contact، Services، Account و Commerce در taskهای جدا انجام می‌شود؛ بنابراین `GATE-UI` هنوز بسته نیست.
