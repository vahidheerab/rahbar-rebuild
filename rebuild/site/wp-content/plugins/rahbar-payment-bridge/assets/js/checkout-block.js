(function () {
  const registry = window.wc && window.wc.wcBlocksRegistry;
  const settingsApi = window.wc && window.wc.wcSettings;
  const element = window.wp && window.wp.element;
  const entities = window.wp && window.wp.htmlEntities;

  if (!registry || !settingsApi || !element || !entities) {
    return;
  }

  const settings = settingsApi.getSetting('rahbar_payment_fake_data', {});
  const title = entities.decodeEntities(settings.title || 'پرداخت آزمایشی راهبر');
  const description = entities.decodeEntities(settings.description || 'هیچ مبلغ واقعی جابه‌جا نمی‌شود.');
  const Content = () => element.createElement('div', { className: 'rahbar-payment-sandbox-notice' }, description);

  registry.registerPaymentMethod({
    name: 'rahbar_payment_fake',
    label: element.createElement('strong', null, title),
    content: element.createElement(Content),
    edit: element.createElement(Content),
    canMakePayment: () => true,
    ariaLabel: title,
    supports: { features: settings.supports || ['products'] },
  });
})();
