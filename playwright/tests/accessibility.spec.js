// @ts-check
const { test, expect } = require('@playwright/test');
const AxeBuilder = require('@axe-core/playwright').default;

const PUBLIC_PATHS = [
  '/',
  '/dataset/100006',
  // '/search/new?keyword=Genomic&type%5B%5D=dataset&dataset_type%5B%5D=Genomic',
  // '/site/contact',
  '/site/help',
  '/site/guide',
  '/site/guidegenomic',
  '/site/guideimaging',
  '/site/guidemetabolomic',
  '/site/guideepigenomic',
  '/site/guidemetagenomic',
  '/site/guidesoftware',
  '/site/faq',
  '/site/about',
  '/site/team',
  '/site/advisory',
  '/site/term',
  '/site/login',
  '/site/forgot',
  '/site/thanks',
  // '/site/create',
];

const USER_PATHS = [
  'site/mapbrowse',
  'user/view_profile',
  // 'datasetSubmission/upload'
]

const ADMIN_PATHS = [
  // 'site/admin',
  // 'adminDataset/admin',
  // 'adminDataset/create',
]


PUBLIC_PATHS.forEach((path) => {
  test.describe(`Page ${path}`, () => {

    // NOTE: skipping general accessibility issues test as it will fail at least until a11y issues are fixed, so going for a more granular approach for now
    test.skip('should not have any automatically detectable accessibility issues', async ({ page }) => {
      await page.goto(path);

      const accessibilityScanResults = await new AxeBuilder({ page }).analyze();

      expect(accessibilityScanResults.violations).toEqual([]);
    });

    test('should not have any color contrast issues', async ({ page }) => {
      await page.goto(path, { waitUntil: 'networkidle' });

      const accessibilityScanResults = await new AxeBuilder({ page })
        .withRules(['color-contrast'])
        .exclude('.text-icon')
        .exclude('.image-background')
        .analyze();

      expect(accessibilityScanResults.violations).toEqual([]);
      expect(accessibilityScanResults.incomplete.filter(item => item.id === 'color-contrast')).toEqual([])

    });
  });
});

USER_PATHS.forEach((path) => {
  test.describe(`User page ${path}`, () => {
    test.use({ storageState: '.auth/user.json' });

    test('should not have any color contrast issues', async ({ page }) => {
      await page.goto(path);

      const accessibilityScanResults = await new AxeBuilder({ page })
        .withRules(['color-contrast'])
        .analyze();

      console.log(accessibilityScanResults.incomplete)

      expect(accessibilityScanResults.violations).toEqual([]);
      expect(accessibilityScanResults.incomplete.some(item => item.id === 'color-contrast')).toBeFalsy()

    });
  });
})

ADMIN_PATHS.forEach((path) => {
  test.describe(`Admin page ${path}`, () => {
    test.use({ storageState: '.auth/admin.json' });

    test('should not have any color contrast issues', async ({ page }) => {
      await page.goto(path);

      const accessibilityScanResults = await new AxeBuilder({ page })
        .withRules(['color-contrast'])
        .analyze();

      expect(accessibilityScanResults.violations).toEqual([]);
      expect(accessibilityScanResults.incomplete.some(item => item.id === 'color-contrast')).toBeFalsy()

    });
  });
})