# Rahbar Payment Bridge sandbox report

Date: 2026-08-23

## Implemented

- Fake payment gateway is registered only when WordPress environment type is not `production`.
- Checkout Block declares and loads its own JavaScript asset.
- Payment sessions snapshot adapter, opaque token, amount, currency, callback URL and correlation ID on the WooCommerce order.
- Callback validates order ID/key, selected payment method and adapter token before verification.
- Per-order lock and durable hashed provider-reference claim are implemented.
- Verified amount/currency must equal the stored request before `payment_complete()`.
- Successful payment emits `rahbar_payment_verified`; no SpotPlayer/course code exists in this plugin.
- Integration-test emails are disabled and created test orders are deleted in `finally`.

## Automated results

- Pure contract tests: passed.
- WordPress/WooCommerce integration: success, repeated callback, cross-order duplicate reference and amount mismatch passed.
- The integration test proves a repeated successful callback does not emit a second `rahbar_payment_verified` event.
- Browser Checkout Block visibility: passed.
- Compose configuration and PHP lint: passed.

## Human checkout confirmation

- On 2026-08-23 the Iran-only checkout was completed manually and an invoice was issued.
- The resulting sandbox order `87` was independently inspected without customer data: status `processing`, paid, fake gateway selected and transaction ID present.
- `processing` is the expected paid WooCommerce status for this order; it is not a pending payment.
- Course access was not asserted because the Course Access consumer is deliberately outside Payment Bridge and has not been implemented yet.

## Remaining before Phase 1 exit

- Prove two genuinely concurrent callbacks complete exactly once.
- Add explicit invalid order key/payment-method callback tests at the HTTP boundary.
- Verify Fake gateway is absent with production environment configuration.
- Add browser journey through sandbox approve/decline pages.
- Do not add Zibal credentials/adapter until all remaining checks pass.
