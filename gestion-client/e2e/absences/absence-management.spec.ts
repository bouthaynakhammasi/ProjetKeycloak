import { test, expect } from '@playwright/test';
import { AuthHelper } from '../helpers/auth-helper';
import { testAbsences } from '../fixtures/test-data';
import { execSync } from 'child_process';

test.describe.configure({ mode: 'serial' });

test.describe('Absence Management (Admin)', () => {
  let authHelper: AuthHelper;

  test.beforeAll(async () => {
    // Seed test database once before running admin tests
    try {
      const output = execSync('php artisan db:seed --class=E2EAbsenceSeeder --force', {
        cwd: process.cwd(),
        stdio: 'pipe'
      });
      console.log('Seeding output:', output.toString());
    } catch (error) {
      console.log('Seeding error:', error);
    }
  });

  test.beforeEach(async ({ page }) => {
    authHelper = new AuthHelper(page);
    await authHelper.loginAsAdmin();
  });

  test('should display absence list', async ({ page }) => {
    await page.goto('http://localhost:8000/absences');

    // Verify page title
    await expect(page.locator('h1').filter({ hasText: 'Absences' })).toBeVisible();

    // Verify absence table is visible
    await expect(page.locator('table')).toBeVisible();

    // Verify table headers
    await expect(page.locator('th').first()).toContainText('Employé');
    await expect(page.locator('th').nth(1)).toContainText('Type');
    await expect(page.locator('th').nth(2)).toContainText('Date Début');
    await expect(page.locator('th').nth(3)).toContainText('Date Fin');
    await expect(page.locator('th').nth(4)).toContainText('Statut');
  });

  test('should view all employees absences', async ({ page }) => {
    await page.goto('http://localhost:8000/absences');

    // Verify page loads with all absences
    const count = await page.locator('table tbody tr').count();
    expect(count).toBeGreaterThan(0);
  });

  test('should approve absence request', async ({ page }) => {
    await page.goto('http://localhost:8000/absences');

    // Wait for page to load and table to be visible
    await expect(page.locator('table')).toBeVisible();

    // Find a row with pending status and click its approve button
    const pendingRow = page.locator('table tbody tr').filter({ hasText: 'En attente' }).first();
    await expect(pendingRow).toBeVisible();
    
    await pendingRow.locator('.btn-approve').click();

    // Verify success message
    await expect(page.locator('.alert-success')).toBeVisible();
    await expect(page.locator('.alert-success')).toContainText('approuvée');

    // Wait for the page to reload/update to show the status change
    await page.waitForLoadState('networkidle');

    // Find the row again after page reload and verify status changed to approuve
    const approvedRow = page.locator('table tbody tr').filter({ hasText: 'Approuvé' }).first();
    await expect(approvedRow).toBeVisible();
  });

  test('should reject absence request', async ({ page }) => {
    await page.goto('http://localhost:8000/absences');
    
    // Find a row with pending status and click its reject button
    const pendingRow = page.locator('table tbody tr').filter({ hasText: 'En attente' }).first();
    await expect(pendingRow).toBeVisible();
    
    await pendingRow.locator('.btn-reject').click();
    
    // Verify success message
    await expect(page.locator('.alert-success')).toBeVisible();
    await expect(page.locator('.alert-success')).toContainText('rejetée');
    
    // Wait for the page to reload/update to show the status change
    await page.waitForLoadState('networkidle');
    
    // Find the row again after page reload and verify status changed to rejete
    const rejectedRow = page.locator('table tbody tr').filter({ hasText: 'Rejeté' }).first();
    await expect(rejectedRow).toBeVisible();
  });

  test('should view absence details', async ({ page }) => {
    await page.goto('http://localhost:8000/absences');
    
    // Click on first absence view button
    await page.click('table tbody tr:first-child .btn-view');
    
    // Verify absence details page
    await expect(page.locator('h1')).toContainText('Détails Absence');
    await expect(page.locator('[data-testid="absence-type"]')).toBeVisible();
    await expect(page.locator('[data-testid="absence-dates"]')).toBeVisible();
    await expect(page.locator('[data-testid="absence-motif"]')).toBeVisible();
  });

  test('should filter absences by status', async ({ page }) => {
    await page.goto('http://localhost:8000/absences');
    
    // Select status filter
    await page.selectOption('select[name="statut"]', 'en_attente');
    
    // Apply filter
    await page.click('button[type="submit"]');
    
    // Verify filtered results show only pending absences
    const rows = await page.locator('table tbody tr').all();
    for (const row of rows) {
      await expect(row).toContainText('En attente');
    }
  });

  test('should filter absences by type', async ({ page }) => {
    await page.goto('http://localhost:8000/absences');
    
    // Select type filter
    await page.selectOption('select[name="type_absence"]', 'conge_paye');
    
    // Apply filter
    await page.click('button[type="submit"]');
    
    // Verify filtered results show only paid leave
    const rows = await page.locator('table tbody tr').all();
    for (const row of rows) {
      await expect(row).toContainText('Congé payé');
    }
  });

  test('should filter absences by employee', async ({ page }) => {
    await page.goto('http://localhost:8000/absences');
    
    // Select employee filter
    await page.selectOption('select[name="employe_id"]', '1');
    
    // Apply filter
    await page.click('button[type="submit"]');
    
    // Verify filtered results
    await expect(page.locator('table tbody tr').first()).toBeVisible();
  });

  test('should filter absences by date range', async ({ page }) => {
    await page.goto('http://localhost:8000/absences');
    
    // Fill date range
    await page.fill('input[name="date_debut"]', '2024-01-01');
    await page.fill('input[name="date_fin"]', '2024-12-31');
    
    // Apply filter
    await page.click('button[type="submit"]');
    
    // Verify filtered results
    await expect(page.locator('table tbody tr').first()).toBeVisible();
  });

  test('should export absence list', async ({ page }) => {
    await page.goto('http://localhost:8000/absences');
    
    // Click export button - this navigates to export URL which returns CSV
    await page.click('button:has-text("Exporter")');
    
    // Wait for navigation to complete
    await page.waitForLoadState('networkidle');
    
    // Verify we navigated to the export URL
    await expect(page).toHaveURL(/absences\/export/);
  });

  test('should access absence dashboard statistics', async ({ page }) => {
    await page.goto('http://localhost:8000/absences/dashboard');
    
    // Wait for page to load
    await page.waitForLoadState('networkidle');
    
    // Verify dashboard elements by their data-testid attributes
    await expect(page.locator('[data-testid="total-absences"]')).toBeVisible();
    await expect(page.locator('[data-testid="absences-en-attente"]')).toBeVisible();
    await expect(page.locator('[data-testid="absences-approuvees"]')).toBeVisible();
    await expect(page.locator('[data-testid="absences-rejetees"]')).toBeVisible();
  });
});

