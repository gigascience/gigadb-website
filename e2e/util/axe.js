const { expect } = require('@playwright/test');
const AxeBuilder = require('@axe-core/playwright').default;

export function makeAxeAssertion(path) {
  return async function axeAssertion(page) {
    await page.goto(path);

    const accessibilityScanResults = await new AxeBuilder({ page }).analyze();

    expect(accessibilityScanResults.violations).toEqual([]);
  }
}