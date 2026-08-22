const { test, expect } = require('@playwright/test');
test.use({ channel: 'chrome' });
test.describe.configure({ mode: 'parallel' });

const viewports = [
  { name: '320', width: 320, height: 800 },
  { name: '375', width: 375, height: 812 },
  { name: '768', width: 768, height: 1024 },
  { name: '1024', width: 1024, height: 900 },
  { name: '1440', width: 1440, height: 1000 },
];

const routes = [
  ['home', '/'], ['blog', '/blog/'], ['article', '/goldstoreaccountingsoftware/'],
  ['shop', '/shop/'], ['product', '/product/gold-accounting-training-2/'],
  ['contact', '/contact/'], ['search', '/?s=سلام'], ['404', '/responsive-qa-missing-page/'],
];

for (const viewport of viewports) {
  for (const [name, route] of routes) {
    test(`${name} has no horizontal overflow at ${viewport.name}`, async ({ page }) => {
      await page.setViewportSize(viewport);
      const response = await page.goto(`http://localhost:8082${route}`, { waitUntil: 'domcontentloaded', timeout: 60000 });
      expect(response).not.toBeNull();
      expect(response.status()).toBe(name === '404' ? 404 : 200);
      const dimensions = await page.evaluate(() => ({
        viewport: document.documentElement.clientWidth,
        document: document.documentElement.scrollWidth,
        body: document.body.scrollWidth,
        offenders: [...document.querySelectorAll('body *')].map((element) => {
          const rect = element.getBoundingClientRect();
          return { tag: element.tagName, className: String(element.className).slice(0, 120), left: Math.round(rect.left), right: Math.round(rect.right), width: Math.round(rect.width), scrollWidth: element.scrollWidth };
        }).filter((item) => item.right > document.documentElement.clientWidth + 1 || item.left < -1).sort((a, b) => b.width - a.width).slice(0, 12),
      }));
      expect(dimensions.document, JSON.stringify(dimensions)).toBeLessThanOrEqual(dimensions.viewport + 1);
    });
  }
}
