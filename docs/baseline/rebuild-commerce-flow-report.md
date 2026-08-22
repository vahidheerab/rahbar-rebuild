# Rebuild commerce flow report

Date: 2026-08-22

## Scope and result

- A real purchasable sample course was added to the cart through the product page.
- Cart persistence after navigation/reload and removal from the WooCommerce session passed.
- Quantity editing is intentionally unavailable because the imported courses are sold individually.
- Empty required checkout fields produce accessible validation alerts.
- The public account login form exposes visible username, password and submit controls.
- Cart, checkout and account now use Rahbar header/footer and Persian WooCommerce translations.
- Store base country and allowed selling country are Iran; currency remains IRT.
- Guest checkout is disabled and checkout/account registration is enabled so every course order can be reconciled with an account and entitlement.

Automated evidence: `scripts/rebuild/commerce.spec.js` (`3 passed`).

Visual evidence:

- `rebuild-commerce-cart.png`
- `rebuild-commerce-checkout.png`
- `rebuild-commerce-account.png`

## Deliberate blocker

No payment gateway is enabled in the rebuild environment, so no order or payment was created. Full order, email, callback and course-entitlement testing remains blocked until the payment/LMS contract and sandbox gateway are implemented.

## Reproduction

```powershell
.\scripts\rebuild\Install-WooCommerceTranslations.ps1
.\scripts\rebuild\Initialize-RebuildPages.ps1 -Confirm:$false
npm run test:commerce
```

The translation installer pins WooCommerce `11.0.1`, locale `fa_IR`, and verifies SHA-256 before extraction.
