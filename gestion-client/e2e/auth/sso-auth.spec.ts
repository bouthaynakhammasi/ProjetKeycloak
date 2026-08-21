import { test, expect } from '@playwright/test';
import { AuthHelper } from '../helpers/auth-helper';
import { testUsers } from '../fixtures/test-data';

test.describe.configure({ mode: 'serial' });

test.describe('SSO Authentication Flow', () => {
  let authHelper: AuthHelper;

  test.beforeEach(async ({ page }) => {
    authHelper = new AuthHelper(page);
  });

  test('should redirect to Keycloak login page', async ({ page }) => {
    await page.goto('/');
    await page.getByRole('link', { name: 'Login' }).first().click();

    await expect(page).toHaveURL(/.*localhost:8080.*\/realms\/CompanyRealm.*/);
    await expect(page.locator('input[name="username"]')).toBeVisible();
    await expect(page.locator('input[name="password"]')).toBeVisible();
  });

  test('should login successfully with valid admin credentials', async ({ page }) => {
    await authHelper.loginAsAdmin();
    
    // Verify redirect to dashboard
    await expect(page).toHaveURL('http://localhost:8000/dashboard');
    await expect(page.locator('body')).toContainText('Tableau de bord');
    
    // Verify admin role is displayed
    const userRole = await authHelper.getCurrentUserRole();
    expect(userRole).toContain('ROLE_ADMIN');
  });

  test('should login successfully with valid employee credentials', async ({ page }) => {
    await authHelper.loginAsEmployee();

    // Verify redirect to pending page (test employee account is not activated)
    await expect(page).toHaveURL('http://localhost:8000/pending');
    await expect(page.locator('body')).toContainText('Compte en attente');

    // Note: Cannot verify role on pending page as user menu is not available
  });

  test('should show error with invalid credentials', async ({ page }) => {
    await page.goto('/');
    await page.getByRole('link', { name: 'Login' }).first().click();

    // Wait for Keycloak redirect
    await expect(page).toHaveURL(/.*localhost:8080.*\/realms\/CompanyRealm.*/);

    // Fill with invalid credentials
    await page.fill('input[name="username"]', 'invalid@test.com');
    await page.fill('input[name="password"]', 'wrongpassword');

    // Submit login
    await page.click('#kc-login');

    // Should show error message (Keycloak uses .kc-feedback-text for errors)
    await expect(page.locator('.kc-feedback-text')).toBeVisible();
    await expect(page.locator('.kc-feedback-text')).toContainText('Invalid');
  });

  test('should handle pending user account', async ({ page }) => {
    await page.goto('/');
    await page.getByRole('link', { name: 'Login' }).first().click();

    // Wait for Keycloak redirect
    await expect(page).toHaveURL(/.*localhost:8080.*\/realms\/CompanyRealm.*/);

    // Login as pending user
    await page.fill('input[name="username"]', testUsers.pendingUser.email);
    await page.fill('input[name="password"]', testUsers.pendingUser.password);
    await page.click('#kc-login');

    // Should redirect to pending account page
    await expect(page).toHaveURL('http://localhost:8000/pending');
    await expect(page.locator('body')).toContainText('en attente');
  });

  test('should logout successfully', async ({ page }) => {
    await authHelper.loginAsAdmin();
    
    // Logout
    await authHelper.logout();
    
    // Verify redirect to Keycloak login page (proof the session was destroyed)
    await expect(page).toHaveURL(/localhost:8080.*\/auth/);
    await authHelper.verifyUserIsLoggedOut();
  });

  test('should maintain session across page navigation', async ({ page }) => {
    await authHelper.loginAsAdmin();
    
    // Navigate to different pages
    await page.goto('http://localhost:8000/employes');
    await expect(page).toHaveURL('http://localhost:8000/employes');
    
    await page.goto('http://localhost:8000/salaires');
    await expect(page).toHaveURL('http://localhost:8000/salaires');
    
    // Verify still logged in
    await authHelper.verifyUserIsLoggedIn();
  });

  test('should handle session expiration', async ({ page }) => {
    await authHelper.loginAsAdmin();
    
    // Simulate session expiration by clearing cookies
    await page.context().clearCookies();
    
    // Try to access protected route
    await page.goto('http://localhost:8000/employes');
    
    // Should redirect to login
    await expect(page).toHaveURL(/.*localhost:8080.*\/realms\/CompanyRealm.*/);
  });

  test('should sync user data from Keycloak on first login', async ({ page }) => {
    await authHelper.loginAsEmployee();

    // L'employé non activé est automatiquement redirigé vers /pending après login
    await expect(page).toHaveURL(/localhost:8000\/pending/);
    await expect(page.locator('[data-testid="user-email"]')).toHaveText(testUsers.employee.email);
    await expect(page.locator('[data-testid="user-name"]')).toHaveText(testUsers.employee.name);
  });

  test('should redirect to correct dashboard based on role', async ({ page }) => {
    // Test admin redirect
    await authHelper.loginAsAdmin();
    await expect(page).toHaveURL('http://localhost:8000/dashboard');
    await page.close();
    
    // Test employee redirect
    const context = await page.context().browser()?.newContext();
    const employeePage = await context?.newPage();
    if (employeePage) {
      const employeeAuth = new AuthHelper(employeePage);
      await employeeAuth.loginAsEmployee();
      await expect(employeePage).toHaveURL('http://localhost:8000/pending');
      await employeePage.close();
    }
  });
});
