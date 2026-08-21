import { test, expect } from '@playwright/test';
import { AuthHelper } from '../helpers/auth-helper';
import { testEmployees } from '../fixtures/test-data';

test.describe('Employee Management (Admin)', () => {
  let authHelper: AuthHelper;

  test.beforeEach(async ({ page }) => {
    authHelper = new AuthHelper(page);
    await authHelper.loginAsAdmin();
  });

  test('should display employee list', async ({ page }) => {
    await page.goto('http://localhost:8000/employes');
    
    // Verify page title
    await expect(page.locator('h1')).toContainText('Employés');
    
    // Verify employee table is visible
    await expect(page.locator('table')).toBeVisible();
    
    // Verify table headers
    await expect(page.locator('th')).toContainText('Nom');
    await expect(page.locator('th')).toContainText('Email');
    await expect(page.locator('th')).toContainText('Poste');
    await expect(page.locator('th')).toContainText('Département');
  });

  test('should create new employee', async ({ page }) => {
    await page.goto('http://localhost:8000/employes/create');
    
    // Fill employee form
    await page.fill('input[name="nom"]', testEmployees[0].nom);
    await page.fill('input[name="prenom"]', testEmployees[0].prenom);
    await page.fill('input[name="email"]', testEmployees[0].email);
    await page.fill('input[name="poste"]', testEmployees[0].poste);
    await page.selectOption('select[name="departement"]', testEmployees[0].departement);
    await page.fill('input[name="date_embauche"]', testEmployees[0].date_embauche);
    await page.fill('input[name="salaire_base"]', testEmployees[0].salaire_base.toString());
    
    // Submit form
    await page.click('button[type="submit"]');
    
    // Verify success message
    await expect(page.locator('.alert-success')).toBeVisible();
    await expect(page.locator('.alert-success')).toContainText('créé avec succès');
    
    // Verify redirect to employee list
    await expect(page).toHaveURL('http://localhost:8000/employes');
  });

  test('should edit existing employee', async ({ page }) => {
    await page.goto('http://localhost:8000/employes');
    
    // Click on first employee edit button
    await page.click('tbody tr:first-child .btn-edit');
    
    // Modify employee data
    await page.fill('input[name="poste"]', 'Senior Développeur');
    
    // Submit form
    await page.click('button[type="submit"]');
    
    // Verify success message
    await expect(page.locator('.alert-success')).toBeVisible();
    await expect(page.locator('.alert-success')).toContainText('modifié avec succès');
  });

  test('should delete employee', async ({ page }) => {
    await page.goto('http://localhost:8000/employes');
    
    // Get initial employee count
    const initialCount = await page.locator('tbody tr').count();
    
    // Click on first employee delete button
    await page.click('tbody tr:first-child .btn-delete');
    
    // Confirm deletion
    await page.click('.btn-confirm-delete');
    
    // Verify success message
    await expect(page.locator('.alert-success')).toBeVisible();
    await expect(page.locator('.alert-success')).toContainText('supprimé avec succès');
    
    // Verify employee count decreased
    const finalCount = await page.locator('tbody tr').count();
    expect(finalCount).toBe(initialCount - 1);
  });

  test('should view employee details', async ({ page }) => {
    await page.goto('http://localhost:8000/employes');
    
    // Click on first employee view button
    await page.click('tbody tr:first-child .btn-view');
    
    // Verify employee details page
    await expect(page.locator('h1')).toContainText('Détails Employé');
    await expect(page.locator('[data-testid="employee-nom"]')).toBeVisible();
    await expect(page.locator('[data-testid="employee-email"]')).toBeVisible();
    await expect(page.locator('[data-testid="employee-poste"]')).toBeVisible();
  });

  test('should filter employees by department', async ({ page }) => {
    await page.goto('http://localhost:8000/employes');
    
    // Select department filter
    await page.selectOption('select[name="departement"]', 'IT');
    
    // Apply filter
    await page.click('button[type="submit"]');
    
    // Verify filtered results
    const rows = await page.locator('tbody tr').all();
    for (const row of rows) {
      await expect(row).toContainText('IT');
    }
  });

  test('should search employees by name', async ({ page }) => {
    await page.goto('http://localhost:8000/employes');
    
    // Enter search term
    await page.fill('input[name="search"]', 'Dupont');
    
    // Submit search
    await page.click('button[type="submit"]');
    
    // Verify search results
    await expect(page.locator('tbody tr')).toContainText('Dupont');
  });

  test('should validate employee form', async ({ page }) => {
    await page.goto('http://localhost:8000/employes/create');
    
    // Submit empty form
    await page.click('button[type="submit"]');
    
    // Verify validation errors
    await expect(page.locator('.error')).toBeVisible();
    await expect(page.locator('.error')).toContainText('required');
  });

  test('should handle duplicate email error', async ({ page }) => {
    await page.goto('http://localhost:8000/employes/create');
    
    // Fill form with existing email
    await page.fill('input[name="nom"]', 'Test');
    await page.fill('input[name="prenom"]', 'User');
    await page.fill('input[name="email"]', testEmployees[0].email);
    await page.fill('input[name="poste"]', 'Test');
    
    // Submit form
    await page.click('button[type="submit"]');
    
    // Verify duplicate email error
    await expect(page.locator('.alert-danger')).toBeVisible();
    await expect(page.locator('.alert-danger')).toContainText('email existe déjà');
  });

  test('should export employee list', async ({ page }) => {
    await page.goto('http://localhost:8000/employes');
    
    // Click export button
    const downloadPromise = page.waitForEvent('download');
    await page.click('button:has-text("Exporter")');
    const download = await downloadPromise;
    
    // Verify file download
    expect(download.suggestedFilename()).toContain('employes');
  });
});

test.describe('Employee Management (Employee - Unauthorized)', () => {
  let authHelper: AuthHelper;

  test.beforeEach(async ({ page }) => {
    authHelper = new AuthHelper(page);
    await authHelper.loginAsEmployee();
  });

  test('should deny access to employee list', async ({ page }) => {
    await page.goto('http://localhost:8000/employes');
    
    // Should be redirected or show access denied
    await expect(page).toHaveURL(/.*403/);
    await expect(page.locator('body')).toContainText('Accès non autorisé');
  });

  test('should deny access to create employee', async ({ page }) => {
    await page.goto('http://localhost:8000/employes/create');
    
    // Should be redirected or show access denied
    await expect(page).toHaveURL(/.*403/);
  });

  test('should deny access to edit employee', async ({ page }) => {
    await page.goto('http://localhost:8000/employes/1/edit');
    
    // Should be redirected or show access denied
    await expect(page).toHaveURL(/.*403/);
  });

  test('should deny access to delete employee', async ({ page }) => {
    await page.goto('http://localhost:8000/employes');
    
    // Delete button should not be visible
    await expect(page.locator('.btn-delete')).not.toBeVisible();
  });
});
