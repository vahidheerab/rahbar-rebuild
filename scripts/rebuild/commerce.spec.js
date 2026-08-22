const { test, expect } = require('@playwright/test');
const fs = require('fs');

const baseURL = process.env.REBUILD_URL || 'http://localhost:8082';
const systemChrome = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';

test.use({
  launchOptions: fs.existsSync(systemChrome) ? { executablePath: systemChrome } : {},
});
test.setTimeout(120_000);

async function waitForCartHydration(page) {
  const loading = page.getByText(/در حال بارگذاری محصولات|loading products/i).first();
  if (await loading.isVisible()) {
    await expect(loading).toBeHidden({ timeout: 45_000 });
  }
}

async function firstPurchasableProduct(request) {
  const response = await request.get(`${baseURL}/wp-json/wc/store/v1/products?per_page=20`);
  expect(response.ok()).toBeTruthy();
  const products = await response.json();
  const product = products.find((item) => item.is_purchasable && item.is_in_stock);
  expect(product, 'A purchasable sample product must exist').toBeTruthy();
  return product;
}

async function addProductToCart(page, request) {
  const product = await firstPurchasableProduct(request);
  await page.goto(product.permalink, { waitUntil: 'domcontentloaded' });
  const button = page.locator('button.single_add_to_cart_button');
  await expect(button).toBeVisible();
  await button.click();
  await expect(page.locator('.woocommerce-message, .wc-block-components-notice-banner')).toContainText(/سبد|cart/i);
  return product;
}

test.describe('WooCommerce public purchase path', () => {
  test('adds, persists, updates and removes a cart item', async ({ page, request }) => {
    const product = await addProductToCart(page, request);

    await page.goto(`${baseURL}/cart/`, { waitUntil: 'domcontentloaded' });
    await waitForCartHydration(page);
    await expect(page.getByText(product.name, { exact: false }).first()).toBeVisible();
    await page.reload({ waitUntil: 'domcontentloaded' });
    await waitForCartHydration(page);
    await expect(page.getByText(product.name, { exact: false }).first()).toBeVisible({ timeout: 30_000 });
    await page.screenshot({ path: 'docs/baseline/rebuild-commerce-cart.png', fullPage: true });

    const quantity = page.locator('input.qty').first();
    if (await quantity.isVisible()) {
      await quantity.fill('2');
      const update = page.getByRole('button', { name: /به.?روزرسانی سبد|update cart/i });
      await update.click();
      await expect(quantity).toHaveValue('2');
    }

    await page.locator('a.remove, .wc-block-cart-item__remove-link').first().click();
    await expect(page.getByText(/از سبد خرید شما حذف شد|has been removed from your cart/i).first()).toBeVisible();
    await page.reload({ waitUntil: 'domcontentloaded' });
    await expect(page.locator('main').getByText(product.name, { exact: false })).toHaveCount(0);
  });

  test('checkout rejects an empty required-address submission', async ({ page, request }) => {
    await addProductToCart(page, request);
    await page.goto(`${baseURL}/checkout/`, { waitUntil: 'domcontentloaded' });
    await expect(page).toHaveURL(/\/checkout\//);
    await expect(page.locator('form.checkout, .wp-block-woocommerce-checkout')).toBeVisible();
    await page.screenshot({ path: 'docs/baseline/rebuild-commerce-checkout.png', fullPage: true });

    const placeOrder = page.getByRole('button', { name: /ثبت سفارش|place order/i }).last();
    await expect(placeOrder).toBeVisible();
    await placeOrder.click();
    await expect(page.locator('.woocommerce-error, .wc-block-components-notice-banner.is-error, [role="alert"]').first()).toBeVisible();
  });

  test('account page exposes a usable login form', async ({ page }) => {
    await page.goto(`${baseURL}/my-account/`, { waitUntil: 'domcontentloaded' });
    const username = page.locator('#username, input[name="username"]').first();
    const password = page.locator('#password, input[name="password"]').first();
    await expect(username).toBeVisible();
    await expect(password).toBeVisible();
    await expect(page.getByRole('button', { name: /ورود|log in/i })).toBeVisible();
    await page.screenshot({ path: 'docs/baseline/rebuild-commerce-account.png', fullPage: true });
  });
});
