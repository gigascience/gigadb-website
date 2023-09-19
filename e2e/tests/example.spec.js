// @ts-check
const { test, expect } = require('@playwright/test');

test('Is up', async ({ page }) => {
  await page.goto('/');
  await expect(page).not.toBeNull();
});