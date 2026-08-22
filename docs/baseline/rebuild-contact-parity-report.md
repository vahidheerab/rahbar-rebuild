# Rebuild Contact parity report

Date: 2026-08-22

Target: `http://localhost:8082/contact/`

## Implemented

- Dedicated block template with the Legacy section order and responsive layout.
- Public telephone, mobile, address, working hours, social links, CTA, and map coordinates aligned with the Legacy inventory.
- A first-party `rahbar-contact` plugin provides the form independently of the theme.
- Required first name, last name, and telephone fields; optional message; server-side sanitization and length limits.
- WordPress nonce validation, honeypot, per-IP hashed rate limiting, safe redirect statuses, and delivery to WordPress `admin_email`.
- The plugin is activated idempotently by `scripts/rebuild/initialize-pages.php`.
- Mobile uses a single-column form and omits the map, matching the observed Legacy mobile behavior.

## Verification

- Plugin and initializer PHP lint: pass.
- PowerShell initializer parse: pass.
- Rebuild Compose config: pass.
- Contact route: HTTP 200.
- Plugin active and shortcode/form fields rendered: pass.
- No PHP fatal, warning, parse, or notice found during the page check.
- Full-page visual evidence captured at 1440, 768, and 375 pixels:
  - `rebuild-contact-desktop-1440.png`
  - `rebuild-contact-tablet-768.png`
  - `rebuild-contact-mobile-375.png`

## Operational note

No real message was sent during QA. Successful SMTP delivery must be checked after the production mail provider is configured. The embedded Google map also depends on external Google availability; a neutral background remains visible while it loads or when blocked.

## Rollback

Deactivate `rahbar-contact`, restore the previous `page-contact.html` and `style.css`, then rerun the initializer. No custom database schema was introduced.
