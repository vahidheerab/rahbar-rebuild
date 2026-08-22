(() => {
  'use strict';

  const focusableSelector = 'a[href], button, input, select, textarea, [tabindex]';

  const syncDrawer = (drawer) => {
    const hidden = drawer.getAttribute('aria-hidden') === 'true';
    drawer.querySelectorAll(focusableSelector).forEach((element) => {
      if (hidden) {
        if (!element.hasAttribute('data-rahbar-tabindex')) {
          element.setAttribute('data-rahbar-tabindex', element.getAttribute('tabindex') ?? '');
        }
        element.setAttribute('tabindex', '-1');
        if ('disabled' in element && !element.disabled) {
          element.setAttribute('data-rahbar-enable-on-open', 'true');
          element.disabled = true;
        }
      } else {
        const previous = element.getAttribute('data-rahbar-tabindex');
        if (previous !== null) {
          previous === '' ? element.removeAttribute('tabindex') : element.setAttribute('tabindex', previous);
          element.removeAttribute('data-rahbar-tabindex');
        }
        if (element.hasAttribute('data-rahbar-enable-on-open')) {
          element.disabled = false;
          element.removeAttribute('data-rahbar-enable-on-open');
        }
      }
    });
  };

  document.querySelectorAll('.wc-block-mini-cart__drawer').forEach((drawer) => {
    syncDrawer(drawer);
    new MutationObserver(() => syncDrawer(drawer)).observe(drawer, { attributes: true, attributeFilter: ['aria-hidden'] });
  });

  document.querySelectorAll('.rahbar-single-post__content table').forEach((table) => {
    table.tabIndex = 0;
    table.setAttribute('aria-label', 'جدول محتوای مقاله؛ برای مشاهده ستون‌ها به‌صورت افقی پیمایش کنید');
  });
})();
