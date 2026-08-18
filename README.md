# Rahbar Rebuild

بازسازی کنترل‌شده وب‌سایت رهبر حساب روی WordPress تمیز. کد Legacy و داده‌های تولیدی بخشی از این repository نیستند.

## اجرای محلی

پیش‌نیاز: Docker Desktop و Docker Compose.

```powershell
Copy-Item rebuild/.env.example rebuild/.env
docker compose -f rebuild/compose.yaml config
docker compose -f rebuild/compose.yaml up -d
```

آدرس‌ها:

- WordPress Rebuild: <http://localhost:8082>
- phpMyAdmin Rebuild: <http://localhost:8084>

مقادیر محرمانه را فقط در `rebuild/.env` نگه دارید؛ این فایل در Git نادیده گرفته می‌شود.

## ساختار نسخه‌شده

- `rebuild/compose.yaml`: محیط مستقل Rebuild
- `rebuild/site/wp-content/themes/rahbar/`: Block Theme اختصاصی
- `docs/adr/`: تصمیم‌های معماری
- `openspec/`: specification و چک‌لیست اجرایی
- `MIGRATION-GUIDE.md`: runbook انتقال و rollback
- `REBUILD-ROADMAP.md`: نقشه راه بازسازی
- `PLUGIN-INVENTORY.md`: تصمیم اولیه افزونه‌ها

WordPress core، افزونه‌های ثالث، uploads، cache، log، dump و کل پوشه محلی `legacy/` عمداً commit نمی‌شوند.

## اعتبارسنجی پایه

```powershell
docker compose -f rebuild/compose.yaml config --quiet
php -l rebuild/site/wp-content/themes/rahbar/functions.php
openspec.cmd validate rebuild-qa-checklist --strict
```
