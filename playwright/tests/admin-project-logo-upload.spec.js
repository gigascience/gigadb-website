// note: run this test with `npm run test:single` to avoid issues with saving multiple projects with the same unique fields
const { test, expect } = require("@playwright/test");
const path = require("path");

async function sleep(ms) {
  await new Promise(resolve => setTimeout(resolve, ms));
}

test.describe("Project logo upload", () => {
  test.use({ storageState: ".auth/admin.json" });

  test("admin can create and edit project with logo", async ({ page }) => {
    // CREATE
    // Navigate to create project page
    await page.goto("adminProject/create");

    // Upload initial logo
    const logoPath = path.join(__dirname, "../images/bgi_logo_new.png");
    const fileChooserPromise = page.waitForEvent("filechooser");
    await page.click(".uppy-Dashboard-browse");
    const fileChooser = await fileChooserPromise;
    await fileChooser.setFiles(logoPath);

    // Verify logo appears in uploader preview
    await expect(page.locator(".uppy-Dashboard-Item-previewImg")).toBeVisible();
    await expect(page.locator('img[alt="bgi_logo_new.png"]')).toBeVisible();

    // Submit upload and verify logo appears
    await page.click('button[aria-label="Upload 1 file"]');
    await page.waitForSelector("img.test-uploaded-project-logo");
    await expect(page.locator("img.test-uploaded-project-logo")).toBeVisible();
    let imagePath = await page
      .locator("img.test-uploaded-project-logo")
      .getAttribute("src");
    expect(imagePath).toContain("bgi-logo-new.png");

    // Fill in project details and submit
    await page.getByLabel("Name").fill("Test Project");
    await page.getByLabel("URL").fill("https://test-project.org");
    await page.click('input[type="submit"]');

    // Verify project was created with correct details
    await page.waitForURL(/adminProject\/view/);
    await expect(page.getByText("Test Project")).toBeVisible();
    await expect(page.getByText("https://test-project.org")).toBeVisible();
    await expect(page.getByText("bgi-logo-new.png")).toBeVisible();
    const projectId = await page.url().split("/").pop();

    // EDIT
    // Navigate to edit project page
    const newLogoPath = path.join(__dirname, "../images/G10Klogo.jpg");
    await page.goto(`adminProject/update/id/${projectId}`);
    await expect(page.locator("img.test-uploaded-project-logo")).toBeVisible();

    // Upload new logo
    const newFileChooserPromise = page.waitForEvent("filechooser");
    await page.click(".uppy-Dashboard-browse");
    const newFileChooser = await newFileChooserPromise;
    await newFileChooser.setFiles(newLogoPath);

    // Verify new logo appears in uploader preview
    await expect(page.locator(".uppy-Dashboard-Item-previewImg")).toBeVisible();
    await expect(page.locator('img[alt="G10Klogo.jpg"]')).toBeVisible();

    // Submit new logo and verify it appears
    await page.click('button[aria-label="Upload 1 file"]');
    await sleep(500);
    await page.waitForSelector("img.test-uploaded-project-logo");
    await expect(page.locator("img.test-uploaded-project-logo")).toBeVisible();
    imagePath = await page
      .locator("img.test-uploaded-project-logo")
      .getAttribute("src");
    expect(imagePath).toContain("G10Klogo.jpg");

    // Update project details and submit
    await page.getByLabel("Name").fill("Updated Test Project");
    await page.getByLabel("URL").fill("https://updated-test-project.org");
    await page.click('input[type="submit"]');

    // Verify project was updated with correct details
    await page.waitForURL(/adminProject\/view/);
    await expect(page.getByText("Updated Test Project")).toBeVisible();
    await expect(page.getByText("https://updated-test-project.org")).toBeVisible();
    await expect(page.getByText("G10Klogo.jpg")).toBeVisible();

    // DELETE
    // Clean up by deleting the project
    await page.goto("adminProject/admin");
    await expect(page.locator(`a[href="/adminProject/delete/id/${projectId}"]`)).toBeVisible();
    page.on('dialog', dialog => dialog.accept());
    await page.click(`a[href="/adminProject/delete/id/${projectId}"]`);
    await expect(page.locator(`a[href="/adminProject/delete/id/${projectId}"]`)).not.toBeVisible();
  });

  test("cannot upload an image taller than 60px", async ({ page }) => {
    await page.goto("adminProject/create");
    const logoPath = path.join(__dirname, "../images/tall-image.png");
    const fileChooserPromise = page.waitForEvent("filechooser");
    await page.click(".uppy-Dashboard-browse");
    const fileChooser = await fileChooserPromise;
    await fileChooser.setFiles(logoPath);
    await sleep(500);
    await expect(page.locator(".uppy-ImageCropper")).toBeVisible();
    await page.click("button.uppy-DashboardContent-save");
    await sleep(500);
    await expect(page.locator('img[alt="tall-image.png"]')).toBeVisible();
    await page.click('button[aria-label="Upload 1 file"]');
    await sleep(2000);
    await expect(page.locator("img.test-uploaded-project-logo")).not.toBeVisible();
    await expect(page.getByText("You did not yet upload a logo")).toBeVisible();
  });
});
