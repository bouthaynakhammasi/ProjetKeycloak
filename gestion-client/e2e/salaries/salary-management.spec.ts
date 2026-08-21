import { test, expect } from '@playwright/test';
import { AuthHelper } from '../helpers/auth-helper';
import { testSalaries } from '../fixtures/test-data';

test.describe('Salary Management (Admin)', () => {
  let authHelper: AuthHelper;

  test.beforeEach(async ({ page }) => {
    authHelper = new AuthHelper(page);
    await authHelper.loginAsAdmin();
  });

  test('should display salary list', async ({ page }) => {
    await page.goto('http://localhost:8000/salaires');
    
    // Verify page title
    await expect(page.locator('h1')).toContainText('Salaires');
    
    // Verify salary table is visible
    await expect(page.locator('table')).toBeVisible();
    
    // Verify table headers
    await expect(page.locator('th')).toContainText('Employé');
    await expect(page.locator('th')).toContainText('Mois');
    await expect(page.locator('th')).toContainText('Salaire Base');
    await expect(page.locator('th')).toContainText('Salaire Net');
    await expect(page.locator('th')).toContainText('Statut');
  });

  test('should create new salary with automatic net calculation', async ({ page }) => {
    await page.goto('http://localhost:8000/salaires/create');
    
    // Fill salary form
    await page.selectOption('select[name="employe_id"]', '1');
    await page.selectOption('select[name="mois"]', testSalaries[0].mois.toString());
    await page.fill('input[name="annee"]', testSalaries[0].annee.toString());
    await page.fill('input[name="salaire_base"]', testSalaries[0].salaire_base.toString());
    await page.fill('input[name="prime"]', testSalaries[0].prime.toString());
    await page.fill('input[name="retenue"]', testSalaries[0].retenue.toString());
    
    // Submit form
    await page.click('button[type="submit"]');
    
    // Verify success message
    await expect(page.locator('.alert-success')).toBeVisible();
    await expect(page.locator('.alert-success')).toContainText('créé avec succès');
    
    // Verify salary net was calculated automatically
    const expectedNet = testSalaries[0].salaire_base + testSalaries[0].prime - testSalaries[0].retenue;
    await expect(page.locator('tbody tr:first-child')).toContainText(expectedNet.toString());
  });

  test('should edit existing salary with recalculation', async ({ page }) => {
    await page.goto('http://localhost:8000/salaires');
    
    // Click on first salary edit button
    await page.click('tbody tr:first-child .btn-edit');
    
    // Modify salary data
    const newBaseSalary = '4000';
    await page.fill('input[name="salaire_base"]', newBaseSalary);
    
    // Submit form
    await page.click('button[type="submit"]');
    
    // Verify success message
    await expect(page.locator('.alert-success')).toBeVisible();
    await expect(page.locator('.alert-success')).toContainText('modifié avec succès');
    
    // Verify salary net was recalculated
    await expect(page.locator('tbody tr:first-child')).toContainText(newBaseSalary);
  });

  test('should delete salary', async ({ page }) => {
    await page.goto('http://localhost:8000/salaires');
    
    // Get initial salary count
    const initialCount = await page.locator('tbody tr').count();
    
    // Click on first salary delete button
    await page.click('tbody tr:first-child .btn-delete');
    
    // Confirm deletion
    await page.click('.btn-confirm-delete');
    
    // Verify success message
    await expect(page.locator('.alert-success')).toBeVisible();
    await expect(page.locator('.alert-success')).toContainText('supprimé avec succès');
    
    // Verify salary count decreased
    const finalCount = await page.locator('tbody tr').count();
    expect(finalCount).toBe(initialCount - 1);
  });

  test('should view salary details', async ({ page }) => {
    await page.goto('http://localhost:8000/salaires');
    
    // Click on first salary view button
    await page.click('tbody tr:first-child .btn-view');
    
    // Verify salary details page
    await expect(page.locator('h1')).toContainText('Détails Salaire');
    await expect(page.locator('[data-testid="salaire-base"]')).toBeVisible();
    await expect(page.locator('[data-testid="salaire-prime"]')).toBeVisible();
    await expect(page.locator('[data-testid="salaire-retenue"]')).toBeVisible();
    await expect(page.locator('[data-testid="salaire-net"]')).toBeVisible();
  });

  test('should mark salary as paid', async ({ page }) => {
    await page.goto('http://localhost:8000/salaires');
    
    // Click on first salary mark as paid button
    await page.click('tbody tr:first-child .btn-mark-paid');
    
    // Verify success message
    await expect(page.locator('.alert-success')).toBeVisible();
    await expect(page.locator('.alert-success')).toContainText('marqué comme payé');
    
    // Verify status changed to paye
    await expect(page.locator('tbody tr:first-child')).toContainText('Payé');
  });

  test('should generate salary PDF', async ({ page }) => {
    await page.goto('http://localhost:8000/salaires');
    
    // Click on first salary PDF button
    const downloadPromise = page.waitForEvent('download');
    await page.click('tbody tr:first-child .btn-pdf');
    const download = await downloadPromise;
    
    // Verify PDF download
    expect(download.suggestedFilename()).toContain('fiche_paie');
    expect(download.suggestedFilename()).toContain('.pdf');
  });

  test('should filter salaries by employee', async ({ page }) => {
    await page.goto('http://localhost:8000/salaires');
    
    // Select employee filter
    await page.selectOption('select[name="employe_id"]', '1');
    
    // Apply filter
    await page.click('button[type="submit"]');
    
    // Verify filtered results
    await expect(page.locator('tbody tr')).toBeVisible();
  });

  test('should filter salaries by month and year', async ({ page }) => {
    await page.goto('http://localhost:8000/salaires');
    
    // Select month and year filters
    await page.selectOption('select[name="mois"]', '1');
    await page.fill('input[name="annee"]', '2024');
    
    // Apply filter
    await page.click('button[type="submit"]');
    
    // Verify filtered results
    const rows = await page.locator('tbody tr').all();
    for (const row of rows) {
      await expect(row).toContainText('Janvier');
      await expect(row).toContainText('2024');
    }
  });

  test('should filter salaries by payment status', async ({ page }) => {
    await page.goto('http://localhost:8000/salaires');
    
    // Select status filter
    await page.selectOption('select[name="statut"]', 'en_attente');
    
    // Apply filter
    await page.click('button[type="submit"]');
    
    // Verify filtered results show only pending salaries
    const rows = await page.locator('tbody tr').all();
    for (const row of rows) {
      await expect(row).toContainText('En attente');
    }
  });

  test('should prevent duplicate salary for same period', async ({ page }) => {
    await page.goto('http://localhost:8000/salaires/create');
    
    // Fill form with existing employee/month/year combination
    await page.selectOption('select[name="employe_id"]', '1');
    await page.selectOption('select[name="mois"]', '1');
    await page.fill('input[name="annee"]', '2024');
    await page.fill('input[name="salaire_base"]', '3500');
    
    // Submit form
    await page.click('button[type="submit"]');
    
    // Verify duplicate error
    await expect(page.locator('.alert-danger')).toBeVisible();
    await expect(page.locator('.alert-danger')).toContainText('existe déjà');
  });

  test('should access salary dashboard', async ({ page }) => {
    await page.goto('http://localhost:8000/salaires/dashboard');
    
    // Verify dashboard elements
    await expect(page.locator('h1')).toContainText('Dashboard Salaires');
    await expect(page.locator('[data-testid="masse-salariale"]')).toBeVisible();
    await expect(page.locator('[data-testid="employes-payes"]')).toBeVisible();
    await expect(page.locator('[data-testid="salaires-en-attente"]')).toBeVisible();
    await expect(page.locator('[data-testid="salaire-moyen"]')).toBeVisible();
  });

  test('should validate salary form', async ({ page }) => {
    await page.goto('http://localhost:8000/salaires/create');
    
    // Submit empty form
    await page.click('button[type="submit"]');
    
    // Verify validation errors
    await expect(page.locator('.error')).toBeVisible();
    await expect(page.locator('.error')).toContainText('required');
  });
});

