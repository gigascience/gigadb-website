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


const runAccessibilityTests = (paths, storageStatePath) => {
  paths.forEach((path) => {
    test.describe(`Page ${path}`, () => {
      if (storageStatePath) {
        test.use({ storageState: storageStatePath });
      }

      async function runColorContrastScan (page) {
        await page.goto(path, { waitUntil: 'networkidle' });
        const accessibilityScanResults = await new AxeBuilder({ page })
          .withRules(['color-contrast'])
          .exclude('.text-icon')
          .exclude('.image-background')
          .analyze();

        return accessibilityScanResults;
      };

      test('should not have any color contrast issues', async ({ page }) => {
        const results = await runColorContrastScan(page);

        // NOTE color contrast issues are not reported as violations but as incomplete checks, see https://github.com/dequelabs/axe-core/blob/master/doc/API.md#results-object
        expect(results.violations).toEqual([]);
        expect(results.incomplete.filter((item) => item.id === 'color-contrast')).toEqual([]);
      });
    });
  });
}

runAccessibilityTests(PUBLIC_PATHS);
runAccessibilityTests(USER_PATHS, '.auth/user.json');
runAccessibilityTests(ADMIN_PATHS, '.auth/admin.json');
