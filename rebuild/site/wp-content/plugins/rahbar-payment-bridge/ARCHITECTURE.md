# Rahbar Payment Bridge architecture

## Purpose

Provide a stable WooCommerce payment boundary that can host multiple payment providers without coupling order state to a specific gateway or to course delivery.

## Layers

```text
WooCommerce gateway/block integration
  -> Application payment service
    -> Provider adapter interface
      -> Fake adapter (Phase 1)
      -> Zibal adapter (future, after sandbox contract)
    -> Transaction claim and per-order lock
    -> WooCommerce order repository
    -> Domain event after verified payment
```

## Core contracts

- `ProviderAdapter`: create payment request and verify a normalized callback.
- `PaymentRequest`: order ID, integer provider amount, currency and callback URL.
- `PaymentSession`: opaque provider token and validated HTTPS redirect URL.
- `VerificationResult`: success/failure category, unique reference, verified amount and currency.
- `PaymentService`: orchestrates locks, verification, transaction uniqueness and `payment_complete()`.

Core code must not inspect provider-specific field names or response shapes.

## State and identity

- WooCommerce order owns payment status and `_transaction_id`.
- Bridge order metadata uses `_rahbar_payment_*` keys for adapter ID, opaque session token, requested amount/currency and correlation ID.
- Transaction uniqueness uses a durable hashed claim keyed by adapter ID + provider reference.
- A repeated callback for the same paid order returns the existing success result.
- A provider reference claimed by another order is a hard conflict and never completes payment.

## Security invariants

- Production requests require HTTPS, TLS verification, bounded response sizes and timeouts.
- Callback order key is required in addition to the order ID.
- Adapter callback authenticity is mandatory (opaque token, signature or provider verify API).
- The verified amount/currency must equal the immutable payment request snapshot.
- Logs contain correlation ID, order ID, adapter ID and error category only.
- Fake adapter registration is limited to non-production environments and explicit enablement.

## Extension boundary

New payment methods are new adapter classes registered through a filter. They do not modify the application service. Offline methods that do not use callback verification remain native WooCommerce gateways unless an explicit adapter contract is approved.

## Course access boundary

After `payment_complete()`, the bridge emits `rahbar_payment_verified` with the order ID and safe transaction identity. A separate Course Access plugin consumes WooCommerce/domain events and owns entitlement/SpotPlayer provisioning. Payment success must remain valid even if course provisioning fails.

## Phase 1

Implement contracts, fake sandbox adapter, WooCommerce registration, callback orchestrator and tests for success, decline, mismatch, duplicate callback and cross-order duplicate reference. No real provider credentials or requests are permitted.
