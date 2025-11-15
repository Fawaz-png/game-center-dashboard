import { test, expect } from '@playwright/test';

// Set baseURL via PLAYWRIGHT_BASE_URL env var, defaults to http://localhost:5173
const base = process.env.PLAYWRIGHT_BASE_URL || 'http://localhost';

test.describe('Login E2E', () => {
  test('user can log in and is redirected to dashboard', async ({ page }) => {
    await page.goto(`${base}/login`);

    // Fill in credentials - these must exist in the test DB (seeded)
    await page.fill('input[name="email"]', 'admin@example.com');
    await page.fill('input[name="password"]', 'password');

    // Submit the form
    await Promise.all([
      page.waitForNavigation({ url: '**/dashboard' }),
      page.click('button[type="submit"]'),
    ]);

    // Assert we are on the dashboard page
    expect(page.url()).toContain('/dashboard');

    // Basic smoke check for an element that exists on Dashboard.vue
    await expect(page.locator('h1')).toHaveText(/Dashboard|Welcome|Overview/i);
  });
});
