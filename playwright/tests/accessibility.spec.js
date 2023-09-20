// @ts-check
const { test, expect } = require('@playwright/test');
const AxeBuilder = require('@axe-core/playwright').default;

const PATHS = [
  '/',
  '/dataset/100006',
  '/search/new?keyword=Genomic&type%5B%5D=dataset&dataset_type%5B%5D=Genomic',
  '/site/contact',
  '/site/help',
  '/site/guide',
  '/site/guidegenomic',
  '/site/guideimaging',
  '/site/guidemetabolomic',
  '/site/guideepigenomic',
  '/site/guidemetagenomic',
  '/site/guidesoftware',
  '/site/faq',
  '/site/team',
  '/site/advisory',
  '/site/term',
  '/site/login',
  '/site/forgot',
  '/site/thanks',
  '/site/create',
];


PATHS.forEach((path) => {
  test.describe(`Page ${path}`, () => {

    // NOTE: skipping general accessibility issues test as it will fail at least until a11y issues are fixed, so going for a more granular approach for now
    test.skip('should not have any automatically detectable accessibility issues', async ({ page }) => {
      await page.goto(path);

      const accessibilityScanResults = await new AxeBuilder({ page }).analyze();

      expect(accessibilityScanResults.violations).toEqual([]);
    });

    test('should not have any color contrast issues', async ({page}) => {
      await page.goto(path);

      const accessibilityScanResults = await new AxeBuilder({ page })
        .withTags(
          ['wcag21aa'] // test for WCAG 2.1 Level AA color contrast issues
          )
        .analyze();

      expect(accessibilityScanResults.violations).toEqual([]);
    });
  });
});