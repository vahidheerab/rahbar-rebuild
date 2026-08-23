const { test, expect } = require('@playwright/test');
const fs = require('fs');

const baseURL = process.env.REBUILD_URL || 'http://localhost:8082';
const systemChrome = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';

test.use({ launchOptions: fs.existsSync(systemChrome) ? { executablePath: systemChrome } : {} });
test.setTimeout(120_000);

test('local checkout exposes only the explicit fake payment adapter', async ({ page, request }) => {
  const response = await request.get(`${baseURL}/wp-json/wc/store/v1/products?per_page=20`);
  const products = await response.json();
  const product = products.find((item) => item.is_purchasable && item.is_in_stock);
  expect(product).toBeTruthy();

  await page.goto(product.permalink, { waitUntil: 'domcontentloaded' });
  await page.locator('button.single_add_to_cart_button').click();
  await page.goto(`${baseURL}/checkout/`, { waitUntil: 'domcontentloaded' });

  await expect(page.getByText('پرداخت آزمایشی (بدون تراکنش واقعی)', { exact: false }).first()).toBeVisible({ timeout: 45_000 });
  await expect(page.getByText('فقط برای آزمون مسیر پرداخت در محیط محلی راهبر.', { exact: false }).first()).toBeVisible();
});
