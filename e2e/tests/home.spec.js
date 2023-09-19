// @ts-check
const { test } = require('@playwright/test');
const { makeAxeAssertion } = require('../util/axe');

test.describe('homepage', () => {
  test('should not have any automatically detectable accessibility issues', async ({ page }) => {
    await makeAxeAssertion('/')(page)
  });
});