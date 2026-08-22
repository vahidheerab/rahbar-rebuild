const { test, expect } = require('@playwright/test');
const AxeBuilder = require('@axe-core/playwright').default;

test.use({ channel: 'chrome' });
test.describe.configure({ mode: 'parallel' });

const routes = [
  ['home', '/'], ['blog', '/blog/'], ['article', '/goldstoreaccountingsoftware/'],
  ['shop', '/shop/'], ['product', '/product/gold-accounting-training-2/'],
  ['contact', '/contact/'], ['search', '/?s=سلام'], ['404', '/accessibility-qa-missing-page/'],
];

for (const [name, route] of routes) {
  test(`${name} has no serious accessibility violations`, async ({ page }) => {
    await page.setViewportSize({ width: 1440, height: 1000 });
    const response = await page.goto(`http://localhost:8082${route}`, { waitUntil: 'domcontentloaded', timeout: 60000 });
    expect(response.status()).toBe(name === '404' ? 404 : 200);
    const results = await new AxeBuilder({ page }).withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa']).analyze();
    expect(results.violations.filter((violation) => ['serious', 'critical'].includes(violation.impact)), JSON.stringify(results.violations, null, 2)).toEqual([]);
  });
}

test('keyboard focus reaches primary controls and contact fields', async ({ page }) => {
  await page.goto('http://localhost:8082/contact/', { waitUntil: 'domcontentloaded', timeout: 60000 });
  await page.keyboard.press('Tab');
  const firstFocused = await page.evaluate(() => document.activeElement?.tagName);
  expect(['A', 'BUTTON', 'INPUT']).toContain(firstFocused);
  await page.locator('#rahbar-contact-first-name').focus();
  await expect(page.locator('#rahbar-contact-first-name')).toBeFocused();
  await page.keyboard.press('Tab');
  await expect(page.locator('#rahbar-contact-last-name')).toBeFocused();
  await page.keyboard.press('Tab');
  await expect(page.locator('#rahbar-contact-phone')).toBeFocused();
});

test('contact exposes screen-reader names and error announcement', async ({ page }) => {
  await page.goto('http://localhost:8082/contact/?contact=invalid#contact-form', { waitUntil: 'domcontentloaded', timeout: 60000 });
  await expect(page.getByRole('main')).toHaveCount(1);
  await expect(page.getByRole('heading', { level: 1, name: 'تماس با ما' })).toBeVisible();
  await expect(page.getByRole('textbox', { name: /^نام$/ })).toBeVisible();
  await expect(page.getByRole('textbox', { name: /نام خانوادگی/ })).toBeVisible();
  await expect(page.getByRole('textbox', { name: /تلفن/ })).toBeVisible();
  await expect(page.getByRole('alert')).toContainText('فیلدهای الزامی');
});

test('pages remain usable at 200 percent zoom', async ({ page }) => {
  await page.setViewportSize({ width: 1280, height: 900 });
  await page.goto('http://localhost:8082/', { waitUntil: 'domcontentloaded', timeout: 60000 });
  await page.evaluate(() => { document.documentElement.style.zoom = '2'; });
  const dimensions = await page.evaluate(() => ({ viewport: document.documentElement.clientWidth, document: document.documentElement.scrollWidth }));
  expect(dimensions.document).toBeLessThanOrEqual(dimensions.viewport + 1);
  await expect(page.locator('main')).toBeVisible();
});

test('reduced motion disables smooth carousel movement', async ({ page }) => {
  await page.emulateMedia({ reducedMotion: 'reduce' });
  await page.goto('http://localhost:8082/', { waitUntil: 'domcontentloaded', timeout: 60000 });
  expect(await page.locator('.rahbar-course-track').evaluate((element) => getComputedStyle(element).scrollBehavior)).toBe('auto');
});
