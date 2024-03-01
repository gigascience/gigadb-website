const { test, expect } = require('@playwright/test');

test('Can scroll horizontally in File table', async ({ page }) => {
    await page.goto('/dataset/102484');

    // Check File Name column name in File table is visible
    const filename_column_header = page.locator('xpath=//*[@id="files_table"]/thead/tr/th[1]');
    await filename_column_header.scrollIntoViewIfNeeded();
    await expect(page.getByText('File Name')).toBeInViewport();
    // Check Download column name in 9th column is visible
    const download_column_header = page.locator('xpath=//*[@id="files_table"]/thead/tr/th[9]');
    await download_column_header.scrollIntoViewIfNeeded();
    await expect(page.locator('xpath=//*[@id="files_table"]/thead/tr/th[9]')).toBeInViewport();
});