// @ts-check
const { test, expect } = require('@playwright/test');
const AxeBuilder = require('@axe-core/playwright').default;

// NOTE the commented out paths have pending issues
const PUBLIC_PATHS = [
  {
    path: '/',
    disabledElements: ['.image-overlay', '.image-background']
  },
  '/dataset/100006',
  {
    path: '/search/new?keyword=Genomic&type%5B%5D=dataset&dataset_type%5B%5D=Genomic',
    disabledElements: ['.image-overlay', '.image-background']
  },
  {
    path: '/site/contact',
    disabledElements: ['textarea']
  },
  '/site/help',
  '/site/guide',
  '/site/guidegenomic',
  '/site/guideimaging',
  '/site/guidemetabolomic',
  '/site/guideepigenomic',
  '/site/guidemetagenomic',
  '/site/guidesoftware',
  // '/site/faq',
  '/site/about',
  {
    path: '/site/team',
    disabledElements: ['.team-content > p']
  },
  '/site/advisory',
  '/site/term',
  '/site/login',
  '/site/forgot',
  '/site/thanks',
  '/site/create',
  {
    path: 'site/mapbrowse',
    disabledElements: ['#map']
  }
];

const USER_PATHS = [
  'user/view_profile',
  'datasetSubmission/upload',
  'user/changePassword'
]

const ADMIN_PATHS = [
  // 'site/admin',
  // 'adminDataset/admin',
  // 'adminDataset/create',
]

const runAccessibilityTests = (paths, storageStatePath) => {
  paths.forEach((pathEntry) => {
    let path;
    let disabledElements = [];

    if (typeof pathEntry === 'string') {
      path = pathEntry;
    } else {
      path = pathEntry.path;
      disabledElements = pathEntry.disabledElements || [];
    }

    test.describe(`Page ${path}`, () => {
      if (storageStatePath) {
        test.use({ storageState: storageStatePath });
      }

      test('should not have any accessibility violations outside of elements with known issues', async ({ page }) => {
        await page.goto(path, { waitUntil: 'networkidle' });

        let axeBuilder = new AxeBuilder({ page });

        disabledElements.forEach(el => {
          axeBuilder = axeBuilder.exclude(el);
        });

        const accessibilityScanResults = await axeBuilder.analyze();

        expect(accessibilityScanResults.violations).toEqual([]);
        expect(accessibilityScanResults.incomplete.filter((item) => item.id === 'color-contrast')).toEqual([]);
      });
    });
  });
}

runAccessibilityTests(PUBLIC_PATHS);
runAccessibilityTests(USER_PATHS, '.auth/user.json');
runAccessibilityTests(ADMIN_PATHS, '.auth/admin.json');
