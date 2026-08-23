const { test, expect } = require('@playwright/test');
const fs = require('fs');

const baseURL = process.env.REBUILD_URL || 'http://localhost:8082';
const systemChrome = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
test.use({ launchOptions: fs.existsSync(systemChrome) ? { executablePath: systemChrome } : {} });
test.setTimeout(120_000);

test('Iran remains accepted after selecting an Iranian province', async ({ page, request }) => {
  const response = await request.get(`${baseURL}/wp-json/wc/store/v1/products?per_page=20`);
  const products = await response.json();
  const product = products.find((item) => item.is_purchasable && item.is_in_stock);
  expect(product).toBeTruthy();

  await page.goto(product.permalink, { waitUntil: 'domcontentloaded' });
  await page.locator('button.single_add_to_cart_button').click();
  await page.goto(`${baseURL}/checkout/`, { waitUntil: 'domcontentloaded' });
  await expect(page.locator('.wp-block-woocommerce-checkout')).toBeVisible();

  const country = page.locator('#billing-country, select[name="country"]').first();
  await expect(country).toHaveValue('IR', { timeout: 45_000 });
  const state = page.locator('#billing-state, select[name="state"]').first();
  await expect(state).toBeVisible();
  const options = await state.locator('option').evaluateAll((nodes) => nodes.map((node) => node.value).filter(Boolean));
  expect(options.length).toBeGreaterThan(0);
  await state.selectOption(options[0]);

  await expect(country).toHaveValue('IR');
  await expect(page.getByText(/سفارش از کشور انتخاب شده را نمی.?پذیریم|do not accept orders from the selected country/i)).toHaveCount(0);
});
