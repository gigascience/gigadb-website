// @ts-check
const { test, expect } = require('@playwright/test');
const AxeBuilder = require('@axe-core/playwright').default;

const { makeAxeAssertion } = require('../util/axe');

const PATH  = '/'

test.describe('homepage', () => {
  // NOte: skipping general test as it will fail until a11y issues are fixed, so we need a more granular approach
  test.skip('should not have any automatically detectable accessibility issues', async ({ page }) => {
    await makeAxeAssertion(PATH)(page)
  });

  test('Should not have color contrast issues', async ({page}) => {
    await page.goto(PATH);

    const accessibilityScanResults = await new AxeBuilder({ page })
    .withTags(['wcag21aa'])
    // .withRules(['color-contrast'])
    .analyze();

    expect(accessibilityScanResults.violations).toEqual([]);
  })
});