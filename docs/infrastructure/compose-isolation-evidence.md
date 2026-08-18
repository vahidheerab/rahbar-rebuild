# Evidence ایزولیشن Legacy و Rebuild

- تاریخ اجرا: 2026-08-18
- Test ID: `INF-ISO-001`
- نتیجه: Pass

## ماتریس منابع

| منبع | Legacy | Rebuild | اشتراک |
|---|---|---|---|
| Compose project | `rahbar-legacy` | `rahbar-rebuild` | ندارد |
| WordPress container | `rahbar-legacy-wordpress-1` | `rahbar-rebuild-wordpress-1` | ندارد |
| DB container | `rahbar-legacy-db-1` | `rahbar-rebuild-db-1` | ندارد |
| phpMyAdmin container | `rahbar-legacy-phpmyadmin-1` | `rahbar-rebuild-phpmyadmin-1` | ندارد |
| network | `rahbar-legacy_legacy_network` | `rahbar-rebuild_rebuild_network` | ندارد |
| database volume | `rahbar-legacy_legacy_db` | `rahbar-rebuild_rebuild_db` | ندارد |
| WordPress bind mount | `legacy/site → /var/www/html` | `rebuild/site → /var/www/html` | مقصد کانتینری یکسان، source میزبان جدا |
| PHP config mount | `legacy/docker/php.ini` | `rebuild/docker/php.ini` | ندارد |
| WordPress HTTP | `8081 → 80` | `8082 → 80` | ندارد |
| phpMyAdmin HTTP | `8083 → 80` | `8084 → 80` | ندارد |
| MySQL host publish | ندارد | ندارد | دیتابیس‌ها فقط داخل network خودشان هستند |

Legacy یک bind اضافه read-only برای `legacy/wp-config-docker.php` و یک init dump read-only برای دیتابیس دارد. این مسیرها در Rebuild mount نشده‌اند.

## تست‌های runtime

| تست | نتیجه |
|---|---|
| هر سه container در project Legacy running | Pass |
| هر سه container در project Rebuild running | Pass |
| health دیتابیس Legacy | Pass — healthy |
| health دیتابیس Rebuild | Pass — healthy |
| resolve شدن `rahbar-rebuild-db-1` از WordPress Legacy | Pass — عمداً شکست خورد |
| resolve شدن `rahbar-legacy-db-1` از WordPress Rebuild | Pass — عمداً شکست خورد |
| network هر project دقیقاً سه عضو خودش را دارد | Pass |
| شناسه و label volumeهای دیتابیس متفاوت است | Pass |
| Rebuild WordPress روی 8082 | Pass — HTTP 200 |
| Legacy phpMyAdmin روی 8083 | Pass — HTTP 200 |
| Rebuild phpMyAdmin روی 8084 | Pass — HTTP 200 |
| Legacy login روی 8081 | Pass — HTTP 200 |

## یافته عملکردی خارج از Gate ایزولیشن

درخواست Home محیط Legacy در probe سی‌ثانیه‌ای timeout شد، اما `wp-login.php` و REST root هر دو HTTP 200 دادند و containerها running/healthy باقی ماندند. بنابراین این مورد failure ایزولیشن نیست و با finding قبلی کندی شدید Legacy هم‌راستا است.

## نتیجه

هیچ project، container، network، volume دیتابیس، host port یا source bind مشترکی میان دو محیط مشاهده نشد. اتصال تصادفی WordPress یک محیط به نام container دیتابیس محیط دیگر نیز به‌علت جدایی networkها ممکن نیست.
