## Why

بازسازی Rahbar چندروزه و وابسته به دو محیط مستقل، داده‌های حساس و چندین Exit Gate است. بدون یک مرجع اجرایی ریزدانه، نقطه توقف، شواهد تست و وضعیت آمادگی هر مرحله به‌سادگی گم می‌شود.

## What Changes

- یک روند واحد و قابل‌ازسرگیری برای baseline، inventory، پیاده‌سازی، migration rehearsal، QA، cutover و rollback تعریف می‌شود.
- هر تست دارای شناسه، نتیجه قابل ثبت، شواهد و معیار عبور خواهد بود.
- یک الگوی session log برای ثبت آخرین نقطه امن، blocker و اقدام بعدی فراهم می‌شود.
- اجرای مراحل حساس به Exit Gateهای صریح و قابل ممیزی وابسته می‌شود.

## Capabilities

### New Capabilities

- `rebuild-verification`: الزامات ردیابی و اثبات کامل بودن بازسازی، انتقال داده و آمادگی انتشار Rahbar.

### Modified Capabilities

هیچ قابلیت موجودی تغییر نمی‌کند.

## Impact

- مستندات اجرایی OpenSpec در `openspec/changes/rebuild-qa-checklist/`.
- روند کاری Legacy و Rebuild، بدون تغییر کد یا داده در این change.
- هماهنگی با `MIGRATION-GUIDE.md` و `REBUILD-ROADMAP.md` به‌عنوان منابع معماری و migration.
