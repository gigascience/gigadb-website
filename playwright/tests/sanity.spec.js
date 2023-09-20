// @ts-check
const { test, expect } = require('@playwright/test');

test.skip('Is up', async ({ page }) => {
  await page.goto('/');
  await expect(page).not.toBeNull();
});