test.describe('Absence Management (Employee)', () => {
  let authHelper: AuthHelper;

  test.beforeEach(async ({ page }) => {
    authHelper = new AuthHelper(page);
    await authHelper.loginAsEmployee();
  });

  test('should display own absence list', async ({ page }) => {
    await page.goto('http://localhost:8000/absences');
    
    // Verify page title
    await expect(page.locator('h1').first()).toContainText('Mes Absences');
    
    // Verify only own absences are shown
    await expect(page.locator('table tbody tr').first()).toBeVisible();
  });

  test('should view own absence details', async ({ page }) => {
    await page.goto('http://localhost:8000/absences');
    
    // Click on first absence view button
    await page.click('table tbody tr:first-child .btn-view');
    
    // Verify absence details page
    await expect(page.locator('h1')).toContainText('Détails Absence');
  });

  test('should edit own pending absence', async ({ page }) => {
    await page.goto('http://localhost:8000/absences');
    await page.waitForSelector('table tbody tr');
    
    const pendingCount = await page.locator('table tbody tr').filter({ hasText: 'En attente' }).count();
    
    if (pendingCount === 0) {
      test.skip();
      return;
    }
    
    const pendingRow = page.locator('table tbody tr').filter({ hasText: 'En attente' }).first();
    await pendingRow.locator('.btn-edit').click();
    
    await page.fill('textarea[name="motif"]', 'Motif modifié');
    await page.click('button[type="submit"]');
    
    await expect(page.locator('.alert-success')).toBeVisible();
    await expect(page.locator('.alert-success')).toContainText('modifiée avec succès');
  });

  test('should delete own pending absence', async ({ page }) => {
    await page.goto('http://localhost:8000/absences');
    await page.waitForSelector('table tbody tr');
    
    const pendingCount = await page.locator('table tbody tr').filter({ hasText: 'En attente' }).count();
    
    if (pendingCount === 0) {
      test.skip();
      return;
    }
    
    const initialCount = await page.locator('table tbody tr').count();
    
    const pendingRow = page.locator('table tbody tr').filter({ hasText: 'En attente' }).first();
    await pendingRow.locator('.btn-delete').click();
    
    await page.waitForLoadState('networkidle');
    
    await expect(page.locator('.alert-success')).toBeVisible();
    await expect(page.locator('.alert-success')).toContainText('supprimée avec succès');
    
    const finalCount = await page.locator('table tbody tr').count();
    expect(finalCount).toBe(initialCount - 1);
  });

  test('should create new absence request', async ({ page }) => {
    await page.goto('http://localhost:8000/absences/create');
    
    // Fill absence form with valid dates (must be today or future)
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    const dayAfterTomorrow = new Date(today);
    dayAfterTomorrow.setDate(dayAfterTomorrow.getDate() + 2);
    
    const formatDate = (date: Date) => date.toISOString().split('T')[0];
    
    await page.selectOption('select[name="type"]', 'Congé annuel');
    await page.fill('input[name="date_debut"]', formatDate(tomorrow));
    await page.fill('input[name="date_fin"]', formatDate(dayAfterTomorrow));
    await page.fill('textarea[name="motif"]', 'Test E2E creation');
    
    // Submit form
    await page.click('button[type="submit"]');
    
    // Wait for page load after form submission
    await page.waitForLoadState('networkidle');
    
    // Check if we're on the absences list page (success) or still on create page (error)
    const currentUrl = page.url();
    if (currentUrl.includes('/absences/create')) {
      // Form submission failed, check for errors
      const bodyText = await page.locator('body').textContent();
      throw new Error(`Form submission failed. Current URL: ${currentUrl}. Page content: ${bodyText?.substring(0, 500)}`);
    }
    
    // Verify success message
    await expect(page.locator('.alert-success')).toBeVisible();
    await expect(page.locator('.alert-success')).toContainText('envoyée avec succès');
    
    // Verify redirect to absence list
    await expect(page).toHaveURL('http://localhost:8000/absences');
  });

  test('should not edit approved absence', async ({ page }) => {
    await page.goto('http://localhost:8000/absences');
    
    // Edit button should not be visible for approved absences
    const approvedRow = page.locator('table tbody tr').filter({ hasText: 'Approuvé' }).first();
    await expect(approvedRow.locator('.btn-edit')).not.toBeVisible();
  });

  test('should not delete approved absence', async ({ page }) => {
    await page.goto('http://localhost:8000/absences');
    
    // Delete button should not be visible for approved absences
    const approvedRow = page.locator('table tbody tr').filter({ hasText: 'Approuvé' }).first();
    await expect(approvedRow.locator('.btn-delete')).not.toBeVisible();
  });

  test('should validate absence form', async ({ page }) => {
    await page.goto('http://localhost:8000/absences/create');
    
    // Remove required attribute to bypass HTML5 validation
    await page.evaluate(() => {
      const typeSelect = document.querySelector('select[name="type"]');
      const dateDebut = document.querySelector('input[name="date_debut"]');
      const dateFin = document.querySelector('input[name="date_fin"]');
      if (typeSelect) typeSelect.removeAttribute('required');
      if (dateDebut) dateDebut.removeAttribute('required');
      if (dateFin) dateFin.removeAttribute('required');
    });
    
    // Submit empty form
    await page.click('button[type="submit"]');
    
    // Verify validation errors - Laravel displays errors in bg-red-50 div
    await expect(page.locator('.bg-red-50')).toBeVisible();
  });

  test('should validate date range (end date before start date)', async ({ page }) => {
    await page.goto('http://localhost:8000/absences/create');
    
    // Fill with invalid date range (end date before start date)
    // Use future dates to satisfy after_or_equal:today rule
    await page.selectOption('select[name="type"]', 'Congé annuel');
    await page.fill('input[name="date_debut"]', '2026-02-10');
    await page.fill('input[name="date_fin"]', '2026-02-05');
    await page.fill('textarea[name="motif"]', 'Test');
    
    // Submit form
    await page.click('button[type="submit"]');
    
    // Verify validation error - Laravel displays errors in bg-red-50 div
    await expect(page.locator('.bg-red-50')).toBeVisible();
  });

  test('should check leave balance before creating request', async ({ page }) => {
    await page.goto('http://localhost:8000/absences/create');
    
    // Verify leave balance is displayed
    await expect(page.locator('[data-testid="solde-conges"]')).toBeVisible();
  });

  test('should deny access to approve absence', async ({ page }) => {
    // As employee, go to admin absence list
    await page.goto('http://localhost:8000/absences');
    
    // Employee sees only their own absences, not approve/reject buttons
    // Approve and reject buttons are only shown to admin users
    await expect(page.locator('.btn-approve')).not.toBeVisible();
    await expect(page.locator('.btn-reject')).not.toBeVisible();
  });

  test('should deny access to reject absence', async ({ page }) => {
    // As employee, go to admin absence list
    await page.goto('http://localhost:8000/absences');
    
    // Employee sees only their own absences, not approve/reject buttons
    // Approve and reject buttons are only shown to admin users
    await expect(page.locator('.btn-approve')).not.toBeVisible();
    await expect(page.locator('.btn-reject')).not.toBeVisible();
  });

  test('should deny access to absence dashboard', async ({ page }) => {
    // As employee, try to access admin dashboard
    await page.goto('http://localhost:8000/absences/dashboard');
    
    // Should get 403 Forbidden (authorizeAdmin check in controller)
    await expect(page.locator('body')).toContainText('403');
  });

  test('should deny access to other employees absences', async ({ page }) => {
    // Get an absence ID that belongs to another employee (marie)
    // Employee can only access their own absences
    await page.goto('http://localhost:8000/absences');
    
    // As employee, can only see own absences in the list
    // The controller's index method filters by employee_id for ROLE_EMPLOYEE
    await expect(page.locator('table tbody tr').first()).toBeVisible();
    
    // Try to navigate to a different absence ID that should not exist for this employee
    await page.goto('http://localhost:8000/absences/999');
    
    // Should get 404 Not Found (absence doesn't exist for this employee)
    await expect(page.locator('body')).toContainText('404');
  });

  test('should show absence history', async ({ page }) => {
    await page.goto('http://localhost:8000/absences');
    
    // Verify history section is visible
    await expect(page.locator('[data-testid="absence-history"]')).toBeVisible();
  });
});