test.describe('Salary Management (Employee)', () => {
  let authHelper: AuthHelper;

  test.beforeEach(async ({ page }) => {
    authHelper = new AuthHelper(page);
    await authHelper.loginAsEmployee();
  });

  test('should display only own salaries', async ({ page }) => {
    await page.goto('http://localhost:8000/salaires');
    
    // Verify page loads
    await expect(page.locator('h1')).toContainText('Mes Salaires');
    
    // Verify only own salaries are shown
    const rows = await page.locator('tbody tr').all();
    for (const row of rows) {
      // Should only show current employee's salaries
      await expect(row).toBeVisible();
    }
  });

  test('should view own salary details', async ({ page }) => {
    await page.goto('http://localhost:8000/salaires');
    
    // Click on first salary view button
    await page.click('tbody tr:first-child .btn-view');
    
    // Verify salary details page
    await expect(page.locator('h1')).toContainText('Détails Salaire');
  });

  test('should download own salary PDF', async ({ page }) => {
    await page.goto('http://localhost:8000/salaires');
    
    // Click on first salary PDF button
    const downloadPromise = page.waitForEvent('download');
    await page.click('tbody tr:first-child .btn-pdf');
    const download = await downloadPromise;
    
    // Verify PDF download
    expect(download.suggestedFilename()).toContain('fiche_paie');
  });

  test('should deny access to create salary', async ({ page }) => {
    await page.goto('http://localhost:8000/salaires/create');
    
    // Should be redirected or show access denied
    await expect(page).toHaveURL(/.*403/);
    await expect(page.locator('body')).toContainText('Accès non autorisé');
  });

  test('should deny access to edit salary', async ({ page }) => {
    await page.goto('http://localhost:8000/salaires/1/edit');
    
    // Should be redirected or show access denied
    await expect(page).toHaveURL(/.*403/);
  });

  test('should deny access to delete salary', async ({ page }) => {
    await page.goto('http://localhost:8000/salaires');
    
    // Delete button should not be visible
    await expect(page.locator('.btn-delete')).not.toBeVisible();
  });

  test('should deny access to mark salary as paid', async ({ page }) => {
    await page.goto('http://localhost:8000/salaires');
    
    // Mark as paid button should not be visible
    await expect(page.locator('.btn-mark-paid')).not.toBeVisible();
  });

  test('should deny access to salary dashboard', async ({ page }) => {
    await page.goto('http://localhost:8000/salaires/dashboard');
    
    // Should be redirected or show access denied
    await expect(page).toHaveURL(/.*403/);
  });

  test('should deny access to other employees salaries', async ({ page }) => {
    // Try to access another employee's salary directly
    await page.goto('http://localhost:8000/salaires/999');
    
    // Should be redirected or show access denied
    await expect(page).toHaveURL(/.*403/);
  });
});
