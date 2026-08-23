# Payment and course-entitlement contract

Date: 2026-08-22  
Status: accepted for implementation; sandbox credentials and refund policy remain open

## Evidence from the Legacy snapshot

This inventory is read-only and contains counts only; no merchant key, API token, card number or customer data is recorded here.

- Active payment implementation: `gateway-woocommerce-multiplexing-main` (`WC_Zibal`).
- `rahbar-ipg-gateway` exists in the filesystem but is not active in the snapshot.
- Active course delivery implementation: `spotplayer`.
- Active CRM connector observes every WooCommerce order-status change, but CRM is already classified as retired for Rebuild.
- Aggregate snapshot counts: 1,249 Zibal orders, 896 non-empty transaction IDs, 883 legacy card metadata rows, 25 products with `_spotplayer_course`, and 744 orders with `_spotplayer_data`.
- These counts are not expected to match one-to-one because cancelled/failed/manual/other-gateway orders exist. Cutover must reconcile by order, payment method, paid status and transaction ID rather than comparing only global counts.

## Source of truth

| Concern | Source of truth | Stable identity |
| --- | --- | --- |
| Customer | WordPress user | source user ID mapped to target user ID + normalized email |
| Order | WooCommerce order | source order ID stored as immutable migration key |
| Payment | Verified WooCommerce payment record | gateway + unique transaction/reference ID |
| Course mapping | WooCommerce product metadata | source product ID mapped to target product ID + SpotPlayer course IDs |
| Entitlement | Rebuild entitlement record | unique `(provider, order_id, product_id, customer_id)` |
| Provider license | SpotPlayer | provider license ID stored against the entitlement/order |

WooCommerce owns order/payment state. SpotPlayer must never mark an order paid. A verified payment event may request entitlement provisioning; entitlement failure must not reverse or duplicate payment.

## Required payment invariants

1. The callback must validate order ID/key, selected gateway, saved token, expected callback status, amount and currency.
2. Payment success is accepted only after a server-to-server verify response.
3. A transaction/reference ID is unique across orders. Repeated callbacks return the existing result and create no duplicate payment or entitlement.
4. Per-order verification locking is required. Network/verify-unavailable errors leave the order pending; only definitive declines may mark it failed.
5. Secrets live in environment/secret storage and are never exported by migration or committed.
6. IRT-to-provider amount conversion is explicit and tested; integer rounding is deterministic.
7. Refund/cancellation is a separate event with an audit record. Automatic SpotPlayer revocation is disabled until the business policy is approved.

The existing `rahbar-ipg-gateway` already demonstrates order-key/token checks, amount/currency verification, transaction claiming and callback locking. It is a behavioral reference, not code approved for bulk copying.

## Required entitlement lifecycle

```text
verified payment
  -> durable entitlement requested
  -> SpotPlayer provision attempt
  -> active (provider license ID stored)
       or retryable_failed (safe retry)
       or manual_review (non-retryable/conflict)
```

- Provisioning is triggered by a durable paid-order event, never by merely opening the order-details page.
- The unique entitlement key prevents duplicate licenses on callback retry, worker retry or migration rerun.
- Provider request payload includes the stable target order reference and mapped SpotPlayer course IDs.
- Provider response and last error are stored with timestamps, but API secrets and unnecessary personal data are not.
- A customer can only view entitlements whose `customer_id` matches the authenticated user.
- Legacy `_spotplayer_data` with a provider `_id` is imported as an existing active entitlement and must not call the provider.
- Orders without provider IDs are classified for retry/manual review using paid status, product mapping and creation time.

## Cutover order

1. Freeze writes and take a fresh production snapshot/checksum.
2. Import users, products and product mapping before orders.
3. Import orders and verified transaction identities without firing email, CRM, payment or SpotPlayer side effects.
4. Import existing SpotPlayer provider IDs as already provisioned entitlements.
5. Build a reconciliation set for paid course orders lacking entitlement/provider ID; do not provision automatically until reviewed.
6. Enable payment callbacks first, then entitlement worker, then notifications.
7. Reconcile order counts/totals, transaction uniqueness, paid course orders, entitlement uniqueness and provider IDs.

## Failure and rollback rules

- Payment verify unavailable: retain pending state and retry verify; do not provision.
- Duplicate callback/transaction: return idempotent result, alert on cross-order conflict.
- SpotPlayer unavailable: keep paid order, queue retry with bounded backoff and visible admin status.
- Product has no course mapping: route to manual review; never guess a course ID.
- Rollback to Legacy: stop Rebuild writes/workers, preserve all new order/payment/entitlement rows for reconciliation, and never delete provider licenses automatically.

## Next implementation slice

Create `rahbar-payment-bridge` as an isolated Rebuild plugin with its own `AGENTS.md`, `ARCHITECTURE.md`, adapters and automated tests. First milestone is a fake sandbox adapter proving callback validation, per-order lock, unique transaction claim and exactly-once entitlement dispatch. Real Zibal and SpotPlayer calls remain disabled until sandbox credentials and the revocation/refund policy are supplied.
