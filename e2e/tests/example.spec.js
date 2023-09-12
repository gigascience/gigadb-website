// @ts-check
const { test, expect } = require('@playwright/test');

const BASE_URL = 'http://gigadb.gigasciencejournal.com:9170';

test('Is up', async ({ page }) => {
  await page.goto(BASE_URL);
  await expect(page).not.toBeNull();
});