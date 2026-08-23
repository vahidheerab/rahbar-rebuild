# Payment gateway architecture selection

Date: 2026-08-23
Decision: build `Rahbar Payment Bridge`; retain `rahbar-ipg-gateway` as reviewed behavioral reference.

## Comparison

| Capability | Legacy `rahbar-ipg-gateway` | Payment Bridge direction |
| --- | --- | --- |
| Callback token/order validation | Good baseline | Required invariant |
| Verify amount/currency/reference | Good baseline | Required invariant |
| Concurrent callback lock | Present | Required, moved to application service |
| Multiple providers at once | No; one configurable gateway ID | Yes; provider registry/adapters |
| Different provider protocols | Limited to JSON token → redirect → JSON verify | Adapter-owned protocol |
| Signed webhook/direct callback | No explicit contract | Adapter capability |
| Refund/provider operations | Not implemented | Extensible future capability |
| Automated tests | None found | Contract and orchestration tests required |
| Checkout Block asset | PHP registers an asset missing from the snapshot | Must ship and test every declared asset |
| Course access coupling | None (correct) | None; emits verified-payment event only |

## Rationale

The existing gateway contains valuable security behavior but achieves provider flexibility through editable JSON templates and field paths inside one gateway class. That works only while providers share the same lifecycle. Adding a provider with signatures, a server webhook, a different redirect model or provider-side refund would add branching to the same class and make regression risk grow with every payment method.

Payment Bridge keeps orchestration stable and moves protocol differences behind `ProviderAdapter`. Adding or replacing a provider becomes registration of a new adapter rather than modification of callback/payment core.

## Implemented Phase 1 sandbox slice

- Plugin-local contributor rules and architecture/exit gate.
- Provider adapter interface and validated payment value objects.
- Duplicate-safe provider registry.
- Local/test-only fake adapter and WooCommerce Checkout Block gateway.
- Durable per-order lock and unique provider transaction claim.
- Callback orchestration with order key, payment method, token, amount and currency validation.
- Contract/integration tests for approved, declined, invalid-token, amount-mismatch and repeated callbacks.
- A repeated callback does not complete payment or emit `rahbar_payment_verified` twice.
- No external request, merchant secret, real provider or course-access call is enabled.

Next slice: genuine concurrent callback and HTTP failure-boundary tests, followed by the real provider adapter only after sandbox approval.
