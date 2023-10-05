// @ts-check
import { expect, test } from '@playwright/test';

test.describe('Admin User', () => {
  test.use({ storageState: '.auth/admin.json' });

  test('Is authenticated as admin', async ({ page }) => {
    await page.goto('/');
    await expect(page.getByRole('link', { name: 'LogOut' })).toBeVisible();
    await expect(page.getByRole('link', { name: /Admin/i })).toBeVisible();
  });
});

test.describe('Regular User', () => {
  test.use({ storageState: '.auth/user.json' });

  test('Is authenticated as user', async ({ page }) => {
    await page.goto('/');
    await expect(page.getByRole('link', { name: 'LogOut' })).toBeVisible();
    await expect(page.getByRole('link', { name: /Admin/i })).not.toBeVisible();
  });
});
