# Rebuild public sample content and responsive QA

Date: 2026-08-22

## Scope

Six published Legacy articles and six published Legacy products were copied into Rebuild for realistic visual QA. This is a test-content import from the stale local Legacy snapshot, not the production cutover.

Included: public title, slug, excerpt, body, date, categories/tags, product display prices/status, and featured image with alt text.

Excluded: users, orders, payments, customer data, entitlements, licenses, downloads, downloadable-file metadata, private content, and LMS/payment state.

## Repeatability and rollback

- Command: `powershell -File scripts/rebuild/Import-PublicSamples.ps1`
- Content key: `_rahbar_legacy_source_id`; image key: `_rahbar_legacy_attachment_for`.
- First run: 12 created. Later runs: 0 created / 12 updated. No duplicates were produced.
- A target-before SQL snapshot and checksum were stored outside Git before import.
- Exact rollback is restoration of that snapshot. Selective cleanup must target only the two source marker keys after another safety snapshot.

## Result

- Six real sample articles and six real sample products are present with featured images.
- Blog cards, a long article, Shop cards, prices/sales, and a single product were visually inspected.
- Shop received a responsive three/two-column grid and localized public labels.
- Fixed-width Legacy article images and the 320px header overflow were corrected.
- Theme version is `1.2.0`; `rahbar-site-core` owns non-theme WooCommerce labels.

## Responsive matrix

Routes: Home, Blog, imported Article, Shop, imported Product, Contact, Search, and 404.

Viewports: 320, 375, 768, 1024, and 1440 pixels.

- 24 cases at 768/1024/1440 passed in the main run.
- All 16 cases at 320/375 passed after the two discovered issues were fixed.
- Root document width and expected HTTP status were verified for every route.

Reusable test: run `npm install`, then `npm run test:responsive`; local Chrome is required.

## Visual evidence

- `rebuild-content-blog-desktop-1440.png`
- `rebuild-content-blog-mobile-320.png`
- `rebuild-content-shop-desktop-1440.png`
- `rebuild-content-shop-mobile-375.png`
- `rebuild-content-article-1024.png`
- `rebuild-content-product-768.png`

## Cutover warning

These records are not the final source of truth. The reviewed production adapter must reconcile/update them by Legacy source identity during cutover, not create duplicates.
