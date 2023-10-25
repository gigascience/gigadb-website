// @ts-check
import { test as setup, expect } from '@playwright/test';

function makeAuthenticate(credentials, authFile) {
  const { email, password } = credentials

  return async ({ page }) => {
    await page.goto('/site/login');

    await page.getByLabel('Email Address ').fill(email);
    await page.getByLabel('Password ').fill(password);
    await page.click('input[type="submit"]');

    await page.waitForURL('/');
    await expect(page.getByRole('link', { name: 'LogOut' })).toBeVisible();

    await page.context().storageState({ path: authFile });
  }
}

setup('authenticate as admin', makeAuthenticate({
  email: 'admin@gigadb.org',
  password: 'gigadb'
}, '.auth/admin.json'));

setup('authenticate as user', makeAuthenticate({
  email: 'user@gigadb.org',
  password: 'gigadb'
}, '.auth/user.json'));
