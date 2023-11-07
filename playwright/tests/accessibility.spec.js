// @ts-check
const { test, expect } = require('@playwright/test');
const AxeBuilder = require('@axe-core/playwright').default;
const TAG = require('../tags');
const { PUBLIC_PATHS, USER_PATHS, ADMIN_PATHS } = require('../paths');

const runAccessibilityTests = (paths, storageStatePath) => {
  paths.forEach((pathEntry) => {
    let path;
    let disabledElements = [];
    let tags = [];


    if (typeof pathEntry === 'string') {
      path = pathEntry;
    } else {
      path = pathEntry.path;
      disabledElements = pathEntry.disabledElements || [];
      tags = pathEntry.tags || [];
    }

    const shouldRunTest = TAG.RUN_ALL || (tags.length === 0 ? TAG.DEFAULT : tags.some(tag => TAG[tag]));

    if (shouldRunTest) {
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
    }
  });
}

(TAG.RUN_ALL || TAG.PUBLIC) && runAccessibilityTests(PUBLIC_PATHS);
(TAG.RUN_ALL || TAG.USER) && runAccessibilityTests(USER_PATHS, '.auth/user.json');
(TAG.RUN_ALL || TAG.ADMIN) && runAccessibilityTests(ADMIN_PATHS, '.auth/admin.json');
