# Latest courses carousel report

Date: 2026-08-22

- Replaced the visible horizontal scrollbar with an interactive RTL carousel.
- Displays 4 cards on desktop, 3/2 on tablet breakpoints, and 1 on mobile.
- Previous/next controls are only shown when overflow exists and disable at their respective boundaries.
- Touch/pointer scrolling remains available without a visible scrollbar.
- ArrowRight/ArrowLeft keyboard navigation and visible focus styles are supported.
- `prefers-reduced-motion` switches movement from smooth to immediate.
- Automated Chromium test verified hidden scrollbar, RTL movement, boundary state, keyboard return, and HTTP 200.
- Home overflow smoke tests passed at 320 and 1440 pixels.
- Evidence: `rebuild-course-carousel-mobile-375.png`.

