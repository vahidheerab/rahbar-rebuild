# Rebuild accessibility report

Date: 2026-08-22

## Automated coverage

Routes: Home, Blog, imported Article, Shop, imported Product, Contact, Search, and 404.

- axe-core rules tagged WCAG 2.0/2.1 A and AA were executed in Chrome.
- Final result: no serious or critical violations on all eight routes.
- Expected HTTP 200/404 status was asserted during each scan.
- Reusable command: `npm run test:accessibility`.

## Interaction and semantics

- Global high-visibility `:focus-visible` outline added.
- Contact fields expose accessible names and follow the expected keyboard order.
- Contact validation message is exposed with `role=alert`.
- Home now has a semantic `main` landmark.
- Hidden WooCommerce mini-cart descendants are removed from the focus order and restored when the drawer opens.
- Wide imported article tables are keyboard-focusable and have an explanatory accessible label.
- Latest-courses carousel supports buttons, ArrowLeft/ArrowRight, boundary disabled states, and an accessible track label.

## Visual accessibility

- Low-contrast yellow/white CTA and link combinations were changed to dark text or primary link color.
- Contact submit button contrast was corrected.
- Page usability and lack of root horizontal overflow were checked at 200% zoom.
- `prefers-reduced-motion: reduce` disables smooth scrolling and effectively removes animation/transition duration.

## Remaining release check

Automated semantics are green, but a short manual pass with the production browser/assistive-technology pair (NVDA + Firefox/Chrome on Windows and VoiceOver + Safari on Apple if in support scope) remains part of pre-launch acceptance. Automated tooling cannot validate pronunciation quality or the complete lived screen-reader experience.

