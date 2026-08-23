# Rahbar Payment Bridge contributor rules

These rules apply to every file in this plugin.

1. Read this file and `ARCHITECTURE.md` completely before changing code.
2. WooCommerce is the source of truth for orders and payment state.
3. Providers are adapters. Provider-specific request, callback and verification logic must remain inside its adapter.
4. A callback can mark an order paid only after server-to-server verification succeeds.
5. Validate order identity, payment method, token/signature, amount, currency and unique transaction reference.
6. Callback processing and transaction claims must be idempotent and safe under concurrent requests.
7. Never log or commit merchant secrets, tokens, complete provider responses, card data or customer PII.
8. Payment completion emits a domain event; this plugin must not call SpotPlayer or implement course access.
9. Production adapters are disabled until their contract and sandbox tests pass. The fake adapter is test/local only.
10. Schema changes require an upgrade path, rollback note and architecture update.
11. Do not copy the Legacy Zibal or generic gateway plugin wholesale. Reuse behavior only after review and tests.
12. Run PHP lint, Compose validation and relevant automated tests before delivery.

## Active phase and exit gate

Phase 1: core contracts and fake sandbox adapter.

Exit gate:

- adapter interface exists without provider-specific branching in core;
- duplicate callback and concurrent verification are exactly-once;
- amount/currency mismatch cannot complete payment;
- fake adapter is unavailable in production;
- no external payment or course-access call is made;
- architecture and test evidence are updated.
