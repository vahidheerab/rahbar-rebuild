(() => {
  'use strict';

  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

  document.querySelectorAll('.rahbar-latest-courses').forEach((section, index) => {
    const track = section.querySelector('.rahbar-course-track');
    const controls = section.querySelector('.rahbar-course-carousel__controls');
    const previous = controls?.querySelector('[data-course-carousel="previous"]');
    const next = controls?.querySelector('[data-course-carousel="next"]');
    if (!track || !controls || !previous || !next) return;

    track.id ||= `rahbar-course-track-${index + 1}`;
    track.tabIndex = 0;
    track.setAttribute('aria-label', 'جدیدترین دوره‌های راهبر حساب');
    previous.setAttribute('aria-controls', track.id);
    next.setAttribute('aria-controls', track.id);
    section.classList.add('has-course-carousel');

    const step = () => {
      const card = track.querySelector('.rahbar-course-card');
      const gap = Number.parseFloat(getComputedStyle(track).columnGap) || 0;
      return card ? card.getBoundingClientRect().width + gap : track.clientWidth;
    };

    const update = () => {
      const distance = Math.abs(track.scrollLeft);
      const maximum = Math.max(0, track.scrollWidth - track.clientWidth);
      previous.disabled = distance < 2;
      next.disabled = maximum - distance < 2;
      controls.hidden = maximum < 2;
    };

    const move = (direction) => {
      track.scrollBy({
        left: direction * step(),
        behavior: reducedMotion.matches ? 'auto' : 'smooth',
      });
    };

    previous.addEventListener('click', () => move(1));
    next.addEventListener('click', () => move(-1));
    track.addEventListener('scroll', update, { passive: true });
    track.addEventListener('keydown', (event) => {
      if (event.key === 'ArrowRight') { event.preventDefault(); move(1); }
      if (event.key === 'ArrowLeft') { event.preventDefault(); move(-1); }
    });
    new ResizeObserver(update).observe(track);
    update();
  });
})();
